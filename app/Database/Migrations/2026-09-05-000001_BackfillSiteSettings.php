<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class BackfillSiteSettings extends Migration
{
    public function up(): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $defaults = [
            'site_subtitle' => '轻量、简洁的中文社区',
            'site_keywords' => 'LetsBBS,论坛,社区',
            'comment_requires_approval' => '0',
        ];

        foreach ($defaults as $key => $value) {
            $this->db
                ->table('site_settings')
                ->ignore(true)
                ->insert([
                    'setting_key' => $key,
                    'setting_value' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                    'updated_at' => $now,
                ]);
        }
    }

    public function down(): void
    {
        // Settings are user data. A rollback must not remove values that an
        // administrator may have changed after this migration was applied.
    }
}
