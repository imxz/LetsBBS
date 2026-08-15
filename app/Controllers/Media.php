<?php

namespace App\Controllers;

use App\Services\ImageStorage;

final class Media extends BaseController
{
    public function image()
    {
        $profile = db_connect()->table('user_profiles')->where('user_id', auth()->id())->get()->getRowArray();
        if (($profile['is_muted'] ?? 0) == 1) {
            return $this->response->setStatusCode(403)->setJSON(['error' => '你已被禁言。']);
        }
        try {
            $path = new ImageStorage()->store($this->request->getFile('file'));
            return $this->response->setJSON(['location' => $path]);
        } catch (\Throwable $e) {
            $code = in_array($e->getCode(), [413, 422], true) ? $e->getCode() : 422;
            return $this->response->setStatusCode($code)->setJSON(['error' => $e->getMessage()]);
        }
    }
}
