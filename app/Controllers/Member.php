<?php

namespace App\Controllers;

final class Member extends BaseController
{
    private const PAGE_SIZE = 20;

    private function member(string $username): array
    {
        $row = db_connect()
            ->table('users u')
            ->select('u.id,u.username,u.created_at,p.*')
            ->join('user_profiles p', 'p.user_id=u.id', 'left')
            ->where('u.username', $username)
            ->get()
            ->getRowArray();
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $row;
    }

    public function show(string $username)
    {
        $member = $this->member($username);
        $db = db_connect();
        $topics = $db
            ->table('topics')
            ->where(['user_id' => $member['id'], 'status' => 'published'])
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
        $comments = $db
            ->table('comments c')
            ->select('c.id,c.topic_id,c.body,c.created_at,t.title,u.username topic_author')
            ->join('topics t', 't.id=c.topic_id')
            ->join('users u', 'u.id=t.user_id')
            ->where(['c.user_id' => $member['id'], 'c.status' => 'published', 't.status' => 'published'])
            ->orderBy('c.id', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
        $following = false;
        if (auth()->loggedIn()) {
            $following =
                $db
                    ->table('user_follows')
                    ->where(['follower_id' => auth()->id(), 'followed_id' => $member['id']])
                    ->countAllResults() > 0;
        }

        return view('member/show', [
            'member' => $member,
            'topics' => $topics,
            'comments' => $comments,
            'following' => $following,
        ]);
    }

    public function topics(string $username, int $page = 1)
    {
        $page = max(1, $page);
        $m = $this->member($username);
        $rows = db_connect()
            ->table('topics')
            ->where(['user_id' => $m['id'], 'status' => 'published'])
            ->orderBy('id', 'DESC')
            ->limit(self::PAGE_SIZE + 1, ($page - 1) * self::PAGE_SIZE)
            ->get()
            ->getResultArray();
        return view('member/activity', [
            'member' => $m,
            'rows' => array_slice($rows, 0, self::PAGE_SIZE),
            'kind' => 'topics',
            'page' => $page,
            'hasNext' => count($rows) > self::PAGE_SIZE,
        ]);
    }

    public function comments(string $username, int $page = 1)
    {
        $page = max(1, $page);
        $m = $this->member($username);
        $rows = db_connect()
            ->table('comments c')
            ->select('c.*,t.title')
            ->join('topics t', 't.id=c.topic_id')
            ->where(['c.user_id' => $m['id'], 'c.status' => 'published', 't.status' => 'published'])
            ->orderBy('c.id', 'DESC')
            ->limit(self::PAGE_SIZE + 1, ($page - 1) * self::PAGE_SIZE)
            ->get()
            ->getResultArray();
        return view('member/activity', [
            'member' => $m,
            'rows' => array_slice($rows, 0, self::PAGE_SIZE),
            'kind' => 'comments',
            'page' => $page,
            'hasNext' => count($rows) > self::PAGE_SIZE,
        ]);
    }

    public function settings()
    {
        if ($this->request->getMethod() === 'POST') {
            $email = strtolower(trim((string) $this->request->getPost('email')));
            $qq = trim((string) $this->request->getPost('qq'));
            $homepage = trim((string) $this->request->getPost('homepage'));
            if ($homepage !== '' && !preg_match('#\Ahttps?://#i', $homepage)) {
                $homepage = 'https://' . $homepage;
            }
            if (
                !filter_var($email, FILTER_VALIDATE_EMAIL) ||
                ($qq !== '' && !preg_match('/\A[0-9]{5,20}\z/', $qq)) ||
                ($homepage !== '' && !filter_var($homepage, FILTER_VALIDATE_URL))
            ) {
                return redirect()->back()->withInput()->with('error', '邮箱、QQ 或个人主页格式不正确。');
            }

            $db = db_connect();
            $db->transException(true)->transBegin();
            try {
                $user = auth()->user();
                $user->email = $email;
                if (!auth()->getProvider()->save($user)) {
                    throw new \RuntimeException(implode('；', auth()->getProvider()->errors()));
                }
                $db->table('user_profiles')
                    ->where('user_id', auth()->id())
                    ->update([
                        'qq' => $qq,
                        'location' => mb_substr(strip_tags((string) $this->request->getPost('location')), 0, 120),
                        'homepage' => mb_substr($homepage, 0, 255),
                        'signature' => mb_substr(strip_tags((string) $this->request->getPost('signature')), 0, 160),
                        'bio' => mb_substr(strip_tags((string) $this->request->getPost('bio')), 0, 500),
                        'updated_at' => gmdate('Y-m-d H:i:s'),
                    ]);
                $db->transCommit();
            } catch (\Throwable $e) {
                $db->transRollback();

                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }

            return redirect()->to('/settings/profile')->with('success', '个人资料已保存。');
        }
        $p = db_connect()->table('user_profiles')->where('user_id', auth()->id())->get()->getRowArray();
        $p['email'] = (string) auth()->user()->email;

        return view('member/settings', ['profile' => $p]);
    }

    public function avatar()
    {
        if ($this->request->getMethod() === 'GET') {
            $profile = db_connect()->table('user_profiles')->where('user_id', auth()->id())->get()->getRowArray();

            return view('member/avatar', ['profile' => $profile]);
        }

        try {
            $file = $this->request->getFile('avatar');
            $storage = new \App\Services\ImageStorage();
            $path = $storage->store($file, 'avatars', 512, 2 * 1024 * 1024);
            $db = db_connect();
            $oldPath =
                $db->table('user_profiles')->select('avatar')->where('user_id', auth()->id())->get()->getRowArray()[
                    'avatar'
                ] ?? null;
            $db->table('user_profiles')
                ->where('user_id', auth()->id())
                ->update(['avatar' => $path, 'updated_at' => gmdate('Y-m-d H:i:s')]);
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
            $confirmation = (string) $this->request->getPost('password_confirmation');
            $result = auth()->check(['username' => auth()->user()->username, 'password' => $current]);
            if (!$result->isOK() || strlen($next) < 12 || !hash_equals($next, $confirmation)) {
                return redirect()->back()->with('error', '当前密码错误、新密码少于 12 位，或两次输入不一致。');
            }
            $user = auth()->user();
            $user->password = $next;
            if (!auth()->getProvider()->save($user)) {
                return redirect()->back()->with('error', implode('；', auth()->getProvider()->errors()));
            }
            auth()->logout();
            session()->regenerate(true);

            return redirect()->to('/login')->with('success', '密码已修改，请重新登录。');
        }
        return view('member/password');
    }
}
