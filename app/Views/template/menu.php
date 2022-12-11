<?php
$db            = \Config\Database::connect();
$request       = \Config\Services::request();
$segment       = $request->uri->getTotalSegments();
$menuaktif     = $segment > 1 ? $request->uri->getSegment(2) : $request->uri->getSegment(1);
$grup_id = $db->table('auth_groups_users')->where('user_id', user()->id)->get()->getRow()->group_id;
$navDrop = $db->table('auth_permissions')->join('auth_groups_permissions', 'permission_id=id')->where('group_id', $grup_id)->where('is_active', 1)->where('jns_menu', 'nav-drop')->orderBy('sort_menu', 'asc')->get()->getResult();
$url     = $db->table('auth_permissions')->where('name', $menuaktif)->get()->getRow();
?>
<?php foreach ($navDrop as $menu) : ?>
    <li class="nav-item dropdown <?= $url->menu_id == $menu->id ? 'active' : null ?>">
        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle"><?= $menu->description ?></a>
        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
            <?php
            $subMenu = $db->table('auth_permissions')->join('auth_groups_permissions', 'permission_id=id')->where('auth_groups_permissions.group_id', $grup_id)->where('is_active', 1)->orderBy('sort_menu', 'asc')->where('menu_id', $menu->id)->get()->getResult();
            ?>
            <?php foreach ($subMenu as $sub) :  ?>
                <li><a class="dropdown-item" href="/<?= $sub->url ?>"><?= $sub->description ?></a></li>
            <?php endforeach ?>
        </ul>
    </li>
<?php endforeach ?>