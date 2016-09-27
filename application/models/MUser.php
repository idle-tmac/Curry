<?php
	$appDir=realpath(dirname(__FILE__).'/../');
	require_once($appDir."/models/db_base.php");
	class MUser extends CI_Model {
		public function __construct() {
			parent::__construct();
			$this->load->database();
			$this->tmacDB = db_base::getInstance("tmac");
		}
		public function GetUserInfo($phone){
	
			$sql = "select * from user where phone=$phone";
			$res = $this->tmacDB->get_data($sql);
			return  $res[0];
		}
		public function InserUserInfo($phone, $passwd1, $time){
			$sql = "insert into user(phone, passwd, create_time) values ($phone, $passwd1, '$time')";
			$res = $this->tmacDB->insert_data($sql);
			return  $res;
		}
		public function GetUserInfoPlus($value, $way){
			$sql = "select * from user where $way=$value";
			$res = $this->tmacDB->get_data($sql);
			if(empty($res)) {
				return array();
			}
			return $res[0];
		}
	}
