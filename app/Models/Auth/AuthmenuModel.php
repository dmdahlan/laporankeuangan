<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class AuthmenuModel extends Model
{
    protected $table            = 'auth_permissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;
    protected $allowedFields    = ['name', 'description', 'url', 'menu_id', 'jns_menu', 'icon', 'background', 'parameter', 'sort_menu', 'is_active'];

    public function DataTableMenu()
    {
        return $this->db->table('auth_permissions as menu')
            ->select('menu.id,menu.name,menu.description,menu.url,menu.menu_id, menu.jns_menu, menu.icon,menu.background,menu.parameter,menu.sort_menu,menu.is_active,idmenu.description as menuid')
            ->join('auth_permissions as idmenu', 'idmenu.id=menu.menu_id', 'left');
    }
    public function listMenu()
    {
        return $this->db->table('auth_permissions')
            ->select('id,description')
            ->where('jns_menu', 'nav-drop')
            ->where('menu_id', 0)
            ->where('is_active', 1)
            ->get()->getResult();
    }
}
