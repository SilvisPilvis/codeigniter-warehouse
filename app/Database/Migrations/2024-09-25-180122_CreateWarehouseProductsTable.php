<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWarehouseProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'warehouse_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'product_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'product_count' => [
                'type'           => 'INT',
                'unsigned'       => true,
            ],
            'created_at' => [
                'type' => 'timestamp',
                'null' => true,
                'default' => 'NOW()',
            ],
            'updated_at' => [
                'type' => 'timestamp',
                'null' => true,
                'default' => 'NOW()',
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        // $this->forge->addForeignKey('warehouse_id', 'warehouse', 'CASCADE', 'CASCADE');
        $this->forge->createTable('warehouse_product');
    }

    public function down()
    {
        $this->forge->dropTable('warehouse_product');
    }
}
