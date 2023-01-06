<?php

namespace App\Models\Psp;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table            = 'psp_transaksi';
    protected $primaryKey       = 'id_transaksi';
    protected $returnType       = 'object';
    protected $useTimestamps    = true;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['tgl_transaksi', 'no_bukti', 'uraian', 'dk', 'nominal', 'akun_debet', 'debet',  'akun_kredit', 'kredit', 'ket_transaksi', 'ket_lain', 'by', 'inputan', 'created_id', 'updated_id', 'deleted_id'];

    public function DataTableKas()
    {
        return $this->db->table('psp_transaksi')
            ->select('id_transaksi,tgl_transaksi,no_bukti,uraian,dk,nominal,akun_debet,akundebet.nama_akun as nama_debet,debet,akun_kredit,akunkredit.nama_akun as nama_kredit,kredit,ket_transaksi,ket_lain,by,inputan')
            ->join('psp_akun as akundebet', 'akundebet.no_akun=akun_debet')
            ->join('psp_akun as akunkredit', 'akunkredit.no_akun=akun_kredit')
            ->like('concat(akunkredit.nama_akun,akundebet.nama_akun)', 'KAS')
            ->where('psp_transaksi.deleted_at', null);
    }
    public function DataTableBank()
    {
        return $this->db->table('psp_transaksi')
            ->select('id_transaksi,tgl_transaksi,no_bukti,uraian,dk,nominal,akun_debet,akundebet.nama_akun as nama_debet,debet,akun_kredit,akunkredit.nama_akun as nama_kredit,kredit,ket_transaksi,ket_lain,by,inputan')
            ->join('psp_akun as akundebet', 'akundebet.no_akun=akun_debet')
            ->join('psp_akun as akunkredit', 'akunkredit.no_akun=akun_kredit')
            ->like('concat(akunkredit.nama_akun,akundebet.nama_akun)', 'BANK')
            ->where('psp_transaksi.deleted_at', null);
    }
    public function DataTableBukuBesar($akun = null)
    {
        return $this->db->table('psp_transaksi')
            ->select('id_transaksi,tgl_transaksi,no_bukti,uraian,dk,nominal,akun_debet,akundebet.nama_akun as nama_debet,debet,akun_kredit,akunkredit.nama_akun as nama_kredit,kredit,ket_transaksi,ket_lain,by,inputan')
            ->join('psp_akun as akundebet', 'akundebet.no_akun=akun_debet')
            ->join('psp_akun as akunkredit', 'akunkredit.no_akun=akun_kredit')
            ->like('concat(akun_debet, " - " ,akun_kredit)', $akun)
            ->where('psp_transaksi.deleted_at', null);
    }
    public function deletedId($id)
    {
        return $this->table('psp_transaksi')->set('deleted_id', user()->id)->where('id_transaksi', $id)->update();
    }
}
