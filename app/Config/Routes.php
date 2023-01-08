<?php

namespace Config;

use App\Controllers\Auth\Loglogin;
use App\Controllers\Error;
use App\Controllers\Auth\Menu;
use App\Controllers\Auth\Migration;
use App\Controllers\Auth\Role;
use App\Controllers\Auth\User;
use App\Controllers\Backupdb;
use App\Controllers\Psp\Akunpsp;
use App\Controllers\Psp\Bankpsp;
use App\Controllers\Psp\Bbpsp;
use App\Controllers\Psp\Getdatapsp;
use App\Controllers\Psp\Kaspsp;
use App\Controllers\Psp\Neracapsp;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('/error', [Error::class, 'index']);
$routes->resource('auth/menu', [Menu::class]);
$routes->post('auth/menu/data', [Menu::class, 'showdata']);
$routes->resource('auth/role', [Role::class]);
$routes->post('auth/role/data', [Role::class, 'showdata']);
$routes->post('auth/role/modalsmenu', [Role::class, 'modalsmenu']);
$routes->post('auth/role/listmenu', [Role::class, 'listmenu']);
$routes->post('auth/role/changeAccess', [Role::class, 'roleAccess']);
$routes->resource('auth/user', [User::class]);
$routes->post('auth/user/data', [User::class, 'showdata']);
$routes->post('auth/user/role', [User::class, 'modalsRole']);
$routes->post('auth/user/updaterole', [User::class, 'updaterole']);
$routes->resource('auth/migration', [Migration::class]);
$routes->post('auth/migration/data', [Migration::class, 'showdata']);
$routes->get('/auth/loglogin', [Loglogin::class, 'index']);
$routes->post('auth/loglogin/history', [Loglogin::class, 'showdata']);
$routes->get('backupdb', [Backupdb::class, 'index']);
$routes->post('backupdb', [Backupdb::class, 'backup']);

// Get PSP
$routes->post('noakunpsp', [Getdatapsp::class, 'noakun']);
// PSP
$routes->resource('psp/akunpsp', [Akunpsp::class]);
$routes->post('psp/akunpsp/data', [Akunpsp::class, 'showdata']);
$routes->resource('psp/kaspsp', [Kaspsp::class]);
$routes->post('psp/kaspsp/data', [Kaspsp::class, 'showdata']);
$routes->post('psp/kaspsp/deleteall', [Kaspsp::class, 'deleteAll']);
$routes->resource('psp/bankpsp', [Bankpsp::class]);
$routes->post('psp/bankpsp/data', [Bankpsp::class, 'showdata']);
$routes->post('psp/bankpsp/deleteall', [Bankpsp::class, 'deleteAll']);
$routes->resource('psp/bbpsp', [Bbpsp::class]);
$routes->post('psp/bbpsp/data', [Bbpsp::class, 'showdata']);
$routes->post('psp/bbpsp/ceksaldo', [Bbpsp::class, 'ceksaldo']);
$routes->get('psp/neracapsp', [Neracapsp::class, 'index']);
$routes->post('psp/neracapsp/data', [Neracapsp::class, 'showdata']);
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
