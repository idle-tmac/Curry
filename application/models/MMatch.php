<?php
	$appDir=realpath(dirname(__FILE__).'/../');
	require_once($appDir."/models/db_base.php");
	class MMatch extends CI_Model {
		public function __construct() {
			parent::__construct();
			$this->load->database();
			$this->tmacDB = db_base::getInstance("tmac");
		}
		public function GetMatchInfo($leagueid) {//(cells []map[string]string) {
        		$sql = "select teamid1,teamid2,match_time,match_address,status,matchid from `match` 
				where leagueid = $leagueid";
			$res = $this->tmacDB->get_data($sql);
        		return $res;
		}
		public function GetMatchInfoByMatchid($matchid) {//(cells []map[string]string) {
        		$sql = "select * from `match` where leagueid = $matchid";
			$res = $this->tmacDB->get_data($sql);
			if(empty($res)) {
				return array();
			}
			return $res[0];
		}
		public function GetMatchCntByLeagueid($leagueid) {
        		$sql = "select count(*) as num from `match` where  leagueid = $leagueid";
			$res = $this->tmacDB->get_data($sql);
        		return $res[0]['num'];
		}
		public function GetOverMatchCntByLeagueid($leagueid) {
        		$sql = "select count(distinct matchid) as num from match_result where  leagueid = $leagueid";
			$res = $this->tmacDB->get_data($sql);
        		return $res[0]['num'];
		}
		public function GetMatchScoreInfo($matchid) {
        		$sql = "select teamid,score from match_result where  matchid = $matchid";
			$res = $this->tmacDB->get_data($sql);
			if(empty($res)) {
				return array();
			}
			$score = array();

			$id = $res[0]['teamid'];
			$fen = $res[0]['score'];
			$score[$id] =  $fen;

			$id = $res[1]['teamid'];
			$fen = $res[1]['score'];
			$score[$id] =  $fen;

        		return $score;
		}
		public function GetMatchResultInfo($matchid) {
        		$sql = "select * from match_result where  matchid = $matchid";
			$res = $this->tmacDB->get_data($sql);
			if(empty($res)) {
				return array();
			}
			$id = $res[0]['teamid'];
                        $score[$id] = $res[0];

                        $id = $res[1]['teamid'];
                        $score[$id] = $res[1];
			return $score;
		}
		public function AddMatchTeamFans($matchid, $teamid) {
			$sql = "update match_result set team_fans=team_fans+1 where matchid=$matchid and teamid=$teamid";
			$res = $this->tmacDB->insert_data($sql);
			return $res;
		}
		public function GetMatchTeamFans($matchid, $teamid) {
        		$sql = "select team_fans from match_result where  matchid = $matchid and teamid=$teamid";
			$res = $this->tmacDB->get_data($sql);
			if(empty($res)) {
				return array();
			}
			return $res[0]['team_fans'];
		}
 	}
