<?php
	$appDir=realpath(dirname(__FILE__).'/../');
	require_once($appDir."/models/db_base.php");
	class League extends CI_Model {
		public function __construct() {
			parent::__construct();
			$this->tmacDB = db_base::getInstance("tmac");
		}
		public function GetLeagueInfo($schoolid, $num, $ticket) {//(cells []map[string]string) {
        		$sql = "select * from league where schoolid = $schoolid and id > $ticket limit $num";
			$res = $this->tmacDB->get_data($sql);
			return $res;
		}
 	}
