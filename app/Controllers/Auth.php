<?php

namespace App\Controllers;

use App\Services\CaptchaService;
use CodeIgniter\Shield\Entities\User;

final class Auth extends BaseController
{
    public function captcha()
    {
        $code = new CaptchaService()->issue();
        $image = imagecreatetruecolor(150, 48);
        imagefill($image, 0, 0, imagecolorallocate($image, 246, 248, 250));
        for ($i = 0; $i < 8; $i++) {
            imageline(
                $image,
                random_int(0, 150),
                random_int(0, 48),
                random_int(0, 150),
                random_int(0, 48),
                imagecolorallocate($image, random_int(120, 210), random_int(120, 210), random_int(120, 210)),
            );
        }
        imagestring($image, 5, 28, 16, strtoupper($code), imagecolorallocate($image, 30, 45, 60));
        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        imagedestroy($image);
        return $this->response
            ->setHeader('Content-Type', 'image/png')
            ->setHeader('Cache-Control', 'no-store')
            ->setBody($png);
    }

    public function register()
    {
        if (auth()->loggedIn()) {
            return redirect()->to('/');
        }
        if ($this->request->getMethod() === 'POST') {
            if (
                !service('throttler')->check('reg-' . $this->request->getIPAddress(), 5, 60) ||
                !new CaptchaService()->consume((string) $this->request->getPost('captcha'))
            ) {
                return redirect()->back()->withInput()->with('error', '验证码错误或请求过于频繁。');
            }
            $username = strtolower(trim((string) $this->request->getPost('username')));
            $password = (string) $this->request->getPost('password');
            if (!preg_match('/^[a-z0-9]{3,12}$/', $username) || strlen($password) < 12) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', '用户名须为 3–12 位小写字母或数字，密码至少 12 位。');
            }
            $provider = auth()->getProvider();
            $user = new User([
                'username' => $username,
                'email' => $username . '@local.invalid',
                'password' => $password,
                'active' => 1,
            ]);
            $db = db_connect();
            $db->transException(true)->transBegin();
            try {
                if (!$provider->save($user)) {
                    throw new \RuntimeException(implode('；', $provider->errors()));
                }
                $user = $provider->findById($provider->getInsertID());
                $user->addGroup('user');
                $now = gmdate('Y-m-d H:i:s');
                $db->table('user_profiles')->insert([
                    'user_id' => $user->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $db->transCommit();
            } catch (\Throwable $e) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
            auth()->login($user);
            session()->regenerate(true);
            return redirect()->to('/')->with('success', '注册成功。');
        }
        return view('auth/form', ['mode' => 'register', 'title' => '注册'] + $this->sidebarData());
    }

    public function login()
    {
        if (auth()->loggedIn()) {
            return redirect()->to('/');
        }
        if ($this->request->getMethod() === 'POST') {
            if (
                !service('throttler')->check('login-' . $this->request->getIPAddress(), 8, 60) ||
                !new CaptchaService()->consume((string) $this->request->getPost('captcha'))
            ) {
                return redirect()->back()->withInput()->with('error', '验证码错误或请求过于频繁。');
            }
            $result = auth()->attempt([
                'username' => strtolower(trim((string) $this->request->getPost('username'))),
                'password' => (string) $this->request->getPost('password'),
            ]);
            if (!$result->isOK()) {
                return redirect()->back()->withInput()->with('error', '用户名或密码错误。');
            }
            session()->regenerate(true);
            return redirect()->to('/')->with('success', '登录成功。');
        }
        return view('auth/form', ['mode' => 'login', 'title' => '登录'] + $this->sidebarData());
    }

    public function logout()
    {
        auth()->logout();
        session()->destroy();
        return redirect()->to('/');
    }
}
