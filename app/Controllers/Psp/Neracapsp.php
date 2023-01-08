<?php

namespace App\Controllers\Psp;

use App\Controllers\BaseController;
use App\Models\Psp\TransaksiModel;

class Neracapsp extends BaseController
{
    protected $helpers = ['md_helper'];
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function index()
    {
        $data = [
            'title' => 'Neraca | Perdana'
        ];
        return view('psp/data/v_neracapsp', $data);
    }
    public function showdata()
    {
        if ($this->request->isAJAX()) {
            $model = new TransaksiModel();
            $tahun = $this->request->getPost('tahun');
            $listsdata = $model->neraca($tahun);
            $data = [];
            foreach ($listsdata as $r) {
                $row = [];
                $row[] = $r->no_akun;
                $row[] = $r->nama_akun;
                $debetold   = $this->db->table('psp_transaksi')->selectSum('debet')->where('akun_debet', $r->no_akun)->where('year(tgl_transaksi) <', $tahun)->get()->getRow()->debet;
                $kreditold  = $this->db->table('psp_transaksi')->selectSum('kredit')->where('akun_kredit', $r->no_akun)->where('year(tgl_transaksi) <', $tahun)->get()->getRow()->kredit;
                $saldoawal = $r->saldo_awal + $debetold - $kreditold;
                $row[] = rupiah($saldoawal);
                $row[] = rupiah($r->djan);
                $row[] = rupiah($r->kjan);
                $saldojan = $saldoawal + $r->djan - $r->kjan;
                $row[] = rupiah($saldojan);
                $row[] = rupiah($r->dfeb);
                $row[] = rupiah($r->kfeb);
                $saldofeb = $saldojan + $r->dfeb - $r->kfeb;
                $row[] = rupiah($saldofeb);
                $row[] = rupiah($r->dmar);
                $row[] = rupiah($r->kmar);
                $saldomar = $saldojan + $r->dmar - $r->kmar;
                $row[] = rupiah($saldomar);
                $row[] = rupiah($r->dapr);
                $row[] = rupiah($r->kapr);
                $saldoapr = $saldojan + $r->dapr - $r->kapr;
                $row[] = rupiah($saldoapr);
                $row[] = rupiah($r->dmei);
                $row[] = rupiah($r->kmei);
                $saldomei = $saldojan + $r->dmei - $r->kmei;
                $row[] = rupiah($saldomei);
                $row[] = rupiah($r->djun);
                $row[] = rupiah($r->kjun);
                $saldojun = $saldojan + $r->djun - $r->kjun;
                $row[] = rupiah($saldojun);
                $row[] = rupiah($r->djul);
                $row[] = rupiah($r->kjul);
                $saldojul = $saldojan + $r->djul - $r->kjul;
                $row[] = rupiah($saldojul);
                $row[] = rupiah($r->dagt);
                $row[] = rupiah($r->kagt);
                $saldoagt = $saldojan + $r->dagt - $r->kagt;
                $row[] = rupiah($saldoagt);
                $row[] = rupiah($r->dsep);
                $row[] = rupiah($r->ksep);
                $saldosep = $saldojan + $r->dsep - $r->ksep;
                $row[] = rupiah($saldosep);
                $row[] = rupiah($r->dokt);
                $row[] = rupiah($r->kokt);
                $saldookt = $saldojan + $r->dokt - $r->kokt;
                $row[] = rupiah($saldookt);
                $row[] = rupiah($r->dnop);
                $row[] = rupiah($r->knop);
                $saldonop = $saldojan + $r->dnop - $r->knop;
                $row[] = rupiah($saldonop);
                $row[] = rupiah($r->ddes);
                $row[] = rupiah($r->kdes);
                $saldodes = $saldojan + $r->ddes - $r->kdes;
                $row[] = rupiah($saldodes);
                // $janc += $r->janC;

                $data[] = $row;
            }
            // $data[] = [
            //     '', 'TOTAL', '', '', //$janc, $jann
            // ];
            $output = [
                'draw' => $this->request->getPost('draw'),
                'data' => $data,
                csrf_token() => csrf_hash(),
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        }
    }
}
