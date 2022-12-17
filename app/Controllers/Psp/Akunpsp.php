<?php

namespace App\Controllers\Psp;

use App\Models\Psp\AkunpspModel;
use Hermawan\DataTables\DataTable;
use CodeIgniter\RESTful\ResourceController;

class Akunpsp extends ResourceController
{
    protected $helpers = ['md_helper'];
    public function __construct()
    {
        $this->akunModel = new AkunpspModel();
        $this->db = \Config\Database::connect();
        $this->dk = [
            ['name' => 'DEBET', 'value' => 'DEBET'],
            ['name' => 'KREDIT', 'value' => 'KREDIT'],
        ];
        $this->ketakun = [
            ['name' => 'ACTIVA', 'value' => 'ACTIVA'],
            ['name' => 'PASIVA', 'value' => 'PASIVA'],
        ];
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $data = [
            'title' => 'Akun | Perdana'
        ];
        return view('psp/data/v_akunpsp', $data);
    }
    public function showdata()
    {
        $data = $this->akunModel->DataTableAkun();
        $output = DataTable::of($data)
            ->addNumbering('no')
            ->add('action', function ($row) {
                return '
            <button type="button" class="btn btn-warning btn-xs py-0" title="Edit" onclick="edit(' . $row->id_akun . ')"><i class="fas fa-pencil-alt"></i></button>
            <button type="button" class="btn btn-danger btn-xs py-0" title="Delete" onclick="hapus(' . "'" . $row->id_akun . "'" . ',' . "'" . $row->no_akun . "'" . ')"><i class="fas fa-trash-alt"></i></button>
            ';
            })
            ->format('saldo_awal', function ($value) {
                return rupiah($value);
            })
            ->setSearchableColumns(['no_akun', 'nama_akun', 'saldo_awal', 'dk_akun', 'ap_akun',  'ket_akun'])
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
                'ok'    => view('psp/create/c_akunpsp'),
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
            $this->_validateUpload();
            $file = $this->request->getFile('file_excel');
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file);
            $kas = $spreadsheet->getSheetByName('no_akun')->toArray();
            foreach ($kas as $key => $value) {
                if ($key == 0) {
                    continue;
                }
                $data = [
                    'no_akun'        => $value[0],
                    'nama_akun'      => $value[1],
                    'saldo_awal'     => $value[2],
                    'dk_akun'        => $value[3],
                    'ap_akun'        => $value[4],
                    'created_id'     => user()->id,
                ];
                $this->akunModel->insert($data);
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
        if ($this->request->isAJAX()) {
            $data = [
                'datalama'      => $this->akunModel->find($id),
                'debetkredit'   => $this->dk,
                'ketakun'       =>  $this->ketakun,
            ];
            $output = [
                'ok'            => view('psp/edit/e_akunpsp', $data),
                'csrfToken'     => csrf_hash(),
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        };
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->_validate($id);
            $data =  [
                'no_akun'       => $this->request->getPost('no_akun'),
                'nama_akun'     => $this->request->getPost('nama_akun'),
                'saldo_awal'    => inputAngka($this->request->getPost('saldo_awal')),
                'dk_akun'       => $this->request->getPost('dk_akun'),
                'ap_akun'       => $this->request->getPost('ap_akun'),
                'updated_id'    => user()->id
            ];
            $this->akunModel->update($id, $data);
            $output = [
                'ok'             => 'Akun berhasil diubah',
                'csrfToken'      => csrf_hash(),
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        };
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
                $this->akunModel->deletedId($id);
                $this->akunModel->delete($id);
                $output = [
                    'ok'         => 'berhasil dihapus',
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
    public function modal()
    {
        if ($this->request->isAJAX()) {
            $output = [
                'ok'    => view('psp/create/c_aakunpsp'),
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        };
    }
    public function import()
    {
        if ($this->request->isAJAX()) {
            $this->_validateUpload();
            $file = $this->request->getFile('file_excel');
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file);
            $kas = $spreadsheet->getSheetByName('no_akun')->toArray();
            foreach ($kas as $key => $value) {
                if ($key == 0) {
                    continue;
                }
                $data = [
                    'no_akun'        => $value[0],
                    'nama_akun'      => $value[1],
                    'saldo_awal'     => $value[2],
                    'dk_akun'        => $value[3],
                    'ap_akun'        => $value[4],
                    'created_id'     => user()->id,
                ];
                $this->akunModel->insert($data);
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
    public function _validateUpload()
    {
        if (!$this->validate($this->_getRulesValidationUpload())) {
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
    public function _getRulesValidationUpload()
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
    public function _validate($id = null)
    {
        if (!$this->validate($this->_getRulesValidation($id))) {
            $validation = \Config\Services::validation();

            $data = [];
            $data['errors'] = [];
            $data['name'] = [];
            $data['status'] = TRUE;

            if ($validation->hasError('no_akun')) {
                $data['name'][] = 'no_akun';
                $data['errors'][] = $validation->getError('no_akun');
                $data['status'] = FALSE;
            }
            if ($validation->hasError('nama_akun')) {
                $data['name'][] = 'nama_akun';
                $data['errors'][] = $validation->getError('nama_akun');
                $data['status'] = FALSE;
            }
            if ($validation->hasError('saldo_awal')) {
                $data['name'][] = 'saldo_awal';
                $data['errors'][] = $validation->getError('saldo_awal');
                $data['status'] = FALSE;
            }
            if ($validation->hasError('dk_akun')) {
                $data['name'][] = 'dk_akun';
                $data['errors'][] = $validation->getError('dk_akun');
                $data['status'] = FALSE;
            }
            if ($validation->hasError('ap_akun')) {
                $data['name'][] = 'ap_akun';
                $data['errors'][] = $validation->getError('ap_akun');
                $data['status'] = FALSE;
            }
            if ($data['status'] === FALSE) {
                echo json_encode($data);
                exit();
            }
        }
    }
    public function _getRulesValidation($id = null)
    {
        $rulesValidation = [
            'no_akun' => [
                'rules' => "required|alpha_numeric_punct|max_length[20]|is_unique[psp_akun.no_akun,id_akun,{$id}]",
                'errors' => [
                    'required'      => 'nomor akun harus diisi',
                    'is_unique'     => 'nomor akun sudah ada',
                    'max_length'    => 'nomor akun terlalu panjang'
                ]
            ],
            'nama_akun' => [
                'rules' => "required|alpha_numeric_punct|max_length[225]|is_unique[psp_akun.nama_akun,id_akun,{$id}]",
                'errors' => [
                    'required'      => 'nama akun harus diisi',
                    'is_unique'     => 'nama akun sudah ada',
                    'max_length'    => 'nama akun terlalu panjang'
                ]
            ],
            'saldo_awal' => [
                'rules' => "permit_empty|alpha_numeric_punct|max_length[20]",
                'errors' => [
                    'max_length'    => 'saldo terlalu panjang'
                ]
            ],
            'dk_akun' => [
                'rules' => "required|alpha_numeric_punct|max_length[20]",
                'errors' => [
                    'required'      => 'debet/kredit harus diisi',
                    'max_length'    => 'debet/kredit panjang'
                ]
            ],
            'ap_akun' => [
                'rules' => "required|alpha_numeric_punct|max_length[20]",
                'errors' => [
                    'required'      => 'activa/pasiva harus diisi',
                    'max_length'    => 'activa/pasiva panjang'
                ]
            ],
        ];
        return $rulesValidation;
    }
}
