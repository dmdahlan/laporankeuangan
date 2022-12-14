<?php

namespace App\Controllers\Psp;

use App\Models\Psp\AkunpspModel;
use App\Models\Psp\TransaksiModel;
use Hermawan\DataTables\DataTable;
use CodeIgniter\RESTful\ResourceController;

class Kaspsp extends ResourceController
{
    protected $helpers = ['md_helper'];
    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->akunpspModel = new AkunpspModel();
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
            'title' => 'Kas | Perdana'
        ];
        return view('psp/data/v_kaspsp', $data);
    }
    public function showdata()
    {
        // <div class="form-check"><input class="form-check-input checklist" type="checkbox" value="' . $row->id_transaksi . '" name="idtransaksi[]"></div>
        $data = $this->transaksiModel->DataTableKas();
        $output = DataTable::of($data)
            ->addNumbering('no')
            ->add('action', function ($row) {
                return '
            <button type="button" class="btn btn-warning btn-xs py-0" title="Edit" onclick="edit(' . $row->id_transaksi . ')"><i class="fas fa-pencil-alt"></i></button>
            <button type="button" class="btn btn-danger btn-xs py-0" title="Delete" onclick="hapus(' . "'" . $row->id_transaksi . "'" . ',' . "'" . $row->no_bukti . "'" . ')"><i class="fas fa-trash-alt"></i></button>
            ';
            })
            ->format('tgl_transaksi', function ($value) {
                return tanggal($value);
            })
            ->format('debet', function ($value) {
                return rupiah($value);
            })
            ->format('kredit', function ($value) {
                return rupiah($value);
            })
            ->setSearchableColumns(['no_bukti', 'uraian', 'akun_debet', 'akundebet.nama_akun', 'akun_kredit', 'akunkredit.nama_akun'])
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
                'ok'    => view('psp/create/c_kaspsp'),
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
            $this->validasiimport();
            $file = $this->request->getFile('file_excel');
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file);
            $kas = $spreadsheet->getSheetByName('kas')->toArray();
            foreach ($kas as $key => $value) {
                if ($key == 0) {
                    continue;
                }
                $data = [
                    'tgl_transaksi'  => inputdate($value[0]),
                    'no_bukti'       => $value[1],
                    'uraian'         => $value[2],
                    'akun_debet'     => $value[3],
                    'debet'          => $value[5],
                    'akun_kredit'    => $value[6],
                    'kredit'         => $value[8],
                    'inputan'        => 'KAS',
                    'created_id'     => user()->id,
                ];
                $this->transaksiModel->insert($data);
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
                'datalama'      => $this->transaksiModel->find($id),
                'noakun'        => $this->akunpspModel->orderBy('no_akun', 'asc')->findAll()
            ];
            $output = [
                'ok'            => view('psp/edit/e_kaspsp', $data),
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
            $this->validasi($id);
            $data =  [
                'tgl_transaksi' => inputdate($this->request->getPost('tgl_transaksi')),
                'no_bukti'      => $this->request->getPost('no_bukti'),
                'uraian'        => $this->request->getPost('uraian'),
                'akun_debet'    => $this->request->getPost('akun_debet'),
                'debet'         => inputAngka($this->request->getPost('debet')),
                'akun_kredit'   => $this->request->getPost('akun_kredit'),
                'kredit'        => inputAngka($this->request->getPost('kredit')),
                'updated_id'    => user()->id
            ];
            $this->transaksiModel->update($id, $data);
            $output = [
                'ok'             => 'Kas berhasil diubah',
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
                // $this->transaksiModel->deletedId($id);
                $this->transaksiModel->delete($id);
                $output = [
                    'ok'         => 'data berhasil dihapus',
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
    public function deleteAll()
    {
        if ($this->request->isAJAX()) {
            $this->db->transBegin();
            try {
                // $this->transaksiModel->deletedId($id);
                $this->transaksiModel->where('inputan', 'KAS')->delete();
                $output = [
                    'ok'         => 'data berhasil dihapus',
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
    public function validasi($id = null)
    {
        $rules = [
            'tgl_transaksi' => [
                'rules' => "required|valid_date|max_length[20]",
                'errors' => [
                    'required'      => 'tgl harus diisi',
                    'max_length'    => 'tgl terlalu panjang'
                ]
            ],
            'no_bukti' => [
                'rules' => "required|alpha_numeric_punct|max_length[225]",
                'errors' => [
                    'required'      => 'bkk/bkm harus diisi',
                    'max_length'    => 'bkk/bkm terlalu panjang'
                ]
            ],
            'uraian' => [
                'rules' => "required|alpha_numeric_punct|max_length[225]",
                'errors' => [
                    'required'      => '{field} harus diisi',
                    'max_length'    => '{field} terlalu panjang'
                ]
            ],
            'akun_debet' => [
                'rules' => "required|alpha_numeric_punct|max_length[20]",
                'errors' => [
                    'required'      => 'noakun harus diisi',
                    'max_length'    => 'akun terlalu panjang'
                ]
            ],
            'debet' => [
                'rules' => "required|alpha_numeric_punct|max_length[20]",
                'errors' => [
                    'required'      => '{field} harus diisi',
                    'max_length'    => '{field} terlalu panjang'
                ]
            ],
            'akun_kredit' => [
                'rules' => "required|alpha_numeric_punct|max_length[20]",
                'errors' => [
                    'required'      => 'noakun harus diisi',
                    'max_length'    => 'akun terlalu panjang'
                ]
            ],
            'kredit' => [
                'rules' => "required|alpha_numeric_punct|max_length[20]",
                'errors' => [
                    'required'      => '{field} harus diisi',
                    'max_length'    => '{field} terlalu panjang'
                ]
            ],
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            $errors = [
                'tgl_transaksi'  => $validation->getError('tgl_transaksi'),
                'no_bukti'       => $validation->getError('no_bukti'),
                'uraian'         => $validation->getError('uraian'),
                'akun_debet'     => $validation->getError('akun_debet'),
                'debet'          => $validation->getError('debet'),
                'akun_kredit'    => $validation->getError('akun_kredit'),
                'kredit'         => $validation->getError('kredit'),
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
    public function validasiimport()
    {
        $rules = [
            'file_excel' => [
                'rules' => "uploaded[file_excel]|ext_in[file_excel,xlsx]",
                'errors' => [
                    'uploaded'      => 'silahkan pilih file',
                ]
            ],
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            $errors = [
                'file_excel'        => $validation->getError('file_excel'),
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
