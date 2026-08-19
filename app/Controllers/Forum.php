<?php

namespace App\Controllers;

use App\Models\ForumModel;

final class Forum extends BaseController
{
    private const FILTERS = ['all', 'nodes', 'topics', 'users'];

    public function index()
    {
        return $this->listing(1, null, false, true);
    }

    public function recent(int $page = 1)
    {
        return $this->listing($page, null, true);
    }

    public function node(int $id, int $page = 1)
    {
        return $this->listing($page, $id, false);
    }

    public function nodes()
    {
        $model = new ForumModel();

        return view('forum/nodes', ['nodes' => $model->nodes(), 'title' => '节点'] + $this->sidebarData($model));
    }

    public function search(int $page = 1)
    {
        $query = mb_substr(trim((string) $this->request->getGet('q')), 0, 80);
        if ($query === '') {
            return redirect()->to('/');
        }

        return $this->listing($page, null, true, false, $query);
    }

    private function listing(int $page, ?int $nodeId, bool $recent, bool $home = false, string $search = '')
    {
        $page = max(1, $page);
        $model = new ForumModel();
        $nodes = $model->nodes();
        $viewerId = auth()->loggedIn() ? (int) auth()->id() : null;
        $filter = (string) $this->request->getGet('filter');
        if (!in_array($filter, self::FILTERS, true) || ($filter !== 'all' && $viewerId === null)) {
            $filter = 'all';
        }
        $currentNode = null;
        $nodeFollowing = false;
        foreach ($nodes as $n) {
            if ((int) $n['id'] === $nodeId) {
                $currentNode = $n;
            }
        }
        if ($nodeId !== null && $currentNode === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        if ($nodeId && auth()->loggedIn()) {
            $nodeFollowing = $model->follows('node_follows', ['user_id' => auth()->id(), 'node_id' => $nodeId]);
        }
        $topics = $model->listing($page, $nodeId, $recent, $filter, $viewerId, $search);
        $hasNext = count($topics) > ForumModel::PAGE_SIZE;
        $settings = service('siteSettings');
        $siteName = $settings->get('site_name', 'LetsBBS');
        $title = $recent ? '最新主题' : ($currentNode ? $currentNode['name'] : '主题');
        if ($home) {
            $title = $settings->get('home_welcome_message', '欢迎访问 ' . $siteName);
        } elseif ($search !== '') {
            $title = '搜索：' . $search;
        }

        return view('forum/list', [
            'topics' => array_slice($topics, 0, ForumModel::PAGE_SIZE),
            'nodes' => $nodes,
            'page' => $page,
            'hasNext' => $hasNext,
            'nodeId' => $nodeId,
            'currentNode' => $currentNode,
            'nodeFollowing' => $nodeFollowing,
            'title' => $title,
            'home' => $home,
            'filter' => $filter,
            'search' => $search,
            'previousUrl' => $page > 1 ? $this->pageUrl($page - 1, $nodeId, $search, $filter) : null,
            'nextUrl' => $hasNext ? $this->pageUrl($page + 1, $nodeId, $search, $filter) : null,
            'hotTopics' => $home ? $model->hotTopics() : [],
            'statistics' => $home ? $model->statistics() : [],
            'viewer' => $home && $viewerId !== null ? $model->viewerSummary($viewerId) : null,
            'siteIntroduction' => $settings->get(
                'home_introduction',
                $settings->get('site_description', '简洁的中文论坛'),
            ),
        ]);
    }

    private function pageUrl(int $page, ?int $nodeId, string $search, string $filter): string
    {
        if ($nodeId !== null) {
            return '/node/' . $nodeId . '/' . $page;
        }

        $path = $search !== '' ? '/search/' . $page : '/recent/' . $page;
        $query = [];
        if ($search !== '') {
            $query['q'] = $search;
        }
        if ($filter !== 'all') {
            $query['filter'] = $filter;
        }

        return $path . ($query ? '?' . http_build_query($query) : '');
    }

    public function topic(int $id)
    {
        $model = new ForumModel();
        $model->incrementViewCount($id);
        $topic = $model->topic($id);
        if (!$topic) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $following = auth()->loggedIn()
            ? $model->follows('topic_follows', ['user_id' => auth()->id(), 'topic_id' => $id])
            : false;
        return view(
            'forum/topic',
            [
                'topic' => $topic,
                'comments' => $model->comments($id),
                'following' => $following,
            ] + $this->sidebarData($model),
        );
    }

    private function sidebarData(ForumModel $model): array
    {
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
