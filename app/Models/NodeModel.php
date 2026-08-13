<?php

namespace App\Models;

use CodeIgniter\Model;

final class NodeModel extends Model
{
    protected $table = 'nodes';
    protected $returnType = 'array';
    protected $allowedFields = ['name','slug','description','sort_order','is_active','topic_count'];
    protected $useTimestamps = true;
}
