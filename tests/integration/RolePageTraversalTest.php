<?php

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

final class RolePageTraversalTest extends CIUnitTestCase
{
    use AuthenticationTesting;
    use FeatureTestTrait;

    private User $ordinary;

    private User $admin;

    private int $nodeId;

    private string $nodeSlug;

    private int $topicId;

    private bool $createdInstallLock = false;

    private array $originalSettings = [];

    protected function setUp(): void
    {
        parent::setUp();
        if (getenv('RUN_MYSQL_TESTS') !== '1') {
            $this->markTestSkipped('Set RUN_MYSQL_TESTS=1 against a disposable MySQL 8.4 database.');
        }
        if (!is_file(WRITEPATH . 'install.lock')) {
            file_put_contents(WRITEPATH . 'install.lock', "test\n");
            $this->createdInstallLock = true;
        }
        auth()->logout();
        $this->originalSettings = service('siteSettings')->all();
        $this->ordinary = $this->createUser('roleuser', 'user');
        $this->admin = $this->createUser('roleadmin', 'admin');
        $now = gmdate('Y-m-d H:i:s');
        $db = db_connect();
        $this->nodeSlug = 'role-' . bin2hex(random_bytes(4));
        $db->table('nodes')->insert([
            'name' => '身份回归节点',
            'slug' => $this->nodeSlug,
            'description' => '三身份页面回归',
            'sort_order' => -999,
            'show_on_home' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->nodeId = (int) $db->insertID();
        $db->table('topics')->insert([
            'node_id' => $this->nodeId,
            'user_id' => $this->ordinary->id,
            'title' => '三身份页面回归主题',
            'body' => '<p>用于覆盖游客、普通用户和管理员页面。</p>',
            'status' => 'published',
            'last_activity_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->topicId = (int) $db->insertID();
        $db->table('comments')->insert([
            'topic_id' => $this->topicId,
            'user_id' => $this->admin->id,
            'body' => '<p>管理员测试回复。</p>',
            'status' => 'published',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    protected function tearDown(): void
    {
        auth()->logout();
        if ($this->originalSettings) {
            service('siteSettings')->save($this->originalSettings);
        }
        if (isset($this->topicId)) {
            db_connect()->table('topics')->where('id', $this->topicId)->delete();
        }
        if (isset($this->nodeId)) {
            db_connect()->table('nodes')->where('id', $this->nodeId)->delete();
        }
        foreach (['ordinary', 'admin'] as $property) {
            if (isset($this->{$property})) {
                db_connect()
                    ->table('users')
                    ->where('id', $this->{$property}->id)
                    ->delete();
            }
        }
        if ($this->createdInstallLock) {
            unlink(WRITEPATH . 'install.lock');
        }
        parent::tearDown();
    }

    public function testGuestCanTraverseEveryPublicPageFamily(): void
    {
        $username = rawurlencode($this->ordinary->username);
        $paths = [
            '/',
            '/topic/show/' . $this->nodeSlug,
            '/node',
            '/node/' . $this->nodeSlug,
            '/recent',
            '/latest',
            '/search?q=回归',
            '/search?q=管理员测试回复&type=comments',
            '/search?q=' . rawurlencode($this->ordinary->username) . '&type=members',
            '/topic/' . $this->topicId,
            '/member/' . $username,
            '/member/' . $username . '/topics/1',
            '/member/' . $username . '/comments/1',
            '/member/' . $username . '/topic/1',
            '/member/' . $username . '/comment/1',
            '/register',
            '/reg',
            '/login',
        ];
        foreach ($paths as $path) {
            $this->get($path)->assertStatus(200);
        }
        $this->get('/node/' . $this->nodeId)->assertRedirectTo('/node/' . $this->nodeSlug, 301);
        $this->get('/topic/follow/' . $this->topicId)->assertRedirectTo('/topic/' . $this->topicId);
        $this->assertSame(
            0,
            db_connect()->table('topic_follows')->where('topic_id', $this->topicId)->countAllResults(),
        );
        $this->get('/settings')->assertRedirect();
        $this->get('/admin')->assertRedirect();
        $this->get('/login/magic-link')->assertRedirectTo('/login');
    }

    public function testHomeNodeSelectionFiltersTopicsAndPersists(): void
    {
        $selectionPath = '/topic/show/' . $this->nodeSlug;
        $selected = $this->get($selectionPath);

        $selected->assertStatus(200);
        $selected->assertSee('三身份页面回归主题');
        $selected->assertSee('topshow');
        $this->assertStringContainsString('aria-current="page"', $selected->getBody());
        $this->assertSame($this->nodeSlug, session()->get('top_show_node'));

        $home = $this->withSession(['top_show_node' => $this->nodeSlug])->get('/');
        $home->assertStatus(200);
        $home->assertSee('三身份页面回归主题');
        $this->assertStringContainsString(
            'href="' . $selectionPath . '" role="button" aria-current="page"',
            $home->getBody(),
        );
    }

    public function testOrdinaryUserCanTraverseMemberBusinessPagesButNotAdmin(): void
    {
        $this->actingAs($this->ordinary);
        $username = rawurlencode($this->ordinary->username);
        foreach (
            [
                '/',
                '/topic/' . $this->topicId,
                '/topic/new',
                '/topic/add',
                '/notification',
                '/settings',
                '/settings/profile',
                '/settings/avatar',
                '/settings/password',
                '/settings/notifications',
                '/my/content',
                '/my/subscriptions',
                '/my/subscriptions?kind=nodes',
                '/my/subscriptions?kind=users',
                '/my/topics/' . $this->topicId . '/edit',
                '/member/' . $username,
            ]
            as $path
        ) {
            $this->get($path)->assertStatus(200);
        }
        $this->get('/admin')->assertRedirect();
    }

    public function testAdministratorCanTraverseEveryAdminPageFamily(): void
    {
        $this->actingAs($this->admin);
        foreach (
            [
                '/admin/topic',
                '/admin/topic/page/1',
                '/admin/topic/verify',
                '/admin/topic/' . $this->topicId . '/edit',
                '/admin/user',
                '/admin/user/page/1',
                '/admin/user/banned',
                '/admin/user/' . $this->ordinary->id . '/edit',
                '/admin/node',
                '/admin/node/add',
                '/admin/node/' . $this->nodeId . '/edit',
                '/admin/settings/site',
                '/admin/settings/verify',
                '/admin/comment',
                '/admin/comment/page/2',
                '/admin/logs',
                '/admin/reports',
                '/admin/topic?status=deleted',
            ]
            as $path
        ) {
            $this->get($path)->assertStatus(200);
        }
        $this->get('/admin')->assertRedirectTo('/admin/topic');
    }

    public function testHiddenTopicIsVisibleOnlyToAdministratorPreview(): void
    {
        db_connect()
            ->table('topics')
            ->where('id', $this->topicId)
            ->update(['status' => 'hidden']);

        $this->assertNull(new \App\Models\ForumModel()->topicVisibleTo($this->topicId, false));
        $this->actingAs($this->admin);
        $preview = $this->get('/topic/' . $this->topicId);
        $preview->assertStatus(200);
        $preview->assertSee('管理员预览：已隐藏');
        $preview->assertDontSee('添加一条新回复');
    }

    public function testAllUsersIncludesMutedMembers(): void
    {
        db_connect()
            ->table('user_profiles')
            ->where('user_id', $this->ordinary->id)
            ->update(['is_muted' => 1]);
        $this->actingAs($this->admin);

        $all = $this->get('/admin/user');
        $all->assertStatus(200);
        $all->assertSee($this->ordinary->username);
        $banned = $this->get('/admin/user/banned');
        $banned->assertStatus(200);
        $banned->assertSee($this->ordinary->username);
    }

    public function testPendingCommentPublishesWithOneMentionNotification(): void
    {
        $settings = service('siteSettings');
        $original = $settings->all();
        try {
            $settings->save(['comment_requires_approval' => '1']);
            $commentId = new \App\Services\TopicService()->comment(
                (int) $this->admin->id,
                $this->topicId,
                '<p>@' . $this->ordinary->username . ' 请查看，@' . $this->ordinary->username . '。</p>',
            );
            $db = db_connect();
            $this->assertSame(
                'pending',
                $db->table('comments')->select('status')->where('id', $commentId)->get()->getRow()->status,
            );
            $this->assertSame(0, $db->table('notifications')->where('comment_id', $commentId)->countAllResults());
            $this->assertSame(
                0,
                (int) $db->table('topics')->select('comment_count')->where('id', $this->topicId)->get()->getRow()
                    ->comment_count,
            );

            $this->actingAs($this->admin);
            helper('form');
            $this->post('/admin/comment/' . $commentId . '/moderate', [
                'status' => 'published',
                csrf_token() => csrf_hash(),
            ])->assertRedirect();
            $this->assertSame(
                1,
                $db
                    ->table('notifications')
                    ->where([
                        'user_id' => $this->ordinary->id,
                        'comment_id' => $commentId,
                        'kind' => 'mention',
                    ])
                    ->countAllResults(),
            );
            $this->assertSame(
                1,
                (int) $db->table('topics')->select('comment_count')->where('id', $this->topicId)->get()->getRow()
                    ->comment_count,
            );
        } finally {
            $settings->save($original);
        }
    }

    public function testHiddenTopicDoesNotLeakReplyTextInNotifications(): void
    {
        $db = db_connect();
        $commentId = (int) $db->table('comments')->where('topic_id', $this->topicId)->get()->getRow()->id;
        $db->table('notifications')->insert([
            'user_id' => $this->ordinary->id,
            'actor_id' => $this->admin->id,
            'topic_id' => $this->topicId,
            'comment_id' => $commentId,
            'kind' => 'comment',
            'created_at' => gmdate('Y-m-d H:i:s'),
        ]);
        $this->actingAs($this->ordinary);
        foreach (['hidden', 'deleted', 'pending'] as $status) {
            $db->table('topics')
                ->where('id', $this->topicId)
                ->update(['status' => $status]);
            $page = $this->get('/notification');
            $page->assertStatus(200);
            $page->assertDontSee('管理员测试回复。');
            $page->assertDontSee('三身份页面回归主题');
        }
    }

    public function testInactiveSessionCannotAccessAdminOrWrite(): void
    {
        $this->actingAs($this->admin);
        db_connect()
            ->table('users')
            ->where('id', $this->admin->id)
            ->update(['active' => 0]);
        $this->get('/admin/topic')->assertRedirectTo('/login');
        $this->assertFalse(auth()->loggedIn());
        $this->post('/admin/topic/' . $this->topicId . '/moderate', [
            'status' => 'deleted',
            csrf_token() => csrf_hash(),
        ])->assertRedirect();
        $this->assertSame(
            'published',
            db_connect()->table('topics')->where('id', $this->topicId)->get()->getRow()->status,
        );
    }

    public function testHiddenNodeBlocksEveryPublicContentSurface(): void
    {
        $db = db_connect();
        $db->table('nodes')
            ->where('id', $this->nodeId)
            ->update(['is_active' => 0]);
        $model = new \App\Models\ForumModel();
        $this->assertNull($model->topic($this->topicId));
        $this->assertNull($model->nodeById($this->nodeId));
        $this->assertSame([], $model->listing(1, $this->nodeId, false));
        $this->assertSame([], $model->searchComments('管理员测试回复', 1));
        $this->get('/member/' . $this->ordinary->username)->assertDontSee('三身份页面回归主题');
        $this->actingAs($this->ordinary);
        $this->post('/topic/' . $this->topicId . '/comment', [
            'body' => '<p>不允许写入</p>',
            csrf_token() => csrf_hash(),
        ])->assertRedirect();
        $this->assertSame(0, $db->table('comments')->where('body', '<p>不允许写入</p>')->countAllResults());
        $this->actingAs($this->admin);
        $this->get('/admin/topic/' . $this->topicId . '/edit')->assertSee('身份回归节点');
    }

    public function testReadonlyNodeRemainsBrowsableButRejectsPosts(): void
    {
        db_connect()
            ->table('nodes')
            ->where('id', $this->nodeId)
            ->update(['accepts_posts' => 0]);
        $page = $this->get('/topic/' . $this->topicId);
        $page->assertSee('三身份页面回归主题');
        $page->assertDontSee('添加一条新回复');
        $this->expectException(\RuntimeException::class);
        new \App\Services\TopicService()->create((int) $this->ordinary->id, $this->nodeId, '不能发帖', '<p>正文</p>');
    }

    public function testParentVisibilityAndCategoryPostingAreEnforced(): void
    {
        $db = db_connect();
        $now = gmdate('Y-m-d H:i:s');
        $db->table('nodes')->insert([
            'name' => '父分类',
            'slug' => 'parent-' . bin2hex(random_bytes(5)),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $parentId = (int) $db->insertID();
        try {
            $db->table('nodes')
                ->where('id', $this->nodeId)
                ->update(['parent_id' => $parentId]);
            try {
                new \App\Services\TopicService()->create(
                    (int) $this->ordinary->id,
                    $parentId,
                    '不能向分类发帖',
                    '<p>正文</p>',
                );
                $this->fail('A parent with active children accepted a topic.');
            } catch (\RuntimeException $e) {
                $this->assertStringContainsString('子节点', $e->getMessage());
            }
            $db->table('nodes')
                ->where('id', $parentId)
                ->update(['is_active' => 0]);
            $this->assertNull(new \App\Models\ForumModel()->topic($this->topicId));
            $this->assertNull(new \App\Models\ForumModel()->nodeById($this->nodeId));
        } finally {
            $db->table('nodes')
                ->where('id', $this->nodeId)
                ->update(['parent_id' => null]);
            $db->table('nodes')->where('id', $parentId)->delete();
        }
    }

    public function testReplyPaginationResolvesStableId(): void
    {
        $db = db_connect();
        $now = gmdate('Y-m-d H:i:s');
        for ($i = 0; $i < 35; ++$i) {
            $db->table('comments')->insert([
                'topic_id' => $this->topicId,
                'user_id' => $this->ordinary->id,
                'body' => '<p>分页回复-' . $i . '</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        $last = (int) $db->insertID();
        $this->get('/topic/' . $this->topicId)->assertDontSee('分页回复-34');
        $page = $this->get('/topic/' . $this->topicId . '?reply=' . $last);
        $page->assertSee('分页回复-34');
        $page->assertDontSee('管理员测试回复。');
        $this->assertStringContainsString('id="comment-' . $last . '"', $page->getBody());
        $this->assertCount(6, new \App\Models\ForumModel()->comments($this->topicId, 2));
    }

    public function testPendingReplyQueueReachesBeyondFirstFifty(): void
    {
        $db = db_connect();
        $now = gmdate('Y-m-d H:i:s');
        $marker = '队列' . bin2hex(random_bytes(4));
        for ($i = 0; $i < 61; ++$i) {
            $db->table('comments')->insert([
                'topic_id' => $this->topicId,
                'user_id' => $this->ordinary->id,
                'body' => '<p>' . $marker . '-' . $i . '</p>',
                'status' => 'pending',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        $this->actingAs($this->admin);
        $page = $this->get('/admin/comment/page/4?q=' . $marker);
        $page->assertStatus(200);
        $page->assertSee($marker . '-0');
        $page->assertDontSee($marker . '-60');
    }

    public function testOwnEditRequiresOwnershipAndThirtyMinuteWindow(): void
    {
        $this->actingAs($this->ordinary);
        service('siteSettings')->save(['topic_requires_approval' => '1']);
        $this->post('/my/topics/' . $this->topicId . '/edit', [
            'title' => '修改后的主题',
            'body' => '<p>修改后的正文</p>',
            csrf_token() => csrf_hash(),
        ])->assertRedirect();
        $row = db_connect()->table('topics')->where('id', $this->topicId)->get()->getRowArray();
        $this->assertSame('pending', $row['status']);
        $this->assertSame('修改后的主题', $row['title']);
        $this->get('/my/content')->assertSee('待审核');
        db_connect()
            ->table('topics')
            ->where('id', $this->topicId)
            ->update(['created_at' => gmdate('Y-m-d H:i:s', time() - 1900)]);
        $this->post('/my/topics/' . $this->topicId . '/edit', [
            'title' => '不应生效',
            'body' => '<p>过期修改</p>',
            csrf_token() => csrf_hash(),
        ])->assertRedirect();
        $this->assertSame(
            '修改后的主题',
            db_connect()->table('topics')->where('id', $this->topicId)->get()->getRow()->title,
        );
        $this->actingAs($this->admin);
        $this->get('/my/content')->assertDontSee('修改后的主题');
    }

    public function testReportsAndModerationRecordReasonAndRestore(): void
    {
        $this->actingAs($this->ordinary);
        $this->post('/topic/' . $this->topicId . '/report', [
            'reason' => '测试举报说明',
            csrf_token() => csrf_hash(),
        ])->assertRedirect();
        $db = db_connect();
        $report = $db->table('reports')->where('topic_id', $this->topicId)->get()->getRowArray();
        $this->assertSame('open', $report['status']);
        $this->actingAs($this->admin);
        foreach (['deleted', 'pending', 'published'] as $status) {
            $this->post('/admin/topic/' . $this->topicId . '/moderate', [
                'status' => $status,
                'reason' => '审核说明',
                csrf_token() => csrf_hash(),
            ])->assertRedirect();
            $this->assertSame($status, $db->table('topics')->where('id', $this->topicId)->get()->getRow()->status);
        }
        $this->assertSame(
            3,
            $db
                ->table('moderation_logs')
                ->where(['resource_type' => 'topic', 'resource_id' => $this->topicId])
                ->countAllResults(),
        );
        $this->get('/admin/logs')->assertSee('审核说明');
        $this->post('/admin/report/' . $report['id'] . '/resolve', [csrf_token() => csrf_hash()])->assertRedirect();
        $this->assertSame('resolved', $db->table('reports')->where('id', $report['id'])->get()->getRow()->status);
    }

    public function testDuplicateReplyAndNotificationPreference(): void
    {
        service('siteSettings')->save(['comment_requires_approval' => '0']);
        db_connect()
            ->table('user_profiles')
            ->where('user_id', $this->ordinary->id)
            ->update(['notify_comments' => 0]);
        $service = new \App\Services\TopicService();
        $first = $service->comment((int) $this->admin->id, $this->topicId, '<p>双击提交测试</p>');
        $second = $service->comment((int) $this->admin->id, $this->topicId, '<p>双击提交测试</p>');
        $this->assertSame($first, $second);
        $this->assertSame(
            0,
            db_connect()
                ->table('notifications')
                ->where(['user_id' => $this->ordinary->id, 'comment_id' => $first])
                ->countAllResults(),
        );
        $this->assertSame(
            1,
            (int) db_connect()->table('topics')->where('id', $this->topicId)->get()->getRow()->comment_count,
        );
    }

    public function testRegistrationToggleAndPrivacyDefaults(): void
    {
        $this->assertFalse((bool) config('Auth')->allowMagicLinkLogins);
        $this->get('/register')->assertSee('email');
        service('siteSettings')->save(['registration_enabled' => '0']);
        $this->get('/register')->assertRedirect();
        $this->post('/reg', [
            'username' => 'closedreg',
            'email' => 'closed@example.test',
            csrf_token() => csrf_hash(),
        ])->assertRedirect();
        $this->assertSame(0, db_connect()->table('users')->where('username', 'closedreg')->countAllResults());
        $this->assertSame('0', service('siteSettings')->get('analytics_enabled'));
    }

    public function testNotificationReadOnlyMarksCurrentPage(): void
    {
        $db = db_connect();
        for ($i = 0; $i < 35; ++$i) {
            $db->table('notifications')->insert([
                'user_id' => $this->ordinary->id,
                'actor_id' => $this->admin->id,
                'kind' => 'follow',
                'created_at' => gmdate('Y-m-d H:i:s'),
            ]);
        }
        $this->actingAs($this->ordinary);
        $this->get('/notification')->assertStatus(200);
        $this->assertSame(
            5,
            $db
                ->table('notifications')
                ->where(['user_id' => $this->ordinary->id, 'read_at' => null])
                ->countAllResults(),
        );
        $this->post('/notification/read', [csrf_token() => csrf_hash()])->assertRedirect();
        $this->assertSame(
            0,
            $db
                ->table('notifications')
                ->where(['user_id' => $this->ordinary->id, 'read_at' => null])
                ->countAllResults(),
        );
    }

    public function testMemberSearchHasDistinctPagesAndIgnoresHtmlTags(): void
    {
        $db = db_connect();
        $ids = [];
        $marker = 'search' . bin2hex(random_bytes(3));
        try {
            for ($i = 0; $i < 25; ++$i) {
                $db->table('users')->insert([
                    'username' => $marker . $i,
                    'active' => 1,
                    'created_at' => gmdate('Y-m-d H:i:s'),
                ]);
                $ids[] = (int) $db->insertID();
            }
            $model = new \App\Models\ForumModel();
            $first = array_slice($model->searchMembers($marker, 1), 0, 20);
            $second = $model->searchMembers($marker, 2);
            $this->assertCount(20, $first);
            $this->assertCount(5, $second);
            $this->assertSame([], array_intersect(array_column($first, 'username'), array_column($second, 'username')));
            $db->table('topics')
                ->where('id', $this->topicId)
                ->update(['body' => '<p style="text-align:center">纯正文</p>']);
            $this->assertSame([], $model->listing(1, $this->nodeId, false, 'all', null, 'text-align'));
            $this->assertCount(1, $model->listing(1, $this->nodeId, false, 'all', null, '纯正文'));
        } finally {
            if ($ids) {
                $db->table('users')->whereIn('id', $ids)->delete();
            }
        }
    }

    public function testPinnedLockedFlagsAndHomeNodeLimit(): void
    {
        $this->actingAs($this->admin);
        $this->post('/admin/topic/' . $this->topicId . '/flags', [
            'is_pinned' => '1',
            'is_locked' => '1',
            csrf_token() => csrf_hash(),
        ])->assertRedirect();
        $model = new \App\Models\ForumModel();
        $this->assertSame($this->topicId, (int) $model->listing(1, $this->nodeId, false)[0]['id']);
        $this->actingAs($this->ordinary);
        $this->get('/topic/' . $this->topicId)->assertDontSee('添加一条新回复');
        $before = db_connect()->table('comments')->where('topic_id', $this->topicId)->countAllResults();
        $this->post('/topic/' . $this->topicId . '/comment', [
            'body' => '<p>锁帖后提交</p>',
            csrf_token() => csrf_hash(),
        ])->assertRedirect();
        $this->assertSame(
            $before,
            db_connect()->table('comments')->where('topic_id', $this->topicId)->countAllResults(),
        );
        service('siteSettings')->save(['home_node_limit' => '0']);
        $this->assertStringNotContainsString('/topic/show/' . $this->nodeSlug, $this->get('/')->getBody());
    }

    public function testLocalAnalyticsOnlyCountsPublicPagesWhenEnabled(): void
    {
        $db = db_connect();
        $count = static fn(): int => (int) ($db->table('daily_page_views')->selectSum('views')->get()->getRow()
            ->views ?? 0);
        $before = $count();
        $this->get('/')->assertStatus(200);
        $this->assertSame($before, $count());
        service('siteSettings')->save(['analytics_enabled' => '1']);
        $this->get('/')->assertStatus(200);
        $this->assertSame($before + 1, $count());
        $this->get('/login')->assertStatus(200);
        $this->assertSame($before + 1, $count());
    }

    public function testDisabledAccountCannotUseEmailRecoveryToken(): void
    {
        $config = config('Auth');
        $original = $config->allowMagicLinkLogins;
        $config->allowMagicLinkLogins = true;
        $token = bin2hex(random_bytes(20));
        try {
            db_connect()
                ->table('auth_identities')
                ->insert([
                    'user_id' => $this->ordinary->id,
                    'type' => 'magic-link',
                    'secret' => $token,
                    'expires' => gmdate('Y-m-d H:i:s', time() + 60),
                ]);
            db_connect()
                ->table('users')
                ->where('id', $this->ordinary->id)
                ->update(['active' => 0]);
            $this->get('/login/verify-magic-link?token=' . $token)->assertRedirectTo('/login');
            $this->assertFalse(auth()->loggedIn());
            $unknown = $this->post('/login/magic-link', [
                'email' => 'notfound@example.test',
                csrf_token() => csrf_hash(),
            ]);
            $disabled = $this->post('/login/magic-link', [
                'email' => $this->ordinary->email,
                csrf_token() => csrf_hash(),
            ]);
            $unknown->assertStatus(200);
            $disabled->assertStatus(200);
            $disabled->assertDontSee('账号已停用');
        } finally {
            $config->allowMagicLinkLogins = $original;
        }
    }

    public function testBrowserFixturesRenderRealEditorAndAdminViews(): void
    {
        // Optional static UI fixtures stay inside the disposable test container.
        // No authentication bypass endpoint is added to the application.
        $this->actingAs($this->admin);
        foreach (
            ['editor' => '/topic/new', 'admin' => '/admin/settings/site', 'reply' => '/topic/' . $this->topicId]
            as $name => $path
        ) {
            $result = $this->get($path);
            $result->assertStatus(200);
            if (getenv('EXPORT_UI_FIXTURES') === '1') {
                $csp = preg_replace(
                    '/(?:frame-ancestors|report-uri)[^;]*(?:;|$)/',
                    '',
                    $result->response()->getHeaderLine('Content-Security-Policy'),
                );
                $html = str_replace(
                    '<head>',
                    '<head><meta http-equiv="Content-Security-Policy" content="' .
                        htmlspecialchars($csp, ENT_QUOTES) .
                        '">',
                    $result->getBody(),
                );
                file_put_contents(FCPATH . '__qa-' . $name . '.html', $html);
            }
        }
    }

    private function createUser(string $prefix, string $group): User
    {
        $provider = auth()->getProvider();
        $suffix = bin2hex(random_bytes(4));
        $user = new User([
            'username' => $prefix . $suffix,
            'email' => $prefix . $suffix . '@example.test',
            'password' => 'RoleTraversal!' . $suffix,
            'active' => 1,
        ]);
        $this->assertTrue($provider->save($user), implode('；', $provider->errors()));
        $created = $provider->findById($provider->getInsertID());
        $created->addGroup($group);
        $now = gmdate('Y-m-d H:i:s');
        db_connect()
            ->table('user_profiles')
            ->insert([
                'user_id' => $created->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

        return $created;
    }
}
