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
        $this->db = \Config\Database::connect();
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
        $this->validasi();
        $db = \Config\Database::connect();
        $model = new TransaksiModel();
        $akun = $this->request->getPost('noakun');
        $tglawal    = $this->request->getPost('tglawal');
        $tglakhir   = $this->request->getPost('tglakhir');

        $this->saldoawal = $db->table('psp_akun')->where('no_akun', $akun)->get()->getRow()->saldo_awal;
        $data = $model->DataTableBukuBesar($akun, $tglawal, $tglakhir);
        return DataTable::of($data, $akun)
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
    }
    public function ceksaldo()
    {
        if ($this->request->isAJAX()) {
            // $this->validasi();
            $tglawal    = $this->request->getPost('tglawal');
            $tglakhir   = $this->request->getPost('tglakhir');
            $noakun     = $this->request->getPost('noakun');
            $debetold   = $this->db->table('psp_transaksi')->selectSum('debet')->where('akun_debet', $noakun)->where('tgl_transaksi <', datefilter($tglawal))->get()->getRow()->debet;
            $kreditold  = $this->db->table('psp_transaksi')->selectSum('kredit')->where('akun_kredit', $noakun)->where('tgl_transaksi <', datefilter($tglawal))->get()->getRow()->kredit;
            $saldoawal  = $this->db->table('psp_akun')->where('no_akun', $noakun)->get()->getRow()->saldo_awal + $debetold - $kreditold;
            $debet      = $this->db->table('psp_transaksi')->selectSum('debet')->where('akun_debet', $noakun)->where('tgl_transaksi >=', datefilter($tglawal))->where('tgl_transaksi <=', datefilter($tglakhir))->get()->getRow()->debet;
            $kredit     = $this->db->table('psp_transaksi')->selectSum('kredit')->where('akun_kredit', $noakun)->where('tgl_transaksi >=', datefilter($tglawal))->where('tgl_transaksi <=', datefilter($tglakhir))->get()->getRow()->kredit;
            $saldoakhir = intval($saldoawal) + intval($debet) - intval($kredit);
            $output = [
                'saldoawal'      => $saldoawal == null ? 0 : $saldoawal,
                'debet'          => $debet == null ? 0 : $debet,
                'kredit'         => $kredit == null ? 0 : $kredit,
                'saldoakhir'     => $saldoakhir == 0 ? 0 : $saldoakhir,
                csrf_token()     => csrf_hash(),
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
    public function validasi()
    {
        $rules = [
            'noakun' => [
                'rules' => "required",
            ],
            'tglawal' => [
                'rules' => "required",
            ],
            'tglakhir' => [
                'rules' => "required",
            ],
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            $errors = [
                'noakun'        => $validation->getError('noakun'),
                'tglawal'       => $validation->getError('tglawal'),
                'tglakhir'      => $validation->getError('tglakhir'),
            ];
            $output = [
                'status'    => FALSE,
                'errors'    => $errors,
                'csrfToken' => csrf_hash()
            ];
            echo json_encode($output);
            exit();
        }
    }
}
