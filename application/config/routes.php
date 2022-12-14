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
|	https://codeigniter.com/userguide3/general/routing.html
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
$route['default_controller'] = 'Welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/* My Route */
$route['user'] = 'usercontroller';
$route['user/demousercontroller']['GET'] = 'usercontroller/demousercontroller';
$route['user/demoget']['GET'] = 'usercontroller/demoGettingUserController';
$route['user/demomodel']['GET'] = 'usercontroller/demoUserModelController';
$route['user/all']['GET'] = 'usercontroller/getAll';
$route['user/register']['get'] = 'usercontroller/registerView';
$route['user/register']['post'] = 'usercontroller/register';
$route['user/verifyregister']['get'] = 'usercontroller/verifyView';
$route['user/verifyregister']['post'] = 'usercontroller/verifyAndRegister';
$route['user/(:any)']['get'] = 'usercontroller/findOneById/$1';
$route['encrypt'] = 'usercontroller/demoEncrypt';
$route['jwt'] = 'usercontroller/demoJwt';
$route['user/resendotp'] = 'usercontroller/resendOtp';
$route['mail'] = 'usercontroller/demoSendMail';
$route['file'] = 'usercontroller/demoDownloadFile';
$route['download/document/(:any)']['get'] = 'usercontroller/downloadFile/$1';
$route['update'] = 'usercontroller/demoUpdateFields';
$route['image/(:any)/logo.png'] = 'usercontroller/checkOpennedEmail/$1';
$route['manage/user/page'] = 'usercontroller/manage';
$route['manage/user/page/(:any)'] = 'usercontroller/manage';
$route['manage/user/filter'] = 'usercontroller/filter';

