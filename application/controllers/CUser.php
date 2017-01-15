<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$appDir=realpath(dirname(__FILE__).'/../');
$imageDir=realpath(dirname(__FILE__).'/../image');

class CUser extends CI_Controller {

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
	public function ReqUserHead(){
        $sUserid = $_GET['owner_userid'];
        $sVisitorid = $_GET['visitor_userid'];
		$aRet = $this->MUser->GetUserInfoByUserid($sUserid, ['username', 'position', 'height', 'weight', 'fans_num']);
		$aUserTeam = $this->MUser->GetTeamUserInfoByUserid($sUserid, ['teamid']);
		$iMatchNum = 0;
		foreach($aUserTeam as $aTeam) {
			$sTeamid = $aTeam['teamid'];
		 	$aTeamMatch = $this->MTeam->GetTeamMatchInfo($sTeamid, ['status']);
			$aStatus = ReveseArray($aTeamMatch);
			$iMatchNum = $iMatchNum + array_sum($aStatus['status']);
		}
        $aUserFans = $this->MUser->GetUserUserFans($sVisitorid);
		$focus = 0;
        if(in_array($sUserid, $aUserFans)) {
			$focus = 1;
		}
		$response = array(
			'username' => $aRet['username'],
			'position' => $aRet['position'],
			'height' => $aRet['height'],
			'weight' => $aRet['weight'],
			'fans_num' => $aRet['fans_num'],
			'match_num' => $iMatchNum,
			'focus' => $focus
		);
		MessageEcho(1, "", $response);
	}
	public function AddUserFans() {
        $sOwnerid = $_GET['owner_userid'];
        $sVisitorid = $_GET['visitor_userid'];
        $reponse = array();
        $aUserFans = $this->MUser->GetUserUserFans($sVisitorid);
        $bRet1 = false;
        $bRet2 =false;
        if(in_array($sOwnerid, $aUserFans)) {
            $bRet1 = $this->MUser->DelUserUserFans($sVisitorid, $sOwnerid);
       } else {
            $bRet2 = $this->MUser->AddUserUserFans($sVisitorid, $sOwnerid);
        }
        if($bRet1 || $bRet2) {
            $code = $this->config->item('MY_ECHO_OK');
        } else {
            $code = $this->config->item('MY_ECHO_FAIL');
        }
        MessageEcho($code);
    }
 	public function ReqUserBattle() {
		$sUserid = $_GET['userid'];
		$sTeamids=$this->MUser->GetTeamUserInfoByUserid($sUserid, ['teamid']);
        $ret = array();
		foreach($sTeamids as $sTeamid) {
			$sTeamid = $sTeamid['teamid'];
        	$aTeamMatchInfo = $this->MTeam->GetTeamMatchInfo($sTeamid);
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
		}
        MessageEcho(1, "", $ret);
	}
	public function ReqUserStatistic(){
		//$sMatchid = $_GET['match_id'];
		$sUserid = $_GET['userid'];
		$aUserAbility  = $this->config->item('user_ability', 'config_ability');	
		$aUserMatchInfo = $this->MUser->GetUserMatchResultInfo($sUserid, ['*', 'substr(create_time, 6, 5) as day', 'left(create_time, 4) as year']);		 
		$aAbility = array();
		$aUserInfo = array();
		foreach($aUserMatchInfo as $aYearUserInfo) {
			$aUserYearInfo[$aYearUserInfo['year']][] = $aYearUserInfo;
		}
		foreach($aUserYearInfo as $year => $userInfo) {
            if(!isset($aAbility[$year])) {
                $aAbility[$year] = array();
            }

            # reverse
            $aReverseInfo = ReveseArray($userInfo);

            #get zhu xing
            $zhuXing = array();
            $aZhuxingKey = array('score', 'blackboard', 'assist', 'steal', 'block');
            foreach($aZhuxingKey as $key ) {
                $val = $aReverseInfo[$key];
                $aItem = array();
                $aItem[$key] = array_sum($val) / count($val);
                $aItem['ratio'] = $aItem[$key] / $aUserAbility[$key];
                $zhuXing[] = $aItem;
			}
			foreach($userInfo as $matchInfo) {
                $sMatchid = $matchInfo['matchid'];
				$sTeamid = $matchInfo['teamid'];
                $aTeams = $this->MMatch->GetMatchInfoByMatchid($sMatchid, ['teamid1', 'teamid2']);
                $oppTeamid = ($aTeams['teamid1'] == $sTeamid ? $aTeams['teamid2'] : $aTeams['teamid1']);
                $aScore = $this->MMatch->GetMatchScoreInfo($sMatchid);
                $sScoreStr = $aScore[$sTeamid] . "-" . $aScore[$oppTeamid];
                $aOppName = $this->MTeam->GetTeamInfo($oppTeamid, ['name']);
                $shootrate = ($matchInfo['okshootcnt'] == 0 ? 0 : $matchInfo['okshootcnt']/$matchInfo['allshootcnt']);
                $threerate = ($matchInfo['okthirdcnt'] == 0 ? 0 : $matchInfo['okthirdcnt']/$matchInfo['allthirdcnt']);
                $penaltyrate = ($matchInfo['okpenaltycnt'] == 0 ? 0 : $matchInfo['okpenaltycnt']/$matchInfo['allpenaltycnt']);

                $aMatchInfo[] = array(
                    'date' => $matchInfo['day'],
                    'score' => $sScoreStr,
                    'opponents' => $aOppName['name'],
                    'blackboard' => $matchInfo['blackboard'],
                    'assist' => $matchInfo['assist'],
                    'steal' => $matchInfo['steal'],
                    'block' => $matchInfo['block'],
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
 	public function ReqUserJoinTeamLeague(){
		$sUserid = $_GET['userid'];
		$sTeamids=$this->MUser->GetTeamUserInfoByUserid($sUserid, ['teamid']);
		$aResponse = array();
		$aTeam = array();
		foreach($sTeamids as $sTeamid) {
			$sTeamid = $sTeamid['teamid'];
			$sNameInfo = $this->MTeam->GetTeamInfo($sTeamid, ['name']);
			$sName = $sNameInfo['name'];
			$aTeam[] = array('teamid' => $sTeamid, 'teamname' => $sName);
		}
	
		$aLeauges = $this->MUser->GetUserMatchResultInfo($sUserid, ['leagueid']);	
		$aLeague = array();
		foreach($aLeauges as $sLeagueid) {
			$sLeagueid = $sLeagueid['leagueid'];
			$aLeagueInfo = $this->MLeague->GetLeagueInfoById($sLeagueid);	
			$sName = $aLeagueInfo['name'];
			$aLeague[] = array('leagueid' => $sLeagueid, 'leaguename' => $sName);
		}
		$aResponse['team'] = $aTeam;
		$aResponse['league'] = $aLeague;
		$this->aResponse = $aResponse;
		MessageEcho(1, "", $aResponse);
	}
	public function UserManage(){
		$sUserid = $_GET['userid'];
        $sTeamids=$this->MUser->GetTeamUserInfoByUserid($sUserid, ['teamid']);
        $aResponse = array();
        $aTeam = array();
        foreach($sTeamids as $sTeamid) {
            $sTeamid = $sTeamid['teamid'];
            $sNameInfo = $this->MTeam->GetTeamInfo($sTeamid, ['name']);
            $sName = $sNameInfo['name'];
            $aTeam[] = array('teamid' => $sTeamid, 'teamname' => $sName);
        }

        $aLeauges = $this->MUser->GetUserMatchResultInfo($sUserid, ['leagueid']);
        $aLeague = array();
        foreach($aLeauges as $sLeagueid) {
            $sLeagueid = $sLeagueid['leagueid'];
            $aLeagueInfo = $this->MLeague->GetLeagueInfoById($sLeagueid);
            $sName = $aLeagueInfo['name'];
            $aLeague[] = array('leagueid' => $sLeagueid, 'leaguename' => $sName);
        }
        $aResponse['team'] = $aTeam;
        $aResponse['league'] = $aLeague;
        $this->aResponse = $aResponse;
		MessageEcho(1, "", $this->aResponse);
	}
	public function UserInfoSet(){
		$sUserid = $_GET['userid'];
		$jParam = $_GET['param'];
		$aParam= json_decode($jParam, true);
		$sName =  $aParam['name'];
		$sBirthDay = $aParam['birthday'];
		$sSex = $aParam['sex']; 
		$sHeight = $aParam['height']; 
		$sWeight = $aParam['weight']; 
		$sAddress = $aParam['address'];
		$sPosition = $aParam['position'];
		$sPlayerNo = $aParam['playerno'];
		$sShoe = $aParam['shoe'];
		$aUserInfo = array(
			'username' => "'$sName'",
			'birthday' => "'$sBirthDay'",
			'sex' => $sSex,
			'height' => $sHeight,
			'weight' => $sWeight,
			'address' => "'$sAddress'",
			'position' => "'$sPosition'",
			'playerno' => "'$sPlayerNo'",
			'shoeno' => "'$sShoe'"
		);
		$bRet = $this->MUser->InsertLeagueInfo($sUserid, $aUserInfo);	
		if ($bRet) {
			MessageEcho(1);
		} else {
			MessageEcho(0);
		}
	}
	public function UserInfoRead(){
		$sUserid = $_GET['userid'];
		$aUserInfo = $this->MUser->GetUserInfoByUserid($sUserid, ['username', 'birthday', 'sex', 'height', 'weight', 'address', 'position', 'playerno', 'shoeno']); 		
		if($aUserInfo) {
			MessageEcho(1, "", $aUserInfo);
		} else {
			MessageEcho(0, "", array());
		}
	}
	public function ReqUserAsAdminitor(){

	}
	public function RegisterPasswdUpLoad(){

	}
}
