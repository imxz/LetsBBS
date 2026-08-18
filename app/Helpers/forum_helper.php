<?php

if (!function_exists('relative_time')) {
    function relative_time(string $timestamp, ?int $now = null): string
    {
        $time = strtotime($timestamp . ' UTC');
        if ($time === false) {
            return $timestamp;
        }

        $seconds = max(0, ($now ?? time()) - $time);
        if ($seconds < 60) {
            return '刚刚';
        }
        if ($seconds < 3600) {
            return intdiv($seconds, 60) . ' 分钟前';
        }
        if ($seconds < 86400) {
            return intdiv($seconds, 3600) . ' 小时前';
        }
        if ($seconds < 86400 * 30) {
            return intdiv($seconds, 86400) . ' 天前';
        }

        return gmdate('Y-m-d', $time);
    }
}
