<?php

use App\Services\HtmlSanitizer;
use CodeIgniter\Test\CIUnitTestCase;

final class HtmlSanitizerTest extends CIUnitTestCase
{
    public function testRemovesScriptsDangerousLinksAndRemoteImages(): void
    {
        $path = '/uploads/editor/2026/08/' . str_repeat('a', 40) . '.png';
        $clean = new HtmlSanitizer()->clean(
            '<script>alert(1)</script><a href="javascript:alert(1)">x</a><img src="https://evil.test/a.png"><img src="/uploads/editor/../../secret.png"><img src="' .
                $path .
                '">',
        );
        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('evil.test', $clean);
        $this->assertStringNotContainsString('../', $clean);
        $this->assertStringContainsString($path, $clean);
    }

    public function testOnlyAllowsTextAlignmentStyle(): void
    {
        $clean = new HtmlSanitizer()->clean('<p style="text-align:center;color:red;position:fixed">ok</p>');
        $this->assertStringContainsString('text-align:center', $clean);
        $this->assertStringNotContainsString('color:', $clean);
        $this->assertStringNotContainsString('position:', $clean);
    }
}
