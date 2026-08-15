<?php

namespace App\Models;

use CodeIgniter\Model;

final class ForumModel extends Model
{
    public const PAGE_SIZE = 20;

    protected $table = 'topics';

    protected $returnType = 'array';

    protected $allowedFields = [];

    public function listing(int $page, ?int $nodeId, bool $recent): array
    {
        $b = $this->db
            ->table('topics t')
            ->select('t.*,n.name node_name,u.username,p.avatar')
            ->join('nodes n', 'n.id=t.node_id')
            ->join('users u', 'u.id=t.user_id')
            ->join('user_profiles p', 'p.user_id=t.user_id', 'left')
            ->where('t.status', 'published');
        if ($nodeId) {
            $b->where('t.node_id', $nodeId);
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
