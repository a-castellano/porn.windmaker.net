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

$route['add'] = "videos_manager/addVideo";
$route['addmultiple'] = "videos_manager/addMultipleVideos";
$route['addmultiple/(:any)'] = "videos_manager/addMultipleVideos/$1";
$route['manage'] = "videos_manager/manageVideos";
$route['manage/(:any)'] = 'videos_manager/manageVideos/$1';
$route['managemultiple'] = "videos_manager/manageMultipleVideos";
$route['managemultiple/(:any)'] = 'videos_manager/manageMultipleVideos/$1';
$route['uploadmultiple'] = "videos_manager/uploadMultipleVideos";
$route['uploadmultiple/(:any)'] = "videos_manager/uploadMultipleVideos/$1";
$route['upload'] = "videos_manager/uploadVideo";
$route['searchmultiple'] = "videos_manager/searchMultipleVideo";
$route['search'] = "videos_manager/searchVideo";
$route['update'] = "videos_manager/updateVideo";
$route['updatemultiple'] = "videos_manager/updateMultipleVideos";
$route['delete/(:any)'] = "videos_manager/deleteVideo/$1";
$route['view/(:any)'] = "videos_manager/viewVideos/$1";
$route['upload/do_upload'] = "Upload/do_upload";
$route['default_controller'] = "videos_manager/index";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */