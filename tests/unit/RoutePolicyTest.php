<?php

use CodeIgniter\Test\CIUnitTestCase;

final class RoutePolicyTest extends CIUnitTestCase
{
    public function testMutationsUsePostAndProtectedRoutesRequireSession(): void
    {
        $routes = file_get_contents(APPPATH . 'Config/Routes.php');
        $this->assertStringContainsString("post('logout'", $routes);
        $this->assertStringContainsString("post('media/images'", $routes);
        $this->assertStringContainsString("'Media::image', ['filter' => 'session']", $routes);
        $this->assertStringContainsString("get('node', 'Forum::nodes')", $routes);
        $this->assertStringContainsString("get('search', 'Forum::search/1')", $routes);
        $this->assertStringNotContainsString('setAutoRoute(true)', $routes);
        $this->assertStringNotContainsString('MethodNotAllowed', $routes);
        $this->assertStringNotContainsString("'reg'", $routes);
    }
}
