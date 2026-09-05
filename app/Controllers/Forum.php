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
        return $this->listing($page, null, false, false, '', null, 'recent');
    }

    public function latest(int $page = 1)
    {
        return $this->listing($page, null, true, false, '', null, 'latest');
    }

    public function legacyNode(int $id, int $page = 1)
    {
        $node = new ForumModel()->nodeById($id);
        if (!$node) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $path = '/node/' . rawurlencode((string) $node['slug']) . ($page > 1 ? '/' . $page : '');

        return redirect()->to($path)->setStatusCode(301);
    }

    public function node(string $slug, int $page = 1)
    {
        $node = new ForumModel()->nodeBySlug(strtolower($slug));
        if (!$node) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->listing($page, (int) $node['id'], false);
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

        $type = strtolower(trim((string) $this->request->getGet('type')));
        if (!in_array($type, ['all', 'topics', 'comments', 'members'], true)) {
            $type = 'all';
        }

        return $this->listing($page, null, false, false, $query, null, 'search', $type);
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
        } else {
            $node = new ForumModel()->nodeBySlug($selection);
            if ($node) {
                $nodeId = (int) $node['id'];
            } elseif ($persist) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            } else {
                $selection = 'all';
            }
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
        bool $latest,
        bool $home = false,
        string $search = '',
        ?string $homeFilter = null,
        string $feed = '',
        string $searchType = 'all',
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
        $topics =
            $searchType === 'members'
                ? []
                : $model->listing($page, $nodeId, $latest, $filter, $viewerId, $search, $searchType);
        $memberMatches =
            $search !== '' && in_array($searchType, ['all', 'members'], true) ? $model->searchMembers($search) : [];
        $hasNext = count($topics) > ForumModel::PAGE_SIZE;
        $settings = service('siteSettings');
        $siteName = $settings->get('site_name', 'LetsBBS');
        $title = match ($feed) {
            'recent' => '最近活跃',
            'latest' => '最新发布',
            default => $currentNode ? $currentNode['name'] : '主题',
        };
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
                'searchType' => $searchType,
                'memberMatches' => $memberMatches,
                'previousUrl' =>
                    $page > 1
                        ? $this->pageUrl($page - 1, $currentNode, $search, $searchType, $filter, $home, $feed)
                        : null,
                'nextUrl' => $hasNext
                    ? $this->pageUrl($page + 1, $currentNode, $search, $searchType, $filter, $home, $feed)
                    : null,
                'hotTopics' => $home ? $model->hotTopics() : [],
            ] + $this->sidebarData(),
        );
    }

    private function pageUrl(
        int $page,
        ?array $node,
        string $search,
        string $searchType,
        string $filter,
        bool $home = false,
        string $feed = '',
    ): string {
        if ($home) {
            $selection = $node !== null ? (string) $node['slug'] : $filter;
            $query = $page > 1 ? '?page=' . $page : '';

            return '/topic/show/' . rawurlencode($selection) . $query;
        }

        if ($node !== null) {
            return '/node/' . rawurlencode((string) $node['slug']) . '/' . $page;
        }

        $path = $search !== '' ? '/search/' . $page : '/' . ($feed === 'latest' ? 'latest' : 'recent') . '/' . $page;
        $query = [];
        if ($search !== '') {
            $query['q'] = $search;
            if ($searchType !== 'all') {
                $query['type'] = $searchType;
            }
        }
        if ($filter !== 'all') {
            $query['filter'] = $filter;
        }

        return $path . ($query ? '?' . http_build_query($query) : '');
    }

    public function topic(int $id)
    {
        $model = new ForumModel();
        $moderator = auth()->loggedIn() && auth()->user()->inGroup('admin');
        $topic = $model->topicVisibleTo($id, $moderator);
        if (!$topic) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        if ($topic['status'] === 'published') {
            $model->incrementViewCount($id);
            ++$topic['view_count'];
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
                'preview' => $topic['status'] !== 'published',
                'title' => $topic['title'],
            ] + $this->sidebarData(),
        );
    }
}
