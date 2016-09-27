<?php
	$appDir=realpath(dirname(__FILE__).'/../');
	require_once($appDir."/models/db_base.php");
	class MDongtai extends CI_Model {
		public function __construct() {
			parent::__construct();
			$this->load->database();
			$this->tmacDB = db_base::getInstance("tmac");
		}
		public function GetTicket($time, $module){
			$this->db->select(array("id","create_time"));
			$this->db->where("type",$module);
			$this->db->where("create_time >= ", $time);
			$query = $this->db->get("dongtai_news",1); 
			$res = $query->result();
			if (count($res) == 0) {
               			return "0";
       	 		}
			$createTime = $res[0]->create_time;
			$id = $res[0]->id;
			$time = strtotime($createTime);
			#$time = str_replace(array("-", ":", " "),"",$createTime);
        		$ticket = $time . "_" . $id;
        		return $ticket;
		
		}
		public function GetDongtaiInfo($nid, $module, $num){// (cells []map[string]string) {
			$sql = "select * from dongtai_news where type = '$module' and id >  $nid limit $num";
			$res = $this->tmacDB->get_data($sql);
			return $res;
		}
 	}
