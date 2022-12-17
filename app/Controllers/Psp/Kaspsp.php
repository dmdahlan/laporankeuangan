<?php

namespace App\Controllers\Psp;

use App\Models\Psp\TransaksiModel;
use Hermawan\DataTables\DataTable;
use CodeIgniter\RESTful\ResourceController;

class Kaspsp extends ResourceController
{
    protected $helpers = ['md_helper'];
    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
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
            'title' => 'Kas | Perdana'
        ];
        return view('psp/data/v_kaspsp', $data);
    }
    public function showdata()
    {
        // <div class="form-check"><input class="form-check-input checklist" type="checkbox" value="' . $row->id_transaksi . '" name="idtransaksi[]"></div>
        $data = $this->transaksiModel->DataTableKas();
        $output = DataTable::of($data)
            ->addNumbering('no')
            ->format('tgl_transaksi', function ($value) {
                return tanggal($value);
            })
            ->format('debet', function ($value) {
                return rupiah($value);
            })
            ->format('kredit', function ($value) {
                return rupiah($value);
            })
            ->setSearchableColumns(['no_bukti', 'uraian', 'akun_debet', 'akundebet.nama_akun', 'akun_kredit', 'akunkredit.nama_akun'])
            ->toJson(true);
        return $output;
    }
    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        if ($this->request->isAJAX()) {
            $output = [
                'ok'    => view('psp/create/c_kaspsp'),
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        };
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        if ($this->request->isAJAX()) {
            $this->_validate();
            $file = $this->request->getFile('file_excel');
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file);
            $kas = $spreadsheet->getSheetByName('kas')->toArray();
            foreach ($kas as $key => $value) {
                if ($key == 0) {
                    continue;
                }
                $data = [
                    'tgl_transaksi'  => inputdate($value[0]),
                    'no_bukti'       => $value[1],
                    'uraian'         => $value[2],
                    'akun_debet'     => $value[3],
                    'debet'          => $value[5],
                    'akun_kredit'    => $value[6],
                    'kredit'         => $value[8],
                    'inputan'        => 'KAS',
                    'created_id'     => user()->id,
                ];
                $this->transaksiModel->insert($data);
            }
            $output = [
                'ok'              => 'File berhasil diimport',
                'csrfToken'       => csrf_hash(),
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        };
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
        if ($this->request->isAJAX()) {
            $this->db->transBegin();
            try {
                // $this->transaksiModel->deletedId($id);
                $this->transaksiModel->delete($id);
                $output = [
                    'ok'         => 'data berhasil dihapus',
                    'csrfToken'  => csrf_hash()
                ];
                $this->db->transCommit();
            } catch (\Exception $e) {
                $this->db->transRollback();
                $output = [
                    'error'      => $e->getMessage(),
                    'csrfToken'  => csrf_hash(),
                ];
            }
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        }
    }
    public function deleteAll()
    {
        if ($this->request->isAJAX()) {
            $this->db->transBegin();
            try {
                // $this->transaksiModel->deletedId($id);
                $this->transaksiModel->where('inputan', 'KAS')->delete();
                $output = [
                    'ok'         => 'data berhasil dihapus',
                    'csrfToken'  => csrf_hash()
                ];
                $this->db->transCommit();
            } catch (\Exception $e) {
                $this->db->transRollback();
                $output = [
                    'error'      => $e->getMessage(),
                    'csrfToken'  => csrf_hash(),
                ];
            }
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        }
    }
    public function _validate($id = null)
    {
        if (!$this->validate($this->_getRulesValidation())) {
            $validation = \Config\Services::validation();

            $data = [];
            $data['errors'] = [];
            $data['name'] = [];
            $data['status'] = TRUE;

            if ($validation->hasError('file_excel')) {
                $data['name'][] = 'file_excel';
                $data['errors'][] = $validation->getError('file_excel');
                $data['status'] = FALSE;
            }
            if ($data['status'] === FALSE) {
                echo json_encode($data);
                exit();
            }
        }
    }
    public function _getRulesValidation()
    {
        $rulesValidation = [
            'file_excel' => [
                'rules' => "uploaded[file_excel]|ext_in[file_excel,xlsx]",
                'errors' => [
                    'uploaded'      => 'silahkan pilih file',
                ]
            ],
        ];
        return $rulesValidation;
    }
}
