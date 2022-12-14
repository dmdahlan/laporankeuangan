<?php

namespace App\Database\Migrations\Psp;

use CodeIgniter\Database\Migration;

class Migrateakunpsp extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_akun'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'no_akun'            => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'nama_akun'          => ['type' => 'VARCHAR', 'constraint' => 225, 'null' => true],
            'saldo_awal'         => ['type' => 'double', 'null' => true],
            'dk_akun'            => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'ap_akun'            => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'ket_akun'           => ['type' => 'VARCHAR', 'constraint' => 225, 'null' => true],
            'created_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'updated_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'deleted_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_akun', true);
        $this->forge->addForeignKey('created_id', 'users', 'id', '', '');
        $this->forge->addForeignKey('updated_id', 'users', 'id', '', '');
        $this->forge->addForeignKey('deleted_id', 'users', 'id', '', '');
        $this->forge->createTable('psp_akun');
    }
    public function down()
    {
        $this->forge->dropForeignKey('psp_akun', 'psp_akun_created_id_foreign');
        $this->forge->dropForeignKey('psp_akun', 'psp_akun_updated_id_foreign');
        $this->forge->dropForeignKey('psp_akun', 'psp_akun_deleted_id_foreign');
        $this->forge->dropTable('psp_akun');
    }
}
