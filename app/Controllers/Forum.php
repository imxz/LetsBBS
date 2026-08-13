<?php

namespace App\Controllers;

use App\Models\ForumModel;

final class Forum extends BaseController
{
    public function index()
    {
        return $this->listing(1, null, false);
    }
    public function recent(int $page = 1)
    {
        return $this->listing($page, null, true);
    }
    public function node(int $id, int $page = 1)
    {
        return $this->listing($page, $id, false);
    }
    private function listing(int $page, ?int $nodeId, bool $recent)
    {
        $page = max(1, $page);
        $model = new ForumModel();
        $topics = $model->listing($page, $nodeId, $recent);
        $nodes = $model->nodes();
        $currentNode = null;
        $nodeFollowing = false;
        foreach ($nodes as $n) {
            if ((int) $n['id'] === $nodeId) {
                $currentNode = $n;
            }
        }if ($nodeId && auth()->loggedIn()) {
            $nodeFollowing = $model->follows('node_follows', ['user_id' => auth()->id(),'node_id' => $nodeId]);
        }return view('forum/list', ['topics' => $topics,'nodes' => $nodes,'page' => $page,'hasNext' => count($topics) === 20,'nodeId' => $nodeId,'currentNode' => $currentNode,'nodeFollowing' => $nodeFollowing,'title' => $recent ? '最新主题' : ($currentNode ? $currentNode['name'] : '主题')]);
    }
    public function topic(int $id)
    {
        $model = new ForumModel();
        $topic = $model->topic($id);
        if (!$topic) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }$following = auth()->loggedIn() ? $model->follows('topic_follows', ['user_id' => auth()->id(),'topic_id' => $id]) : false;
        return view('forum/topic', ['topic' => $topic,'comments' => $model->comments($id),'following' => $following]);
    }
}
