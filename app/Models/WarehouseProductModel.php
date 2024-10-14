<?php
namespace App\Models;

use CodeIgniter\Model;

class WarehouseProductModel extends Model
{
    protected $table = 'warehouse_product';
    protected $allowedFields = [
        'id', 'warehouse_id', 'product_id', 'product_count', 'created_at', 'updated_at'
    ];
    // protected $returnType    = \App\Entities\WarehouseProduct::class;
    protected $returnType = 'array';
    protected $useTimestamps = true;
}
?>