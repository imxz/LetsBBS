<?php

namespace App\Services;

final class CaptchaService
{
    private const KEY = 'letsbbs_captcha';

    public function issue(): string
    {
        $alphabet = '23456789abcdefghjkmnpqrstuvwxyz';
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        session()->set(self::KEY, ['hash' => hash('sha256', $code),'expires' => time() + 300]);
        return $code;
    }

    public function consume(string $answer): bool
    {
        $data = session()->get(self::KEY);
        session()->remove(self::KEY);
        return is_array($data) && ($data['expires'] ?? 0) >= time() && hash_equals((string) $data['hash'], hash('sha256', strtolower(trim($answer))));
    }
}
