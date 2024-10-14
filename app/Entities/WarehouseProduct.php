<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class WarehouseProduct extends Entity
{
    protected $attributes = [
        'id',
        'warehouse_id',
        'product_id',
        'product_count',
        'created_at',
        'updated_at',
    ];

    protected $datamap = [
        'id' => 'id',
        'warehouse_id' => 'warehouse_id',
        'product_id' => 'product_id',
        'product_count' => 'product_count',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
    ];

    protected $dates = ['created_at', 'updated_at'];
}