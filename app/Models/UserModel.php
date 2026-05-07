<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $useTimestamps = true;
    protected $returnType    = 'array';
}