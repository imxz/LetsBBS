<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "topic";
$route['404_override'] = '';

$route['reg'] = 'user/reg';
$route['logout'] = 'user/logout';
$route['login'] = 'user/login';

$route['recent/(:num)'] = 'topic/recent/$1';
$route['topic/(:num)'] = 'topic/detail/$1';

$route['node/(:num)'] = 'node/recent/$1';
$route['node/(:num)/(:num)'] = 'node/recent/$1/$2';

$route['member/([0-9a-zA-Z]+)'] = 'member/index/$1';
$route['member/([0-9a-zA-Z]+)/topic/(:num)'] = 'member/topic/$1/$2';
$route['member/([0-9a-zA-Z]+)/comment/(:num)'] = 'member/comment/$1/$2';

$route['notification/(:num)'] = 'notification/index/$1';

$route['admin'] = 'admin/topic';

/* End of file routes.php */
/* Location: ./application/config/routes.php */