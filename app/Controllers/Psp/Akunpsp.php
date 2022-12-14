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
            $this->_validate();
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
                'datalama'  => $this->dariModel->find($id),
            ];
            $output = [
                'ok'            => view('dacont/edit/e_dari', $data),
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
            $old = $this->dariModel->find($id);
            $data =  [
                'dari'       => $this->request->getPost('dari'),
                'ket_dari'   => $this->request->getPost('ket_dari'),
                'updated_id' => user()->id
            ];
            $this->dariModel->update($id, $data);
            $output = [
                'ok'             => 'Dari berhasil diubah',
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
