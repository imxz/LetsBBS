<?php

namespace App\Controllers;

final class Notification extends BaseController
{
    public function index(int $page = 1)
    {
        $page = max(1, $page);
        $items = db_connect()->table('notifications n')->select('n.*,u.username actor_name,t.title')->join('users u', 'u.id=n.actor_id', 'left')->join('topics t', 't.id=n.topic_id', 'left')->where('n.user_id', auth()->id())->orderBy('n.id', 'DESC')->limit(30, ($page - 1) * 30)->get()->getResultArray();
        return view('notification/index', ['items' => $items,'page' => $page]);
    }
    public function readAll()
    {
        db_connect()->table('notifications')->where('user_id', auth()->id())->where('read_at', null)->update(['read_at' => gmdate('Y-m-d H:i:s')]);
        return redirect()->back()->with('success', '已全部标为已读。');
    }
}
