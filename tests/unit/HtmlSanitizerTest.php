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

    public function testAllowsEditorFormatsButRejectsPositionAndActiveContent(): void
    {
        $clean = new HtmlSanitizer()->clean('<p style="text-align:center;color:red;position:fixed">ok</p>');
        $this->assertStringContainsString('text-align:center', $clean);
        $this->assertStringContainsString('color:', $clean);
        $this->assertStringNotContainsString('position:', $clean);
        $formats = new HtmlSanitizer()->clean(
            '<p><span style="font-size:24px;color:#123456;text-decoration:underline">文字</span></p><hr><pre><code>&lt;script&gt;</code></pre>',
        );
        foreach (
            ['font-size:24px', 'color:', 'text-decoration:underline', '<hr', '<pre>', '<code>', '&lt;script&gt;']
            as $part
        ) {
            $this->assertStringContainsString($part, $formats);
        }
        foreach (
            ['<p>&nbsp; <br></p>', '<script>alert(1)</script>', '<img src="https://bad.test/a.png">', '<p>　</p>']
            as $empty
        ) {
            $this->assertSame('', new HtmlSanitizer()->clean($empty));
        }
    }
}
