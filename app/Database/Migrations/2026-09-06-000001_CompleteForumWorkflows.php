<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CompleteForumWorkflows extends Migration
{
    public function up(): void
    {
        foreach (['topics', 'comments'] as $table) {
            $this->db->query(
                "ALTER TABLE {$table} MODIFY status ENUM('published','pending','hidden','deleted') NOT NULL DEFAULT 'published', ADD moderation_reason VARCHAR(500) NOT NULL DEFAULT ''",
            );
        }
        $this->db->query('ALTER TABLE nodes ADD accepts_posts TINYINT(1) NOT NULL DEFAULT 1');
        $this->db->query(
            'ALTER TABLE topics ADD is_pinned TINYINT(1) NOT NULL DEFAULT 0, ADD is_locked TINYINT(1) NOT NULL DEFAULT 0',
        );
        $this->db->query(
            'ALTER TABLE user_profiles ADD notify_topics TINYINT(1) NOT NULL DEFAULT 1, ADD notify_comments TINYINT(1) NOT NULL DEFAULT 1, ADD notify_mentions TINYINT(1) NOT NULL DEFAULT 1, ADD notify_follows TINYINT(1) NOT NULL DEFAULT 1',
        );
        $this->db->query(
            "CREATE TABLE moderation_logs (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, actor_id INT UNSIGNED NULL, resource_type VARCHAR(20) NOT NULL, resource_id BIGINT UNSIGNED NOT NULL, action VARCHAR(40) NOT NULL, reason VARCHAR(500) NOT NULL DEFAULT '', details JSON NULL, created_at DATETIME NOT NULL, KEY idx_moderation_created (id), CONSTRAINT fk_moderation_actor FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",
        );
        $this->db->query(
            "CREATE TABLE reports (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id INT UNSIGNED NOT NULL, topic_id BIGINT UNSIGNED NOT NULL, comment_id BIGINT UNSIGNED NULL, reason VARCHAR(500) NOT NULL, status ENUM('open','resolved') NOT NULL DEFAULT 'open', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, KEY idx_reports_status (status,id), CONSTRAINT fk_report_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, CONSTRAINT fk_report_topic FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE, CONSTRAINT fk_report_comment FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",
        );
        $defaults = [
            'registration_enabled' => '1',
            'site_timezone' => 'Asia/Shanghai',
            'home_node_limit' => '10',
            'footer_text' => '',
            'site_version' => '1.0',
            'analytics_enabled' => '0',
        ];
        $this->db->query(
            'CREATE TABLE daily_page_views (day DATE PRIMARY KEY, views BIGINT UNSIGNED NOT NULL DEFAULT 0) ENGINE=InnoDB',
        );
        foreach ($defaults as $key => $value) {
            $this->db
                ->table('site_settings')
                ->ignore(true)
                ->insert([
                    'setting_key' => $key,
                    'setting_value' => json_encode($value),
                    'updated_at' => gmdate('Y-m-d H:i:s'),
                ]);
        }
    }

    public function down(): void
    {
        // Operational state and moderation history must not be silently discarded.
        throw new \RuntimeException('此迁移包含审核记录，请通过备份恢复，不支持自动回退。');
    }
}
