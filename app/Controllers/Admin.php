<?php

namespace App\Controllers;

final class Admin extends BaseController
{
    public function index()
    {
        $db = db_connect();
        return view('admin/index', [
            'nodes' => $db->table('nodes')->orderBy('sort_order')->get()->getResultArray(),
            'pendingTopics' => $db->table('topics t')->select('t.*,u.username')->join('users u', 'u.id=t.user_id')->where('t.status', 'hidden')->orderBy('t.id', 'DESC')->limit(50)->get()->getResultArray(),
            'pendingComments' => $db->table('comments c')->select('c.*,u.username,t.title')->join('users u', 'u.id=c.user_id')->join('topics t', 't.id=c.topic_id')->where('c.status', 'hidden')->orderBy('c.id', 'DESC')->limit(50)->get()->getResultArray(),
            'users' => $db->table('users u')->select('u.id,u.username,p.is_muted')->join('user_profiles p', 'p.user_id=u.id', 'left')->orderBy('u.id', 'DESC')->limit(50)->get()->getResultArray(),
            'settings' => service('siteSettings')->all(),
        ]);
    }
    public function node()
    {
        if (!$this->validate(['name' => 'required|max_length[80]','slug' => 'required|alpha_dash|max_length[80]'])) {
            return redirect()->back()->with('error', implode('；', $this->validator->getErrors()));
        }$now = gmdate('Y-m-d H:i:s');
        db_connect()->table('nodes')->insert(['name' => $this->request->getPost('name'),'slug' => strtolower((string) $this->request->getPost('slug')),'description' => mb_substr((string) $this->request->getPost('description'), 0, 255),'sort_order' => (int) $this->request->getPost('sort_order'),'created_at' => $now,'updated_at' => $now]);
        return redirect()->back()->with('success', '节点已创建。');
    }
    public function moderate(int $id)
    {
        $status = (string) $this->request->getPost('status');
        if (!in_array($status, ['published','hidden','deleted'], true)) {
            return $this->response->setStatusCode(422);
        }$db = db_connect();
        $db->transException(true)->transBegin();
        try {
            $topic = $db->query('SELECT node_id,user_id,status FROM topics WHERE id=? FOR UPDATE', [$id])->getRowArray();
            if (!$topic) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }$was = $topic['status'] === 'published';
            $will = $status === 'published';
            $db->table('topics')->where('id', $id)->update(['status' => $status,'updated_at' => gmdate('Y-m-d H:i:s')]);
            if ($was !== $will) {
                $delta = $will ? 1 : -1;
                $now = gmdate('Y-m-d H:i:s');
                $db->query('UPDATE nodes SET topic_count=GREATEST(topic_count+?,0) WHERE id=?', [$delta,$topic['node_id']]);
                $db->query('UPDATE user_profiles SET topic_count=GREATEST(topic_count+?,0) WHERE user_id=?', [$delta,$topic['user_id']]);
                (new \App\Services\ContentCounters($db))->adjustVisibleCommentsForTopic($id, $delta, $now);
            }$db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }return redirect()->back()->with('success', '审核状态已更新。');
    }
    public function mute(int $id)
    {
        $db = db_connect();
        $p = $db->table('user_profiles')->where('user_id', $id)->get()->getRowArray();
        if (!$p) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }$db->table('user_profiles')->where('user_id', $id)->update(['is_muted' => $p['is_muted'] ? 0 : 1,'updated_at' => gmdate('Y-m-d H:i:s')]);
        return redirect()->back()->with('success', $p['is_muted'] ? '已解除禁言。' : '已禁言。');
    }
    public function moderateComment(int $id)
    {
        $status = (string) $this->request->getPost('status');
        if (!in_array($status, ['published','hidden','deleted'], true)) {
            return $this->response->setStatusCode(422);
        }$db = db_connect();
        $db->transException(true)->transBegin();
        try {
            $c = $db->query('SELECT c.topic_id,c.user_id,c.status,t.status AS topic_status FROM comments c JOIN topics t ON t.id=c.topic_id WHERE c.id=? FOR UPDATE', [$id])->getRowArray();
            if (!$c) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }$was = $c['status'] === 'published';
            $will = $status === 'published';
            $db->table('comments')->where('id', $id)->update(['status' => $status,'updated_at' => gmdate('Y-m-d H:i:s')]);
            if ($was !== $will) {
                $delta = $will ? 1 : -1;
                $db->query('UPDATE topics SET comment_count=GREATEST(comment_count+?,0) WHERE id=?', [$delta,$c['topic_id']]);
                if ($c['topic_status'] === 'published') {
                    $db->query('UPDATE user_profiles SET comment_count=GREATEST(comment_count+?,0) WHERE user_id=?', [$delta,$c['user_id']]);
                }
            }
            $now = gmdate('Y-m-d H:i:s');
            $db->query("UPDATE topics t SET last_activity_at=GREATEST(t.created_at,COALESCE((SELECT MAX(c.created_at) FROM comments c WHERE c.topic_id=t.id AND c.status='published'),t.created_at)),updated_at=? WHERE t.id=?", [$now,$c['topic_id']]);
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }return redirect()->back()->with('success', '回复状态已更新。');
    }
    public function settings()
    {
        $siteName = mb_substr(trim((string) $this->request->getPost('site_name')), 0, 80);
        if ($siteName === '') {
            return redirect()->back()->withInput()->with('error', '站点名称不能为空。');
        }
        service('siteSettings')->save([
            'site_name' => $siteName,
            'site_description' => mb_substr(trim((string) $this->request->getPost('site_description')), 0, 160),
        ]);

        return redirect()->back()->with('success', '站点设置已保存。');
    }
}
