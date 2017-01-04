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
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
/**
 * @author liuhongyu
 * 自定义路由规则
 **/

/*inschool league*/
$route['league/inschool/cells?(:any)'] = 'CLeague/ReqinSchoolLeagueCells';
$route['league/inschool/cell?(:any)'] = 'CLeague/ReqinSchoolLeagueCell';
$route['league/fans?(:any)'] = 'CLeague/AddLeagueFans';
$route['league/inschool/team?(:any)'] = 'CLeague/ReqTeamMembers';
$route['league/inschool/start?(:any)'] = 'CLeague/ReqStartData';
$route['league/live/head?(:any)'] = 'CLeague/ReqMatchLiveHead';
$route['league/live/message?(:any)'] = 'CLeague/ReqMatchLiveMessage';
$route['league/live/fans?(:any)'] = 'CLeague/AddMatchTeamFans';
$route['league/live/matchinfo?(:any)'] = 'CLeague/ReqLiveMatchInfo';
$route['league/live/statistic?(:any)'] = 'CLeague/ReqLiveMatchStatistic';
$route['league/tool/matchset/matchinfo?(:any)'] = 'CLeague/SetMatchInfoTool';
$route['league/tool/matchset/teaminfo?(:any)'] = 'CLeague/SetTeamInfoTool';
$route['league/tool/matchevent?(:any)'] = 'CLeague/UploadMatchEvent';
$route['league/tool/endmatch?(:any)'] = 'CLeague/EndLiveMatch';
$route['league/create?(:any)'] = 'CLeague/CreateLeague';

/*match*/
$route['match/matchinfo/fans?(:any)'] = 'CMatch/AddMatchFans';

/*user*/
$route['user/userinfo/head?(:any)'] = 'CUser/ReqUserHead';
$route['user/userinfo/fans?(:any)'] = 'CUser/AddUserFans';
$route['user/userinfo/battle?(:any)'] = 'CUser/ReqUserBattle';
$route['user/userinfo/statistic?(:any)'] = 'CUser/ReqUserStatistic';
$route['user/userinfo/join?(:any)'] = 'CUser/ReqUserJoinTeamLeague';
$route['user/userinfo/manage?(:any)'] = 'CUser/UserManage';

/*team*/
$route['team/create?(:any)'] = 'CTeam/CreateTeam';
$route['team/teaminfo/head?(:any)'] = 'CTeam/ReqTeamHead';
$route['team/teaminfo/fans?(:any)'] = 'CTeam/AddTeamFans';
$route['team/teaminfo/dongtai?(:any)'] = 'CTeam/ReqTeamDongtai';
$route['team/teaminfo/statistic?(:any)'] = 'CTeam/ReqTeamStatistic';
$route['team/teaminfo/member?(:any)'] = 'CTeam/ReqTeamMembers';
$route['team/teaminfo/glory?(:any)'] = 'CTeam/ReqTeamGlory';
$route['team/teaminfo/manage?(:any)'] = 'CTeam/TeamManage';
$route['team/teaminfo/join?(:any)'] = 'CTeam/TeamJoin';


/*register*/
$route['register/verifycode?(:any)'] = 'CRegister/RegisterDeal';  #post api
$route['register/userinfo?(:any)'] = 'CRegister/RegisterPasswdUpLoad'; #post api

/*login*/
$route['login?(:any)'] = 'CLogin/LoginCheck'; #post api

/*picture*/
$route['image?(:any)'] = 'CImage/GetImageFromApp'; #post api 
