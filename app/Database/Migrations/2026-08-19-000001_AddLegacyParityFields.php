<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class AddLegacyParityFields extends Migration
{
    public function up(): void
    {
        $this->db->query(
            "ALTER TABLE nodes
                ADD COLUMN parent_id INT UNSIGNED NULL AFTER id,
                ADD COLUMN keywords VARCHAR(160) NOT NULL DEFAULT '' AFTER description,
                ADD COLUMN featured TINYINT(1) NOT NULL DEFAULT 1 AFTER sort_order,
                ADD COLUMN show_on_home TINYINT(1) NOT NULL DEFAULT 0 AFTER featured,
                ADD KEY idx_nodes_parent_sort (parent_id, sort_order, id),
                ADD CONSTRAINT fk_nodes_parent FOREIGN KEY (parent_id) REFERENCES nodes(id) ON DELETE SET NULL",
        );
        $this->db->query(
            'ALTER TABLE topics ADD COLUMN view_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER follower_count',
        );
        $this->db->query(
            'ALTER TABLE notifications ADD COLUMN comment_id BIGINT UNSIGNED NULL AFTER topic_id, ADD KEY idx_notifications_comment (comment_id), ADD CONSTRAINT fk_notifications_comment FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE SET NULL',
        );
    }

    public function down(): void
    {
        $this->db->query(
            'ALTER TABLE notifications DROP FOREIGN KEY fk_notifications_comment, DROP INDEX idx_notifications_comment, DROP COLUMN comment_id',
        );
        $this->db->query('ALTER TABLE topics DROP COLUMN view_count');
        $this->db->query(
            'ALTER TABLE nodes DROP FOREIGN KEY fk_nodes_parent, DROP INDEX idx_nodes_parent_sort, DROP COLUMN show_on_home, DROP COLUMN featured, DROP COLUMN keywords, DROP COLUMN parent_id',
        );
    }
}
