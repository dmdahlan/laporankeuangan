<?php

namespace App\Controllers;

use Ifsnop\Mysqldump\Mysqldump;
use App\Controllers\BaseController;

class Backupdb extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Backup | database'
        ];
        return view('v_backupdb', $data);
    }
    public function backup()
    {
        try {
            $tanggal = date('dmy');
            $dump = new Mysqldump('mysql:host=localhost;dbname=lk;port=3306', 'root', '');
            $dump->start('F:backup/database/laporankeuangan' . $tanggal . '.sql');
            $pesan = "Database berhasil dibackup!";
            session()->setFlashdata('sukses', $pesan);
        } catch (\Exception $e) {
            $pesan = "Error! " . $e->getMessage();
            session()->setFlashdata('error', $pesan);
        }
        return redirect()->to('backupdb');
    }
}
