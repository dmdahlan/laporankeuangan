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
    protected $helpers = ['md_helper'];
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
    public function DataTableBukuBesar($akun = null, $tglawal = null, $tglakhir = null)
    {
        return $this->db->table('psp_transaksi')
            ->select('id_transaksi,tgl_transaksi,no_bukti,uraian,dk,nominal,akun_debet,akundebet.nama_akun as nama_debet,debet,akun_kredit,akunkredit.nama_akun as nama_kredit,kredit,ket_transaksi,ket_lain,by,inputan')
            ->join('psp_akun as akundebet', 'akundebet.no_akun=akun_debet')
            ->join('psp_akun as akunkredit', 'akunkredit.no_akun=akun_kredit')
            ->like('concat(akun_debet, " - " ,akun_kredit)', $akun)
            ->where('tgl_transaksi >=', datefilter($tglawal))
            ->where('tgl_transaksi <=', datefilter($tglakhir))
            ->where('psp_transaksi.deleted_at', null);
    }
    public function neraca($tahun = null)
    {
        $builder = $this->db->table('psp_akun');
        $builder->select(
            '
            psp_akun.no_akun,
            nama_akun,
            saldo_awal,
            sum(if(month(tgl_transaksi)=1,debet,0)) as djan,
            sum(if(month(tgl_transaksi)=1,kredit,0)) as kjan,
            sum(if(month(tgl_transaksi)=2,debet,0)) as dfeb,
            sum(if(month(tgl_transaksi)=2,kredit,0)) as kfeb,
            sum(if(month(tgl_transaksi)=3,debet,0)) as dmar,
            sum(if(month(tgl_transaksi)=3,kredit,0)) as kmar,
            sum(if(month(tgl_transaksi)=4,debet,0)) as dapr,
            sum(if(month(tgl_transaksi)=4,kredit,0)) as kapr,
            sum(if(month(tgl_transaksi)=5,debet,0)) as dmei,
            sum(if(month(tgl_transaksi)=5,kredit,0)) as kmei,
            sum(if(month(tgl_transaksi)=6,debet,0)) as djun,
            sum(if(month(tgl_transaksi)=6,kredit,0)) as kjun,
            sum(if(month(tgl_transaksi)=7,debet,0)) as djul,
            sum(if(month(tgl_transaksi)=7,kredit,0)) as kjul,
            sum(if(month(tgl_transaksi)=8,debet,0)) as dagt,
            sum(if(month(tgl_transaksi)=8,kredit,0)) as kagt,
            sum(if(month(tgl_transaksi)=9,debet,0)) as dsep,
            sum(if(month(tgl_transaksi)=9,kredit,0)) as ksep,
            sum(if(month(tgl_transaksi)=10,debet,0)) as dokt,
            sum(if(month(tgl_transaksi)=10,kredit,0)) as kokt,
            sum(if(month(tgl_transaksi)=11,debet,0)) as dnop,
            sum(if(month(tgl_transaksi)=11,kredit,0)) as knop,
            sum(if(month(tgl_transaksi)=12,debet,0)) as ddes,
            sum(if(month(tgl_transaksi)=12,kredit,0)) as kdes
            ',
            false
        )
            ->join('v_reportneraca', 'akun_no=no_akun')
            ->groupBy('id_akun')
            ->orderBy('no_akun', 'asc');
        if (isset($tahun)) {
            $query = $builder->like('tgl_transaksi', $tahun);
            // $query = $builder->like('akunkredit.tgl_transaksi', $tahun);
        }
        $query = $builder->get()->getResult();
        return $query;
    }
    public function deletedId($id)
    {
        return $this->table('psp_transaksi')->set('deleted_id', user()->id)->where('id_transaksi', $id)->update();
    }
}
