<?php

namespace App\Controllers;

final class MethodNotAllowed extends BaseController
{
    public function reject()
    {
        return $this->response->setStatusCode(405)->setHeader('Allow', 'POST')->setBody('Method Not Allowed');
    }
    public function gone()
    {
        return $this->response->setStatusCode(404);
    }
}
