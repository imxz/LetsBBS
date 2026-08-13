<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

final class PublicBootstrapTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testLiveHealthDoesNotRequireDatabase(): void
    {
        $result = $this->get('/health/live');
        $result->assertStatus(200);
        $result->assertJSONFragment(['status' => 'ok']);
    }

    public function testInstallerFormDoesNotExposeDatabaseCredentials(): void
    {
        if (is_file(WRITEPATH . 'install.lock')) {
            $this->markTestSkipped('Already installed test workspace.');
        }
        $result = $this->get('/install');
        $result->assertStatus(200);
        $result->assertSee('INSTALL_TOKEN');
        $this->assertStringNotContainsString('database.default.password', $result->response()->getBody());
        $this->assertStringNotContainsString('name="database', $result->response()->getBody());
    }
}
