<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class ActiveAccount implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!is_file(WRITEPATH . 'install.lock') || !auth()->loggedIn()) {
            return;
        }
        $user = auth()->getProvider()->findById(auth()->id());
        if (!$user || !$user->active || $user->isBanned()) {
            auth()->logout();
            session()->regenerate(true);
            return redirect()->to('/login')->with('error', '账号已停用，请联系管理员。');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void {}
}
