<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class AuthroleModel extends Model
{
    protected $table            = 'auth_groups';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;
    protected $allowedFields    = ['name', 'description'];

    public function DataTableRole()
    {
        return $this->db->table('auth_groups')
            ->select('id,name,description');
    }
    public function getRoleAkses($role_id, $id)
    {
        return $this->db->table('auth_groups_permissions')->getWhere(['group_id' => $role_id, 'permission_id' => $id])->getRow();
    }
}
