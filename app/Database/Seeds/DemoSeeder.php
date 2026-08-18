<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

final class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = time();
        $this->db->transException(true)->transBegin();
        try {
            $admin = $this->db
                ->table('users u')
                ->select('u.id,u.username')
                ->join('auth_groups_users g', "g.user_id=u.id AND g.group='admin'")
                ->orderBy('u.id')
                ->get()
                ->getRowArray();
            if ($admin) {
                $adminId = (int) $admin['id'];
                $this->ensureProfile($adminId, '', gmdate('Y-m-d H:i:s', $now - 864000));
            } else {
                $adminId = $this->ensureUser('admin', 'LetsBBS 本地演示管理员。', $now - 864000);
                $this->ensureGroup($adminId, 'admin', gmdate('Y-m-d H:i:s', $now - 864000));
            }
            $ethanId = $this->ensureUser('ethan', '保持简单，持续创造。', $now - 604800);
            $xiaomingId = $this->ensureUser('xiaoming', '喜欢轻社区和开源软件。', $now - 345600);
            $this->setProfileDetails($ethanId, '保持简单，持续创造。', '上海', 'https://example.com/ethan');
            $this->setProfileDetails($xiaomingId, '今天也要认真交流。', '北京', 'https://example.com/xiaoming');

            $nodes = [];
            foreach (
                [
                    ['development', '开发动态', '开发计划与版本动态', 30],
                    ['release-notes', '程序发布', '版本发布与升级说明', 40],
                    ['tutorials', '教程帮助', '安装和使用教程', 50],
                    ['feedback', '反馈建议', '问题反馈与功能建议', 60],
                    ['off-topic', '自言自语', '随手记录和轻松交流', 70],
                ]
                as [$slug, $name, $description, $sortOrder]
            ) {
                $nodes[$slug] = $this->ensureNode($slug, $name, $description, $sortOrder, $now);
            }

            $topics = [];
            $specs = [
                [
                    'LetsBBS 新版预览已经启动',
                    'release-notes',
                    $adminId,
                    '<p>欢迎来到 LetsBBS 的 CodeIgniter 4 新版。</p><p>首页、节点、关注、通知和内容管理功能已经可以体验。</p>',
                    7200,
                ],
                [
                    '如何开始使用这个轻社区？',
                    'tutorials',
                    $ethanId,
                    '<p>注册、选择一个节点，然后就可以发表主题和参与回复。</p>',
                    6500,
                ],
                [
                    '关于新版重构的一些想法',
                    'development',
                    $adminId,
                    '<p>保留简洁的社区体验，同时更新底层技术、安全策略和测试体系。</p>',
                    5600,
                ],
                [
                    '希望增加哪些社区功能？',
                    'feedback',
                    $xiaomingId,
                    '<p>欢迎留下你的建议。这个主题用于体验回复、热门主题与通知功能。</p>',
                    4300,
                ],
                ['第一次来到 LetsBBS', 'off-topic', $ethanId, '<p>界面很清爽，节点式的信息组织也很直观。</p>', 3000],
                [
                    '本地预览数据说明',
                    'tutorials',
                    $xiaomingId,
                    '<p>这些主题、回复和演示用户由 DemoSeeder 生成，仅用于本机调试。</p><p>演示用户没有固定登录密码，请使用安装时创建的管理员账号体验登录功能。</p>',
                    1800,
                ],
            ];
            foreach ($specs as [$title, $node, $userId, $body, $age]) {
                $topics[] = $this->ensureTopic($title, $nodes[$node], $userId, $body, $now - $age);
            }

            foreach (
                [
                    [$topics[0], $xiaomingId, '<p>新版仍然保留了轻社区的简洁风格。</p>', 6900],
                    [$topics[0], $ethanId, '<p>预览数据准备好了，可以继续体验搜索和节点筛选。</p>', 6600],
                    [$topics[1], $xiaomingId, '<p>从节点页面选择感兴趣的板块就可以了。</p>', 5900],
                    [$topics[2], $xiaomingId, '<p>先保留产品气质，再更新技术底座。</p>', 5000],
                    [$topics[3], $ethanId, '<p>希望继续保持简单、快速，并补充更多自动化测试。</p>', 3600],
                    [$topics[5], $adminId, '<p>这些内容可以安全地重复灌入，不会产生重复数据。</p>', 1200],
                ]
                as [$topicId, $userId, $body, $age]
            ) {
                $this->ensureComment($topicId, $userId, $body, $now - $age);
            }

            $createdAt = gmdate('Y-m-d H:i:s', $now - 900);
            $viewerIds = $this->authenticatedMemberIds() ?: [$adminId];
            foreach ($viewerIds as $viewerId) {
                $this->ensureFollow(
                    'node_follows',
                    ['user_id' => $viewerId, 'node_id' => $nodes['tutorials']],
                    $createdAt,
                );
                $this->ensureFollow(
                    'node_follows',
                    ['user_id' => $viewerId, 'node_id' => $nodes['feedback']],
                    $createdAt,
                );
                $this->ensureFollow('topic_follows', ['user_id' => $viewerId, 'topic_id' => $topics[3]], $createdAt);
                $this->ensureFollow('topic_follows', ['user_id' => $viewerId, 'topic_id' => $topics[5]], $createdAt);
                if ($viewerId !== $xiaomingId) {
                    $this->ensureFollow(
                        'user_follows',
                        ['follower_id' => $viewerId, 'followed_id' => $xiaomingId],
                        $createdAt,
                    );
                }

                $notification = [
                    'user_id' => $viewerId,
                    'actor_id' => $ethanId,
                    'topic_id' => $topics[3],
                    'kind' => 'comment',
                ];
                if (!$this->db->table('notifications')->where($notification)->countAllResults()) {
                    $this->db->table('notifications')->insert($notification + ['created_at' => $createdAt]);
                }
            }

            $this->refreshCounters($topics, $now);
            $this->db
                ->table('site_settings')
                ->ignore(true)
                ->insertBatch([
                    [
                        'setting_key' => 'home_welcome_message',
                        'setting_value' => json_encode('欢迎访问 LetsBBS', JSON_UNESCAPED_UNICODE),
                        'updated_at' => gmdate('Y-m-d H:i:s', $now),
                    ],
                    [
                        'setting_key' => 'home_introduction',
                        'setting_value' => json_encode('这是 LetsBBS 新版的本地功能预览站。', JSON_UNESCAPED_UNICODE),
                        'updated_at' => gmdate('Y-m-d H:i:s', $now),
                    ],
                ]);
            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    private function ensureUser(string $username, string $bio, int $createdAt): int
    {
        $row = $this->db->table('users')->select('id')->where('username', $username)->get()->getRowArray();
        if ($row) {
            $id = (int) $row['id'];
            $this->ensureProfile($id, $bio, gmdate('Y-m-d H:i:s', $createdAt));

            return $id;
        }

        $timestamp = gmdate('Y-m-d H:i:s', $createdAt);
        $this->db->table('users')->insert([
            'username' => $username,
            'active' => 1,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
        $id = (int) $this->db->insertID();
        $this->ensureProfile($id, $bio, $timestamp);
        $this->ensureGroup($id, 'user', $timestamp);

        return $id;
    }

    private function ensureProfile(int $userId, string $bio, string $timestamp): void
    {
        if (!$this->db->table('user_profiles')->where('user_id', $userId)->countAllResults()) {
            $this->db->table('user_profiles')->insert([
                'user_id' => $userId,
                'bio' => $bio,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }

    private function ensureGroup(int $userId, string $group, string $timestamp): void
    {
        if (
            !$this->db
                ->table('auth_groups_users')
                ->where(['user_id' => $userId, 'group' => $group])
                ->countAllResults()
        ) {
            $this->db
                ->table('auth_groups_users')
                ->insert(['user_id' => $userId, 'group' => $group, 'created_at' => $timestamp]);
        }
    }

    private function setProfileDetails(int $userId, string $signature, string $location, string $homepage): void
    {
        $this->db
            ->table('user_profiles')
            ->where('user_id', $userId)
            ->update([
                'signature' => $signature,
                'location' => $location,
                'homepage' => $homepage,
            ]);
    }

    /** @return list<int> */
    private function authenticatedMemberIds(): array
    {
        $rows = $this->db
            ->table('auth_identities i')
            ->select('i.user_id')
            ->where('i.type', 'email_password')
            ->orderBy('i.id')
            ->get()
            ->getResultArray();
        $ids = [];
        foreach ($rows as $row) {
            $userId = (int) $row['user_id'];
            if (
                !$this->db
                    ->table('auth_groups_users')
                    ->where(['user_id' => $userId, 'group' => 'admin'])
                    ->countAllResults()
            ) {
                $ids[] = $userId;
            }
        }

        return array_values(array_unique($ids));
    }

    private function ensureNode(string $slug, string $name, string $description, int $sortOrder, int $now): int
    {
        $row = $this->db->table('nodes')->select('id')->where('slug', $slug)->get()->getRowArray();
        if ($row) {
            return (int) $row['id'];
        }

        $timestamp = gmdate('Y-m-d H:i:s', $now);
        $this->db->table('nodes')->insert([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'sort_order' => $sortOrder,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        return (int) $this->db->insertID();
    }

    private function ensureTopic(string $title, int $nodeId, int $userId, string $body, int $createdAt): int
    {
        $row = $this->db->table('topics')->select('id')->where('title', $title)->get()->getRowArray();
        if ($row) {
            return (int) $row['id'];
        }

        $timestamp = gmdate('Y-m-d H:i:s', $createdAt);
        $this->db->table('topics')->insert([
            'node_id' => $nodeId,
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'status' => 'published',
            'last_activity_at' => $timestamp,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        return (int) $this->db->insertID();
    }

    private function ensureComment(int $topicId, int $userId, string $body, int $createdAt): void
    {
        if (
            $this->db
                ->table('comments')
                ->where(['topic_id' => $topicId, 'body' => $body])
                ->countAllResults()
        ) {
            return;
        }

        $timestamp = gmdate('Y-m-d H:i:s', $createdAt);
        $this->db->table('comments')->insert([
            'topic_id' => $topicId,
            'user_id' => $userId,
            'body' => $body,
            'status' => 'published',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    /** @param array<string, int> $key */
    private function ensureFollow(string $table, array $key, string $createdAt): void
    {
        if (!$this->db->table($table)->where($key)->countAllResults()) {
            $this->db->table($table)->insert($key + ['created_at' => $createdAt]);
        }
    }

    /** @param list<int> $topicIds */
    private function refreshCounters(array $topicIds, int $now): void
    {
        foreach ($topicIds as $topicId) {
            $lastComment = $this->db
                ->table('comments')
                ->selectMax('created_at')
                ->where(['topic_id' => $topicId, 'status' => 'published'])
                ->get()
                ->getRowArray()['created_at'];
            $topic = $this->db->table('topics')->select('created_at')->where('id', $topicId)->get()->getRowArray();
            $this->db
                ->table('topics')
                ->where('id', $topicId)
                ->update([
                    'comment_count' => $this->db
                        ->table('comments')
                        ->where(['topic_id' => $topicId, 'status' => 'published'])
                        ->countAllResults(),
                    'follower_count' => $this->db
                        ->table('topic_follows')
                        ->where('topic_id', $topicId)
                        ->countAllResults(),
                    'last_activity_at' => $lastComment ?: $topic['created_at'],
                ]);
        }

        $this->db->query(
            "UPDATE nodes n SET topic_count=(SELECT COUNT(*) FROM topics t WHERE t.node_id=n.id AND t.status='published')",
        );
        $this->db->query(
            "UPDATE user_profiles p SET topic_count=(SELECT COUNT(*) FROM topics t WHERE t.user_id=p.user_id AND t.status='published'),comment_count=(SELECT COUNT(*) FROM comments c WHERE c.user_id=p.user_id AND c.status='published'),follower_count=(SELECT COUNT(*) FROM user_follows f WHERE f.followed_id=p.user_id),following_count=(SELECT COUNT(*) FROM user_follows f WHERE f.follower_id=p.user_id),updated_at=?",
            [gmdate('Y-m-d H:i:s', $now)],
        );
    }
}
