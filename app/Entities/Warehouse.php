<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Warehouse extends Entity
{
    protected $attributes = [
        'id',
        'name',
        'address',
        'created_at',
        'updated_at',
    ];

    protected $datamap = [
        'id' => 'id',
        'name' => 'name',
        'address' => 'address',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
    ];

    protected $dates = ['created_at', 'updated_at'];
}