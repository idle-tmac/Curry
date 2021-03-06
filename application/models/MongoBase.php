<?php
class MongoBase {
	private  $mongo;
	private static $instance;
	
	public static function getInstance($dbName){
    
        	if (!isset(self::$mongo)) {
            		self::$instance = new self($dbName); 
        	}

        	return self::$instance;
    	}
	function __construct($dbName) {
		 $tmp = new MongoClient();
		 $this->mongo = $tmp->$dbName;
	}
	public function GetData($table, $condition, $db = "tmac"){
		$collection=$this->mongo->$table; 
		$cur = $collection->find($condition);
		$res = array();
		foreach ($cur as $document) {
        		$res[] = $document;
		}
		return $res;
	}
	public function InsertData($table, $dataArr, $db = "tmac"){
		$collection=$this->mongo->$table; 
		$result=$collection->insert($dataArr); #简单插入  
		var_dump($dataArr['_id']);
		return $dataArr['_id']; #MongoDB会返回一个记录标识  
	}
}
#$m = MongoBase::getInstance("tmac"); // 连接
#$res = $m->GetData("t1",array("_id"=>1));
#var_dump($res);
/*$team1 = array(array("uid"=>"1","name"=>"maidi","score"=>"25"),array("uid"=>"2","name"=>"kobi","score"=>"24"));
$team2 = array(array("uid"=>"3","name"=>"james","score"=>"25"),array("uid"=>"4","name"=>"dulante","score"=>"24"));
$team3 = array(array("uid"=>"5","name"=>"andongni","score"=>"25"),array("uid"=>"6","name"=>"harden","score"=>"24"));

$info1 = array("matchid"=>"1");
$info1["team_1"] = $team1;
$info1["team_2"] = $team2;
$res = $m->InsertData("match_info",$info1);


$info2 = array("matchid"=>"2");
$info2["team_1"] = $team1;
$info2["team_3"] = $team3;
$res = $m->InsertData("match_info",$info2);

$info1 = array(1=>array(1 => '麦迪', 21 => '邓肯', 0 => '阿里纳斯', 1 => '比卢普斯',22 => '加内特'));
$info2 = array(1=>array(23 => '詹姆斯', 24 => '科比', 35 => '杜兰特', 23 => '戴维斯', 2 => '保罗'));
$res = $m->InsertData("team_member",$info1);
$res = $m->InsertData("team_member",$info2);

*/
?>
