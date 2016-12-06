<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$appDir=realpath(dirname(__FILE__).'/../');
$imageDir=realpath(dirname(__FILE__).'/../image');

class CLeague extends CI_Controller {

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

		$this->aTeamStatisticItem = array('SCORE', 'BLACKBOARD', 'ASSIST', 'STEAL', 'BLOCK', 'THREEPOINTSCORE', 'PENALTYPOINTSCORE', 'MISTAKE', 'FOULS');	
		$this->aUserStatisticItem = array('SCORE', 'ASSIST', 'BLACKBOARD', 'OKSHOOTCNT', 'ALLSHOOTCNT', 'OKTHIRDCNT', 'ALLTHIRDCNT', 'OKPENALTYCNT', 'ALLPENALTYCNT', 'STEAL',  'BLOCK', 'MISTAKE', 'FOULS');
	}	

	
	public function index()
	{
		#$this->load->view('welcome_message');
		echo "hello umvp!";
	}
	
	
	/*
	beego.Router("/league/inschool/cells?schoolid=xxx&num=xxx&ticket=xxx", &controllers.LeagueController{}, "get:ReqinSchoolLeagueCells")
i	*/
	public function ReqinSchoolLeagueCells(){
		$schoolid = $_GET['schoolid'];
        $num = $_GET["num"];
        $ticket = $_GET["ticket"];
		$type = $this->config->item('MY_LEAGUE_INSCHOOL'); 	
		
		$rcells = array();
		$rcell = array();	
        	$cells = $this->MLeague->GetLeagueInfo($schoolid, $num, $ticket, $type);
		foreach($cells as $cell){
			$rcell["league_id"] = $cell['leagueid'];
			#rcell["league_logo_address"] = $imageDir + "/leagueLogoDir/$logoid.jpg";
			#rcell["league_poster_address"] = $imageDir + "/leaguePosterDir/$posterid.jpg"；
			$leagueid = $cell['leagueid'];
			$matchnum = $this->MMatch->GetMatchCntByLeagueid($leagueid);
			$finishmatchnum = $this->MMatch->GetOverMatchCntByLeagueid($leagueid);
			$rcell["league_match_finish_num"] = $finishmatchnum;
			$rcell["league_match_num"] = $matchnum;
			$rcell["league_name"] = $cell['name'];
			$rcell["league_team_num"] = $cell['team_num'];
			#$rcell["league_fans_num"] = $cell['team_fans'];
			$typeFlag = 'MY_LEAGUE_TYPE_' . $cell['league_type'];
			$rcell["league_type"] = $this->config->item($typeFlag);
			$rcell["league_start_date"] = strtotime($cell['start_time']);
			$rcell["league_end_date"] = strtotime($cell['end_time']);
			#$rcell["league_introduction"] = $cell['introduction'];
			$rcell["ticket"] = $cell['id'];
			$rcells[] = $rcell;
		}
		if(empty($rcells)) {
			$code = $this->config->item('MY_ECHO_FAIL');	
		} else {
			$code = $this->config->item('MY_ECHO_OK');
		}
		MessageEcho($code, "", $rcells);
	}
	/*
	 *  beego.Router("/league/inschool/cell", &controllers.LeagueController{}, "get:ReqinSchoolLeagueCell")
	 */
	public function ReqinSchoolLeagueCell() {
		$leagueid = $_GET['leagueid'];
		$userid = $_GET['userid'];
		$response = array();
		$cells = array();
		$cell = array();

		$leagueInfo = $this->MLeague->GetLeagueInfoById($leagueid);
		$cell["league_introduction"] = $leagueInfo['introduction'];
		$cell["league_name"] = $leagueInfo['name'];
		$cell["league_team_num"] = $leagueInfo['team_num'];
		$cell["league_fans_num"] = $leagueInfo['team_fans'];
		$matchnum = $this->MMatch->GetMatchCntByLeagueid($leagueid);
		$finishmatchnum = $this->MMatch->GetOverMatchCntByLeagueid($leagueid);
		$cell["league_match_finish_num"] = $finishmatchnum;
		$cell["league_match_num"] = $matchnum;
		$leagueArr = $this->MUser->GetUserLeagueFans($userid);
		$cell['focus'] = 0;
		if(in_array($leagueid, $leagueArr)) {
			$cell['focus'] = 1;
		}
		$response['league_info'] = $cell;
		
		$cell = array();

		$matches = $this->MMatch->GetMatchInfo($leagueid);
		foreach($matches as $match) {
			$cell["matchid"] = $match["matchid"];
			$cell["match_time"] = strtotime($match["match_time"]);
			$cell["match_address"] = $match["match_address"];
			$cell["teamid1"] = $match["teamid1"];
			$cell["teamid2"] = $match["teamid2"];
			$mresult = $this->MMatch->GetMatchScoreInfo($match["matchid"]);
			$cell["status"] = 0;
			if($mresult) {
				$cell["status"] = 2;
			}

			$team_cells = array();
	 		$team_cell = array();

			$teamid1 = $match["teamid1"];
			$teamInfo = $this->MTeam->GetTeamInfo($teamid1);
			$team_cell["team_id"] = $teamInfo["teamid"];
			$team_cell["team_name"] = $teamInfo["name"];
			$team_cell["score"] = 0;
			if($mresult) {
				$team_cell["score"] = $mresult[$teamid1];
			}
			#$team_cell["team_logo_address"] = imageServer + "/" + beego.AppConfig.String("leagueLogoDir") + "/" + teamInfo["logoid"] + ".jpg"
			$team_cells[] = $team_cell;
		
	 		$team_cell1 = array();
			$teamid2 = $match["teamid2"];
			$teamInfo = $this->MTeam->GetTeamInfo($teamid2);
			$team_cell1["team_id"] = $teamInfo["teamid"];
			$team_cell1["team_name"] = $teamInfo["name"];
			$team_cell1["score"] = 0;
			if($mresult) {
				$team_cell1["score"] = $mresult[$teamid2];
			}
			#team_cell1["team_logo_address"] = imageServer + "/" + beego.AppConfig.String("leagueLogoDir") + "/" + teamInfo["logoid"] + ".jpg"
			$team_cells[] = $team_cell1;
			$cell["match_info"] = $team_cells;
			$cells[] = $cell;	
       	 	}
		
		$response['matches'] = $cells;

		if(empty($cells)) {
			$code = $this->config->item('MY_ECHO_FAIL');	
		} else {
			$code = $this->config->item('MY_ECHO_OK');
		}
		MessageEcho($code, "", $response);
	}	
	public function AddLeagueFans() {
		$leagueid = $_GET['leagueid'];
		$userid = $_GET['userid'];
		$reponse = array();
		$leagueArr = $this->MUser->GetUserLeagueFans($userid);
		if(in_array($leagueid, $leagueArr)) {
			$res1 = $this->MLeague->DelLeagueFans($leagueid);
			$res2 = $this->MUser->DelUserLeagueFans($userid, $leagueid);
		} else {
			$res1 = $this->MLeague->AddLeagueFans($leagueid);
			$res2 = $this->MUser->AddUserLeagueFans($userid, $leagueid);
		}

		if($res1 && $res2) {
			$code = $this->config->item('MY_ECHO_OK');	
		} else {
			$code = $this->config->item('MY_ECHO_FAIL');	
		}
		MessageEcho($code);
	}
	public function ReqTeamMembers() {
		$ret = array();
		$teamid1 = $_GET['teamid1'];
		$teamid2 = $_GET['teamid2'];
		$teaminfo = $this->MMTeam->GetTeamMembers( $teamid1);
		if (!empty($teaminfo)) {
			$teaminfo = $teaminfo[0]['teaminfo'];
		}
		$ret[$teamid1] = $teaminfo;
		$teaminfo = $this->MMTeam->GetTeamMembers( $teamid2);
		if (!empty($teaminfo)) {
			$teaminfo = $teaminfo[0]['teaminfo'];
		}
		$ret[$teamid2] = $teaminfo;
		$jsonstr = json_encode($ret);
                echo $jsonstr;	
	}
	public function ReqStartData(){
		$userid = $_GET['userid'];
        	$matchid = $_GET['matchid'];
		$failStatus = $this->config->item('MY_ECHO_FAIL');
		$succStatus = $this->config->item('MY_ECHO_OK');
		
		$key = MY_REDIS_MATCH_SESSION . $matchid;
		if( ! $this->redis->exists($key)) {
			$this->redis->setex($key, self::TTL, $userid);
			 $code = $succStatus;//第一个人
		} else {
			$preid = $this->redis->get($key);
			if($preid != $userid){
				$code = $failStatus;//不是之前那个人		
			} else {

				$code = $succStatus;//是之前那个人
			}
		}
		if($code != $failStatus) {
			$mresult = $this->MMatch->GetMatchResultInfo($matchid);
			$res['match_data'] = $mresult;
		}

		MessageEcho($code, $code, "");
	}
	public function UploadMatchEvent() {
        $matchid = $_GET['matchid'];
		$msg = $_GET['param'];
		$eventInfo = json_decode($msg, true);
		$eventType = $eventInfo['event_type'];
		$playerid = $eventInfo['userid'];
		$playerNo =  $eventInfo['userNo'];
		$playerName = $eventInfo['userName'];
		$teamid1 = $eventInfo['teamid1'];
		$teamid2 = $eventInfo['teamid2'];
		$teamName1 = $eventInfo['teamname1'];
		$teamName2 = $eventInfo['teamname2'];
		$matchPattern = $eventInfo['match_pattern'];//3v3什么的
		$part = $eventInfo['part'];
		$eventTeamid = $eventInfo['event_teamid'];

		$sKey = MY_REDIS_MATCH_LIVE_STATISTIC . "_" . $matchid;
		if (!$this->redis->exists($sKey)) {
			$this->_InitializeStatisticRedisArray($sKey, $teamid1, $teamid2);
		}
		
		//更新redis
		$this->_UpdateRedisMatchInfo($matchid, $eventType, $eventTeamid, $part, $playerid);
		
		//generate text (time score part teamname playerNo playName eventType )
		$this->_GenerateLiveText($matchid, $teamid1, $teamid2, $teamName1, $teamName2, $part, $matchPattern, $eventType, $eventTeamid, $playerName);
	}
	public function ReqLiveMatchInfo() {
		$sTeamid1 = $_GET['teamid1'];
		$sTeamid2 = $_GET['teamid2'];
		$aTeaminfo = $this->MTeam->GetTeamInfo($sTeamid1);
		$sTeamName1 = $aTeaminfo['name'];
		$aTeaminfo = $this->MTeam->GetTeamInfo($sTeamid2);
		$sTeamName2 = $aTeaminfo['name'];
		
		$aUserInfo = $this->MTeam->GetTeamUserInfoByTeamid($sTeamid1, ['teamid', 'userid', 'playername', 'userno']);
		$aUseRet1 = array();
		foreach($aUserInfo as $aUser) {
			$aUseRet1[$aUser['userid']] = array(
				'username' => $aUser['playername'],
				'userno' => $aUser['userno']
			);
		}
		$aUserInfo = $this->MTeam->GetTeamUserInfoByTeamid($sTeamid2, ['teamid', 'userid', 'playername', 'userno']);
		$aUseRet2 = array();
		foreach($aUserInfo as $aUser) {
			$aUseRet2[$aUser['userid']] = array(
				'username' => $aUser['playername'],
				'userno' => $aUser['userno']
			);
		}
	
		$aLiveMatchInfo = array(
				$sTeamid1 => array('team_name' => $sTeamName1, 'team_members' => $aUseRet1),
				$sTeamid2 => array('team_name' => $sTeamName2, 'team_members' => $aUseRet2)
			);
		MessageEcho(1, "", $aLiveMatchInfo);

	}
	public function ReqMatchLiveHead(){
		$matchid = $_GET['matchid'];
		$response = array();
		$cell = array();
		$match = $this->MMatch->GetMatchInfoByMatchid($matchid);
		$cell["match_time"] = strtotime($match["match_time"]);
		$cell["match_address"] = $match["match_address"];
		$cell["teamid1"] = $match["teamid1"];
		$cell["teamid2"] = $match["teamid2"];
		$mresult = $this->MMatch->GetMatchScoreInfo($match["matchid"]);
		$cell["status"] = 0;
		if($mresult) {
			$cell["status"] = 2;
		}
		$response['match_info'] = $cell;
		
		$teamInfo = $this->MTeam->GetTeamInfo($match["teamid1"]);
		$fans = $this->MMatch->GetMatchTeamFans($matchid, $match["teamid1"]);
		$team_cell["team_id"] = $teamInfo["teamid"];
		$team_cell["team_name"] = $teamInfo["name"];
		$team_cell["fans"] = $fans;
		$team_cell["score"] = 16;
		$teamInfo = $this->MTeam->GetTeamInfo($match["teamid2"]);
		$fans = $this->MMatch->GetMatchTeamFans($matchid, $match["teamid2"]);
		$team_cell1["team_id"] = $teamInfo["teamid"];
		$team_cell1["team_name"] = $teamInfo["name"];
		$team_cell1["fans"] = $fans;
		$team_cell1["score"] = 18;
		$response['team_info'] = array($team_cell, $team_cell1);
		$code = $this->config->item('MY_ECHO_OK');
		MessageEcho($code, "", $response);
	}
	public function ReqMatchLiveMessage(){
		$matchid = $_GET['matchid'];
		//$userid = $_GET['userid'];
		$ticket = $_GET['ticket'];
		
		$key = MY_REDIS_MATCH_LIVE_TICKET . "_" . $matchid;	
		$maxTicket = $this->redis->get($key);

		#set context
		$response = array();
		$i = ($ticket == 0) ? 1 : $ticket;
		for(;$i <= $maxTicket; $i ++) {
			$key = MY_REDIS_MATCH_LIVE_MESSAGE . "_" . $matchid . "_" . $i;
			$response[] = $this->redis->get($key);
		}
		$code = $this->config->item('MY_ECHO_OK');
		MessageEcho($code, "", $response);
	}
	public function AddMatchTeamFans() {
		$matchid = $_GET['matchid'];
		$teamid = $_GET['teamid'];
		$response = array();
		$ret = $this->MMatch->AddMatchTeamFans($matchid, $teamid);
		if($ret) {
			$code = $this->config->item('MY_ECHO_OK');
		} else {
			$code = $this->config->item('MY_ECHO_FAIL');
		}
		MessageEcho($code);
	}
	protected function _updateRedisMatchInfo($matchid, $eventType, $eventTeamid, $part, $playerid){
		
		//update redis array
		//现在前台给了你一个事件
		//首先肯定取出数组来
		$sKey = MY_REDIS_MATCH_LIVE_STATISTIC . "_" . $matchid ;
		$rRes = $this->redis->get($sKey);
		$aRes = json_decode($rRes, true);
		$aMatchStatisticInfo = $this->config->item('MY_MATCH_STATISTIC');
		$aMatchEventInfo = $this->config->item('MY_MATCH_EVENT');
		$aMatchEventStatisticInfo = $this->config->item('MY_MATCH_EVENT_STATISTIC');
		$aMatchEvent = array_keys($aMatchEventInfo);
		$aScore = array(1 => 1, 3 => 2, 5 => 3);
		$iScore = 0;
		switch($eventType){
			case 1:  #罚球命中
			case 3:  #二分命中
			case 5:  #三分命中
				$iScore = $aScore[$eventType];
				UpdateSaiKuang($aRes[self::SAIKUANG],$eventTeamid,$part, $iScore);
			case 7:  #篮板
			case 9:  #助攻
				//UpdateBest($aRes[self::BEST],$aRes[self::PLAYERSTATISTIC],$eventTeamid);
			case 8:	 #抢断
			case 10: #盖帽
			case 11: #犯规
			case 12: #失误
				$aStatisticItems = array_intersect($this->aTeamStatisticItem, $aMatchEventStatisticInfo[$eventType]);
				UpdateTeamStatistic($aRes[self::TEAMSTATISTIC], $eventTeamid, $aStatisticItems, $aMatchStatisticInfo, $iScore);
			case 2: #罚球不中
			case 4:	#二分不中
			case 6: #三分不中
				$aStatisticItems = array_intersect($this->aUserStatisticItem, $aMatchEventStatisticInfo[$eventType]);
				UpdatePlayerStatistic($aRes[self::PLAYERSTATISTIC], $eventTeamid, $playerid, $aStatisticItems, $aMatchStatisticInfo, $iScore);
				break;
			default:break;
		}
		//把数据再存回去
		$sValue=json_encode($aRes);
		$bRet = $this->redis->setex($sKey, self::TTL, $sValue);
		if($bRet) {
            $code = $this->config->item('MY_ECHO_OK');
            MessageEcho($code);
		}
	}
	protected function _GenerateLiveText($matchid, $teamid1, $teamid2, $teamName1, $teamName2, $part, $matchPattern, $eventType, $eventTeamid, $playerName) {

		$key = MY_REDIS_MATCH_LIVE_STATISTIC . "_" . $matchid ;
		$res = $this->redis->get($key);
		$res = json_decode($res,true);
		$scoreArr = array($teamid1=>array_sum($res[self::SAIKUANG][$teamid1]), $teamid2=>array_sum($res[self::SAIKUANG][$teamid2]));
		$teamnameArr = array($teamid1 => $teamName1, $teamid2 => $teamName2);
		
		$partName = GetPartName($matchPattern, $part);
		$thirdArr = array($scoreArr[$teamid1] . '-' .  $scoreArr[$teamid2], $partName);
		
		$eventArr = $this->config->item('MY_MATCH_EVENT'); 
		$text =  $eventArr[$eventType];
		$fourthArr = array($teamnameArr[$eventTeamid], $playerName, $text);
		
		$time = strtotime(GetTime());
		$message = array($time, $scoreArr, $thirdArr, $fourthArr);
		
		$key = MY_REDIS_MATCH_LIVE_TICKET . "_" . $matchid;	
		$this->redis->incr($key);
		$ticket = $this->redis->get($key);

		#set context
		$key = MY_REDIS_MATCH_LIVE_MESSAGE . "_" . $matchid . "_" . $ticket;
		$this->redis->setex($key, self::TTL, json_encode($message));
	}
	protected function _InitializeStatisticRedisArray($sKey, $teamid1, $teamid2){
		//获取配置
		$aMatchStatisticInfo = $this->config->item('MY_MATCH_STATISTIC');

		$aStatisticRedis = array();

		//初始化赛况
		$aSaiKuang = InitSaiKuang($teamid1, $teamid2);

		//初始化最佳球员
		$aBestItem = InitBestItem($aMatchStatisticInfo, self::NO, self::PLAYERNAME);
		$aBest = array( $teamid1 => $aBestItem, $teamid2 => $aBestItem);

		//初始化球员统计		
		$aMatchStatisticInfo = $this->config->item('MY_MATCH_STATISTIC');
		$aTeamInfo = $this->MTeam->GetTeamUserInfoByTeamid($teamid1);
		$aPlayerInfos1 = InitPlayerStatistic($aTeamInfo, $aMatchStatisticInfo, $this->aUserStatisticItem);
		$aTeamInfo = $this->MTeam->GetTeamUserInfoByTeamid($teamid2);
		$aPlayerInfos2 = InitPlayerStatistic($aTeamInfo, $aMatchStatisticInfo,  $this->aUserStatisticItem);
		$aPlayerStatistic = array($teamid1 => $aPlayerInfos1, $teamid2 => $aPlayerInfos2);

		//初始化球队统计
		$aTeamStatistic = array(
			$teamid1 => InitTeamStatistic($aMatchStatisticInfo, $this->aTeamStatisticItem),
			$teamid2 => InitTeamStatistic($aMatchStatisticInfo, $this->aTeamStatisticItem)
		);

		$aStatisticRedis = array(
			self::SAIKUANG => $aSaiKuang,
			self::BEST => $aBest,
			self::PLAYERSTATISTIC => $aPlayerStatistic,
			self::TEAMSTATISTIC => $aTeamStatistic
		);

		$sStatisticRedis = json_encode($aStatisticRedis);
		$this->redis->setex($sKey, self::TTL, $sStatisticRedis);
	}
}

	
