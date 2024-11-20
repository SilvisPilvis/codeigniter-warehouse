<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TemplateValues extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'value_sets' => [
                'type'       => 'VARCHAR',
                'null'       => true,
                'default'    => null,
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
        $this->forge->createTable("template_values");
    }

    public function down()
    {
        //
    }
}
