<?php

use CodeIgniter\Test\CIUnitTestCase;

final class MySQL84IntegrationTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (getenv('RUN_MYSQL_TESTS') !== '1') {
            $this->markTestSkipped('Set RUN_MYSQL_TESTS=1 against a disposable MySQL 8.4 database.');
        }
    }

    public function testDatabaseVersionEngineIndexesAndForeignKeys(): void
    {
        $db = db_connect();
        $this->assertStringStartsWith('8.4.', (string) $db->query('SELECT VERSION() v')->getRow()->v);
        $tables = [
            'user_profiles',
            'nodes',
            'topics',
            'comments',
            'notifications',
            'node_follows',
            'user_follows',
            'topic_follows',
            'site_settings',
        ];
        foreach ($tables as $table) {
            $row = $db
                ->query(
                    'SELECT ENGINE,TABLE_COLLATION FROM information_schema.tables WHERE table_schema=? AND table_name=?',
                    [$db->database, $table],
                )
                ->getRowArray();
            $this->assertSame('InnoDB', $row['ENGINE']);
            $this->assertSame('utf8mb4_0900_ai_ci', $row['TABLE_COLLATION']);
        }
        $unique = $db
            ->query(
                "SELECT COUNT(*) n FROM information_schema.statistics WHERE table_schema=? AND table_name='topic_follows' AND index_name='PRIMARY'",
                [$db->database],
            )
            ->getRow()->n;
        $this->assertGreaterThanOrEqual(2, (int) $unique);
        $fks = $db
            ->query('SELECT COUNT(*) n FROM information_schema.referential_constraints WHERE constraint_schema=?', [
                $db->database,
            ])
            ->getRow()->n;
        $this->assertGreaterThanOrEqual(12, (int) $fks);
        $profileColumns = array_column(
            $db
                ->query(
                    "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema=? AND table_name='user_profiles'",
                    [$db->database],
                )
                ->getResultArray(),
            'COLUMN_NAME',
        );
        foreach (['qq', 'location', 'homepage', 'signature'] as $column) {
            $this->assertContains($column, $profileColumns);
        }
        $nodeColumns = array_column(
            $db
                ->query(
                    "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema=? AND table_name='nodes'",
                    [$db->database],
                )
                ->getResultArray(),
            'COLUMN_NAME',
        );
        foreach (['parent_id', 'keywords', 'featured', 'show_on_home'] as $column) {
            $this->assertContains($column, $nodeColumns);
        }
        $this->assertTrue($db->fieldExists('view_count', 'topics'));
        $this->assertTrue($db->fieldExists('comment_id', 'notifications'));
    }

    public function testDuplicateFollowIsRejected(): void
    {
        $db = db_connect();
        $now = gmdate('Y-m-d H:i:s');
        $db->transBegin();
        try {
            $username = 'test' . bin2hex(random_bytes(4));
            $db->table('users')->insert([
                'username' => $username,
                'active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $userId = (int) $db->insertID();
            $db->table('user_profiles')->insert(['user_id' => $userId, 'created_at' => $now, 'updated_at' => $now]);
            $nodeId = (int) $db->table('nodes')->select('id')->get()->getRow()->id;
            $db->table('topics')->insert([
                'node_id' => $nodeId,
                'user_id' => $userId,
                'title' => 'constraint test',
                'body' => '<p>test</p>',
                'status' => 'published',
                'last_activity_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $topicId = (int) $db->insertID();
            $row = ['user_id' => $userId, 'topic_id' => $topicId, 'created_at' => $now];
            $db->table('topic_follows')->insert($row);
            try {
                $db->table('topic_follows')->insert($row);
                $this->fail('Duplicate follow was accepted.');
            } catch (\Throwable) {
                $this->addToAssertionCount(1);
            }
        } finally {
            $db->transRollback();
        }
    }

    public function testTopicCommentFollowAndCountersStayConsistent(): void
    {
        $db = db_connect();
        $now = gmdate('Y-m-d H:i:s');
        $ids = [];
        try {
            foreach (['author', 'reader'] as $prefix) {
                $db->table('users')->insert([
                    'username' => $prefix . bin2hex(random_bytes(4)),
                    'active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $id = (int) $db->insertID();
                $ids[] = $id;
                $db->table('user_profiles')->insert(['user_id' => $id, 'created_at' => $now, 'updated_at' => $now]);
            }
            $nodeId = (int) $db->table('nodes')->select('id')->get()->getRow()->id;
            $topicId = new \App\Services\TopicService()->create(
                $ids[0],
                $nodeId,
                'integration ' . bin2hex(random_bytes(3)),
                '<p>Hello<script>alert(1)</script></p>',
            );
            $topic = $db->table('topics')->where('id', $topicId)->get()->getRowArray();
            $this->assertSame(1, (int) $topic['follower_count']);
            $this->assertStringNotContainsString('<script', $topic['body']);
            $follow = new \App\Services\FollowService();
            $this->assertTrue($follow->toggleTopic($ids[1], $topicId));
            $this->assertFalse($follow->toggleTopic($ids[1], $topicId));
            $this->assertSame(
                1,
                (int) $db->table('topics')->select('follower_count')->where('id', $topicId)->get()->getRow()
                    ->follower_count,
            );
            $commentId = new \App\Services\TopicService()->comment($ids[1], $topicId, '<p>Reply</p>');
            $this->assertSame(
                1,
                (int) $db->table('topics')->select('comment_count')->where('id', $topicId)->get()->getRow()
                    ->comment_count,
            );
            $this->assertSame(
                1,
                (int) $db->table('user_profiles')->select('comment_count')->where('user_id', $ids[1])->get()->getRow()
                    ->comment_count,
            );
            $this->assertSame(
                1,
                $db
                    ->table('notifications')
                    ->where(['user_id' => $ids[0], 'topic_id' => $topicId, 'kind' => 'comment'])
                    ->countAllResults(),
            );
            $this->assertSame(
                $commentId,
                (int) $db
                    ->table('notifications')
                    ->select('comment_id')
                    ->where(['user_id' => $ids[0], 'topic_id' => $topicId, 'kind' => 'comment'])
                    ->get()
                    ->getRow()->comment_id,
            );
            new \App\Services\TopicService()->delete($ids[0], $topicId, false);
            $this->assertSame(
                0,
                (int) $db->table('user_profiles')->select('comment_count')->where('user_id', $ids[1])->get()->getRow()
                    ->comment_count,
            );
            $this->assertSame(
                0,
                (int) $db->table('user_profiles')->select('topic_count')->where('user_id', $ids[0])->get()->getRow()
                    ->topic_count,
            );
            $db->table('topics')->where('id', $topicId)->delete();
        } finally {
            foreach ($ids as $id) {
                $db->table('users')->where('id', $id)->delete();
            }
        }
    }

    public function testFailedTopicCreationRollsBack(): void
    {
        $db = db_connect();
        $now = gmdate('Y-m-d H:i:s');
        $name = 'rollback' . bin2hex(random_bytes(4));
        $title = 'rollback ' . bin2hex(random_bytes(5));
        $db->table('users')->insert(['username' => $name, 'active' => 1, 'created_at' => $now, 'updated_at' => $now]);
        $userId = (int) $db->insertID();
        $db->table('user_profiles')->insert(['user_id' => $userId, 'created_at' => $now, 'updated_at' => $now]);
        try {
            try {
                new \App\Services\TopicService()->create($userId, 4294967295, $title, '<p>body</p>');
                $this->fail('Foreign-key failure was not raised.');
            } catch (\Throwable) {
                $this->addToAssertionCount(1);
            }
            $this->assertSame(0, $db->table('topics')->where('title', $title)->countAllResults());
            $this->assertSame(
                0,
                (int) $db->table('user_profiles')->select('topic_count')->where('user_id', $userId)->get()->getRow()
                    ->topic_count,
            );
        } finally {
            $db->table('users')->where('id', $userId)->delete();
        }
    }

    public function testTopicCreationRejectsInvalidTitleBeforeWriting(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('标题须为');
        new \App\Services\TopicService()->create(1, 1, ' ', '<p>body</p>');
    }

    public function testSiteSettingsArePersistedAndReloaded(): void
    {
        $settings = new \App\Services\SiteSettings();
        $original = $settings->all();
        try {
            $settings->save(['site_name' => 'Test BBS', 'site_description' => 'Integration test']);
            $reloaded = new \App\Services\SiteSettings();
            $this->assertSame('Test BBS', $reloaded->get('site_name'));
            $this->assertSame('Integration test', $reloaded->get('site_description'));
        } finally {
            $settings->save($original);
        }
    }

    public function testTopicApprovalSettingQueuesNewTopicsWithoutVisibleCounters(): void
    {
        $db = db_connect();
        $settings = service('siteSettings');
        $original = $settings->all();
        $now = gmdate('Y-m-d H:i:s');
        $username = 'approval' . bin2hex(random_bytes(4));
        $db->table('users')->insert([
            'username' => $username,
            'active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $userId = (int) $db->insertID();
        $db->table('user_profiles')->insert(['user_id' => $userId, 'created_at' => $now, 'updated_at' => $now]);
        $node = $db->table('nodes')->select('id,topic_count')->where('is_active', 1)->get()->getRowArray();
        $topicId = null;
        try {
            $settings->save(['topic_requires_approval' => '1']);
            $topicId = new \App\Services\TopicService()->create(
                $userId,
                (int) $node['id'],
                'approval ' . bin2hex(random_bytes(4)),
                '<p>pending</p>',
            );
            $topic = $db->table('topics')->where('id', $topicId)->get()->getRowArray();
            $this->assertSame('hidden', $topic['status']);
            $this->assertSame(
                (int) $node['topic_count'],
                (int) $db->table('nodes')->select('topic_count')->where('id', $node['id'])->get()->getRow()
                    ->topic_count,
            );
            $this->assertSame(0, $db->table('topic_follows')->where('topic_id', $topicId)->countAllResults());
        } finally {
            if ($topicId !== null) {
                $db->table('topics')->where('id', $topicId)->delete();
            }
            $db->table('users')->where('id', $userId)->delete();
            $settings->save($original);
        }
    }

    public function testTopicViewCounterRetainsLegacyContext(): void
    {
        $db = db_connect();
        $topic = $db->table('topics')->select('id,view_count')->where('status', 'published')->get()->getRowArray();
        if (!$topic) {
            \Config\Database::seeder()->call('App\\Database\\Seeds\\DemoSeeder');
            $topic = $db->table('topics')->select('id,view_count')->where('status', 'published')->get()->getRowArray();
        }
        $model = new \App\Models\ForumModel();
        $model->incrementViewCount((int) $topic['id']);
        $this->assertSame(
            (int) $topic['view_count'] + 1,
            (int) $db->table('topics')->select('view_count')->where('id', $topic['id'])->get()->getRow()->view_count,
        );
    }

    public function testHomeQueriesProvideLegacyHomepageFeatures(): void
    {
        $db = db_connect();
        $now = gmdate('Y-m-d H:i:s');
        $db->transBegin();
        try {
            $userIds = [];
            foreach (['homeauthor', 'homeviewer'] as $prefix) {
                $db->table('users')->insert([
                    'username' => $prefix . bin2hex(random_bytes(4)),
                    'active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $userIds[] = (int) $db->insertID();
                $db->table('user_profiles')->insert([
                    'user_id' => end($userIds),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            $nodeId = (int) $db->table('nodes')->select('id')->get()->getRow()->id;
            $title = 'home query ' . bin2hex(random_bytes(5));
            $db->table('topics')->insert([
                'node_id' => $nodeId,
                'user_id' => $userIds[0],
                'title' => $title,
                'body' => '<p>searchable homepage body</p>',
                'status' => 'published',
                'comment_count' => 1,
                'last_activity_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $topicId = (int) $db->insertID();
            $db->table('comments')->insert([
                'topic_id' => $topicId,
                'user_id' => $userIds[1],
                'body' => '<p>reply</p>',
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $db->table('node_follows')->insert([
                'user_id' => $userIds[1],
                'node_id' => $nodeId,
                'created_at' => $now,
            ]);
            $db->table('topic_follows')->insert([
                'user_id' => $userIds[1],
                'topic_id' => $topicId,
                'created_at' => $now,
            ]);
            $db->table('user_follows')->insert([
                'follower_id' => $userIds[1],
                'followed_id' => $userIds[0],
                'created_at' => $now,
            ]);

            $model = new \App\Models\ForumModel();
            $search = $model->listing(1, null, false, 'all', $userIds[1], $title);
            $this->assertSame($topicId, (int) $search[0]['id']);
            $this->assertStringStartsWith('homeviewer', $search[0]['last_reply_username']);
            foreach (['nodes', 'topics', 'users'] as $filter) {
                $rows = $model->listing(1, null, false, $filter, $userIds[1]);
                $this->assertContains($topicId, array_map(static fn(array $row): int => (int) $row['id'], $rows));
            }
            $summary = $model->viewerSummary($userIds[1]);
            $this->assertSame(1, $summary['node_follows']);
            $this->assertSame(1, $summary['topic_follows']);
            $this->assertSame(1, $summary['user_follows']);
            $this->assertArrayHasKey('users', $model->statistics());
        } finally {
            $db->transRollback();
        }
    }

    public function testDemoSeederIsIdempotent(): void
    {
        $seeder = \Config\Database::seeder();
        $seeder->call('App\\Database\\Seeds\\DemoSeeder');
        $db = db_connect();
        $titles = [
            'LetsBBS 新版预览已经启动',
            '如何开始使用这个轻社区？',
            '关于新版重构的一些想法',
            '希望增加哪些社区功能？',
            '第一次来到 LetsBBS',
            '本地预览数据说明',
        ];
        $first = [
            'topics' => $db->table('topics')->whereIn('title', $titles)->countAllResults(),
            'comments' => $db
                ->table('comments c')
                ->join('topics t', 't.id=c.topic_id')
                ->whereIn('t.title', $titles)
                ->countAllResults(),
            'notifications' => $db->table('notifications')->where('kind', 'comment')->countAllResults(),
        ];
        $seeder->call('App\\Database\\Seeds\\DemoSeeder');
        $this->assertSame(6, $first['topics']);
        $this->assertSame(6, $first['comments']);
        $this->assertSame($first, [
            'topics' => $db->table('topics')->whereIn('title', $titles)->countAllResults(),
            'comments' => $db
                ->table('comments c')
                ->join('topics t', 't.id=c.topic_id')
                ->whereIn('t.title', $titles)
                ->countAllResults(),
            'notifications' => $db->table('notifications')->where('kind', 'comment')->countAllResults(),
        ]);
    }

    public function testDemoSeederPopulatesEveryAuthenticatedMemberWorkspace(): void
    {
        $provider = auth()->getProvider();
        $username = 'dv' . bin2hex(random_bytes(4));
        $user = new \CodeIgniter\Shield\Entities\User([
            'username' => $username,
            'email' => $username . '@example.test',
            'password' => bin2hex(random_bytes(8)),
            'active' => 1,
        ]);
        $this->assertTrue($provider->save($user), implode('; ', $provider->errors()));
        $viewer = $provider->findById($provider->getInsertID());
        $viewer->addGroup('user');
        $viewerId = (int) $viewer->id;
        $db = db_connect();
        $now = gmdate('Y-m-d H:i:s');
        $db->table('user_profiles')->insert(['user_id' => $viewerId, 'created_at' => $now, 'updated_at' => $now]);
        try {
            \Config\Database::seeder()->call('App\\Database\\Seeds\\DemoSeeder');
            $this->assertSame(2, $db->table('node_follows')->where('user_id', $viewerId)->countAllResults());
            $this->assertSame(2, $db->table('topic_follows')->where('user_id', $viewerId)->countAllResults());
            $this->assertSame(1, $db->table('user_follows')->where('follower_id', $viewerId)->countAllResults());
            $this->assertSame(1, $db->table('notifications')->where('user_id', $viewerId)->countAllResults());
        } finally {
            $db->table('users')->where('id', $viewerId)->delete();
        }
    }
}
