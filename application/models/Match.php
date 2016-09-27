<?php
	$appDir=realpath(dirname(__FILE__).'/../');
	require_once($appDir."/models/db_base.php");
	class Match extends CI_Model {
		public function __construct() {
			parent::__construct();
			$this->load->database();
			$this->tmacDB = db_base::getInstance("tmac");
		}
		public function GetMatchInfo($schoolid, $leagueid) {//(cells []map[string]string) {
        		$sql = "select teamid1,teamid2,match_time,match_address,status,matchid from `match` 
				where  schoolid = $schoolid and leagueid = $leagueid";
			$res = $this->tmacDB->get_data($sql);
        		return $res;
		}
 	}
