<?php

namespace App\Services;

final class ContentPolicy
{
    public static function bodyLike(\CodeIgniter\Database\BaseConnection $db, string $column, string $query): string
    {
        if (!in_array($column, ['t.body', 'c.body'], true)) {
            throw new \InvalidArgumentException('Unsupported content column.');
        }
        return "REGEXP_REPLACE({$column}, '<[^>]*>', ' ') LIKE " .
            $db->escape('%' . $db->escapeLikeString($query) . '%') .
            " ESCAPE '!'";
    }

    public static function nodeSql(string $alias = 'n'): string
    {
        return "{$alias}.is_active=1 AND ({$alias}.parent_id IS NULL OR EXISTS (SELECT 1 FROM nodes visible_parent WHERE visible_parent.id={$alias}.parent_id AND visible_parent.is_active=1))";
    }

    public static function topicSql(string $alias = 't'): string
    {
        return "{$alias}.status='published' AND EXISTS (SELECT 1 FROM nodes visible_node WHERE visible_node.id={$alias}.node_id AND " .
            self::nodeSql('visible_node') .
            ')';
    }

    public static function assertPostable(
        \CodeIgniter\Database\BaseConnection $db,
        int $nodeId,
        bool $newTopic = true,
    ): void {
        $node = $db->query('SELECT * FROM nodes WHERE id=? FOR UPDATE', [$nodeId])->getRowArray();
        $parent =
            $node && $node['parent_id']
                ? $db->query('SELECT * FROM nodes WHERE id=? FOR UPDATE', [$node['parent_id']])->getRowArray()
                : null;
        if (
            !$node ||
            !$node['is_active'] ||
            !$node['accepts_posts'] ||
            ($node['parent_id'] && (!$parent || !$parent['is_active'] || !$parent['accepts_posts']))
        ) {
            throw new \RuntimeException('此节点已隐藏或停止接收新内容。');
        }
        if (
            $newTopic &&
            $db
                ->table('nodes')
                ->where(['parent_id' => $nodeId, 'is_active' => 1])
                ->countAllResults()
        ) {
            throw new \RuntimeException('请选择具体的子节点发表主题。');
        }
    }
}
