<?php
	$appDir=realpath(dirname(__FILE__).'/../');
	require_once($appDir."/models/db_base.php");
	class MLeague extends CI_Model {
		public function __construct() {
			parent::__construct();
			$this->tmacDB = db_base::getInstance("tmac");
		}
		public function GetLeagueInfo($schoolid, $num, $ticket, $type) {//(cells []map[string]string) {
        		$sql = "select * from league where schoolid = $schoolid and id > $ticket and type = $type limit $num";
			$res = $this->tmacDB->get_data($sql);
			return $res;
		}
		public function AddLeagueFans($leagueid) {
			$res = $this->GetLeagueInfoById($leagueid);
			if(!$res) {
				return False;
			}
			$sql = "update league set team_fans=team_fans+1 where leagueid=$leagueid";
			$res = $this->tmacDB->insert_data($sql);
			return $res;
		}
		public function DelLeagueFans($leagueid) {
			$sql = "update league set team_fans=team_fans-1 where leagueid=$leagueid";
			$res = $this->tmacDB->insert_data($sql);
			return $res;
		}
	
		public function InsertLeagueInfo($aLeagueInfo) {
			$aFields = array_keys($aLeagueInfo);
			$aVals = array_values($aLeagueInfo);
			$sFields = implode(",",$aFields);
			$sVals = implode(",",$aVals);
			$sSql = "insert into league (" . $sFields . ") values (" . $sVals . ")";
			$res = $this->tmacDB->insert_data($sSql);
			return $res;
		}
		public function GetLeagueInfoById($leagueid) {//(cells []map[string]string) {
        	$sql = "select * from league  where leagueid=$leagueid";
			$res = $this->tmacDB->get_data($sql);
			if(!$res) {
				return False;
			}
			return $res[0];
		}
 	}
