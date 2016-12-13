<?php
	$redis = new Redis();
	$redis->connect('127.0.0.1', '6379');
	$r = $redis->get("n");
	var_dump($r);
	echo "haha" . $r;
	/*$redis->set("name","lhy");
	$arr = array('data'=>'abc','id'=>1);
	$redis->hmset("xxx_1", $arr);
	$r = $redis->hgetall("xxx_1");*/
	//var_dump($r);
