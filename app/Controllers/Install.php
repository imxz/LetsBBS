<?php

namespace App\Controllers;

use App\Services\InstallService;

final class Install extends BaseController
{
    public function index()
    {
        if (is_file(WRITEPATH . 'install.lock')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        if ($this->request->getMethod() === 'POST') {
            try {
                (new InstallService())->install((string) $this->request->getPost('install_token'), (string) $this->request->getPost('username'), (string) $this->request->getPost('password'));
                return redirect()->to('/')->with('success', '安装完成，请登录。');
            } catch (\Throwable $e) {
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        }
        return view('install');
    }
}
