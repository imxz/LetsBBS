<?php

namespace App\Controllers;

use App\Models\ForumModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->helpers = ['form', 'url', 'auth'];
        parent::initController($request, $response, $logger);
    }

    /**
     * Data shared by the legacy-style sidebar on public and account pages.
     */
    protected function sidebarData(): array
    {
        $model = new ForumModel();
        $viewerId = auth()->loggedIn() ? (int) auth()->id() : null;
        $settings = service('siteSettings');

        return [
            'statistics' => $model->statistics(),
            'viewer' => $viewerId !== null ? $model->viewerSummary($viewerId) : null,
            'siteIntroduction' => $settings->get(
                'home_introduction',
                $settings->get('site_description', '简洁的中文论坛'),
            ),
        ];
    }
}
