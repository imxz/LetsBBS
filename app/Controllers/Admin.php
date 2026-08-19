<?php

namespace App\Controllers;

use App\Services\ContentCounters;
use App\Services\HtmlSanitizer;
use CodeIgniter\Exceptions\PageNotFoundException;

final class Admin extends BaseController
{
    private const PAGE_SIZE = 20;

    public function index()
    {
        return redirect()->to('/admin/topic');
    }

    public function topics(int $page = 1)
    {
        return $this->topicList($page, false);
    }

    public function topicVerify(int $page = 1)
    {
        return $this->topicList($page, true);
    }

    private function topicList(int $page, bool $pending)
    {
        $page = max(1, $page);
        $query = mb_substr(trim((string) $this->request->getGet('q')), 0, 80);
        $builder = db_connect()
            ->table('topics t')
            ->select('t.*,u.username,n.name node_name')
            ->join('users u', 'u.id=t.user_id')
            ->join('nodes n', 'n.id=t.node_id')
            ->where($pending ? 't.status' : 't.status !=', $pending ? 'hidden' : 'deleted');
        if ($query !== '') {
            $builder->like('t.title', $query);
        }
        $rows = $builder
            ->orderBy('t.id', 'DESC')
            ->limit(self::PAGE_SIZE + 1, ($page - 1) * self::PAGE_SIZE)
            ->get()
            ->getResultArray();
        $comments = [];
        if ($pending) {
            $comments = db_connect()
                ->table('comments c')
                ->select('c.*,u.username,t.title')
                ->join('users u', 'u.id=c.user_id')
                ->join('topics t', 't.id=c.topic_id')
                ->where('c.status', 'hidden')
                ->orderBy('c.id', 'DESC')
                ->limit(50)
                ->get()
                ->getResultArray();
        }

        return view('admin/topics', [
            'title' => $pending ? '待审核主题' : '所有主题',
            'section' => $pending ? 'topic-verify' : 'topic',
            'topics' => array_slice($rows, 0, self::PAGE_SIZE),
            'pendingComments' => $comments,
            'query' => $query,
            'page' => $page,
            'hasNext' => count($rows) > self::PAGE_SIZE,
            'basePath' => $pending ? '/admin/topic/verify' : '/admin/topic/page',
        ]);
    }

    public function topicEdit(int $id)
    {
        $db = db_connect();
        $topic = $db->table('topics')->where('id', $id)->get()->getRowArray();
        if (!$topic) {
            throw PageNotFoundException::forPageNotFound();
        }
        if ($this->request->getMethod() === 'POST') {
            try {
                $title = trim((string) $this->request->getPost('title'));
                $nodeId = (int) $this->request->getPost('node_id');
                $body = new HtmlSanitizer()->clean((string) $this->request->getPost('body'));
                if (mb_strlen($title) < 2 || mb_strlen($title) > 160 || $body === '') {
                    throw new \RuntimeException('标题须为 2–160 个字符，正文不能为空。');
                }
                $db->transException(true)->transBegin();
                $locked = $db->query('SELECT node_id,status FROM topics WHERE id=? FOR UPDATE', [$id])->getRowArray();
                $node = $db
                    ->query('SELECT id FROM nodes WHERE id=? AND is_active=1 FOR UPDATE', [$nodeId])
                    ->getRowArray();
                if (!$locked || !$node) {
                    throw new \RuntimeException('主题或节点不存在。');
                }
                $db->table('topics')
                    ->where('id', $id)
                    ->update([
                        'node_id' => $nodeId,
                        'title' => $title,
                        'body' => $body,
                        'updated_at' => gmdate('Y-m-d H:i:s'),
                    ]);
                if ($locked['status'] === 'published' && (int) $locked['node_id'] !== $nodeId) {
                    $db->query('UPDATE nodes SET topic_count=GREATEST(topic_count-1,0) WHERE id=?', [
                        $locked['node_id'],
                    ]);
                    $db->query('UPDATE nodes SET topic_count=topic_count+1 WHERE id=?', [$nodeId]);
                }
                $db->transCommit();

                return redirect()->to('/admin/topic')->with('success', '主题已更新。');
            } catch (\Throwable $e) {
                if ($db->transStatus() !== false) {
                    $db->transRollback();
                }

                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        }

        return view('admin/topic_edit', [
            'title' => '编辑主题',
            'section' => 'topic',
            'topic' => $topic,
            'nodes' => $db->table('nodes')->where('is_active', 1)->orderBy('sort_order')->get()->getResultArray(),
            'editor' => true,
        ]);
    }

    public function moderate(int $id)
    {
        $status = (string) $this->request->getPost('status');
        if (!in_array($status, ['published', 'hidden', 'deleted'], true)) {
            return $this->response->setStatusCode(422);
        }
        $db = db_connect();
        $db->transException(true)->transBegin();
        try {
            $topic = $db
                ->query('SELECT node_id,user_id,status FROM topics WHERE id=? FOR UPDATE', [$id])
                ->getRowArray();
            if (!$topic) {
                throw PageNotFoundException::forPageNotFound();
            }
            $was = $topic['status'] === 'published';
            $will = $status === 'published';
            $db->table('topics')
                ->where('id', $id)
                ->update(['status' => $status, 'updated_at' => gmdate('Y-m-d H:i:s')]);
            if ($was !== $will) {
                $delta = $will ? 1 : -1;
                $now = gmdate('Y-m-d H:i:s');
                $db->query('UPDATE nodes SET topic_count=GREATEST(topic_count+?,0) WHERE id=?', [
                    $delta,
                    $topic['node_id'],
                ]);
                $db->query('UPDATE user_profiles SET topic_count=GREATEST(topic_count+?,0) WHERE user_id=?', [
                    $delta,
                    $topic['user_id'],
                ]);
                new ContentCounters($db)->adjustVisibleCommentsForTopic($id, $delta, $now);
                if ($will) {
                    $db->table('topic_follows')
                        ->ignore(true)
                        ->insert([
                            'user_id' => $topic['user_id'],
                            'topic_id' => $id,
                            'created_at' => $now,
                        ]);
                    $db->query(
                        'UPDATE topics SET follower_count=(SELECT COUNT(*) FROM topic_follows WHERE topic_id=?) WHERE id=?',
                        [$id, $id],
                    );
                    $recipients = $db
                        ->query(
                            'SELECT user_id FROM node_follows WHERE node_id=? AND user_id<>? UNION SELECT follower_id AS user_id FROM user_follows WHERE followed_id=? AND follower_id<>?',
                            [$topic['node_id'], $topic['user_id'], $topic['user_id'], $topic['user_id']],
                        )
                        ->getResultArray();
                    foreach ($recipients as $recipient) {
                        $notification = [
                            'user_id' => $recipient['user_id'],
                            'actor_id' => $topic['user_id'],
                            'topic_id' => $id,
                            'kind' => 'topic',
                        ];
                        if (!$db->table('notifications')->where($notification)->countAllResults()) {
                            $db->table('notifications')->insert($notification + ['created_at' => $now]);
                        }
                    }
                }
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }

        return redirect()->back()->with('success', '审核状态已更新。');
    }

    public function moderateComment(int $id)
    {
        $status = (string) $this->request->getPost('status');
        if (!in_array($status, ['published', 'hidden', 'deleted'], true)) {
            return $this->response->setStatusCode(422);
        }
        $db = db_connect();
        $db->transException(true)->transBegin();
        try {
            $comment = $db
                ->query(
                    'SELECT c.topic_id,c.user_id,c.status,t.status topic_status FROM comments c JOIN topics t ON t.id=c.topic_id WHERE c.id=? FOR UPDATE',
                    [$id],
                )
                ->getRowArray();
            if (!$comment) {
                throw PageNotFoundException::forPageNotFound();
            }
            $was = $comment['status'] === 'published';
            $will = $status === 'published';
            $db->table('comments')
                ->where('id', $id)
                ->update(['status' => $status, 'updated_at' => gmdate('Y-m-d H:i:s')]);
            if ($was !== $will) {
                $delta = $will ? 1 : -1;
                $db->query('UPDATE topics SET comment_count=GREATEST(comment_count+?,0) WHERE id=?', [
                    $delta,
                    $comment['topic_id'],
                ]);
                if ($comment['topic_status'] === 'published') {
                    $db->query('UPDATE user_profiles SET comment_count=GREATEST(comment_count+?,0) WHERE user_id=?', [
                        $delta,
                        $comment['user_id'],
                    ]);
                }
            }
            $db->query(
                "UPDATE topics t SET last_activity_at=GREATEST(t.created_at,COALESCE((SELECT MAX(c.created_at) FROM comments c WHERE c.topic_id=t.id AND c.status='published'),t.created_at)),updated_at=? WHERE t.id=?",
                [gmdate('Y-m-d H:i:s'), $comment['topic_id']],
            );
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }

        return redirect()->back()->with('success', '回复状态已更新。');
    }

    public function users(int $page = 1)
    {
        return $this->userList($page, false);
    }

    public function bannedUsers(int $page = 1)
    {
        return $this->userList($page, true);
    }

    private function userList(int $page, bool $muted)
    {
        $page = max(1, $page);
        $query = mb_substr(trim((string) $this->request->getGet('q')), 0, 80);
        $builder = db_connect()
            ->table('users u')
            ->select('u.id,u.username,u.active,u.created_at,p.is_muted,i.secret email')
            ->join('user_profiles p', 'p.user_id=u.id', 'left')
            ->join('auth_identities i', "i.user_id=u.id AND i.type='email_password'", 'left', false)
            ->where('p.is_muted', $muted ? 1 : 0);
        if ($query !== '') {
            $builder->like('u.username', $query);
        }
        $rows = $builder
            ->orderBy('u.id', 'DESC')
            ->limit(self::PAGE_SIZE + 1, ($page - 1) * self::PAGE_SIZE)
            ->get()
            ->getResultArray();

        return view('admin/users', [
            'title' => $muted ? '禁言用户' : '所有用户',
            'section' => $muted ? 'user-banned' : 'user',
            'users' => array_slice($rows, 0, self::PAGE_SIZE),
            'query' => $query,
            'page' => $page,
            'hasNext' => count($rows) > self::PAGE_SIZE,
            'basePath' => $muted ? '/admin/user/banned' : '/admin/user/page',
        ]);
    }

    public function userEdit(int $id)
    {
        $provider = auth()->getProvider();
        $user = $provider->findById($id);
        $profile = db_connect()->table('user_profiles')->where('user_id', $id)->get()->getRowArray();
        if (!$user || !$profile) {
            throw PageNotFoundException::forPageNotFound();
        }
        if ($this->request->getMethod() === 'POST') {
            try {
                $username = trim((string) $this->request->getPost('username'));
                $email = strtolower(trim((string) $this->request->getPost('email')));
                $qq = trim((string) $this->request->getPost('qq'));
                $homepage = trim((string) $this->request->getPost('homepage'));
                if ($homepage !== '' && !preg_match('#\Ahttps?://#i', $homepage)) {
                    $homepage = 'https://' . $homepage;
                }
                if (
                    !preg_match('/\A[a-zA-Z0-9_.-]{3,30}\z/', $username) ||
                    !filter_var($email, FILTER_VALIDATE_EMAIL)
                ) {
                    throw new \RuntimeException('用户名或电子邮件格式不正确。');
                }
                $user->username = $username;
                $user->email = $email;
                $user->active = $this->request->getPost('active') === '1' ? 1 : 0;
                if (!$provider->save($user)) {
                    throw new \RuntimeException(implode('；', $provider->errors()));
                }
                db_connect()
                    ->table('user_profiles')
                    ->where('user_id', $id)
                    ->update([
                        'qq' => preg_match('/\A[0-9]{0,20}\z/', $qq) ? $qq : '',
                        'location' => mb_substr(strip_tags((string) $this->request->getPost('location')), 0, 120),
                        'homepage' => mb_substr($homepage, 0, 255),
                        'signature' => mb_substr(strip_tags((string) $this->request->getPost('signature')), 0, 160),
                        'bio' => mb_substr(strip_tags((string) $this->request->getPost('bio')), 0, 500),
                        'avatar' => $this->request->getPost('reset_avatar') === '1' ? null : $profile['avatar'],
                        'updated_at' => gmdate('Y-m-d H:i:s'),
                    ]);

                return redirect()->to('/admin/user')->with('success', '用户资料已更新。');
            } catch (\Throwable $e) {
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        }

        $profile['email'] = (string) $user->email;
        $profile['username'] = $user->username;
        $profile['active'] = (int) $user->active;

        return view('admin/user_edit', ['title' => '编辑用户', 'section' => 'user', 'member' => $profile]);
    }

    public function mute(int $id)
    {
        $db = db_connect();
        $profile = $db->table('user_profiles')->where('user_id', $id)->get()->getRowArray();
        if (!$profile) {
            throw PageNotFoundException::forPageNotFound();
        }
        $db->table('user_profiles')
            ->where('user_id', $id)
            ->update([
                'is_muted' => $profile['is_muted'] ? 0 : 1,
                'updated_at' => gmdate('Y-m-d H:i:s'),
            ]);

        return redirect()
            ->back()
            ->with('success', $profile['is_muted'] ? '已解除禁言。' : '已禁言。');
    }

    public function nodes()
    {
        return view('admin/nodes', [
            'title' => '节点列表',
            'section' => 'node',
            'nodes' => $this->allNodes(),
        ]);
    }

    public function nodeCreate()
    {
        if ($this->request->getMethod() === 'POST') {
            return $this->saveNode(null);
        }

        return view('admin/node_form', [
            'title' => '添加节点',
            'section' => 'node-add',
            'node' => null,
            'parents' => $this->rootNodes(),
        ]);
    }

    public function nodeEdit(int $id)
    {
        $node = db_connect()->table('nodes')->where('id', $id)->get()->getRowArray();
        if (!$node) {
            throw PageNotFoundException::forPageNotFound();
        }
        if ($this->request->getMethod() === 'POST') {
            return $this->saveNode($id);
        }

        return view('admin/node_form', [
            'title' => '编辑节点',
            'section' => 'node',
            'node' => $node,
            'parents' => array_values(
                array_filter($this->rootNodes(), static fn(array $parent): bool => (int) $parent['id'] !== $id),
            ),
        ]);
    }

    private function saveNode(?int $id)
    {
        try {
            $name = trim((string) $this->request->getPost('name'));
            $slug = strtolower(trim((string) $this->request->getPost('slug')));
            $parentId = (int) $this->request->getPost('parent_id') ?: null;
            if ($name === '' || !preg_match('/\A[a-z0-9-]{2,80}\z/', $slug)) {
                throw new \RuntimeException('节点名称不能为空，slug 只能包含小写字母、数字和连字符。');
            }
            if ($id !== null && $parentId === $id) {
                throw new \RuntimeException('节点不能作为自己的父节点。');
            }
            $db = db_connect();
            if (
                $parentId !== null &&
                !$db
                    ->table('nodes')
                    ->where(['id' => $parentId, 'parent_id' => null])
                    ->countAllResults()
            ) {
                throw new \RuntimeException('父节点不存在或不是顶级节点。');
            }
            if ($id !== null && $parentId !== null && $db->table('nodes')->where('parent_id', $id)->countAllResults()) {
                throw new \RuntimeException('存在子节点的节点不能再选择父节点。');
            }
            $data = [
                'parent_id' => $parentId,
                'name' => mb_substr($name, 0, 80),
                'slug' => $slug,
                'description' => mb_substr(strip_tags((string) $this->request->getPost('description')), 0, 255),
                'keywords' => mb_substr(strip_tags((string) $this->request->getPost('keywords')), 0, 160),
                'sort_order' => (int) $this->request->getPost('sort_order'),
                'featured' => $this->request->getPost('featured') === '1' ? 1 : 0,
                'show_on_home' => $this->request->getPost('show_on_home') === '1' ? 1 : 0,
                'is_active' => $this->request->getPost('is_active') === '1' ? 1 : 0,
                'updated_at' => gmdate('Y-m-d H:i:s'),
            ];
            if ($id === null) {
                $data['created_at'] = $data['updated_at'];
                $db->table('nodes')->insert($data);
            } else {
                $db->table('nodes')->where('id', $id)->update($data);
            }

            return redirect()
                ->to('/admin/node')
                ->with('success', $id === null ? '节点已创建。' : '节点已更新。');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function settings()
    {
        $settings = service('siteSettings');
        if ($this->request->getMethod() === 'POST') {
            $siteName = mb_substr(trim((string) $this->request->getPost('site_name')), 0, 80);
            if ($siteName === '') {
                return redirect()->back()->withInput()->with('error', '网站名不能为空。');
            }
            $settings->save([
                'site_name' => $siteName,
                'site_subtitle' => mb_substr(trim((string) $this->request->getPost('site_subtitle')), 0, 120),
                'home_welcome_message' => mb_substr(
                    trim((string) $this->request->getPost('home_welcome_message')),
                    0,
                    120,
                ),
                'site_keywords' => mb_substr(trim((string) $this->request->getPost('site_keywords')), 0, 255),
                'site_description' => mb_substr(trim((string) $this->request->getPost('site_description')), 0, 500),
                'home_introduction' => mb_substr(trim((string) $this->request->getPost('home_introduction')), 0, 1000),
            ]);

            return redirect()->back()->with('success', '基本设置已保存。');
        }

        return view('admin/settings', [
            'title' => '基本设置',
            'section' => 'settings-site',
            'mode' => 'site',
            'settings' => $settings->all(),
        ]);
    }

    public function verifySettings()
    {
        $settings = service('siteSettings');
        if ($this->request->getMethod() === 'POST') {
            $settings->save([
                'topic_requires_approval' => $this->request->getPost('topic_requires_approval') === '1' ? '1' : '0',
            ]);

            return redirect()->back()->with('success', '审核设置已保存。');
        }

        return view('admin/settings', [
            'title' => '审核设置',
            'section' => 'settings-verify',
            'mode' => 'verify',
            'settings' => $settings->all(),
        ]);
    }

    private function allNodes(): array
    {
        return db_connect()
            ->table('nodes n')
            ->select('n.*,p.name parent_name')
            ->join('nodes p', 'p.id=n.parent_id', 'left')
            ->orderBy('n.parent_id IS NOT NULL', 'ASC', false)
            ->orderBy('n.sort_order')
            ->orderBy('n.id')
            ->get()
            ->getResultArray();
    }

    private function rootNodes(): array
    {
        return db_connect()->table('nodes')->where('parent_id', null)->orderBy('sort_order')->get()->getResultArray();
    }
}
