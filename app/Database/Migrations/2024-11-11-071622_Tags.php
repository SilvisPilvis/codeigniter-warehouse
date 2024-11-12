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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->addPrimaryKey("id");
        $this->forge->createTable("category");
    }

    public function down()
    {
        $this->forge->dropTable("tag");
    }
}
