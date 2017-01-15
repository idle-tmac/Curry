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

		public function UpdateTeamInfo($sTeamid, $aUpdateInfo) {
			$sSql = "update team set ";
			$bFisrt = True;
			foreach($aUpdateInfo as $sField => $sVal) {
				if($bFisrt) {
					$sSql = $sSql . $sField . " = " . $sVal;
					$bFisrt = False;
				}else {
					$sSql = $sSql . ", " . $sField . " = " . $sVal;
				}
			}
			$sSql =  $sSql . " where teamid = $sTeamid;";
			$res = $this->tmacDB->insert_data($sSql);
			return $res;
		}
		public function GetUserTeamInfoByTeamidUserid($teamid, $userid, $fields = array()){
			if(empty($fields)) {
        		$sql = "select * from user_team where  teamid = $teamid and userid = $userid";
			} else {
				$sql = "select " . implode(",",$fields) . " from user_team where  teamid = $teamid and userid = $userid";
			}
			$res = $this->tmacDB->get_data($sql);
			if(empty($res)) {
				return array();
			}
        	return $res[0];
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
		public function DeleteTeamUserInfo($teamid, $userid){
        	$sql = "delete from user_team where  teamid = $teamid and userid=$userid";
			$res = $this->tmacDB->insert_data($sql);
        	return $res;
		}
		public function InsertUserTeamInfo($aTeamInfo){
			$aFields = array_keys($aTeamInfo);
			$aVals = array_values($aTeamInfo);
			$sFields = implode(",",$aFields);
			$sVals = implode(",",$aVals);
			$sSql = "insert into user_team (" . $sFields . ") values (" . $sVals . ")";
			$res = $this->tmacDB->insert_data($sSql);
        	return $res;
		}
		public function DeleteTeamAdminitorInfo($teamid, $adminitorid){
        	$sql = "delete from team_adminitor where  teamid = $teamid and adminitorid=$adminitorid";
			$res = $this->tmacDB->insert_data($sql);
        	return $res;
		}
		public function AddTeamAdminitorInfo($aAdminInfo) {
			$aFields = array_keys($aAdminInfo);
            $aVals = array_values($aAdminInfo);
            $sFields = implode(",",$aFields);
            $sVals = implode(",",$aVals);
            $sSql = "insert into team_adminitor (" . $sFields . ") values (" . $sVals . ")";
            $res = $this->tmacDB->insert_data($sSql);
            return $res;
		}
		public function GetTeamAdminitorInfoByTeamid($teamid, $fields = array()){
			if(empty($fields)) {
        		$sql = "select * from team_adminitor where  teamid = $teamid";
			} else {
				$sql = "select " . implode(",",$fields) . " from team_adminitor where  teamid = $teamid";
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
				$sSql = "select " . implode(",", $aFields) . " from `match` where  (teamid1=$teamid or teamid2=$teamid);";
			}
			$res = $this->tmacDB->insert_data($sSql);
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
		//join_application加入申请表插入操作
		public function InsertJoinApplication($aJoinInfo) {
			$aFields = array_keys($aJoinInfo);
			$aVals = array_values($aJoinInfo);
			$sFields = implode(",",$aFields);
			$sVals = implode(",",$aVals);
			$sSql = "insert into join_application (" . $sFields . ") values (" . $sVals . ")";
			$res = $this->tmacDB->insert_data($sSql);
			return $res;
		}
		public function GetJoinApplication($sWantJoinid, $sType) {
			$sSql = "select * from join_application where wantjoinid=$sWantJoinid and type=$sType";
			$aJoinInfo = $this->tmacDB->get_data($sSql);
			return $aJoinInfo;
		}
 	}
