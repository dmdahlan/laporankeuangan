<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Error extends BaseController
{
    public function index()
    {
        $data = [
            'title' => '404 | Page not found'
        ];
        return view('v_404', $data);
    }
}
