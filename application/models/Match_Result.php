<?php
	$appDir=realpath(dirname(__FILE__).'/../');
	require_once($appDir."/models/db_base.php");
	class Match_Result extends CI_Model {
		public function __construct() {
			parent::__construct();
			$this->tmacDB = db_base::getInstance("tmac");
		}
		public function GetMatchInfo($schoolid, $leagueid) {//(cells []map[string]string) {
        		$sql = "select teamid1,teamid2,match_time,match_address,status,matchid from `match` 
				where  schoolid = $schoolid and leagueid = $leagueid";
			$res = $this->tmacDB->get_data($sql);
        		return $res;
		}
		public function GetMatchResultInfo($matchid){// (cells []map[string]string) {
        		$sql = "select matchid, teamid, firstpart, secondpart, thirdpart, fourthpart, score, penalty_hit_rate, twopoint_hit_rate, threepoint_hit_rate, backboard, foul, assist, steal, mistake, modify_time, create_time  from match_result where  matchid = $matchid";
			$res = $this->tmacDB->get_data($sql);
        		return $res;
 		}
	}	
