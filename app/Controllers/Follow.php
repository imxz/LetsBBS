<?php

namespace App\Controllers;

use App\Services\FollowService;

final class Follow extends BaseController
{
    public function topic(int $id)
    {
        $on = (new FollowService())->toggleTopic((int) auth()->id(), $id);
        return redirect()->back()->with('success', $on ? '已关注主题。' : '已取消关注。');
    }
    public function node(int $id)
    {
        $on = (new FollowService())->toggleNode((int) auth()->id(), $id);
        return redirect()->back()->with('success', $on ? '已关注节点。' : '已取消关注。');
    }
    public function member(string $username)
    {
        $target = auth()->getProvider()->findByCredentials(['username' => $username]);
        if (!$target) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }$on = (new FollowService())->toggleUser((int) auth()->id(), (int) $target->id);
        return redirect()->back()->with('success', $on ? '已关注用户。' : '已取消关注。');
    }
}
