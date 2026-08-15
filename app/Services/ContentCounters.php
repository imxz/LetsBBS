<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

final class ContentCounters
{
    public function __construct(private BaseConnection $db) {}

    public function adjustVisibleCommentsForTopic(int $topicId, int $delta, string $now): void
    {
        if (!in_array($delta, [-1, 1], true)) {
            throw new \InvalidArgumentException('Counter delta must be -1 or 1.');
        }

        $this->db->query(
            "UPDATE user_profiles p
             JOIN (
                 SELECT user_id, COUNT(*) AS total
                 FROM comments
                 WHERE topic_id=? AND status='published'
                 GROUP BY user_id
             ) c ON c.user_id=p.user_id
             SET p.comment_count=GREATEST(p.comment_count + (? * c.total),0),p.updated_at=?",
            [$topicId, $delta, $now],
        );
    }
}
