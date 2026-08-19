<?php

namespace App\Controllers;

use App\Models\ForumModel;

final class Forum extends BaseController
{
    private const FILTERS = ['all', 'nodes', 'topics', 'users'];

    private const HOME_SELECTION_KEY = 'top_show_node';

    public function index()
    {
        $querySelection = strtolower(trim((string) $this->request->getGet('filter')));
        if (in_array($querySelection, self::FILTERS, true)) {
            return $this->homeListing($querySelection, true);
        }

        $selection = (string) (session()->get(self::HOME_SELECTION_KEY) ?? 'all');

        return $this->homeListing($selection === '' ? 'all' : $selection, false);
    }

    public function show(string $selection)
    {
        return $this->homeListing($selection, true);
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

        return view('forum/nodes', ['nodes' => $model->nodes(), 'title' => '节点'] + $this->sidebarData());
    }

    public function search(int $page = 1)
    {
        $query = mb_substr(trim((string) $this->request->getGet('q')), 0, 80);
        if ($query === '') {
            return redirect()->to('/');
        }

        return $this->listing($page, null, true, false, $query);
    }

    private function homeListing(string $selection, bool $persist)
    {
        $selection = strtolower(trim($selection));
        $nodeId = null;
        $filter = 'all';

        if (in_array($selection, self::FILTERS, true)) {
            $filter = $selection;
        } elseif (ctype_digit($selection) && (int) $selection > 0) {
            $nodeId = (int) $selection;
        } elseif ($persist) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        } else {
            $selection = 'all';
        }

        if ($filter !== 'all' && !auth()->loggedIn()) {
            $selection = 'all';
            $filter = 'all';
        }
        if ($persist) {
            session()->set(self::HOME_SELECTION_KEY, $selection);
        }

        return $this->listing(max(1, (int) $this->request->getGet('page')), $nodeId, false, true, '', $filter);
    }

    private function listing(
        int $page,
        ?int $nodeId,
        bool $recent,
        bool $home = false,
        string $search = '',
        ?string $homeFilter = null,
    ) {
        $page = max(1, $page);
        $model = new ForumModel();
        $nodes = $model->nodes();
        $viewerId = auth()->loggedIn() ? (int) auth()->id() : null;
        $filter = $homeFilter ?? (string) $this->request->getGet('filter');
        if (!in_array($filter, self::FILTERS, true) || ($filter !== 'all' && $viewerId === null)) {
            $filter = 'all';
        }
        if ($nodeId !== null) {
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
            if (!$home) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
            $nodeId = null;
            $filter = 'all';
            session()->set(self::HOME_SELECTION_KEY, 'all');
        }
        if ($nodeId && auth()->loggedIn() && !$home) {
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

        return view(
            'forum/list',
            [
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
                'previousUrl' => $page > 1 ? $this->pageUrl($page - 1, $nodeId, $search, $filter, $home) : null,
                'nextUrl' => $hasNext ? $this->pageUrl($page + 1, $nodeId, $search, $filter, $home) : null,
                'hotTopics' => $home ? $model->hotTopics() : [],
            ] + $this->sidebarData(),
        );
    }

    private function pageUrl(int $page, ?int $nodeId, string $search, string $filter, bool $home = false): string
    {
        if ($home) {
            $selection = $nodeId !== null ? (string) $nodeId : $filter;
            $query = $page > 1 ? '?page=' . $page : '';

            return '/topic/show/' . rawurlencode($selection) . $query;
        }

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
                'title' => $topic['title'],
            ] + $this->sidebarData(),
        );
    }
}
