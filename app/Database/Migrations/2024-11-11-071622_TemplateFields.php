<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Category extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'template' => [
                'type'       => 'JSONB',
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
            'category_id' => [
                'type'       => 'INT',
                'unique'     => true,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
            ],
        ]);
        $this->forge->addPrimaryKey("id");
        $this->forge->createTable("category");
    }

    public function down()
    {
        $this->forge->dropTable("tag");
    }
}
