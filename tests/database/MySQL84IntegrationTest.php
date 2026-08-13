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
        $tables = ['user_profiles','nodes','topics','comments','notifications','node_follows','user_follows','topic_follows','site_settings'];
        foreach ($tables as $table) {
            $row = $db->query('SELECT ENGINE,TABLE_COLLATION FROM information_schema.tables WHERE table_schema=? AND table_name=?', [$db->database,$table])->getRowArray();
            $this->assertSame('InnoDB', $row['ENGINE']);
            $this->assertSame('utf8mb4_0900_ai_ci', $row['TABLE_COLLATION']);
        }
        $unique = $db->query("SELECT COUNT(*) n FROM information_schema.statistics WHERE table_schema=? AND table_name='topic_follows' AND index_name='PRIMARY'", [$db->database])->getRow()->n;
        $this->assertGreaterThanOrEqual(2, (int) $unique);
        $fks = $db->query('SELECT COUNT(*) n FROM information_schema.referential_constraints WHERE constraint_schema=?', [$db->database])->getRow()->n;
        $this->assertGreaterThanOrEqual(12, (int) $fks);
    }

    public function testDuplicateFollowIsRejected(): void
    {
        $db = db_connect();
        $now = gmdate('Y-m-d H:i:s');
        $db->transBegin();
        try {
            $username = 'test' . bin2hex(random_bytes(4));
            $db->table('users')->insert(['username' => $username,'active' => 1,'created_at' => $now,'updated_at' => $now]);
            $userId = (int) $db->insertID();
            $db->table('user_profiles')->insert(['user_id' => $userId,'created_at' => $now,'updated_at' => $now]);
            $nodeId = (int) $db->table('nodes')->select('id')->get()->getRow()->id;
            $db->table('topics')->insert(['node_id' => $nodeId,'user_id' => $userId,'title' => 'constraint test','body' => '<p>test</p>','status' => 'published','last_activity_at' => $now,'created_at' => $now,'updated_at' => $now]);
            $topicId = (int) $db->insertID();
            $row = ['user_id' => $userId,'topic_id' => $topicId,'created_at' => $now];
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
            foreach (['author','reader'] as $prefix) {
                $db->table('users')->insert(['username' => $prefix . bin2hex(random_bytes(4)),'active' => 1,'created_at' => $now,'updated_at' => $now]);
                $id = (int) $db->insertID();
                $ids[] = $id;
                $db->table('user_profiles')->insert(['user_id' => $id,'created_at' => $now,'updated_at' => $now]);
            }
            $nodeId = (int) $db->table('nodes')->select('id')->get()->getRow()->id;
            $topicId = (new \App\Services\TopicService())->create($ids[0], $nodeId, 'integration ' . bin2hex(random_bytes(3)), '<p>Hello<script>alert(1)</script></p>');
            $topic = $db->table('topics')->where('id', $topicId)->get()->getRowArray();
            $this->assertSame(1, (int) $topic['follower_count']);
            $this->assertStringNotContainsString('<script', $topic['body']);
            $follow = new \App\Services\FollowService();
            $this->assertTrue($follow->toggleTopic($ids[1], $topicId));
            $this->assertFalse($follow->toggleTopic($ids[1], $topicId));
            $this->assertSame(1, (int) $db->table('topics')->select('follower_count')->where('id', $topicId)->get()->getRow()->follower_count);
            (new \App\Services\TopicService())->comment($ids[1], $topicId, '<p>Reply</p>');
            $this->assertSame(1, (int) $db->table('topics')->select('comment_count')->where('id', $topicId)->get()->getRow()->comment_count);
            $this->assertSame(1, (int) $db->table('user_profiles')->select('comment_count')->where('user_id', $ids[1])->get()->getRow()->comment_count);
            $this->assertSame(1, $db->table('notifications')->where(['user_id' => $ids[0],'topic_id' => $topicId,'kind' => 'comment'])->countAllResults());
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
        $db->table('users')->insert(['username' => $name,'active' => 1,'created_at' => $now,'updated_at' => $now]);
        $userId = (int) $db->insertID();
        $db->table('user_profiles')->insert(['user_id' => $userId,'created_at' => $now,'updated_at' => $now]);
        try {
            try {
                (new \App\Services\TopicService())->create($userId, 4294967295, $title, '<p>body</p>');
                $this->fail('Foreign-key failure was not raised.');
            } catch (\Throwable) {
                $this->addToAssertionCount(1);
            }$this->assertSame(0, $db->table('topics')->where('title', $title)->countAllResults());
            $this->assertSame(0, (int) $db->table('user_profiles')->select('topic_count')->where('user_id', $userId)->get()->getRow()->topic_count);
        } finally {
            $db->table('users')->where('id', $userId)->delete();
        }
    }
}
