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
            'category_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
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
        $this->forge->addForeignKey('category_id', 'category', 'id');
        $this->forge->createTable('product');
        $this->db->insert('category', [
            'name' => 'Box',
            'name' => 'Stuff',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('product');
    }
}
