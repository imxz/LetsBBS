<?php

namespace App\Models;

use CodeIgniter\Model;

final class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $returnType = 'array';
    protected $allowedFields = ['user_id','actor_id','topic_id','kind','payload','read_at','created_at'];
    protected $useTimestamps = false;
}
