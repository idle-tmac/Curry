<?php
	require_once "../models/db_base.php";
	$tmacDB = db_base::getInstance("tmac");
	$sql = "select * from student where sid=10;";
	$res = $tmacDB->get_data($sql);
	$jName = $res[0]['name'];
	$aName = json_decode($jName, true);
	var_dump($aName);
