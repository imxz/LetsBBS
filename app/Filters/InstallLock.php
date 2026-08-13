<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class InstallLock implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!is_file(WRITEPATH . 'install.lock')) {
            return service('response')->setStatusCode(503)->setBody('站点尚未安装，请访问 /install。');
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void {}
}
