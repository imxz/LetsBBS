<?php

namespace App\Controllers;

final class Member extends BaseController
{
    private const PAGE_SIZE = 20;

    private function member(string $username): array
    {
        $row = db_connect()->table('users u')->select('u.id,u.username,u.created_at,p.*')->join('user_profiles p', 'p.user_id=u.id', 'left')->where('u.username', $username)->get()->getRowArray();
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }return $row;
    }
    public function show(string $username)
    {
        $member = $this->member($username);
        $topics = db_connect()->table('topics')->where(['user_id' => $member['id'],'status' => 'published'])->orderBy('id', 'DESC')->limit(10)->get()->getResultArray();
        return view('member/show', ['member' => $member,'topics' => $topics]);
    }
    public function topics(string $username, int $page = 1)
    {
        $page = max(1, $page);
        $m = $this->member($username);
        $rows = db_connect()->table('topics')->where(['user_id' => $m['id'],'status' => 'published'])->orderBy('id', 'DESC')->limit(self::PAGE_SIZE + 1, ($page - 1) * self::PAGE_SIZE)->get()->getResultArray();
        return view('member/activity', ['member' => $m,'rows' => array_slice($rows, 0, self::PAGE_SIZE),'kind' => 'topics','page' => $page,'hasNext' => count($rows) > self::PAGE_SIZE]);
    }
    public function comments(string $username, int $page = 1)
    {
        $page = max(1, $page);
        $m = $this->member($username);
        $rows = db_connect()->table('comments c')->select('c.*,t.title')->join('topics t', 't.id=c.topic_id')->where(['c.user_id' => $m['id'],'c.status' => 'published','t.status' => 'published'])->orderBy('c.id', 'DESC')->limit(self::PAGE_SIZE + 1, ($page - 1) * self::PAGE_SIZE)->get()->getResultArray();
        return view('member/activity', ['member' => $m,'rows' => array_slice($rows, 0, self::PAGE_SIZE),'kind' => 'comments','page' => $page,'hasNext' => count($rows) > self::PAGE_SIZE]);
    }
    public function settings()
    {
        if ($this->request->getMethod() === 'POST') {
            db_connect()->table('user_profiles')->where('user_id', auth()->id())->update(['bio' => mb_substr(strip_tags((string) $this->request->getPost('bio')), 0, 500),'updated_at' => gmdate('Y-m-d H:i:s')]);
            return redirect()->back()->with('success', '设置已保存。');
        }$p = db_connect()->table('user_profiles')->where('user_id', auth()->id())->get()->getRowArray();
        return view('member/settings', ['profile' => $p]);
    }
    public function avatar()
    {
        try {
            $file = $this->request->getFile('avatar');
            $storage = new \App\Services\ImageStorage();
            $path = $storage->store($file, 'avatars', 512, 2 * 1024 * 1024);
            $db = db_connect();
            $oldPath = $db->table('user_profiles')->select('avatar')->where('user_id', auth()->id())->get()->getRowArray()['avatar'] ?? null;
            $db->table('user_profiles')->where('user_id', auth()->id())->update(['avatar' => $path,'updated_at' => gmdate('Y-m-d H:i:s')]);
            if (is_string($oldPath)) {
                $storage->delete($oldPath, 'avatars');
            }
            return redirect()->back()->with('success', '头像已更新。');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function password()
    {
        if ($this->request->getMethod() === 'POST') {
            $current = (string) $this->request->getPost('current_password');
            $next = (string) $this->request->getPost('password');
            $result = auth()->check(['username' => auth()->user()->username,'password' => $current]);
            if (!$result->isOK() || strlen($next) < 12) {
                return redirect()->back()->with('error', '当前密码错误，或新密码少于 12 位。');
            }$user = auth()->user();
            $user->password = $next;
            if (!auth()->getProvider()->save($user)) {
                return redirect()->back()->with('error', implode('；', auth()->getProvider()->errors()));
            }session()->regenerate(true);
            return redirect()->back()->with('success', '密码已修改。');
        }return view('member/password');
    }
}
