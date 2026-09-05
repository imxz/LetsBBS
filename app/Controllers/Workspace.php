<?php

namespace App\Controllers;

use App\Services\ContentPolicy;
use App\Services\HtmlSanitizer;
use App\Services\ModerationLog;

final class Workspace extends BaseController
{
    public function content(int $page = 1)
    {
        $page = max(1, $page);
        $kind = $this->request->getGet('kind') === 'comments' ? 'comments' : 'topics';
        $status = (string) $this->request->getGet('status');
        $builder = db_connect()->table($kind)->where('user_id', auth()->id());
        if (in_array($status, ['published', 'pending', 'hidden', 'deleted'], true)) {
            $builder->where('status', $status);
        } else {
            $status = '';
        }
        $rows = $builder
            ->orderBy('id', 'DESC')
            ->limit(21, ($page - 1) * 20)
            ->get()
            ->getResultArray();
        foreach ($rows as &$row) {
            $row['publicly_visible'] =
                $row['status'] === 'published' &&
                new \App\Models\ForumModel()->topic((int) ($kind === 'topics' ? $row['id'] : $row['topic_id'])) !==
                    null;
        }
        unset($row);
        return view(
            'member/content',
            [
                'title' => '我的内容',
                'kind' => $kind,
                'statusFilter' => $status,
                'rows' => array_slice($rows, 0, 20),
                'hasNext' => count($rows) > 20,
                'page' => $page,
            ] + $this->sidebarData(),
        );
    }

    public function preferences()
    {
        $db = db_connect();
        if ($this->request->getMethod() === 'POST') {
            $values = [];
            foreach (['notify_topics', 'notify_comments', 'notify_mentions', 'notify_follows'] as $key) {
                $values[$key] = $this->request->getPost($key) === '1' ? 1 : 0;
            }
            $db->table('user_profiles')->where('user_id', auth()->id())->update($values);
            return redirect()->back()->with('success', '通知偏好已保存。');
        }
        return view(
            'member/preferences',
            [
                'title' => '通知偏好',
                'profile' => $db->table('user_profiles')->where('user_id', auth()->id())->get()->getRowArray(),
            ] + $this->sidebarData(),
        );
    }

    public function subscriptions(int $page = 1)
    {
        $page = max(1, $page);
        $kind = (string) $this->request->getGet('kind');
        $db = db_connect();
        if ($kind === 'nodes') {
            $b = $db
                ->table('node_follows f')
                ->select('n.id,n.name label,n.slug,f.created_at')
                ->join('nodes n', 'n.id=f.node_id')
                ->where('f.user_id', auth()->id())
                ->where(ContentPolicy::nodeSql(), null, false);
        } elseif ($kind === 'users') {
            $b = $db
                ->table('user_follows f')
                ->select('u.id,u.username label,f.created_at')
                ->join('users u', 'u.id=f.followed_id')
                ->where('f.follower_id', auth()->id());
        } else {
            $kind = 'topics';
            $b = $db
                ->table('topic_follows f')
                ->select('t.id,t.title label,f.created_at')
                ->join('topics t', 't.id=f.topic_id')
                ->where('f.user_id', auth()->id())
                ->where(ContentPolicy::topicSql(), null, false);
        }
        $rows = $b
            ->orderBy('f.created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(21, ($page - 1) * 20)
            ->get()
            ->getResultArray();
        return view(
            'member/subscriptions',
            [
                'title' => '收藏与关注',
                'kind' => $kind,
                'rows' => array_slice($rows, 0, 20),
                'hasNext' => count($rows) > 20,
                'page' => $page,
            ] + $this->sidebarData(),
        );
    }

    public function edit(string $kind, int $id)
    {
        if (!in_array($kind, ['topics', 'comments'], true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $db = db_connect();
        $row = $db
            ->table($kind)
            ->where(['id' => $id, 'user_id' => auth()->id()])
            ->get()
            ->getRowArray();
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $allowed =
            in_array($row['status'], ['published', 'pending'], true) &&
            strtotime($row['created_at'] . ' UTC') >= time() - 1800;
        if (!$allowed) {
            return redirect()->to('/my/content')->with('error', '仅可在发表后 30 分钟内修改自己的已发布或待审核内容。');
        }
        if ($this->request->getMethod() === 'POST') {
            $db->transException(true)->transBegin();
            try {
                $locked = $db->query('SELECT * FROM ' . $kind . ' WHERE id=? FOR UPDATE', [$id])->getRowArray();
                if ($locked['status'] !== $row['status'] || $locked['updated_at'] !== $row['updated_at']) {
                    throw new \RuntimeException('内容状态已改变，请刷新后重试。');
                }
                $profile = $db->table('user_profiles')->where('user_id', auth()->id())->get()->getRowArray();
                if ($profile['is_muted']) {
                    throw new \RuntimeException('你已被禁言。');
                }
                $topic =
                    $kind === 'topics'
                        ? $row
                        : $db->table('topics')->where('id', $row['topic_id'])->get()->getRowArray();
                if (!$topic || ($kind === 'comments' && $topic['status'] !== 'published') || $topic['is_locked']) {
                    throw new \RuntimeException('主题当前不可修改。');
                }
                ContentPolicy::assertPostable($db, (int) $topic['node_id'], false);
                $body = new HtmlSanitizer()->clean((string) $this->request->getPost('body'));
                $title = trim((string) $this->request->getPost('title'));
                if ($body === '' || ($kind === 'topics' && (mb_strlen($title) < 2 || mb_strlen($title) > 160))) {
                    throw new \RuntimeException('请填写有效正文和 2–160 个字符的标题。');
                }
                $setting = $kind === 'topics' ? 'topic_requires_approval' : 'comment_requires_approval';
                $status =
                    !auth()->user()->inGroup('admin') && service('siteSettings')->get($setting, '0') === '1'
                        ? 'pending'
                        : $row['status'];
                if ($row['status'] === 'published' && $status === 'pending') {
                    if ($kind === 'topics') {
                        $db->query(
                            'UPDATE nodes SET topic_count=GREATEST(CAST(topic_count AS SIGNED)-1,0) WHERE id=?',
                            [$row['node_id']],
                        );
                        $db->query(
                            'UPDATE user_profiles SET topic_count=GREATEST(CAST(topic_count AS SIGNED)-1,0) WHERE user_id=?',
                            [auth()->id()],
                        );
                        new \App\Services\ContentCounters($db)->adjustVisibleCommentsForTopic(
                            $id,
                            -1,
                            gmdate('Y-m-d H:i:s'),
                        );
                    } else {
                        $db->query(
                            'UPDATE topics SET comment_count=GREATEST(CAST(comment_count AS SIGNED)-1,0) WHERE id=?',
                            [$row['topic_id']],
                        );
                        $db->query(
                            'UPDATE user_profiles SET comment_count=GREATEST(CAST(comment_count AS SIGNED)-1,0) WHERE user_id=?',
                            [auth()->id()],
                        );
                        $db->table('notifications')->where('comment_id', $id)->delete();
                        $db->query(
                            "UPDATE topics t SET last_activity_at=GREATEST(t.created_at,COALESCE((SELECT MAX(c.created_at) FROM comments c WHERE c.topic_id=t.id AND c.status='published'),t.created_at)) WHERE t.id=?",
                            [$row['topic_id']],
                        );
                    }
                }
                $values = ['body' => $body, 'status' => $status, 'updated_at' => gmdate('Y-m-d H:i:s')];
                if ($kind === 'topics') {
                    $values['title'] = $title;
                }
                $db->table($kind)->where('id', $id)->update($values);
                ModerationLog::record($kind === 'topics' ? 'topic' : 'comment', $id, 'author_edit');
                $db->transCommit();
                session()->setFlashdata('clearDraft', 'edit-' . $kind . '-' . $id);
                return redirect()
                    ->to('/my/content?kind=' . $kind)
                    ->with('success', '修改已保存；需要审核的内容将在通过后公开。');
            } catch (\Throwable $e) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        }
        return view(
            'member/edit_content',
            ['title' => '修改我的内容', 'kind' => $kind, 'row' => $row, 'editor' => true] + $this->sidebarData(),
        );
    }

    public function report(int $topicId)
    {
        $topic = new \App\Models\ForumModel()->topic($topicId);
        $commentId = (int) $this->request->getPost('comment_id') ?: null;
        $reason = mb_substr(trim((string) $this->request->getPost('reason')), 0, 500);
        if (
            !$topic ||
            $reason === '' ||
            ($commentId &&
                !db_connect()
                    ->table('comments')
                    ->where(['id' => $commentId, 'topic_id' => $topicId, 'status' => 'published'])
                    ->countAllResults())
        ) {
            return redirect()->back()->with('error', '请填写举报理由并选择可见内容。');
        }
        if (!service('throttler')->check('reports-' . auth()->id(), 5, 3600)) {
            return redirect()->back()->with('error', '举报过于频繁，请稍后再试。');
        }
        $key = ['user_id' => auth()->id(), 'topic_id' => $topicId, 'comment_id' => $commentId, 'status' => 'open'];
        if (!db_connect()->table('reports')->where($key)->countAllResults()) {
            db_connect()
                ->table('reports')
                ->insert(
                    $key + [
                        'reason' => $reason,
                        'created_at' => gmdate('Y-m-d H:i:s'),
                        'updated_at' => gmdate('Y-m-d H:i:s'),
                    ],
                );
        }
        return redirect()->back()->with('success', '举报已提交，管理员将进行处理。');
    }
}
