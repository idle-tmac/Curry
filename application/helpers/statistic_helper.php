<?php
	function UpdateSaiKuang(&$saiKuang,$eventTeamid,$part,$scoreFlag){
		$saiKuang[$eventTeamid][$part]+=$scoreFlag;
		//return $saiKuang;
	}
	//按照得分排序
	function CmpScore($a,$b){
		if($a['score'] < $b['score'])
			return 1;
		else
			return 0;
	}
	//按照助攻排序
	function CmpAssist($a,$b){
		if($a['assist'] < $b['assist'])
			return 1;
		else
			return 0;
	}
	//按照篮板排序
	function CmpBackbord($a,$b){
		if($a['backbord'] < $b['backbord'])
			return 1;
		else
			return 0;
	}
	function UpdateBest(&$best,$playerStatistic,$eventTeamid){
		//计算出目前分数最好的球员
		usort($playerStatistic[$eventTeamid], "CmpScore");
		while(list($key,$value) = each ($playerStatistic[$eventTeamid])){
			//这是拿到的最高的数据，拿到第一个数据停下来即可
			//echo $value['No']." ". $value["score"] . "\n";
			$best[$eventTeamid]['score']=$value['score'];
			$best[$eventTeamid]['No']=$value['No'];
			$best[$eventTeamid]['playerName']=$value['playerName'];
			break;
		}
		//计算目前助攻最好的球员
		usort($playerStatistic[$eventTeamid], "CmpAssist");
		while(list($key,$value) = each ($playerStatistic[$eventTeamid])){
			//这是拿到的最高的数据，拿到第一个数据停下来即可
			//echo $value['No']." ". $value["score"] . "\n";
			$best[$eventTeamid]['assist']=$value['assist'];
			$best[$eventTeamid]['No']=$value['No'];
			$best[$eventTeamid]['playerName']=$value['playerName'];
			break;
		}
		//计算目前篮板最好的球员
		usort($playerStatistic[$eventTeamid], "CmpBackbord");
		while(list($key,$value) = each ($playerStatistic[$eventTeamid])){
			//这是拿到的最高的数据，拿到第一个数据停下来即可
			//echo $value['No']." ". $value["score"] . "\n";
			$best[$eventTeamid]['backbord']=$value['backbord'];
			$best[$eventTeamid]['No']=$value['No'];
			$best[$eventTeamid]['playerName']=$value['playerName'];
			break;
		}
			
		
		//return $best;
	}
	function UpdateTeamStatistic(&$teamStatistic, $eventTeamid, $aStatisticItems, $aMatchStatisticInfo, $iScore){
		foreach($aStatisticItems as $sItem) {
			if(strstr($sItem, "SCORE")) {
				$teamStatistic[$eventTeamid][$aMatchStatisticInfo[$sItem]] += $iScore;
			} else {
				$teamStatistic[$eventTeamid][$aMatchStatisticInfo[$sItem]] += 1;
			}
		}
	}
	function UpdatePlayerStatistic(&$playerStatistic, $eventTeamid, $playerid, $aStatisticItems, $aMatchStatisticInfo, $iScore){
		var_dump($aMatchStatisticInfo);
		//var_dump($aStatisticItems);
		foreach($aStatisticItems as $sItem) {
			if(strstr($sItem, "SCORE")) {
				$playerStatistic[$eventTeamid][$playerid][$aMatchStatisticInfo[$sItem]] += $iScore;
			} else {
				$playerStatistic[$eventTeamid][$playerid][$aMatchStatisticInfo[$sItem]] += 1;
			}
		}
	}
	function UpdateRecordName(&$recordName,$newRecordName){
		$recordName=$newRecordName;
		return $recordName;
	}
	function InitSaiKuang($teamid1, $teamid2) {
		$aSaiKuang = array(
			$teamid1 => array("", 0, 0, 0, 0),
			$teamid2 => array("", 0, 0, 0, 0)
		);
		return $aSaiKuang;
	}
	function InitBestItem($aMatchStatisticInfo, $sNo, $sName) {
		$aBestItem = array(
				array($aMatchStatisticInfo['SCORE'] => 0, $sNo => 0, $sName => 0),
				array($aMatchStatisticInfo['ASSIST'] => 0, $sNo => 0, $sName => 0),
				array($aMatchStatisticInfo['BLACKBOARD'] => 0, $sNo => 0, $sName => 0)
		);
		return $aBestItem;
	}
	function InitPlayerStatistic($aTeamInfo, $aMatchStatisticInfo,  $aUserStatisticItem){
		$aPlayerInfos = array();

		$aUserStatistic = array();
		foreach ($aUserStatisticItem as $key) {
			$aUserStatistic[$aMatchStatisticInfo[$key]] = 0;
		}
		foreach($aTeamInfo as $aTeamUser){
			$iPlayerid = $aTeamUser['userid'];
			$aPlayerInfos[$iPlayerid] = $aUserStatistic;	
		}
		return $aPlayerInfos;
	}
	function InitTeamStatistic($aMatchStatisticInfo, $aTeamStatisticItem) {
		$teamStatistic = array();
		foreach ($aTeamStatisticItem as $sItem) {
			$teamStatistic[$aMatchStatisticInfo[$sItem]] = 0;
		}
		return $teamStatistic;
	}

?>
