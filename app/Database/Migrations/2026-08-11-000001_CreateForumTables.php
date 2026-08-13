<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateForumTables extends Migration
{
    public function up(): void
    {
        if ($this->db->DBDriver !== 'MySQLi') {
            throw new \RuntimeException('LetsBBS only supports MySQL 8.4 with the MySQLi driver.');
        }
        foreach ($this->statements() as $sql) {
            $this->db->query($sql);
        }
    }

    public function down(): void
    {
        $this->db->disableForeignKeyChecks();
        foreach (['topic_follows', 'user_follows', 'node_follows', 'notifications', 'comments', 'topics', 'nodes', 'user_profiles', 'site_settings'] as $table) {
            $this->forge->dropTable($table, true);
        }
        $this->db->enableForeignKeyChecks();
    }

    /** @return list<string> */
    private function statements(): array
    {
        $suffix = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci';
        return [
            "CREATE TABLE user_profiles (user_id INT UNSIGNED NOT NULL, bio VARCHAR(500) NOT NULL DEFAULT '', avatar VARCHAR(255) NULL, is_muted TINYINT(1) NOT NULL DEFAULT 0, topic_count INT UNSIGNED NOT NULL DEFAULT 0, comment_count INT UNSIGNED NOT NULL DEFAULT 0, follower_count INT UNSIGNED NOT NULL DEFAULT 0, following_count INT UNSIGNED NOT NULL DEFAULT 0, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (user_id), CONSTRAINT fk_profiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE) $suffix",
            "CREATE TABLE nodes (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(80) NOT NULL, slug VARCHAR(80) NOT NULL, description VARCHAR(255) NOT NULL DEFAULT '', sort_order INT NOT NULL DEFAULT 0, is_active TINYINT(1) NOT NULL DEFAULT 1, topic_count INT UNSIGNED NOT NULL DEFAULT 0, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE KEY uq_nodes_slug (slug), KEY idx_nodes_active_sort (is_active, sort_order, id)) $suffix",
            "CREATE TABLE topics (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, node_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, title VARCHAR(160) NOT NULL, body MEDIUMTEXT NOT NULL, status ENUM('published','pending','hidden','deleted') NOT NULL DEFAULT 'published', comment_count INT UNSIGNED NOT NULL DEFAULT 0, follower_count INT UNSIGNED NOT NULL DEFAULT 0, last_activity_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, CONSTRAINT fk_topics_node FOREIGN KEY (node_id) REFERENCES nodes(id) ON DELETE RESTRICT, CONSTRAINT fk_topics_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT, KEY idx_topics_list (status, last_activity_at DESC, id DESC), KEY idx_topics_node (node_id, status, last_activity_at DESC), KEY idx_topics_user (user_id, status, created_at DESC)) $suffix",
            "CREATE TABLE comments (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, topic_id BIGINT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, body MEDIUMTEXT NOT NULL, status ENUM('published','hidden','deleted') NOT NULL DEFAULT 'published', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, CONSTRAINT fk_comments_topic FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE, CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT, KEY idx_comments_topic (topic_id, status, id), KEY idx_comments_user (user_id, status, created_at DESC)) $suffix",
            "CREATE TABLE notifications (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id INT UNSIGNED NOT NULL, actor_id INT UNSIGNED NULL, topic_id BIGINT UNSIGNED NULL, kind VARCHAR(32) NOT NULL, payload JSON NULL, read_at DATETIME NULL, created_at DATETIME NOT NULL, CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, CONSTRAINT fk_notifications_actor FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL, CONSTRAINT fk_notifications_topic FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE, KEY idx_notifications_inbox (user_id, read_at, id DESC)) $suffix",
            "CREATE TABLE node_follows (user_id INT UNSIGNED NOT NULL, node_id INT UNSIGNED NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (user_id, node_id), CONSTRAINT fk_nf_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, CONSTRAINT fk_nf_node FOREIGN KEY (node_id) REFERENCES nodes(id) ON DELETE CASCADE, KEY idx_nf_node (node_id, user_id)) $suffix",
            "CREATE TABLE user_follows (follower_id INT UNSIGNED NOT NULL, followed_id INT UNSIGNED NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (follower_id, followed_id), CONSTRAINT fk_uf_follower FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE, CONSTRAINT fk_uf_followed FOREIGN KEY (followed_id) REFERENCES users(id) ON DELETE CASCADE, KEY idx_uf_followed (followed_id, follower_id), CONSTRAINT chk_no_self_follow CHECK (follower_id <> followed_id)) $suffix",
            "CREATE TABLE topic_follows (user_id INT UNSIGNED NOT NULL, topic_id BIGINT UNSIGNED NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (user_id, topic_id), CONSTRAINT fk_tf_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, CONSTRAINT fk_tf_topic FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE, KEY idx_tf_topic (topic_id, user_id)) $suffix",
            "CREATE TABLE site_settings (setting_key VARCHAR(80) PRIMARY KEY, setting_value JSON NOT NULL, updated_at DATETIME NOT NULL) $suffix",
        ];
    }
}
