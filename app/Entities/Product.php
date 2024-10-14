<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Product extends Entity
{
    protected $attributes = [
        'id',
        'name',
        'metadata',
        'created_at',
        'updated_at',
    ];

    protected $datamap = [
        'id' => 'id',
        'name' => 'name',
        'metadata' => 'metadata',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
    ];

    protected $dates = ['created_at', 'updated_at'];
}