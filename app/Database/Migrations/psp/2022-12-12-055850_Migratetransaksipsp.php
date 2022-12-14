<?php

namespace App\Database\Migrations\Psp;

use CodeIgniter\Database\Migration;

class Migratetransaksipsp extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_transaksi'       => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'tgl_transaksi'      => ['type' => 'date', 'null' => true],
            'no_bukti'           => ['type' => 'VARCHAR', 'constraint' => '225', 'null' => true],
            'uraian'             => ['type' => 'VARCHAR', 'constraint' => '225', 'null' => true],
            'dk'                 => ['type' => 'VARCHAR', 'constraint' => '20', 'null' => true],
            'nominal'            => ['type' => 'double', 'null' => true],
            'akun_debet'         => ['type' => 'VARCHAR', 'constraint' => '20', 'null' => true],
            'debet'              => ['type' => 'double', 'null' => true],
            'akun_kredit'        => ['type' => 'VARCHAR', 'constraint' => '20', 'null' => true],
            'kredit'             => ['type' => 'double', 'null' => true],
            'ket_transaksi'      => ['type' => 'VARCHAR', 'constraint' => '225', 'null' => true],
            'ket_lain'           => ['type' => 'VARCHAR', 'constraint' => '225', 'null' => true],
            'by'                 => ['type' => 'VARCHAR', 'constraint' => '225', 'null' => true],
            'inputan'            => ['type' => 'VARCHAR', 'constraint' => '50'],
            'created_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'updated_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'deleted_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_transaksi', true);
        $this->forge->addForeignKey('akun_debet', 'psp_akun', 'no_akun', '', '');
        $this->forge->addForeignKey('akun_kredit', 'psp_akun', 'no_akun', '', '');
        $this->forge->addForeignKey('created_id', 'users', 'id', '', '');
        $this->forge->addForeignKey('updated_id', 'users', 'id', '', '');
        $this->forge->addForeignKey('deleted_id', 'users', 'id', '', '');
        $this->forge->createTable('psp_transaksi');
    }
    public function down()
    {
        $this->forge->dropForeignKey('psp_transaksi', 'psp_transaksi_akun_debet_foreign');
        $this->forge->dropForeignKey('psp_transaksi', 'psp_transaksi_akun_kredit_foreign');
        $this->forge->dropForeignKey('psp_transaksi', 'psp_transaksi_created_id_foreign');
        $this->forge->dropForeignKey('psp_transaksi', 'psp_transaksi_updated_id_foreign');
        $this->forge->dropForeignKey('psp_transaksi', 'psp_transaksi_deleted_id_foreign');
        $this->forge->dropTable('psp_transaksi');
    }
}
