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
        $db->table('nodes')->insert([
            'name' => '身份回归节点',
            'slug' => 'role-' . bin2hex(random_bytes(4)),
            'description' => '三身份页面回归',
            'sort_order' => 999,
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
            '/node',
            '/node/' . $this->nodeId,
            '/recent',
            '/search?q=回归',
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
        $this->get('/settings')->assertRedirect();
        $this->get('/admin')->assertRedirect();
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
