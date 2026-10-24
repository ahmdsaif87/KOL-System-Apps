<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/


$route_path = APPPATH . 'routes/';
// require_once $route_path . 'routes_landing.php';


$route['default_controller'] = 'auth/backend/auth/login';
$route['404_override'] = 'not_found';
$route['translate_uri_dashes'] = FALSE;

$route['administrator/login'] = 'auth/backend/auth/login';
$route['administrator/register'] = 'auth/backend/auth/register';
$route['administrator/forgot-password'] = 'auth/backend/auth/forgot_password';

$route['page/chart/pie_chart'] = 'page/chart/pie_chart';
$route['page/(:any)'] = 'page/detail/$1';
$route['blog/index'] = 'blog/index';
$route['blog/(:any)'] = 'blog/detail/$1';
$route['administrator/web-page'] = 'page/backend/page/admin';


$route[ADMIN_NAMESPACE_URL.'/manage-form/(:any)'] = 'form/backend/$1';
$route[ADMIN_NAMESPACE_URL.'/manage-form/(:any)/(:any)'] = 'form/backend/$1/$2';
$route[ADMIN_NAMESPACE_URL.'/manage-form/(:any)/(:any)/(:any)'] = 'form/backend/$1/$2/$3';
$route[ADMIN_NAMESPACE_URL.'/manage-form/(:any)/(:any)/(:any)/(:any)'] = 'form/backend/$1/$2/$3/$4';

// Route alias for aauth_users -> user module (resolves 404 on creator & PIC preview popups)
$route[ADMIN_NAMESPACE_URL.'/aauth_users'] = 'user/backend/user';
$route[ADMIN_NAMESPACE_URL.'/aauth_users/(:any)'] = 'user/backend/user/$1';
$route[ADMIN_NAMESPACE_URL.'/aauth_users/(:any)/(:any)'] = 'user/backend/user/$1/$2';

// Route alias for KOL "Tugas Saya"
$route[ADMIN_NAMESPACE_URL.'/my_tasks'] = 'tasks/backend/tasks/my_tasks';
$route[ADMIN_NAMESPACE_URL.'/my_tasks/(:any)'] = 'tasks/backend/tasks/my_tasks/$1';

// Route alias for KOL "Kontrak Saya"
$route[ADMIN_NAMESPACE_URL.'/my_contracts'] = 'campaign_kol_contracts/backend/campaign_kol_contracts/my_contracts';
$route[ADMIN_NAMESPACE_URL.'/my_contracts/(:any)'] = 'campaign_kol_contracts/backend/campaign_kol_contracts/my_contracts/$1';

// Route alias for KOL "Tagihan Saya"
$route[ADMIN_NAMESPACE_URL.'/my_invoices'] = 'invoices/backend/invoices/my_invoices';
$route[ADMIN_NAMESPACE_URL.'/my_invoices/(:any)'] = 'invoices/backend/invoices/my_invoices/$1';

$route[ADMIN_NAMESPACE_URL.'/(:any)'] = '$1/backend/$1';
$route[ADMIN_NAMESPACE_URL.'/(:any)/(:any)'] = '$1/backend/$1/$2';
$route[ADMIN_NAMESPACE_URL.'/(:any)/(:any)/(:any)'] = '$1/backend/$1/$2/$3';
$route[ADMIN_NAMESPACE_URL.'/(:any)/(:any)/(:any)/(:any)'] = '$1/backend/$1/$2/$3/$3';

$route['api/user/(:any)'] = 'api/user/$1';
$route['api/group/(:any)'] = 'api/group/$1';

$route['api/(:any)'] = '$1/api/$1';
$route['api/(:any)/(:any)'] = '$1/api/$1/$2';
$route['api/(:any)/(:any)/(:any)'] = '$1/api/$1/$2/$3';
$route['api/(:any)/(:any)/(:any)/(:any)'] = '$1/api/$1/$2/$3/$3';

$route['articles/feed'] = 'articles/articles/feed';
$route['articles/feed/(:any)'] = 'articles/articles/feed/$1';

