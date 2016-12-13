<?php
	$param = array(
                	'event_type' => 1,
                	'userid' => 1,
			'userNo' => 1,
			'userName' => '詹姆斯',
			'teamid1' => 1,
			'teamid2' => 2,
			'teamname1' => '骑士',
			'teamname2' => '勇士',
			'match_pattern' => '1',
			'part' => 3,
			'event_teamid' => 1,
	);
	echo json_encode($param) . "\n";
