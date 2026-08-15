<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use RuntimeException;

final class FollowService
{
    public function __construct(private ?BaseConnection $db = null)
    {
        $this->db ??= db_connect();
    }

    public function toggleTopic(int $userId, int $topicId): bool
    {
        if (
            !$this->db
                ->table('topics')
                ->where(['id' => $topicId, 'status' => 'published'])
                ->countAllResults()
        ) {
            throw new RuntimeException('主题不存在或不可关注。');
        }
        return $this->toggle(
            'topic_follows',
            ['user_id' => $userId, 'topic_id' => $topicId],
            'UPDATE topics SET follower_count=GREATEST(follower_count+?,0) WHERE id=?',
            $topicId,
        );
    }

    public function toggleNode(int $userId, int $nodeId): bool
    {
        if (
            !$this->db
                ->table('nodes')
                ->where(['id' => $nodeId, 'is_active' => 1])
                ->countAllResults()
        ) {
            throw new RuntimeException('节点不存在或不可关注。');
        }
        return $this->toggle('node_follows', ['user_id' => $userId, 'node_id' => $nodeId], null, $nodeId);
    }

    public function toggleUser(int $userId, int $targetId): bool
    {
        if ($userId === $targetId) {
            throw new RuntimeException('不能关注自己。');
        }
        return $this->transaction(function () use ($userId, $targetId): bool {
            $key = ['follower_id' => $userId, 'followed_id' => $targetId];
            $active = $this->insertOrDelete('user_follows', $key);
            $delta = $active ? 1 : -1;
            $this->db->query('UPDATE user_profiles SET following_count=GREATEST(following_count+?,0) WHERE user_id=?', [
                $delta,
                $userId,
            ]);
            $this->db->query('UPDATE user_profiles SET follower_count=GREATEST(follower_count+?,0) WHERE user_id=?', [
                $delta,
                $targetId,
            ]);
            if ($active) {
                $this->db->table('notifications')->insert([
                    'user_id' => $targetId,
                    'actor_id' => $userId,
                    'topic_id' => null,
                    'kind' => 'follow',
                    'created_at' => gmdate('Y-m-d H:i:s'),
                ]);
            }
            return $active;
        });
    }

    private function toggle(string $table, array $key, ?string $counterSql, int $target): bool
    {
        return $this->transaction(function () use ($table, $key, $counterSql, $target): bool {
            $active = $this->insertOrDelete($table, $key);
            if ($counterSql) {
                $this->db->query($counterSql, [$active ? 1 : -1, $target]);
            }
            return $active;
        });
    }

    private function insertOrDelete(string $table, array $key): bool
    {
        $this->db
            ->table($table)
            ->ignore(true)
            ->insert($key + ['created_at' => gmdate('Y-m-d H:i:s')]);
        if ($this->db->affectedRows() === 1) {
            return true;
        }
        $this->db->table($table)->where($key)->delete();
        return false;
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
