<?php

use App\Services\InstallService;
use CodeIgniter\Test\CIUnitTestCase;

final class InstallServiceTest extends CIUnitTestCase
{
    public function testInstallerRunsAllRequiredMigrationNamespaces(): void
    {
        $source = file_get_contents(APPPATH . 'Services/InstallService.php');

        $this->assertStringContainsString("setNamespace('CodeIgniter\\\\Shield')->latest()", $source);
        $this->assertStringContainsString("setNamespace('CodeIgniter\\\\Settings')->latest()", $source);
        $this->assertStringContainsString("setNamespace('App')->latest()", $source);
    }

    public function testWrongTokenIsRejectedBeforeDatabaseAccess(): void
    {
        $old = getenv('INSTALL_TOKEN');
        putenv('INSTALL_TOKEN=expected-token');
        try {
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessage('安装令牌错误');
            new InstallService()->install('wrong-token', 'admin', 'long-enough-password');
        } finally {
            $old === false ? putenv('INSTALL_TOKEN') : putenv('INSTALL_TOKEN=' . $old);
        }
    }
}
