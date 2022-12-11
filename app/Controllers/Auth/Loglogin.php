<?php

namespace App\Controllers\Auth;

use Hermawan\DataTables\DataTable;
use App\Controllers\BaseController;

class Loglogin extends BaseController
{
    protected $helpers = ['md_helper'];
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function index()
    {
        $data = [
            'title'     =>  'History | Login',
            'email'     => $this->db->table('auth_logins')->orderBy('email', 'asc')->groupBy('email')->get()->getResult(),
        ];
        return view('auth/login/v_authhistorylogin', $data);
    }
    public function showdata()
    {
        $data = $this->db->table('auth_logins')
            ->select('auth_logins.date,auth_logins.ip_address,auth_logins.email,success,username')
            ->join('users', 'users.id=user_id', 'left');
        $output = DataTable::of($data)
            ->addNumbering('no')
            ->add('status', function ($row) {
                return $row->success == 1 ? '<span class="badge badge-success">Success</span>' : '<span class="badge badge-danger">Failed</span>';
            })
            ->format('date', function ($value) {
                return dateAll($value);
            })
            ->setSearchableColumns(['auth_logins.email', 'username'])
            ->filter(function ($data, $request) {
                $request->status === '1' ? $data->where('success', 1) : null;
                $request->status === '0' ? $data->where('success', 0) : null;
                $request->email ? $data->like('auth_logins.email', $request->email) : null;
            })
            ->toJson(true);
        return $output;
    }
}
