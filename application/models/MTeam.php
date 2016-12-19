<?php
	$appDir=realpath(dirname(__FILE__).'/../');
	require_once($appDir."/models/db_base.php");
	class MTeam extends CI_Model {
		public function __construct() {
			parent::__construct();
			$this->tmacDB = db_base::getInstance("tmac");
		}
		public function GetTeamInfo($teamid, $fields = array()){
			if(empty($fields)) {
        		$sql = "select * from team where  teamid = $teamid";
			} else {
				$sql = "select " . implode(",",$fields) . " from team where  teamid = $teamid";
			}
			$res = $this->tmacDB->get_data($sql);
			if(empty($res)) {
				return array();
			} else {
        		return $res[0];
			}
		}

		public function GetTeamUserInfoByTeamid($teamid, $fields = array()){
			if(empty($fields)) {
        		$sql = "select * from user_team where  teamid = $teamid";
			} else {
				$sql = "select " . implode(",",$fields) . " from user_team where  teamid = $teamid";
			}
			$res = $this->tmacDB->get_data($sql);
			if(empty($res)) {
				return array();
			}
        	return $res;
		}
		public function GetTeamMatchInfo($teamid, $aFields = array()){
			if(empty($aFields)) {
				$sSql = "select * from `match` where (teamid1=$teamid or teamid2=$teamid);";
			} else {
				$sSql = "select " . implode(",",$fields) . " from `match` where  (teamid1=$teamid or teamid2=$teamid);";
			}
			$res = $this->tmacDB->get_data($sSql);
			if(empty($res)) {
				return array();
			}
        	return $res;
		}
		public function InsertTeamInfo($aTeamInfo) {
			$aFields = array_keys($aTeamInfo);
			$aVals = array_values($aTeamInfo);
			$sFields = implode(",",$aFields);
			$sVals = implode(",",$aVals);
			$sSql = "insert into team (" . $sFields . ") values (" . $sVals . ")";
			$res = $this->tmacDB->insert_data($sSql);
			return $res;
		}
		public function GetTeamGlory($teamid, $fields = array()){
			if(empty($fields)) {
        		$sql = "select * from team_glory where  teamid = $teamid";
			} else {
				$sql = "select " . implode(",",$fields) . " from team_glory where  teamid = $teamid";
			}
			$res = $this->tmacDB->get_data($sql);
			if(empty($res)) {
				return array();
			}
        	return $res;
		}
 	}
