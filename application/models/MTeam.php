<?php
	$appDir=realpath(dirname(__FILE__).'/../');
	require_once($appDir."/models/db_base.php");
	class MTeam extends CI_Model {
		public function __construct() {
			parent::__construct();
			$this->tmacDB = db_base::getInstance("tmac");
		}
		public function GetTeamInfo($teamid){
        		$sql = "select teamid, logoid, name from team where  teamid = $teamid";
			$res = $this->tmacDB->get_data($sql);
        		return $res[0];
		}
 	}
