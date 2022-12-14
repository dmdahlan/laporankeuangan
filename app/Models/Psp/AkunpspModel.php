<?php

namespace App\Models\Psp;

use CodeIgniter\Model;

class AkunpspModel extends Model
{
    protected $table            = 'psp_akun';
    protected $primaryKey       = 'id_akun';
    protected $returnType       = 'object';
    protected $useTimestamps    = true;
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['no_akun', 'nama_akun', 'saldo_awal', 'dk_akun', 'ap_akun',  'ket_akun', 'created_id', 'updated_id', 'deleted_id'];

    public function DataTableAkun()
    {
        return $this->db->table('psp_akun')
            ->select('id_akun,no_akun,nama_akun,saldo_awal,dk_akun,ap_akun,ket_akun')->where('deleted_at', null);
    }
    public function deletedId($id)
    {
        return $this->table('psp_akun')->set('deleted_id', user()->id)->where('id_akun', $id)->update();
    }
}
