<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$appDir=realpath(dirname(__FILE__).'/../');
$imageDir=realpath(dirname(__FILE__).'/../image');

class CTeam extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	const SAIKUANG = 'saikuang';
	const BEST = 'best';
	const PLAYERSTATISTIC = 'playerstatistic';
	const TEAMSTATISTIC = 'teamStatistic';
	const NO = 'No'; //球员号码
	const PLAYERNAME = 'playername'; //球员名字
		
	const TTL = 24 * 3600;

	public function __construct() {
		parent::__construct();
		$this->load->model('MMatch');
		$this->load->model('MLeague');
		$this->load->model('MUser');
		$this->load->model("MTeam");
		$this->load->model("MRedis");
		$this->load->model("MMTeam");
		$this->redis = $this->MRedis->_getInstance();
	}	

	
	public function index()
	{
		#$this->load->view('welcome_message');
		echo "hello umvp!";
	}
	public function CreateTeam(){
		$sUserid = $_GET['userid'];
		$sTeamName = $_GET['team_name'];
		$sTeamAddress = $_GET['team_address'];
		$time = GetTime();
		$aTeamInfo = array(
			'createrid' => $sUserid,
			'name' => "'$sTeamName'",
			'address' => "'$sTeamAddress'",
			'create_time' => "'$time'"
		);
		$bRet = $this->MTeam->InsertTeamInfo($aTeamInfo);
		if($bRet) {
			$code = $this->config->item('MY_ECHO_OK');
		} else {
			$code = $this->config->item('MY_ECHO_FAIL');
		}
		MessageEcho($code);
	}
	public function TeamManage() {


	}
#$route['team/teaminfo/head?(:any)'] = 'CTeam/ReqTeamHead';
	public function ReqTeamHead() {
		$sTeamid = $_GET['teamid'];
		$sUserid = $_GET['userid'];
		$aTeamInfo = $this->MTeam->GetTeamInfo($sTeamid, ['name', 'logoid', 'fans_num', 'introduction']);
		if(empty($aTeamInfo)) {
			$code = $this->config->item('MY_ECHO_FAIL');
			MessageEcho($code);
			return;
		}
		$sTeamName = $aTeamInfo['name'];
		$iMatchNum = count($this->MTeam->GetTeamMatchInfo($sTeamid)); 
		$iMatchMemberNum = count($this->MTeam->GetTeamUserInfoByTeamid($sTeamid)); 
		$iFansNum = $aTeamInfo['fans_num'];
		$sIntroduction = $aTeamInfo['introduction'];
		$focus = 0;
		$aUserTeamFansInfo = $this->MUser->GetUserTeamFans($sUserid);
		if(in_array($sTeamid, $aUserTeamFansInfo)) {
			$focus = 1;
		}
		$iLogoid =  $aTeamInfo['logoid'];
		$aTeamInfoHead = array(
			'team_name' => $sTeamName,
			'team_match_num' => $iMatchNum,
			'team_member_num' => $iMatchMemberNum,
			'team_fans_num' => $iFansNum,
			'team_introduction' => $sIntroduction,
			'focus' => $focus
		);
		$code = $this->config->item('MY_ECHO_OK');
		MessageEcho($code, "", $aTeamInfoHead);
	}
	public function AddTeamFans() {
		$sTeamid = $_GET['teamid'];
		$sUserid = $_GET['userid'];
		$reponse = array();
		$aTeamFans = $this->MUser->GetUserTeamFans($sUserid);
		$bRet1 = false;
		$bRet2 =false;
		if(in_array($sTeamid, $aTeamFans)) {
			$bRet1 = $this->MUser->DelUserTeamFans($sUserid, $sTeamid);
		} else {
			$bRet2 = $this->MUser->AddUserTeamFans($sUserid, $sTeamid);
		}
		if($bRet1 || $bRet2) {
			$code = $this->config->item('MY_ECHO_OK');	
		} else {
			$code = $this->config->item('MY_ECHO_FAIL');	
		}
		MessageEcho($code);
	}
	//$route['team/teaminfo/dongtai?(:any)'] = 'CTeam/ReqTeamDongtai';
	public function  ReqTeamDongtai(){
		$sTeamid = $_GET['teamid'];
		$aTeamMatchInfo = $this->MTeam->GetTeamMatchInfo($sTeamid, []); 
	}
}
