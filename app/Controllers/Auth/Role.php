<?php

namespace App\Controllers\Auth;

use App\Models\Auth\AuthmenuModel;
use App\Models\Auth\AuthroleModel;
use Hermawan\DataTables\DataTable;
use CodeIgniter\RESTful\ResourceController;

class Role extends ResourceController
{
    public function __construct()
    {
        $this->db        = \Config\Database::connect();
        $this->roleModel = new AuthroleModel();
        $this->menuModel = new AuthmenuModel();
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $data = [
            'title'     =>  'Role',
        ];
        return view('auth/role/v_authrole', $data);
    }
    public function showdata()
    {
        $data = $this->roleModel->DataTableRole();
        $output = DataTable::of($data)
            ->addNumbering('no')
            ->add('action', function ($row) {
                return '
            <button type="button" class="btn btn-success btn-xs py-0" title="Akses" onclick="akses(' . "'" . $row->id . "'" . ',' . "'" . $row->name . "'" . ')"><i class="fas fa-check-double"></i></button>
            <button type="button" class="btn btn-warning btn-xs py-0" title="Edit" onclick="edit(' . $row->id . ')"><i class="fas fa-pencil-alt"></i></button>
            <button type="button" class="btn btn-danger btn-xs py-0" title="Delete" onclick="hapus(' . "'" . $row->id . "'" . ',' . "'" . $row->name . "'" . ')"><i class="fas fa-trash-alt"></i></button>
            ';
            })
            ->setSearchableColumns(['name', 'description'])
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
        if ($this->request->isAJAX()) {
            $output = [
                'ok'    => view('auth/role/c_authrole'),
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
            $data = $this->request->getPost();
            $this->roleModel->insert($data);
            $output = [
                'ok'             => 'Role berhasil disimpan',
                'csrfToken'          => csrf_hash(),
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
                'datalama'  => $this->roleModel->find($id),
            ];
            $output = [
                'ok'            => view('auth/role/e_authrole', $data),
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
            $data = $this->request->getPost();
            $this->roleModel->update($id, $data);
            $output = [
                'ok'             => 'Role berhasil diubah',
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
            $this->roleModel->delete($id);
            $output = [
                'ok'         => 'berhasil dihapus',
                'csrfToken'  => csrf_hash()
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        }
    }
    public function modalsmenu()
    {
        if ($this->request->isAJAX()) {
            $roleId = $this->request->getPost('idRole');
            $data = [
                'role'          => $this->db->table('auth_groups')->getWhere(['id' => $roleId])->getRow(),
                'namarole'      => $this->request->getPost('namaRole'),
            ];
            $output = [
                'ok'         => view('auth/role/v_authmodalsmenu', $data),
                'csrfToken'  => csrf_hash()
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        }
    }
    public function listmenu()
    {
        if ($this->request->isAJAX()) {
            $data = $this->menuModel->DataTableMenu();
            $output = DataTable::of($data)
                ->add('status', function ($row) {
                    return $row->is_active == 1 ? '<style="font-size: small">AKTIF' : '<style="font-size: small">NON AKTIF';
                })
                ->add('action', function ($row) {
                    return $this->roleModel->getRoleAkses($this->request->getPost('roleId'), $row->id) !== null ?
                        '<div class="form-check"><input class="form-check-input" checked type="checkbox" onclick="access(' . "'" . $row->id . "'" . ',' . "'" . $this->request->getPost('roleId') . "'" . ')"></div>' :
                        '<div class="form-check"><input class="form-check-input" type="checkbox" onclick="access(' . "'" . $row->id . "'" . ',' . "'" . $this->request->getPost('roleId') . "'" . ')"></div>';
                })
                ->addNumbering('no')
                ->setSearchableColumns(['menu.name', 'menu.description', 'menu.jns_menu'])
                ->toJson(true);
            return $output;
        } else {
            return redirect()->to('/error');
        }
    }
    public function roleAccess()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'group_id'         => $this->request->getPost('roleId'),
                'permission_id'    => $this->request->getPost('menuId'),
            ];
            $this->db->table('auth_groups_permissions')->getWhere($data)->getRowArray() == null ?
                $this->db->table('auth_groups_permissions')->insert($data) :
                $this->db->table('auth_groups_permissions')->delete($data);
            $output = [
                'ok'         => 'berhasil',
                'csrfToken'  => csrf_hash()
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        }
    }
    public function _validate($id = null)
    {
        if (!$this->validate($this->_getRulesValidation($id))) {
            $validation = \Config\Services::validation();

            $data = [];
            $data['errors'] = [];
            $data['name'] = [];
            $data['status'] = TRUE;
            $data['csrfToken'] = csrf_hash();

            if ($validation->hasError('description')) {
                $data['name'][] = 'description';
                $data['errors'][] = $validation->getError('description');
                $data['status'] = FALSE;
            }
            if ($validation->hasError('name')) {
                $data['name'][] = 'name';
                $data['errors'][] = $validation->getError('name');
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
            'name' => [
                'rules' => "required|max_length[100]|is_unique[auth_groups.name,id,{$id}]",
                'errors' => [
                    'required'      => 'Role harus diisi',
                    'is_unique'     => 'Role sudah ada',
                    'max_length'    => 'Role terlalu panjang'
                ]
            ],
            'description' => [
                'rules' => "required|max_length[100]|is_unique[auth_groups.description,id,{$id}]",
                'errors' => [
                    'required'      => 'Keterangan harus diisi',
                    'is_unique'     => 'Keterangan sudah ada',
                    'max_length'    => 'Keterangan terlalu panjang'
                ]
            ],

        ];
        return $rulesValidation;
    }
}
