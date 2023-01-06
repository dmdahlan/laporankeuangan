<?php

namespace App\Controllers\Psp;

use App\Controllers\BaseController;
use App\Models\Psp\AkunpspModel;

class Getdatapsp extends BaseController
{
    public function __construct()
    {
        $this->akunModel = new AkunpspModel();
    }
    public function noakun()
    {
        if ($this->request->isAJAX()) {
            $list = [];
            $query = $this->akunModel->like('no_akun', $this->request->getPost('search'))->orLike('nama_akun', $this->request->getPost('search'))->orderBy('no_akun', 'asc')
                ->select('no_akun as id, concat(no_akun, " - " ,nama_akun) as text')->limit(50);
            $list = $query->findAll();
            echo json_encode($list);
        };
    }
}
