<?php

namespace App\Services;

final class ModerationLog
{
    public static function record(string $type, int $id, string $action, string $reason = '', array $details = []): void
    {
        db_connect()
            ->table('moderation_logs')
            ->insert([
                'actor_id' => auth()->id(),
                'resource_type' => $type,
                'resource_id' => $id,
                'action' => $action,
                'reason' => mb_substr($reason, 0, 500),
                'details' => json_encode($details, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                'created_at' => gmdate('Y-m-d H:i:s'),
            ]);
    }
}
