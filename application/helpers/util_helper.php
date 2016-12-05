<?php
	function f() {
		$aMatchStatisticInfo = $this->config->item('MY_MATCH_STATISTIC');
		var_dump($aMatchStatisticInfo);
	}
	function MessageEcho($code, $message="", $response = "") {
		$jsonstr = json_encode(array(
			'code' => $code,
			'message' => $message,
			'response' => $response
		));
		echo $jsonstr;
	}
	function WriteToFile($filepath, $text, $parttion = 'w') {
		$f = fopen($filepath, $parttion);
		fwrite($f, $text);
		fclose($f);
	}
	function GetTime($diff = 0) { 
		date_default_timezone_set('PRC');
		$t = date('Y-m-d H:i:s', time());
   	     	$timestamp = strtotime($t); 
		return  date('Y-m-d H:i:s', $timestamp + $diff);
	}

	function GetInfoByTicket($ticket) {
		$stamp_newid = explode("_", $ticket);
		$t = $stamp_newid[0];
		$stamp = date('Y-m-d H:i:s', $t);
		$newid = $stamp_newid[1];
		$tickteinfo = array($stamp, $newid);
		return $tickteinfo;
	}
	function JudgePasswd($passwd1, $passwd2, $config) {
		$type = $config->item('MY_REGISTER_PASSWDDIFFERROR'); 	
		if ($passwd1 == $passwd2) {
			if (strlen($passwd1) < 6 ){
				$type = $config->item('MY_REGISTER_PASSWDLENGTHERROR');
			} else {
				 $type = $config->item('MY_REGISTER_PASSWDOK');
			}
		}	
		return $type;
	}
