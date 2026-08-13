<?php

namespace App\Models;

use CodeIgniter\Model;

final class ProfileModel extends Model
{
    protected $table = 'user_profiles';
    protected $primaryKey = 'user_id';
    protected $returnType = 'array';
    protected $allowedFields = ['user_id','bio','avatar','is_muted','topic_count','comment_count','follower_count','following_count'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
