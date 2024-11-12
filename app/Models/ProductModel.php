<?php
namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'product';
    protected $allowedFields = [
        'id', 'name', 'metadata', 'category_id', 'created_at', 'updated_at'
    ];
    // protected $returnType    = \App\Entities\Product::class;
    protected $returnType = 'array';
    protected $useTimestamps = true;
}