<?php

namespace App\Controllers\Auth;

use App\Models\Auth\AuthuserModel;
use App\Models\Auth\AuthroleModel;
use Hermawan\DataTables\DataTable;
use Myth\Auth\Password;
use CodeIgniter\RESTful\ResourceController;

class User extends ResourceController
{
    protected $helpers = ['auth', 'md_helper'];
    public function __construct()
    {
        $this->userModel           = new AuthuserModel();
        $this->roleModel           = new AuthroleModel();
        $this->config              = config('Auth');
        $this->db                  = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'title'     =>  'User',
        ];
        return view('auth/user/v_authuser', $data);
    }
    public function showdata()
    {
        $data = $this->userModel->DataTableUsers();
        $output = DataTable::of($data)
            ->addNumbering('no')
            ->add('status', function ($row) {
                return $row->active == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Non Active</span>';
            })
            ->add('action', function ($row) {
                return '
            <button type="button" class="btn btn-success btn-xs py-0" title="Role" onclick="editRole(' . $row->id . ')"><i class="fas fa-user-alt"></i></button>
            <button type="button" class="btn btn-warning btn-xs py-0" title="Edit" onclick="edit(' . $row->id . ')"><i class="fas fa-pencil-alt"></i></button>
            ';
            })
            ->edit('created_at', function ($row) {
                return dateAll($row->created_at);
            })
            ->setSearchableColumns(['email', 'username', 'name'])
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
                'ok'    => view('auth/user/c_authuser'),
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
            $data = [
                'email'          => $this->request->getPost('email'),
                'username'       => $this->request->getPost('username'),
                'password_hash'  => Password::hash($this->request->getPost('pass_confirm')),
                'active'         => $this->request->getPost('active') == null ? 0 : $this->request->getPost('active'),
            ];
            $this->db->transBegin();
            try {
                $this->userModel->withGroup($this->config->defaultUserGroup);
                $this->userModel->insert($data);
                $output = [
                    'ok'             => 'User berhasil disimpan',
                    'csrfToken'      => csrf_hash(),
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
                'datalama'  => $this->userModel->find($id),
            ];
            $output = [
                'ok'            => view('auth/user/e_authuser', $data),
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
            $cekPassword  = $this->request->getPost('password') . $this->request->getPost('pass_confirm');
            $passwordold  = $this->request->getPost('pass_confirmold');
            $data = [
                'email'          => $this->request->getPost('email'),
                'username'       => $this->request->getPost('username'),
                'password_hash'  => $cekPassword == null ? $passwordold : Password::hash($this->request->getPost('pass_confirm')),
                'active'         => $this->request->getPost('active') == null ? 0 : $this->request->getPost('active'),
            ];
            $this->userModel->update($id, $data);
            $output = [
                'ok'             => 'User berhasil diubah',
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
            $this->userModel->delete($id);
            $output = [
                'ok'         => 'berhasil dihapus',
                'csrfToken'  => csrf_hash()
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        }
    }
    public function modalsRole()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('id');
            $data = [
                'datalama'    => $this->userModel->DataTableUsers()->where('users.id', $id)->get()->getRow(),
                'role'        => $this->roleModel->findAll(),
            ];
            $output = [
                'ok'            => view('auth/user/v_modalsrole', $data),
                'csrfToken'     => csrf_hash(),
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        };
    }
    public function updaterole()
    {
        if ($this->request->isAJAX()) {
            $userId = $this->request->getPost('id');
            $groupId = $this->request->getPost('group_id');
            $this->userModel->updateUserRole($userId, $groupId);
            $output = [
                'ok'             => 'Role berhasil diubah',
                'csrfToken'      => csrf_hash(),
            ];
            echo json_encode($output);
        } else {
            return redirect()->to('/error');
        };
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

            if ($validation->hasError('email')) {
                $data['name'][] = 'email';
                $data['errors'][] = $validation->getError('email');
                $data['status'] = FALSE;
            }
            if ($validation->hasError('username')) {
                $data['name'][] = 'username';
                $data['errors'][] = $validation->getError('username');
                $data['status'] = FALSE;
            }
            // if ($validation->hasError('old_password')) {
            //     $data['name'][] = 'old_password';
            //     $data['errors'][] = $validation->getError('old_password');
            //     $data['status'] = FALSE;
            // }
            if ($validation->hasError('password')) {
                $data['name'][] = 'password';
                $data['errors'][] = $validation->getError('password');
                $data['status'] = FALSE;
            }
            if ($validation->hasError('pass_confirm')) {
                $data['name'][] = 'pass_confirm';
                $data['errors'][] = $validation->getError('pass_confirm');
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
        $password  = $this->request->getPost('password') . $this->request->getPost('pass_confirm');
        // Strong Password
        // $rulesPassword = $password == null ? 'matches[pass_confirm]' : 'required|strong_password';
        // $rulesPasswordConfirm = $password == null ? 'matches[password]' : 'required|matches[password]|strong_password';


        $rulesPassword = $password == null ? 'matches[pass_confirm]' : 'required';
        $rulesPasswordConfirm = $password == null ? 'matches[password]' : 'required|matches[password]';

        $rulesValidation = [
            'email' => [
                'rules' => "required|valid_email|is_unique[users.email,id,{$id}]",
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
            'username' => [
                'rules' => "required|alpha_numeric_punct|min_length[3]|max_length[30]|is_unique[users.username,id,{$id}]",
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
            // 'old_password' => [
            //     'rules' => 'required',
            //     'errors' => [
            //         'required' => 'old password tidak boleh kosong',
            //     ]
            // ],
            'password' => [
                // 'rules' => $id == null ? 'required|strong_password' : $rulesPassword,
                'rules' => $id == null ? 'required' : $rulesPassword,
                'errors' => [
                    'required' => 'password tidak boleh kosong',
                ]
            ],
            'pass_confirm' => [
                // 'rules' => $id == null ? 'required|strong_password|matches[password]' : $rulesPasswordConfirm,
                'rules' => $id == null ? 'required|matches[password]' : $rulesPasswordConfirm,
                'errors' => [
                    'required' => 'password tidak boleh kosong',
                    'matches'  => 'password tidak sama',
                ]
            ],
        ];
        return $rulesValidation;
    }
}
