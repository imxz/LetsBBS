<?php

namespace App\Controllers;

final class Notification extends BaseController
{
    public function index(int $page = 1)
    {
        $page = max(1, $page);
        $items = db_connect()->table('notifications n')->select('n.*,u.username actor_name,t.title')->join('users u', 'u.id=n.actor_id', 'left')->join('topics t', "t.id=n.topic_id AND t.status='published'", 'left')->where('n.user_id', auth()->id())->orderBy('n.id', 'DESC')->limit(31, ($page - 1) * 30)->get()->getResultArray();
        return view('notification/index', ['items' => array_slice($items, 0, 30),'page' => $page,'hasNext' => count($items) > 30]);
    }
    public function readAll()
    {
        db_connect()->table('notifications')->where('user_id', auth()->id())->where('read_at', null)->update(['read_at' => gmdate('Y-m-d H:i:s')]);
        return redirect()->back()->with('success', '已全部标为已读。');
    }
}
