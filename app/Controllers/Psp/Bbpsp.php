<?php

namespace App\Controllers\Psp;

use App\Models\Psp\AkunpspModel;
use App\Models\Psp\TransaksiModel;
use CodeIgniter\RESTful\ResourceController;
use Hermawan\DataTables\DataTable;

class Bbpsp extends ResourceController
{
    protected $helpers = ['md_helper'];
    public function __construct()
    {
        $db = \Config\Database::connect();
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $data = [
            'title' => 'Buku Besar'
        ];
        return view('psp/data/v_bbpsp', $data);
    }
    public function showdata()
    {
        $db = \Config\Database::connect();
        $model = new TransaksiModel();
        $akun = $this->request->getPost('noakun');
        $this->saldoawal = $db->table('psp_akun')->where('no_akun', $akun)->get()->getRow()->saldo_awal;
        $data = $model->DataTableBukuBesar($akun);
        $output = DataTable::of($data, $akun)
            ->addNumbering('no')
            ->add('debet', function ($row) {
                $debet = $row->akun_debet == $this->request->getPost('noakun') ? $row->debet : null;
                return rupiah($debet);
            })
            ->add('kredit', function ($row) {
                $kredit = $row->akun_kredit == $this->request->getPost('noakun') ? $row->debet : null;
                return rupiah($kredit);
            })
            ->format('tgl_transaksi', function ($value) {
                return tanggal($value);
            })
            ->setSearchableColumns(['no_bukti', 'uraian', 'akun_debet', 'akundebet.nama_akun', 'akun_kredit', 'akunkredit.nama_akun'])
            ->filter(function ($data, $request) {
                $request->tglawal && $request->tglakhir ? $data->where('tgl_transaksi >=', datefilter($request->tglawal))->where('tgl_transaksi <=', datefilter($request->tglakhir)) : null;
                $request->noakun ? $data->like('concat(akun_debet, " - " ,akun_kredit)', $request->noakun) : null;
            })
            ->add('saldo', function ($row) {
                $debet              = $row->akun_debet == $this->request->getPost('noakun') ? $row->debet : null;
                $kredit             = $row->akun_kredit == $this->request->getPost('noakun') ? $row->kredit : null;
                $this->saldoawal    = $this->saldoawal + $debet - $kredit;
                return rupiah($this->saldoawal);
            })
            ->toJson(true);
        return $output;
    }
    public function saloAwal()
    {
        if ($this->request->isAJAX()) {
            $db = \Config\Database::connect();
            $noakun = $this->request->getPost('noakun');
            $output = [
                'saldo'      => $db->table('psp_akun')->where('no_akun', $noakun)->get()->getRow(),
                csrf_token() => csrf_hash(),
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        };
    }
    public function saloAkhir()
    {
        if ($this->request->isAJAX()) {
            $db = \Config\Database::connect();
            $noakun = $this->request->getPost('noakun');
            $saldoawal = $db->table('psp_akun')->where('no_akun', $noakun)->get()->getRow()->saldo_awal;
            $debet = $db->table('psp_transaksi')->selectSum('debet')->where('akun_debet', $noakun)->get()->getRow()->debet;
            $kredit = $db->table('psp_transaksi')->selectSum('kredit')->where('akun_kredit', $noakun)->get()->getRow()->kredit;
            $output = [
                'saldo'      => intval($saldoawal) + intval($debet) - intval($kredit),
                csrf_token() => csrf_hash(),
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        };
    }
    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        //
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        //
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
    }
}
