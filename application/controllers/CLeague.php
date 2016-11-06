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

	public function __construct() {
		parent::__construct();
		$this->load->model('MMatch');
		$this->load->model('MLeague');
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
			$rcell["league_fans_num"] = $cell['team_fans'];
			$typeFlag = 'MY_LEAGUE_TYPE_' . $cell['league_type'];
			$rcell["league_type"] = $this->config->item($typeFlag);
			$rcell["league_start_date"] = strtotime($cell['start_time']);
			$rcell["league_end_date"] = strtotime($cell['end_time']);
			$rcell["league_introduction"] = $cell['introduction'];
			$rcell["ticket"] = $cell['id'];
			$rcells[] = $rcell;
		}
		if(empty($recells)) {
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
		
		$cells = array();
		$cell = array();
		$matches = $this->MMatch->GetMatchInfo($leagueid);
		foreach($matches as $match) {
			$cell["matchid"] = $match["matchid"];
			$cell["match_time"] = strtotime($match["match_time"]);
			$cell["match_address"] = $match["match_address"];
			$cell["teamid1"] = $match["teamid1"];
			$cell["teamid2"] = $match["teamid2"];
			$mresult = $this->MMatch->GetMatchScoreInfo($match["matchid"]);
			$cell["is_end"] = 0;
			if($mresult) {
				$cell["is_end"] = 1;
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
			$cell["matches"] = $team_cells;
			$cells[] = $cell;	
       	 	}
		if(empty($cells)) {
			$code = $this->config->item('MY_ECHO_FAIL');	
		} else {
			$code = $this->config->item('MY_ECHO_OK');
		}
		MessageEcho($code, $code, $cells);
	}	
	public function AddLeagueFans() {
		$leagueid = $_GET['leagueid'];
		$r = array();
		$res = $this->MLeague->AddLeagueFans($leagueid);
		if($res) {
			$code = $this->config->item('MY_ECHO_OK');	
		} else {
			$code = $this->config->item('MY_ECHO_FAIL');	
		}
		MessageEcho($code, $code, array());
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
		
		$prefix = $this->config->item('MY_REDIS_MATCH_SESSION');
		$key = $prefix . $matchid;
		if( ! $this->redis->exists($key)) {
			$this->redis->set($key, $userid);
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
	public function MatchEvent() {
                $matchid = $_GET['matchid'];
		$msg = $_GET['message'];
		$eventInfo = json_decode($msg);
		$eventType = $eventInfo['event_type'];
		$playerid = $eventInfo['userid'];
		$playerNo =  $eventInfo['userNo'];
		$playerName = $eventInfo['userName'];
		$teamid1 = $eventInfo['teamid1'];
		$teamid2 = $eventInfo['teamid2'];
		$teamName1 = $eventInfo['teamname1'];
		$teamName2 = $eventInfo['teamname2'];
		$matchPattern = $eventInfo['match_pattern'];
		$part = $eventInfo['part'];
		$eventTeamid = $eventInfo['event_teamid'];
		//update redis array
		//updateRedisMatchInfo($matchid, $eventType, $playerid , $part);


		//generate text (time score part teamname playerNo playName eventType )
		$time = date('H:i:s', time());
		$scoreArr = GetTotalScoreFromRedis(); //array(1=>10,2=>13)
		$teamnameArr = array($teamid1 => $teamName1, $teamid2 => $teamName2);
		
		$partName = GetPartName($matchPattern, $part);
		$thirdArr = array($scoreArr[$teamid1] . '-' .  $scoreArr[$teamid2], $partName);
		
		$eventArr = $this->config->item('MY_MATCH_EVENT'); 
		$text =  $eventArr[$eventType];
		$fourthArr = array($teamnameArr[$eventTeamid] . " " . $playName . " " . $text;
		
		$message = array($time, $scoreArr, $thirdArr, $fourthArr);

		
		$code = $this->config->item('MY_ECHO_OK');
		MessageEcho($code, "", $message);

	}
}

	
