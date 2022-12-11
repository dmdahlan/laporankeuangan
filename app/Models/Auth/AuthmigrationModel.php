<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class AuthmigrationModel extends Model
{
    protected $table            = 'migrations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;
    protected $allowedFields    = ['batch'];

    public function DataTableMigration()
    {
        return $this->db->table('migrations')->select('id,version,class,group,namespace,time,batch');
    }
}
