<?php
	
	date_default_timezone_set('UTC');
	function GetTime($diff) { 
		date_default_timezone_set('UTC');
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
