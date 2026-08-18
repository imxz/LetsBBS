<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class AddMemberProfileFields extends Migration
{
    public function up(): void
    {
        $this->db->query(
            "ALTER TABLE user_profiles
                ADD COLUMN qq VARCHAR(20) NOT NULL DEFAULT '' AFTER avatar,
                ADD COLUMN location VARCHAR(120) NOT NULL DEFAULT '' AFTER qq,
                ADD COLUMN homepage VARCHAR(255) NOT NULL DEFAULT '' AFTER location,
                ADD COLUMN signature VARCHAR(160) NOT NULL DEFAULT '' AFTER homepage",
        );
    }

    public function down(): void
    {
        $this->db->query(
            'ALTER TABLE user_profiles DROP COLUMN signature, DROP COLUMN homepage, DROP COLUMN location, DROP COLUMN qq',
        );
    }
}
