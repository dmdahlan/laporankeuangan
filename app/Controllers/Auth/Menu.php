<?php

namespace App\Controllers\Auth;

use App\Models\Auth\AuthmenuModel;
use Hermawan\DataTables\DataTable;
use CodeIgniter\RESTful\ResourceController;

class Menu extends ResourceController
{
    public function __construct()
    {
        $this->menuModel = new AuthmenuModel();
        $this->jenismenu = ['nav-menu', 'nav-drop', 'dashboard'];
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $data = [
            'title'     =>  'Menu',
        ];
        return view('auth/menu/v_authmenu', $data);
    }
    public function showdata()
    {
        $data = $this->menuModel->DataTableMenu();
        $output = DataTable::of($data)
            ->addNumbering('no')
            ->add('status', function ($row) {
                return $row->is_active == 1 ? 'AKTIF' : 'NON AKTIF';
            })
            ->add('action', function ($row) {
                return '
                <button type="button" class="btn btn-warning btn-xs py-0 btn-edit" title="Edit" id="btn-edit" data-id="' . $row->id . '"><i class="fas fa-pencil-alt"></i></button>
                <button type="button" class="btn btn-danger btn-xs py-0" title="Delete" id="btn-delete" data-id="' . $row->id . '" data-ket="' . $row->description . '"><i class="fas fa-trash-alt"></i></button>
            ';
            })
            ->setSearchableColumns(['menu.name', 'menu.description', 'idmenu.description', 'menu.jns_menu', 'menu.url'])
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
        return redirect()->to('/error');
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'datamenu'  => $this->menuModel->listMenu(),
                'jenismenu' => $this->jenismenu,
            ];
            $output = [
                'ok'    => view('auth/menu/c_authmenu', $data),
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
            $this->validasi();
            $data = $this->request->getPost();
            $this->menuModel->insert($data);
            $output = [
                'ok'             => 'Menu berhasil disimpan',
                'csrfToken'      => csrf_hash(),
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
                'datalama'  => $this->menuModel->find($id),
                'jenismenu' => $this->jenismenu,
                'datamenu'  => $this->menuModel->listMenu(),
            ];
            $output = [
                'ok'            => view('auth/menu/e_authmenu', $data),
                'csrfToken'         => csrf_hash(),
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
            $this->validasi($id);
            $data = $this->request->getPost();
            $this->menuModel->update($id, $data);
            $output = [
                'ok'             => 'Menu berhasil diubah',
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
            $this->menuModel->delete($id);
            $output = [
                'ok'         => 'berhasil dihapus',
                'csrfToken'  => csrf_hash()
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        }
    }
    public function validasi($id = null)
    {
        $rules = [
            'description' => [
                // 'rules' => "required|max_length[100]|is_unique[auth_permissions.description,id,{$id}]",
                'rules' => "required|max_length[100]",
                'errors' => [
                    'required'      => 'nama menu harus diisi',
                    'is_unique'     => 'nama menu sudah ada',
                    'max_length'    => 'nama menu terlalu panjang'
                ]
            ],
            'name' => [
                // 'rules' => "required|max_length[100]|is_unique[auth_permissions.name,id,{$id}]",
                'rules' => "required|max_length[100]",
                'errors' => [
                    'required'      => 'menu harus diisi',
                    'is_unique'     => 'menu sudah ada',
                    'max_length'    => 'menu terlalu panjang'
                ]
            ],

        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            $errors = [
                'description' => $validation->getError('description'),
                'name'        => $validation->getError('name'),
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
