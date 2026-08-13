<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use RuntimeException;

final class TopicService
{
    public function __construct(private ?BaseConnection $db = null, private ?HtmlSanitizer $sanitizer = null)
    {
        $this->db ??= db_connect();
        $this->sanitizer ??= new HtmlSanitizer();
    }

    public function create(int $userId, int $nodeId, string $title, string $html): int
    {
        $body = $this->sanitizer->clean($html);
        if ($body === '') {
            throw new RuntimeException('正文不能为空。');
        }$now = gmdate('Y-m-d H:i:s');
        return $this->transaction(function () use ($userId, $nodeId, $title, $body, $now): int {
            $this->db->table('topics')->insert(['node_id' => $nodeId,'user_id' => $userId,'title' => trim($title),'body' => $body,'status' => 'published','last_activity_at' => $now,'created_at' => $now,'updated_at' => $now]);
            $id = (int) $this->db->insertID();
            $this->db->query('UPDATE nodes SET topic_count=topic_count+1,updated_at=? WHERE id=?', [$now,$nodeId]);
            $this->db->query('UPDATE user_profiles SET topic_count=topic_count+1,updated_at=? WHERE user_id=?', [$now,$userId]);
            $this->db->table('topic_follows')->insert(['user_id' => $userId,'topic_id' => $id,'created_at' => $now]);
            $this->db->query('UPDATE topics SET follower_count=1 WHERE id=?', [$id]);
            $recipients = $this->db->query('SELECT user_id FROM node_follows WHERE node_id=? AND user_id<>? UNION SELECT follower_id AS user_id FROM user_follows WHERE followed_id=? AND follower_id<>?', [$nodeId,$userId,$userId,$userId])->getResultArray();
            foreach ($recipients as $r) {
                $this->db->table('notifications')->insert(['user_id' => $r['user_id'],'actor_id' => $userId,'topic_id' => $id,'kind' => 'topic','payload' => json_encode([]),'created_at' => $now]);
            }return $id;
        });
    }

    public function comment(int $userId, int $topicId, string $html): int
    {
        $body = $this->sanitizer->clean($html);
        if ($body === '') {
            throw new RuntimeException('回复不能为空。');
        }$now = gmdate('Y-m-d H:i:s');
        return $this->transaction(function () use ($userId, $topicId, $body, $now): int {
            $topic = $this->db->query('SELECT user_id FROM topics WHERE id=? AND status=? FOR UPDATE', [$topicId,'published'])->getRowArray();
            if (!$topic) {
                throw new RuntimeException('主题不存在或不可回复。');
            }
            $this->db->table('comments')->insert(['topic_id' => $topicId,'user_id' => $userId,'body' => $body,'status' => 'published','created_at' => $now,'updated_at' => $now]);
            $id = (int) $this->db->insertID();
            $this->db->query('UPDATE topics SET comment_count=comment_count+1,last_activity_at=?,updated_at=? WHERE id=?', [$now,$now,$topicId]);
            $this->db->query('UPDATE user_profiles SET comment_count=comment_count+1,updated_at=? WHERE user_id=?', [$now,$userId]);
            $recipients = $this->db->query('SELECT user_id FROM topic_follows WHERE topic_id=? AND user_id<>?', [$topicId,$userId])->getResultArray();
            foreach ($recipients as $r) {
                $this->db->table('notifications')->insert(['user_id' => $r['user_id'],'actor_id' => $userId,'topic_id' => $topicId,'kind' => 'comment','payload' => json_encode([]),'created_at' => $now]);
            }return $id;
        });
    }

    public function delete(int $actorId, int $topicId, bool $admin): void
    {
        $this->transaction(function () use ($actorId, $topicId, $admin): void {
            $topic = $this->db->query('SELECT node_id,user_id,status FROM topics WHERE id=? FOR UPDATE', [$topicId])->getRowArray();
            if (!$topic || (!$admin && (int) $topic['user_id'] !== $actorId)) {
                throw new RuntimeException('无权删除该主题。');
            }if ($topic['status'] === 'published') {
                $this->db->table('topics')->where('id', $topicId)->update(['status' => 'deleted','updated_at' => gmdate('Y-m-d H:i:s')]);
                $this->db->query('UPDATE nodes SET topic_count=GREATEST(topic_count-1,0) WHERE id=?', [$topic['node_id']]);
                $this->db->query('UPDATE user_profiles SET topic_count=GREATEST(topic_count-1,0) WHERE user_id=?', [$topic['user_id']]);
            }
        });
    }

    private function transaction(callable $work): mixed
    {
        $this->db->transException(true)->transBegin();
        try {
            $result = $work();
            $this->db->transCommit();
            return $result;
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }
}
