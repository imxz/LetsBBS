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

if (!function_exists('forum_date')) {
    function forum_date(string $timestamp): string
    {
        try {
            $zone = service('siteSettings')->get('site_timezone', 'Asia/Shanghai');
            return new DateTimeImmutable($timestamp, new DateTimeZone('UTC'))
                ->setTimezone(new DateTimeZone($zone))
                ->format('Y-m-d H:i');
        } catch (Throwable) {
            return $timestamp;
        }
    }
}

if (!function_exists('content_status')) {
    function content_status(string $status): string
    {
        return ['published' => '已发布', 'pending' => '待审核', 'hidden' => '已隐藏', 'deleted' => '已删除'][$status] ??
            $status;
    }
}

if (!function_exists('reply_url')) {
    function reply_url(int $topicId, int $commentId): string
    {
        return '/topic/' . $topicId . '?reply=' . $commentId . '#comment-' . $commentId;
    }
}

if (!function_exists('forum_pager')) {
    function forum_pager(string $base, int $page, bool $hasNext, array $query = []): string
    {
        $url = static fn(int $number): string => esc(
            $base . '/' . $number . ($query ? '?' . http_build_query($query) : ''),
        );
        return '<nav class="d-flex justify-content-between align-items-center my-3" aria-label="分页">' .
            ($page > 1 ? '<a href="' . $url($page - 1) . '">上一页</a>' : '<span></span>') .
            '<span>第 ' .
            $page .
            ' 页</span>' .
            ($hasNext ? '<a href="' . $url($page + 1) . '">下一页</a>' : '<span></span>') .
            '</nav>';
    }
}
