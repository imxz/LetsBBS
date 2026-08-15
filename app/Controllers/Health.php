<?php

namespace App\Controllers;

final class Health extends BaseController
{
    public function live()
    {
        return $this->response->setJSON(['status' => 'ok']);
    }

    public function ready()
    {
        if (!is_file(WRITEPATH . 'install.lock')) {
            return $this->response->setStatusCode(503)->setJSON(['status' => 'not-installed']);
        }
        try {
            db_connect()->query('SELECT 1');
            return $this->response->setJSON(['status' => 'ready']);
        } catch (\Throwable) {
            return $this->response->setStatusCode(503)->setJSON(['status' => 'database-unavailable']);
        }
    }
}
