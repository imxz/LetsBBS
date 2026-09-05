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
        $preview->assertSee('管理员预览：待审核主题');
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
                'hidden',
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
