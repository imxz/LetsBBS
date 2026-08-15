<?php

namespace App\Controllers;

use App\Services\TopicService;

final class Topic extends BaseController
{
    private function assertCanPost(): void
    {
        $p = db_connect()->table('user_profiles')->where('user_id', auth()->id())->get()->getRowArray();
        if (($p['is_muted'] ?? 0) == 1) {
            throw new \RuntimeException('你已被禁言。');
        }
    }

    public function create()
    {
        return view('forum/editor', [
            'nodes' => db_connect()
                ->table('nodes')
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->get()
                ->getResultArray(),
        ]);
    }

    public function store()
    {
        try {
            $this->assertCanPost();
            if (
                !$this->validate([
                    'title' => 'required|min_length[2]|max_length[160]',
                    'node_id' => 'required|is_natural_no_zero',
                ])
            ) {
                throw new \RuntimeException(implode('；', $this->validator->getErrors()));
            }
            $id = new TopicService()->create(
                (int) auth()->id(),
                (int) $this->request->getPost('node_id'),
                (string) $this->request->getPost('title'),
                (string) $this->request->getPost('body'),
            );
            return redirect()
                ->to('/topic/' . $id)
                ->with('success', '主题已发布。');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function comment(int $id)
    {
        try {
            $this->assertCanPost();
            new TopicService()->comment((int) auth()->id(), $id, (string) $this->request->getPost('body'));
            return redirect()
                ->to('/topic/' . $id)
                ->with('success', '回复已发布。');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function delete(int $id)
    {
        try {
            new TopicService()->delete((int) auth()->id(), $id, auth()->user()->inGroup('admin'));
            return redirect()->to('/')->with('success', '主题已删除。');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
