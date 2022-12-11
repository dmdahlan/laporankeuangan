<?php

namespace App\Controllers\Auth;

use App\Models\Auth\AuthmigrationModel;
use Hermawan\DataTables\DataTable;
use CodeIgniter\RESTful\ResourceController;

class Migration extends ResourceController
{
    protected $helpers = ['md_helper'];
    public function __construct()
    {
        $this->migrationModel = new AuthmigrationModel();
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $data = [
            'title'     =>  'Migration',
        ];
        return view('auth/migration/v_authmigration', $data);
    }
    public function showdata()
    {
        $data = $this->migrationModel->DataTableMigration();
        $output = DataTable::of($data)
            ->addNumbering('no')
            ->add('action', function ($row) {
                return '
            <button type="button" class="btn btn-warning btn-xs py-0" title="Edit" onclick="edit(' . $row->id . ')"><i class="fas fa-pencil-alt"></i></button> ';
            })
            ->format('time', function ($value) {
                return dateInt($value);
            })
            ->setSearchableColumns(['version', 'class', 'group', 'namespace', 'time', 'batch'])
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
        if ($this->request->isAJAX()) {
            $data = [
                'datalama'  => $this->migrationModel->find($id),
            ];
            $output = [
                'ok'            => view('auth/migration/e_authmigration', $data),
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
            $this->_validate();
            $data = $this->request->getPost();
            $this->migrationModel->update($id, $data);
            $output = [
                'ok'             => 'Migration berhasil diubah',
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
        //
    }
    public function _validate()
    {
        if (!$this->validate($this->_getRulesValidation())) {
            $validation = \Config\Services::validation();

            $data = [];
            $data['errors'] = [];
            $data['name'] = [];
            $data['status'] = TRUE;
            $data['csrfToken'] = csrf_hash();

            if ($validation->hasError('batch')) {
                $data['name'][] = 'batch';
                $data['errors'][] = $validation->getError('batch');
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
            'batch' => [
                'rules' => 'required|integer',
                'errors' => [
                    'required' => '{field} harus diisi',
                    'integer'  => 'Format harus angka',
                ]
            ],

        ];
        return $rulesValidation;
    }
}
