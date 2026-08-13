<?php

namespace App\Models;

use CodeIgniter\Model;

final class TopicModel extends Model
{
    protected $table = 'topics';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['node_id','user_id','title','body','status','comment_count','follower_count','last_activity_at'];
    protected $useTimestamps = true;
}
