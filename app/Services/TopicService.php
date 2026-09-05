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

    public function create(int $userId, int $nodeId, string $title, string $html, bool $bypassApproval = false): int
    {
        $body = $this->sanitizer->clean($html);
        if ($body === '') {
            throw new RuntimeException('正文不能为空。');
        }
        $title = trim($title);
        if (mb_strlen($title) < 2 || mb_strlen($title) > 160) {
            throw new RuntimeException('标题须为 2–160 个字符。');
        }
        $status =
            !$bypassApproval && service('siteSettings')->get('topic_requires_approval', '0') === '1'
                ? 'hidden'
                : 'published';
        $now = gmdate('Y-m-d H:i:s');
        return $this->transaction(function () use ($userId, $nodeId, $title, $body, $status, $now): int {
            $node = $this->db
                ->query('SELECT id FROM nodes WHERE id=? AND is_active=1 FOR UPDATE', [$nodeId])
                ->getRowArray();
            if (!$node) {
                throw new RuntimeException('节点不存在或已停用。');
            }
            $this->db->table('topics')->insert([
                'node_id' => $nodeId,
                'user_id' => $userId,
                'title' => $title,
                'body' => $body,
                'status' => $status,
                'last_activity_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $id = (int) $this->db->insertID();
            if ($status === 'published') {
                $this->db->query('UPDATE nodes SET topic_count=topic_count+1,updated_at=? WHERE id=?', [$now, $nodeId]);
                $this->db->query('UPDATE user_profiles SET topic_count=topic_count+1,updated_at=? WHERE user_id=?', [
                    $now,
                    $userId,
                ]);
                $this->db->table('topic_follows')->insert([
                    'user_id' => $userId,
                    'topic_id' => $id,
                    'created_at' => $now,
                ]);
                $this->db->query('UPDATE topics SET follower_count=1 WHERE id=?', [$id]);
                $recipients = $this->db
                    ->query(
                        'SELECT user_id FROM node_follows WHERE node_id=? AND user_id<>? UNION SELECT follower_id AS user_id FROM user_follows WHERE followed_id=? AND follower_id<>?',
                        [$nodeId, $userId, $userId, $userId],
                    )
                    ->getResultArray();
                foreach ($recipients as $r) {
                    $this->db->table('notifications')->insert([
                        'user_id' => $r['user_id'],
                        'actor_id' => $userId,
                        'topic_id' => $id,
                        'kind' => 'topic',
                        'created_at' => $now,
                    ]);
                }
            }
            return $id;
        });
    }

    public function comment(int $userId, int $topicId, string $html, bool $bypassApproval = false): int
    {
        $body = $this->sanitizer->clean($html);
        if ($body === '') {
            throw new RuntimeException('回复不能为空。');
        }
        $status =
            !$bypassApproval && service('siteSettings')->get('comment_requires_approval', '0') === '1'
                ? 'hidden'
                : 'published';
        $now = gmdate('Y-m-d H:i:s');
        return $this->transaction(function () use ($userId, $topicId, $body, $status, $now): int {
            $topic = $this->db
                ->query('SELECT user_id FROM topics WHERE id=? AND status=? FOR UPDATE', [$topicId, 'published'])
                ->getRowArray();
            if (!$topic) {
                throw new RuntimeException('主题不存在或不可回复。');
            }
            $this->db->table('comments')->insert([
                'topic_id' => $topicId,
                'user_id' => $userId,
                'body' => $body,
                'status' => $status,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $id = (int) $this->db->insertID();
            if ($status === 'published') {
                $this->db->query(
                    'UPDATE topics SET comment_count=comment_count+1,last_activity_at=?,updated_at=? WHERE id=?',
                    [$now, $now, $topicId],
                );
                $this->db->query(
                    'UPDATE user_profiles SET comment_count=comment_count+1,updated_at=? WHERE user_id=?',
                    [$now, $userId],
                );
                $this->notifyCommentRecipients($userId, $topicId, $id, $body, $now);
            }
            return $id;
        });
    }

    public function notifyPublishedComment(int $commentId): void
    {
        $comment = $this->db
            ->table('comments c')
            ->select('c.id,c.topic_id,c.user_id,c.body')
            ->join('topics t', 't.id=c.topic_id')
            ->where(['c.id' => $commentId, 'c.status' => 'published', 't.status' => 'published'])
            ->get()
            ->getRowArray();
        if (!$comment) {
            return;
        }

        $this->notifyCommentRecipients(
            (int) $comment['user_id'],
            (int) $comment['topic_id'],
            (int) $comment['id'],
            (string) $comment['body'],
            gmdate('Y-m-d H:i:s'),
        );
    }

    private function notifyCommentRecipients(
        int $userId,
        int $topicId,
        int $commentId,
        string $body,
        string $createdAt,
    ): void {
        $mentioned = [];
        $usernames = $this->extractMentions($body);
        if ($usernames !== []) {
            $members = $this->db
                ->table('users')
                ->select('id,username')
                ->where('active', 1)
                ->whereIn('username', $usernames)
                ->get()
                ->getResultArray();
            foreach ($members as $member) {
                $recipientId = (int) $member['id'];
                if ($recipientId === $userId) {
                    continue;
                }
                $mentioned[$recipientId] = true;
                $this->insertNotificationOnce([
                    'user_id' => $recipientId,
                    'actor_id' => $userId,
                    'topic_id' => $topicId,
                    'comment_id' => $commentId,
                    'kind' => 'mention',
                    'created_at' => $createdAt,
                ]);
            }
        }

        $followers = $this->db
            ->query('SELECT user_id FROM topic_follows WHERE topic_id=? UNION SELECT user_id FROM topics WHERE id=?', [
                $topicId,
                $topicId,
            ])
            ->getResultArray();
        foreach ($followers as $follower) {
            $recipientId = (int) $follower['user_id'];
            if ($recipientId === $userId || isset($mentioned[$recipientId])) {
                continue;
            }
            $this->insertNotificationOnce([
                'user_id' => $recipientId,
                'actor_id' => $userId,
                'topic_id' => $topicId,
                'comment_id' => $commentId,
                'kind' => 'comment',
                'created_at' => $createdAt,
            ]);
        }
    }

    /** @return list<string> */
    private function extractMentions(string $html): array
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        preg_match_all('/(?<![a-z0-9_.-])@([a-z0-9_.-]{3,30})/iu', $text, $matches);
        $usernames = array_map(static fn(string $username): string => strtolower($username), $matches[1] ?? []);

        return array_values(array_unique($usernames));
    }

    /** @param array<string, int|string> $notification */
    private function insertNotificationOnce(array $notification): void
    {
        $identity = array_intersect_key(
            $notification,
            array_flip(['user_id', 'actor_id', 'topic_id', 'comment_id', 'kind']),
        );
        if (!$this->db->table('notifications')->where($identity)->countAllResults()) {
            $this->db->table('notifications')->insert($notification);
        }
    }

    public function delete(int $actorId, int $topicId, bool $admin): void
    {
        $this->transaction(function () use ($actorId, $topicId, $admin): void {
            $topic = $this->db
                ->query('SELECT node_id,user_id,status FROM topics WHERE id=? FOR UPDATE', [$topicId])
                ->getRowArray();
            if (!$topic || (!$admin && (int) $topic['user_id'] !== $actorId)) {
                throw new RuntimeException('无权删除该主题。');
            }
            if ($topic['status'] !== 'deleted') {
                $now = gmdate('Y-m-d H:i:s');
                $this->db
                    ->table('topics')
                    ->where('id', $topicId)
                    ->update(['status' => 'deleted', 'updated_at' => $now]);
            }
            if ($topic['status'] === 'published') {
                $this->db->query('UPDATE nodes SET topic_count=GREATEST(topic_count-1,0) WHERE id=?', [
                    $topic['node_id'],
                ]);
                $this->db->query('UPDATE user_profiles SET topic_count=GREATEST(topic_count-1,0) WHERE user_id=?', [
                    $topic['user_id'],
                ]);
                new ContentCounters($this->db)->adjustVisibleCommentsForTopic($topicId, -1, $now);
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
