<?php

namespace App\Models;

use CodeIgniter\Model;

final class CommentModel extends Model
{
    protected $table = 'comments';
    protected $returnType = 'array';
    protected $allowedFields = ['topic_id','user_id','body','status'];
    protected $useTimestamps = true;
}
