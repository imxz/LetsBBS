<?php

use CodeIgniter\Test\CIUnitTestCase;

final class ForumHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('forum');
    }

    public function testRelativeTimeUsesReadableChineseUnits(): void
    {
        $now = 1700000000;
        $this->assertSame('刚刚', relative_time(gmdate('Y-m-d H:i:s', $now - 30), $now));
        $this->assertSame('5 分钟前', relative_time(gmdate('Y-m-d H:i:s', $now - 300), $now));
        $this->assertSame('2 小时前', relative_time(gmdate('Y-m-d H:i:s', $now - 7200), $now));
        $this->assertSame('3 天前', relative_time(gmdate('Y-m-d H:i:s', $now - 259200), $now));
    }

    public function testRelativeTimePreservesInvalidInput(): void
    {
        $this->assertSame('not-a-date', relative_time('not-a-date'));
    }
}
