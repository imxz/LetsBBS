<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class PageViews implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null) {}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (
            $request->getMethod() !== 'GET' ||
            $response->getStatusCode() !== 200 ||
            !is_file(WRITEPATH . 'install.lock')
        ) {
            return;
        }
        $path = trim($request->getUri()->getPath(), '/');
        if (
            !preg_match(
                '#\A(?:$|recent(?:/\d+)?|latest(?:/\d+)?|node(?:/.*)?|topic/(?:\d+|show/[^/]+)|member/[^/]+(?:/(?:topics|comments|topic|comment)/\d+)?)\z#',
                $path,
            )
        ) {
            return;
        }
        try {
            if (service('siteSettings')->get('analytics_enabled', '0') !== '1') {
                return;
            }
            // Only an aggregate public-page count is stored. No IP, cookie, account, or referrer.
            $day = new \DateTimeImmutable(
                'now',
                new \DateTimeZone(service('siteSettings')->get('site_timezone', 'Asia/Shanghai')),
            )->format('Y-m-d');
            db_connect()->query(
                'INSERT INTO daily_page_views (day,views) VALUES (?,1) ON DUPLICATE KEY UPDATE views=views+1',
                [$day],
            );
        } catch (\Throwable $e) {
            log_message('error', 'Local page counter failed: {message}', ['message' => $e->getMessage()]);
        }
    }
}
