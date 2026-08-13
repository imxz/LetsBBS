<?php

use CodeIgniter\Test\CIUnitTestCase;

final class RoutePolicyTest extends CIUnitTestCase
{
    public function testLegacyWriteRoutesAreNotGetMutations(): void
    {
        $routes = file_get_contents(APPPATH . 'Config/Routes.php');
        $this->assertStringContainsString("post('logout'", $routes);
        $this->assertStringContainsString("post('media/images'", $routes);
        $this->assertStringContainsString('MethodNotAllowed::reject', $routes);
        $this->assertStringNotContainsString('setAutoRoute(true)', $routes);
    }
}
