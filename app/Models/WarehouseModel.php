<?php
namespace App\Models;

use CodeIgniter\Model;

class WarehouseModel extends Model
{
    protected $table = 'warehouse';
    protected $allowedFields = [
        'id', 'name', 'address', 'created_at', 'updated_at'
    ];
    // protected $returnType    = \App\Entities\Warehouse::class;
    protected $returnType = 'array';
    protected $useTimestamps = true;
}
?>
