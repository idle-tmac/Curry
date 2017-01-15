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
		$this->load->config('config_ability', TRUE);
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
	public function TeamManageRead() {
		$sTeamid = $_GET['teamid'];
		$aTeamInfo = $this->MTeam->GetTeamInfo($sTeamid,	['name', 'address', 'createrid', 'createrid', 'introduction']);
		$sCreaterid = $aTeamInfo['createrid'];
		$aUser = $this->MUser->GetUserInfoByUserid($sCreaterid, ['username']);
		$sUserName = $aUser['username'];
		$aResponse = array(
			'name' => $aTeamInfo['name'],
			'address' => $aTeamInfo['address'],
			'creater' => $sUserName,
			'adminitor' => $sUserName,
			'introduction' => $aTeamInfo['introduction'],
		);
		MessageEcho(1, "", $aResponse);
	}
	public function TeamManageSet(){
		$sTeamid = $_GET['teamid'];
		$sTeaminfoItem = $_GET['type'];
		$sText = $_GET['text'];
	 	$aTeamInfoMap = array(
            "",
            'name',
            'address',
			'introduction'
        );
		$aUpdateInfo = array($aTeamInfoMap[$sTeaminfoItem] => "'$sText'");
		$bRet = $this->MTeam->UpdateTeamInfo($sTeamid, $aUpdateInfo);
		if($bRet) {
			MessageEcho(1);
		} else {
			MessageEcho(0);	
		}
	}
	public function TeamManageMembers(){
		$sTeamid = $_GET['teamid'];
		$sType = 1;
		$aApplication = $this->MTeam->GetJoinApplication($sTeamid, $sType);
		$aAppInfo = array();
		foreach($aApplication as $aItem) {
			$sWantJoinid = $aItem['wantjoinid'];
			$sJoinid = $aItem['joinid'];
		 	$sStatus = $aItem['status'];
			if($sStatus == 1) {
				$aUserInfo = $this->MUser->GetUserInfoByUserid($sJoinid, ['username', 'position']);
				$aAppInfo[] = $aUserInfo;
			}
		}
		$aTeamUserInfo = $this->MTeam->GetTeamUserInfoByTeamid($sTeamid, ['playername', 'position']);
		$aResponse = array('application' => $aAppInfo, 'hasmembers' => $aTeamUserInfo);
        MessageEcho(1, "", $aResponse);
	}
	public function TeamManageDeleteMembers(){
		$sTeamid = $_GET['teamid'];
		$sUserid = $_GET['userid'];
		$bRet = $this->MTeam->DeleteTeamUserInfo($sTeamid, $sUserid);	
		if($bRet) {
			MessageEcho(1);
		} else {
			MessageEcho(0);	
		}
	}
	public function TeamManageAcceptMembers(){
		$sTeamid = $_GET['teamid'];
		$sUserid = $_GET['userid'];
		
		#get userinfo
		$aUserInfo = $this->MUser->GetUserInfoByUserid($sUserid, ['username', 'position', 'playerno']);
		$sTime = GetTime();
		$sName = $aUserInfo['username'];
		$sNo = $aUserInfo['playerno'];
		$sPosition = $aUserInfo['position'];
		
		#update user_team table
		$aUserTeamInfo = array(
			'teamid' => $sTeamid,
			'userid' => $sUserid,
			'playername' => "'$sName'",
			'userno' => "'$sNo'",
			'position' => "'$sPosition'",
			'create_time' => "'$sTime'"
		);
		$bRet = $this->MTeam->InsertUserTeamInfo($aUserTeamInfo);
		if($bRet) {
			MessageEcho(1);
		} else {
			MessageEcho(0);	
		}
		
	}
	public function TeamJoin(){
		$sUserid = $_GET['userid'];
		$sTeamid = $_GET['teamid'];
		$sTime = GetTime();
		$aJoinItem = array(
			'wantjoinid' => $sTeamid,
  			'joinid' => $sUserid,
 			'type' => 1,
			'status' => 1,
			'create_time' => "'$sTime'",
			'modify_time' => "'$sTime'"
		);
		$bRet = $this->MTeam->InsertJoinApplication($aJoinItem);
		if($bRet) {
			MessageEcho(1);
		} else {
			MessageEcho(0);
		}
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
		$aTeamMatchInfo = $this->MTeam->GetTeamMatchInfo($sTeamid); 
		$ret = array();
		foreach($aTeamMatchInfo as $aMatchinfo) {
			$sMatchid = $aMatchinfo['matchid'];
			$sTeamid1 = $aMatchinfo['teamid1'];
			$sTeamid2 = $aMatchinfo['teamid2'];
			#get score 
			$aScore = $this->MMatch->GetMatchScoreInfo($sMatchid);
			if(empty($aScore)) {
				$aScore["$sTeamid1"] = 0;
				$aScore["$sTeamid2"] = 0;
			}
			$teamInfo = array();	
			$res = $this->MTeam->GetTeamInfo($sTeamid1, ['name', 'logoid']);
			$teamInfo[] = array('teamid'=> $sTeamid1, 'name' => $res['name'], 'score' => $aScore["$sTeamid1"]);
			$res = $this->MTeam->GetTeamInfo($sTeamid2, ['name', 'logoid']);
			$teamInfo[] = array('teamid'=> $sTeamid1, 'name' => $res['name'], 'score' => $aScore["$sTeamid2"]);
			
			//$teamName = $aMatchinfo[''];
			$MatchTime = $aMatchinfo['match_time'];
			$MatchStatus = $aMatchinfo['status'];
			$MatchFansNum = $aMatchinfo['fans_num'];
			$MatchAddress = $aMatchinfo['match_address'];
			$ret[] = array(
				'match_time' => $MatchTime,
				'match_status' => $MatchStatus,
				'match_focus_num' => $MatchFansNum,
				'match_address' => $MatchAddress,
				'teaminfo' => $teamInfo
			);
		}
		MessageEcho(1, "", $ret);
	}
	public function ReqTeamStatistic() {
		$sTeamid = $_GET['teamid'];
		$aTeamAbility  = $this->config->item('team_ability', 'config_ability');	
		
		$aResponse = array();
		
		$aTeamInfo = $this->MMatch->GetTeamMatchResultInfoByTeamid($sTeamid, ['left(create_time,4) as year', 'substring(create_time, 6, 5) as date', 'score', 'blackboard', 'assist', 'steal', 'block', 'mistake', 'okshootcnt', 'allshootcnt', 'okthirdcnt', 'allthirdcnt', 'okpenaltycnt', 'allpenaltycnt', 'matchid' ]);
		$aAbility = array();
		$aTeamYearInfo = array();
		foreach($aTeamInfo as $yearInfo) {
			$aTeamYearInfo[$yearInfo['year']][] = $yearInfo;
		}
		$response = array();
		foreach($aTeamYearInfo as $year => $teamInfo) {
			if(!isset($aAbility[$year])) {
				$aAbility[$year] = array();
			}
	
			# reverse 
			$aReverseInfo = ReveseArray($teamInfo);	

			#get zhu xing 
			$zhuXing = array();
			$aZhuxingKey = array('score', 'blackboard', 'assist', 'steal', 'block');
			foreach($aZhuxingKey as $key ) {
				$val = $aReverseInfo[$key];
				$aItem = array();
				$aItem[$key] = array_sum($val) / count($val);
				$aItem['ratio'] = $aItem[$key] / $aTeamAbility[$key];
				$zhuXing[] = $aItem;
			}
			# get match 
			$aMatchInfo = array();
			foreach($teamInfo as $matchInfo) {
				$sMatchid = $matchInfo['matchid'];
				$aTeams = $this->MMatch->GetMatchInfoByMatchid($sMatchid, ['teamid1', 'teamid2']);
				$oppTeamid = ($aTeams['teamid1'] == $sTeamid ? $aTeams['teamid2'] : $aTeams['teamid1']);
				$aScore = $this->MMatch->GetMatchScoreInfo($sMatchid);
				$sScoreStr = $aScore[$sTeamid] . "-" . $aScore[$oppTeamid];
				$aOppName = $this->MTeam->GetTeamInfo($oppTeamid, ['name']);
				$shootrate = ($matchInfo['okshootcnt'] == 0 ? 0 : $matchInfo['okshootcnt']/$matchInfo['allshootcnt']);
				$threerate = ($matchInfo['okthirdcnt'] == 0 ? 0 : $matchInfo['okthirdcnt']/$matchInfo['allthirdcnt']);
				$penaltyrate = ($matchInfo['okpenaltycnt'] == 0 ? 0 : $matchInfo['okpenaltycnt']/$matchInfo['allpenaltycnt']);
				
				$aMatchInfo[] = array(
					'date' => $matchInfo['date'],
					'score' => $sScoreStr,
					'opponents' => $aOppName['name'],
					'blackboard' => $matchInfo['blackboard'], 
					'assist' => $matchInfo['assist'],
					'steal' => $matchInfo['steal'], 
					'block' => $matchInfo['block'],
					'mistake' => $matchInfo['mistake'],
					'shootrate' => $shootrate, 
					'threerate' => $threerate, 
					'penaltyrate' => $penaltyrate
				);
			}
			#get average
			$aAverageKey1 = array('score', 'blackboard', 'assist', 'steal', 'block', 'mistake');
			$aAverageKey2 = array('okshootcnt', 'allshootcnt', 'okthirdcnt', 'allthirdcnt', 'okpenaltycnt', 'allpenaltycnt');
			$aMatchAverage = array();
			foreach($aAverageKey1 as $key) {
				$val = $aReverseInfo[$key];
                $aMatchAverage[$key] = array_sum($val) / count($val);
            }
			$okshootcnt = array_sum($aReverseInfo['okshootcnt']);
			$allshootcnt = array_sum($aReverseInfo['allshootcnt']);
			$okthirdcnt = array_sum($aReverseInfo['okthirdcnt']);
			$allthirdcnt = array_sum($aReverseInfo['allthirdcnt']);
			$okpenaltycnt = array_sum($aReverseInfo['okpenaltycnt']);
			$allpenaltycnt = array_sum($aReverseInfo['allpenaltycnt']);

			$shootrate = ($okshootcnt == 0 ? 0 : $okshootcnt/$allshootcnt);
            $aMatchAverage['shootrate'] = $shootrate;
			$threerate = ($okthirdcnt ==0 ? 0: $okthirdcnt/$allthirdcnt);
			$aMatchAverage['threerate'] = $threerate;
			$penaltyrate = ($okpenaltycnt == 0 ? 0: $okpenaltycnt/$allpenaltycnt);
			$aMatchAverage['penaltyrate'] = $penaltyrate;
			$yearAbility = array('diagram' => $zhuXing, 'match' => $aMatchInfo, 'average' => $aMatchAverage);
			$response[$year] = $yearAbility;
		}
		MessageEcho(1, "", $response);
	}
	public  function ReqTeamMembers() {
		$sTeamid = $_GET['teamid'];
		$aTeamUserInfo = $this->MTeam->GetTeamUserInfoByTeamid($sTeamid, ['playername', 'userno', 'position']);
		MessageEcho(1, "", $aTeamUserInfo);
	}
	public  function ReqTeamGlory() {
		$sTeamid = $_GET['teamid'];
		$aTeamGlory = $this->MTeam->GetTeamGlory($sTeamid, ['leagueid', 'glory', 'left(create_time,4) as year']);
		$response = array();
		foreach($aTeamGlory as $aItem) {
			$sYear = $aItem['year'];
			$leagueid = $aItem['leagueid'];
			echo $leagueid . "\n";
			$glory = ($aItem['glory'] == 1 ? '冠军' : ($aItem['glory'] == 2 ? '亚军' : '季军'));
			$aRet = $this->MLeague->GetLeagueInfoById($leagueid);
			$name = $aRet['name'];
			$response[] = array(
				'league_name' => $name,
				'year' => $sYear,
				'glory' => $glory
			);
		}
		MessageEcho(1, "", $response);
	}
	public function TeamManageDeleteAdminitor() {
		$sTeamid = $_GET['teamid'];
		$sAdminitorid = $_GET['adminitorid'];
		$res = $this->MTeam->DeleteTeamAdminitorInfo($sTeamid, $sAdminitorid);	
		if($res) {
			MessageEcho(1);
		} else {
			MessageEcho(0);
		}
    }
	public function TeamManageAddAdminitor() {
		$sTeamid = $_GET['teamid'];
		$sAdminitorid = $_GET['adminitorid'];
		$sTime = GetTime();
		$aAdminitorInfo = array(
			'teamid' => $sTeamid,
			'adminitorid' => $sAdminitorid,
			'create_time' => "'$sTime'"
		);
		$res = $this->MTeam->AddTeamAdminitorInfo($aAdminitorInfo);
		if($res) {
			MessageEcho(1);
		} else {
			MessageEcho(0);
		}
	}
	public function TeamManageAdminitors(){
		$sTeamid = $_GET['teamid'];
		$aTeamAdminitor = $this->MTeam->GetTeamAdminitorInfoByTeamid($sTeamid);	
		$aHasAdminitor = array();
		$aAdminitorids = array();
		foreach($aTeamAdminitor as $aInfo) {
			$sAdminitorid = $aInfo['adminitorid'];
			$aAdminitorids[$sAdminitorid] = 1;
			$aUserTeamInfo = $this->MTeam->GetUserTeamInfoByTeamidUserid($sTeamid, $sAdminitorid);
        	$sPlayerName = $aUserTeamInfo['playername'];
        	$sPosition = $aUserTeamInfo['position'];
			$aHasAdminitor[] = array('userid' => $sAdminitorid, 'username' => $sPlayerName, 'position' => $sPosition);
		}
		$aTeamNoAdminitor = array();
		$aTeamUserInfo = $this->MTeam->GetTeamUserInfoByTeamid($sTeamid, ['playername', 'position', 'userid']);
		foreach($aTeamUserInfo as $aUserInfo){
			$userid = $aUserInfo['userid'];
			if(isset($aAdminitorids[$userid])) {
				continue;	
			}
			$aTeamNoAdminitor[] = $aUserInfo;
		}
		$aResponse = array('has_adminitors' => $aHasAdminitor, 'teammembers' => $aTeamNoAdminitor);
        MessageEcho(1, "", $aResponse);
	}
}
