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
        $this->assertStringContainsString("'settings/profile', 'Member::settings', ['filter' => 'session']", $routes);
        $this->assertStringContainsString("'settings/avatar', 'Member::avatar', ['filter' => 'session']", $routes);
        $this->assertStringContainsString("'reg', 'Auth::register'", $routes);
        $this->assertStringContainsString("get('topic', 'Admin::topics/1')", $routes);
        $this->assertStringContainsString("'settings/verify', 'Admin::verifySettings'", $routes);
        $this->assertStringNotContainsString('setAutoRoute(true)', $routes);
        $this->assertStringNotContainsString('MethodNotAllowed', $routes);
        $this->assertStringNotContainsString("get('topic/(:num)/delete'", $routes);
    }

    public function testMemberNavigationRetainsLegacyUserActions(): void
    {
        $layout = file_get_contents(APPPATH . 'Views/layouts/main.php');
        $this->assertStringContainsString('href="/settings"', $layout);
        $this->assertStringContainsString('>设置</a>', $layout);
        $this->assertStringContainsString('>登出</button>', $layout);
        $this->assertStringNotContainsString('href="/recent">最新</a>', $layout);
    }
}
