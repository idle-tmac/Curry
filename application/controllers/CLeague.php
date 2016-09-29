<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$appDir=realpath(dirname(__FILE__).'/../');
$imageDir=realpath(dirname(__FILE__).'/../image');
require_once($appDir."/libraries/util.php");

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
		$this->redis = $this->MRedis->_getInstance();
	}	

	
	public function index()
	{
		#$this->load->view('welcome_message');
		echo "hello huyong!";
	}
	
	
	/*
	beego.Router("/CLeague/cells?type=xxx&schoolid=xxx&num=xxx&ticket=xxx", &controllers.LeagueController{}, "get:ReqinSchoolLeagueCells")
	*/
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
			$rcell["process"] = $finishmatchnum / $matchnum;
			$rcell["league_name"] = $cell['name'];
			$rcell["league_team_num"] = $cell['team_num'];
			$rcell["league_fans_num"] = $cell['team_fans'];
			$rcell["league_type"] = $cell['league_type'];
			$rcell["league_start_date"] = $cell['start_time'];
			$rcell["league_end_date"] = $cell['end_time'];
			$rcell["ticket"] = $cell['id'];
			$rcells[] = $rcell;
		}
		$jsonstr = json_encode($rcells);
		echo $jsonstr;
	}
	/*
	 *  beego.Router("/league/inschool/cell", &controllers.LeagueController{}, "get:ReqinSchoolLeagueCell")
	 */
	public function ReqinSchoolLeagueCell() {
		$schoolid = $_GET['schoolid'];
		$leagueid = $_GET['leagueid'];
		
		$cells = array();
		$cell = array();
		$matches = $this->MMatch->GetMatchInfo($schoolid, $leagueid);
		foreach($matches as $match) {
			$cell["matchid"] = $match["matchid"];
			$cell["match_time"] = $match["match_time"];
			$cell["match_address"] = $match["match_address"];
			$cell["teamid1"] = $match["teamid1"];
			$cell["teamid2"] = $match["teamid2"];
			$cell["match_time"] = $match["match_time"];
			$cell["match_address"] = $match["match_address"];
			$cell["is_end"] = $match["status"];

			$team_cells = array();
	 		$team_cell = array();

			$teamid1 = $match["teamid1"];
			$teamInfo = $this->MTeam->GetTeamInfo($teamid1);
			$team_cell["team_id"] = $teamInfo["teamid"];
			$team_cell["team_name"] = $teamInfo["name"];
			#$team_cell["team_logo_address"] = imageServer + "/" + beego.AppConfig.String("leagueLogoDir") + "/" + teamInfo["logoid"] + ".jpg"
			$team_cell["is_home_team"] = "1";
			$team_cells[] = $team_cell;
		
	 		$team_cell1 = array();
			$teamid2 = $match["teamid2"];
			$teamInfo = $this->MTeam->GetTeamInfo($teamid2);
			$team_cell1["team_id"] = $teamInfo["teamid"];
			$team_cell1["team_name"] = $teamInfo["name"];
			#team_cell1["team_logo_address"] = imageServer + "/" + beego.AppConfig.String("leagueLogoDir") + "/" + teamInfo["logoid"] + ".jpg"
			$team_cell1["is_home_team"] = "0";
			$team_cells[] = $team_cell1;
			$cell["matches"] = $team_cells;
			$cells[] = $cell;	
       	 	}
		$jsonstr = json_encode($cells);
		echo $jsonstr;
	}	
}

	
