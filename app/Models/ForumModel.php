<?php

namespace App\Models;

use CodeIgniter\Model;

final class ForumModel extends Model
{
    public const PAGE_SIZE = 20;

    protected $table = 'topics';

    protected $returnType = 'array';

    protected $allowedFields = [];

    public function listing(
        int $page,
        ?int $nodeId,
        bool $recent,
        string $filter = 'all',
        ?int $viewerId = null,
        string $search = '',
    ): array {
        $b = $this->db
            ->table('topics t')
            ->select('t.*,n.name node_name,u.username,p.avatar,reply_user.username last_reply_username')
            ->join('nodes n', 'n.id=t.node_id')
            ->join('users u', 'u.id=t.user_id')
            ->join('user_profiles p', 'p.user_id=t.user_id', 'left')
            ->join(
                'comments last_reply',
                "last_reply.id=(SELECT MAX(c.id) FROM comments c WHERE c.topic_id=t.id AND c.status='published')",
                'left',
                false,
            )
            ->join('users reply_user', 'reply_user.id=last_reply.user_id', 'left')
            ->where('t.status', 'published');
        if ($nodeId) {
            $b->where('t.node_id', $nodeId);
        }
        if ($search !== '') {
            $b->groupStart()->like('t.title', $search)->orLike('t.body', $search)->groupEnd();
        }
        if ($viewerId !== null) {
            if ($filter === 'nodes') {
                $b->join('node_follows home_nf', 'home_nf.node_id=t.node_id')->where('home_nf.user_id', $viewerId);
            } elseif ($filter === 'topics') {
                $b->join('topic_follows home_tf', 'home_tf.topic_id=t.id')->where('home_tf.user_id', $viewerId);
            } elseif ($filter === 'users') {
                $b->join('user_follows home_uf', 'home_uf.followed_id=t.user_id')->where(
                    'home_uf.follower_id',
                    $viewerId,
                );
            }
        }

        return $b
            ->orderBy($recent ? 't.created_at' : 't.last_activity_at', 'DESC')
            ->orderBy('t.id', 'DESC')
            ->limit(self::PAGE_SIZE + 1, ($page - 1) * self::PAGE_SIZE)
            ->get()
            ->getResultArray();
    }

    public function nodes(): array
    {
        return $this->db->table('nodes')->where('is_active', 1)->orderBy('sort_order')->get()->getResultArray();
    }

    public function hotTopics(int $limit = 10): array
    {
        return $this->db
            ->table('topics')
            ->select('id,title,comment_count')
            ->where('status', 'published')
            ->where('last_activity_at >=', gmdate('Y-m-d H:i:s', time() - 86400 * 90))
            ->orderBy('comment_count', 'DESC')
            ->orderBy('last_activity_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /** @return array{users: int, topics: int, comments: int} */
    public function statistics(): array
    {
        return [
            'users' => $this->db->table('users')->where('active', 1)->countAllResults(),
            'topics' => $this->db->table('topics')->where('status', 'published')->countAllResults(),
            'comments' => $this->db->table('comments')->where('status', 'published')->countAllResults(),
        ];
    }

    public function viewerSummary(int $userId): ?array
    {
        $viewer = $this->db
            ->table('users u')
            ->select('u.username,p.avatar')
            ->join('user_profiles p', 'p.user_id=u.id', 'left')
            ->where('u.id', $userId)
            ->get()
            ->getRowArray();
        if (!$viewer) {
            return null;
        }

        $viewer['node_follows'] = $this->db->table('node_follows')->where('user_id', $userId)->countAllResults();
        $viewer['topic_follows'] = $this->db->table('topic_follows')->where('user_id', $userId)->countAllResults();
        $viewer['user_follows'] = $this->db->table('user_follows')->where('follower_id', $userId)->countAllResults();
        $viewer['unread_notifications'] = $this->db
            ->table('notifications')
            ->where('user_id', $userId)
            ->where('read_at', null)
            ->countAllResults();

        return $viewer;
    }

    public function topic(int $id): ?array
    {
        return $this->db
            ->table('topics t')
            ->select('t.*,n.name node_name,u.username,p.avatar')
            ->join('nodes n', 'n.id=t.node_id')
            ->join('users u', 'u.id=t.user_id')
            ->join('user_profiles p', 'p.user_id=t.user_id', 'left')
            ->where(['t.id' => $id, 't.status' => 'published'])
            ->get()
            ->getRowArray();
    }

    public function comments(int $topicId): array
    {
        return $this->db
            ->table('comments c')
            ->select('c.*,u.username,p.avatar')
            ->join('users u', 'u.id=c.user_id')
            ->join('user_profiles p', 'p.user_id=c.user_id', 'left')
            ->where(['c.topic_id' => $topicId, 'c.status' => 'published'])
            ->orderBy('c.id')
            ->get()
            ->getResultArray();
    }

    public function follows(string $table, array $where): bool
    {
        return (bool) $this->db->table($table)->where($where)->countAllResults();
    }
}
