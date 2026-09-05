<?php

namespace App\Controllers;

use App\Models\ForumModel;
use CodeIgniter\Exceptions\PageNotFoundException;

final class Legacy extends BaseController
{
    public function topicAction(int $id)
    {
        if (!new ForumModel()->topic($id)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return redirect()
            ->to('/topic/' . $id)
            ->with('error', '旧版操作链接不会自动执行，请在主题页面重新确认操作。');
    }

    public function nodeAction(int $id)
    {
        $node = new ForumModel()->nodeById($id);
        if (!$node) {
            throw PageNotFoundException::forPageNotFound();
        }

        return redirect()
            ->to('/node/' . rawurlencode((string) $node['slug']))
            ->with('error', '旧版操作链接不会自动执行，请在节点页面重新确认操作。');
    }

    public function memberAction(int $id)
    {
        $member = db_connect()
            ->table('users')
            ->select('username')
            ->where(['id' => $id, 'active' => 1])
            ->get()
            ->getRowArray();
        if (!$member) {
            throw PageNotFoundException::forPageNotFound();
        }

        return redirect()
            ->to('/member/' . rawurlencode((string) $member['username']))
            ->with('error', '旧版操作链接不会自动执行，请在成员页面重新确认操作。');
    }

    public function adminTopic(int $id)
    {
        return redirect()
            ->to('/admin/topic/' . $id . '/edit')
            ->setStatusCode(301);
    }

    public function adminNode(int $id)
    {
        return redirect()
            ->to('/admin/node/' . $id . '/edit')
            ->setStatusCode(301);
    }

    public function adminUser(int $id)
    {
        return redirect()
            ->to('/admin/user/' . $id . '/edit')
            ->setStatusCode(301);
    }
}
