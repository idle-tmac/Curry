<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$appDir=realpath(dirname(__FILE__).'/../');
$imageDir=realpath(dirname(__FILE__).'/../image');

class CMatch extends CI_Controller {

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
	public function AddMatchFans() {
		$sMatchid = $_GET['matchid'];
		$sUserid = $_GET['userid'];
		$reponse = array();
		$aMatchFans = $this->MUser->GetUserMatchFans($sUserid);
		$bRet1 = false;
		$bRet2 =false;
		if(in_array($sMatchid, $aMatchFans)) {
			$bRet1 = $this->MUser->DelUserMatchFans($sUserid, $sMatchid);
		} else {
			$bRet2 = $this->MUser->AddUserMatchFans($sUserid, $sMatchid);
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
		$aTeamMatchInfo = $this->MTeam->GetTeamMatchInfo($sTeamid); 
		foreach($aTeamMatchInfo as $aMatchinfo) {
			$sMatchid = $aMatchinfo['matchid'];
			$sTeamid1 = $aMatchinfo['teamid1'];
			$sTeamid2 = $aMatchinfo['teamid2'];
			#get score 
			$aScore = $this->MMatch->GetMatchScoreInfo($sMatchid);
	
			$res = $this->MTeam->GetTeamInfo($sTeamid1, ['name', 'logoid']);
			
			//$teamName = $aMatchinfo[''];
			$MatchTime = $aMatchinfo['match_time'];
			$MatchAddress = $aMatchinfo['match_address'];
		//var_dump($aTeamMatchInfo);
		}
	}
}
