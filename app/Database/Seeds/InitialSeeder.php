<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

final class InitialSeeder extends Seeder
{
    public function run(): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $this->db
            ->table('nodes')
            ->ignore(true)
            ->insertBatch([
                [
                    'name' => '讨论',
                    'slug' => 'discussion',
                    'description' => '自由讨论区',
                    'sort_order' => 10,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => '问答',
                    'slug' => 'questions',
                    'description' => '提问与互助',
                    'sort_order' => 20,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        $this->db
            ->table('site_settings')
            ->ignore(true)
            ->insertBatch([
                [
                    'setting_key' => 'site_name',
                    'setting_value' => json_encode('LetsBBS', JSON_UNESCAPED_UNICODE),
                    'updated_at' => $now,
                ],
                [
                    'setting_key' => 'site_description',
                    'setting_value' => json_encode('简洁的中文论坛', JSON_UNESCAPED_UNICODE),
                    'updated_at' => $now,
                ],
                [
                    'setting_key' => 'home_welcome_message',
                    'setting_value' => json_encode('欢迎访问 LetsBBS', JSON_UNESCAPED_UNICODE),
                    'updated_at' => $now,
                ],
                [
                    'setting_key' => 'home_introduction',
                    'setting_value' => json_encode('这是一个简洁、安全的中文社区。', JSON_UNESCAPED_UNICODE),
                    'updated_at' => $now,
                ],
                [
                    'setting_key' => 'site_subtitle',
                    'setting_value' => json_encode('轻量、简洁的中文社区', JSON_UNESCAPED_UNICODE),
                    'updated_at' => $now,
                ],
                [
                    'setting_key' => 'site_keywords',
                    'setting_value' => json_encode('LetsBBS,论坛,社区', JSON_UNESCAPED_UNICODE),
                    'updated_at' => $now,
                ],
                [
                    'setting_key' => 'topic_requires_approval',
                    'setting_value' => json_encode('0', JSON_UNESCAPED_UNICODE),
                    'updated_at' => $now,
                ],
            ]);
    }
}
