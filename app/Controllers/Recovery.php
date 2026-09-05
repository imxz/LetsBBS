<?php

namespace App\Controllers;

use CodeIgniter\Shield\Controllers\MagicLinkController;
use CodeIgniter\Shield\Models\UserIdentityModel;

final class Recovery extends MagicLinkController
{
    public function loginAction()
    {
        if (!setting('Auth.allowMagicLinkLogins')) {
            return parent::loginAction();
        }
        $email = strtolower(trim((string) $this->request->getPost('email')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
            return redirect()->to('/login/magic-link')->with('error', '请填写有效邮箱。');
        }
        $user = auth()
            ->getProvider()
            ->findByCredentials(['email' => $email]);
        if (!$user || !$user->active || $user->isBanned()) {
            return $this->displayMessage();
        }
        $this->request->setGlobal('post', ['email' => $email] + $this->request->getPost());
        try {
            parent::loginAction();
        } catch (\Throwable $e) {
            log_message('error', 'Email recovery delivery failed: {message}', ['message' => $e->getMessage()]);
        }
        session()->remove(['error', 'errors']);
        $this->response->removeHeader('Location')->setStatusCode(200);
        return $this->displayMessage();
    }

    public function verify(): \CodeIgniter\HTTP\RedirectResponse
    {
        if (setting('Auth.allowMagicLinkLogins')) {
            $token = $this->request->getGet('token');
            if (!is_string($token) || strlen($token) > 255) {
                return redirect()->to('/login')->with('error', '登录链接无效。');
            }
            $identity = model(UserIdentityModel::class)->getIdentityBySecret('magic-link', $token);
            $user = $identity ? auth()->getProvider()->findById($identity->user_id) : null;
            if ($identity && (!$user || !$user->active || $user->isBanned())) {
                return redirect()->to('/login')->with('error', '账号已停用，请联系管理员。');
            }
        }
        return parent::verify();
    }
}
