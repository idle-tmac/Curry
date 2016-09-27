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
 	}
