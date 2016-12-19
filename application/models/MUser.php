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
			if(empty($res)) {
				return array();
			}
			return  $res[0];
		}
		public function UpdateUserPassword($phone,$password) {
			$sql = "update user set passwd=$password where phone=$phone";
			$res = $this->tmacDB->insert_data($sql);
			return $res;

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
		public function GetUserLeagueFans($userid){
			$sql = "select leagueid from user_league_fans where userid=$userid";
			$res = $this->tmacDB->get_data($sql);
			if(empty($res)) {
                return array();
            }
			$ret = array();
			foreach($res  as $item) {
				$ret[] = $item['leagueid'];
			}
			return $ret;
		}
		public function DelUserLeagueFans($userid, $leagueid) {
			$sql = "delete from user_league_fans where userid=$userid and leagueid=$leagueid";
			$ret = $this->tmacDB->insert_data($sql);
			return $ret;
		}
		public function AddUserLeagueFans($userid, $leagueid) {
			$sql = "insert into user_league_fans(userid, leagueid) values($userid, $leagueid)";
			$ret = $this->tmacDB->insert_data($sql);
			return $ret;
		}
		public function GetUserTeamFans($userid){
			$sql = "select teamid from user_team_fans where userid=$userid";
			$res = $this->tmacDB->get_data($sql);
			if(empty($res)) {
                return array();
            }
			$ret = array();
			foreach($res  as $item) {
				$ret[] = $item['teamid'];
			}
			return $ret;
		}
		public function DelUserTeamFans($userid, $teamid) {
			$sql = "delete from user_team_fans where userid=$userid and teamid=$teamid";
			$ret = $this->tmacDB->insert_data($sql);
			$sql = "update team set fans_num=fans_num-1 where teamid=$teamid";
			$ret2 = $this->tmacDB->insert_data($sql);
			return $ret && $ret2;
		}
		public function AddUserTeamFans($userid, $teamid) {
			$sql = "insert into user_team_fans(userid, teamid) values($userid, $teamid)";
			$ret1 = $this->tmacDB->insert_data($sql);
			$sql = "update team set fans_num=fans_num+1 where teamid=$teamid";
			$ret2 = $this->tmacDB->insert_data($sql);
			return $ret1 && $ret2;
		}
		public function DelUserMatchFans($userid, $matchid) {
			$sql = "delete from user_match_fans where userid=$userid and matchid=$matchid";
			$ret = $this->tmacDB->insert_data($sql);
			$sql = "update `match` set fans_num=fans_num-1 where matchid=$matchid";
			$ret2 = $this->tmacDB->insert_data($sql);
			return $ret && $ret2;
		}
		public function AddUserMatchFans($userid, $matchid) {
			$sql = "insert into user_match_fans(userid, matchid) values($userid, $matchid)";
			$ret1 = $this->tmacDB->insert_data($sql);
			$sql = "update `match` set fans_num=fans_num+1 where matchid=$matchid";
			$ret2 = $this->tmacDB->insert_data($sql);
			return $ret1 && $ret2;
		}
		public function GetUserMatchFans($userid){
			$sql = "select matchid from `user_match_fans` where userid=$userid";
			$res = $this->tmacDB->get_data($sql);
			if(empty($res)) {
                return array();
            }
			$ret = array();
			foreach($res  as $item) {
				$ret[] = $item['matchid'];
			}
			return $ret;
		}
	}
