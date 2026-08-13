<?php

use App\Services\CaptchaService;
use CodeIgniter\Test\CIUnitTestCase;

final class CaptchaServiceTest extends CIUnitTestCase
{
    public function testCaptchaIsOneTimeUse(): void
    {
        $service = new CaptchaService();
        $code = $service->issue();
        $this->assertTrue($service->consume($code));
        $this->assertFalse($service->consume($code));
    }
}
