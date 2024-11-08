<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'metadata' => [
                'type' => 'JSONB',
                'null' => true,
            ],
            'tags' => [
                'type' => 'JSONB',
                'null' => true,
            ],
            // 'created_at timestamp default NOW()'
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
        $this->forge->createTable('product');
    }

    public function down()
    {
        $this->forge->dropTable('product');
    }
}
