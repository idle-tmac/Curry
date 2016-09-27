<?php
$appDir=realpath(dirname(__FILE__).'/../');
require_once($appDir."/libraries/ParseXML.php");
class db_base{
    
    public $db;
    private $dbname;
    private $dbArr;
    private static $dbInstanceArr = array();
    

    public static function getInstance($dbname){
        
        if (!isset(self::$dbInstanceArr[$dbname]) ||
            !self::$dbInstanceArr[$dbname] instanceof self ||
            !mysqli_ping(self::$dbInstanceArr[$dbname]->db)) {

            self::$dbInstanceArr[$dbname] = new self($dbname);
            }

        return self::$dbInstanceArr[$dbname];
    }

    private function __construct($dbname) {
        global  $baseDir;
        $this->dbname = $dbname;
        $this->dbArr = $this->getDbInfo();
        $this->db = $this->openDB();
    }

    public function getShellDBCommand(){
        $dbinfo = $this->dbArr[$this->dbname];
        $commandString = "mysql -h". $dbinfo['host'] . " -P" . $dbinfo['port'];
        $commandString .= " -u". $dbinfo['username'] . " -p" . $dbinfo['password'];
        $commandString .= " ". $dbinfo['db'];
        return $commandString;
    }

    private function openDB()
    {
        $dbinfo = $this->dbArr[$this->dbname];
        $mysqli = new mysqli($dbinfo['host'], $dbinfo['username'],
                  $dbinfo['password'], $dbinfo['db'], $dbinfo['port']);

        if ($mysqli->connect_error) {
            Log::error('model',"mysqli connect error!");
            exit('Error : ('.$mysqli->connect_error);
        }
        $mysqli->query("SET NAMES $dbinfo[charset]");
        return $mysqli;
    }

    public function getOneRow($sql){
        $result = $this->get_data($sql);
        if ($result && !empty($result)) {
            return $result[0];
        }
        return 0;
    }

    public function getOneElement($sql){
        $row=$this->getOneRow($sql);
        if($row){
            $res = trim(current($row));
            return $res;
        }
        return 0;
    }

    private function reconnectDb() {
        $i = 0;
        $this->db = $this->openDB();

        while ($this->db === false && $i < 3) {
            $this->db = $this->openDB();
            $i += 1;
        }
    }

    public function __destruct()
    {
        if (is_resource($this->db)) {
            $this->db->close();
        }
    }

    public function get_data($sql) {

        $ret = array();
        $res = $this->db->query($sql);
        if (!$res) {
            return $res;
        }
        while ($row = $res->fetch_assoc()) {
            $ret[]=$row;
        }
        return $ret;
    }

    public function getDataBySchema($sql,$schema)
    {
        /*
            only select columns in schema
           */
        $db = $this->db;
        $ret=array();
        $res = $this->db->query($sql);
        if ($res === false) {
            $this->reconnect_db();
            $res = $this->db->query($sql);
        }
        if(!$res){return $res;}
        while ($row = $res->fetch_assoc()) {
            $temp=array();
            foreach($schema as $k=>$v)
            {
                if(array_key_exists($k,$row))
                {
                    $temp[$k]=$row[$k];
                }
            }
            $ret[]=$temp;
        }
        return $ret;
    }

    public function insert_data($sql) 
    {
        $res = $this->db->query($sql);
        return $res; 
    }

    public function query($sql)
    {
        $res = $this->db->query($sql);
        return $res;
    }

    public function getDb() {
        return $this->db;
    }

    public function getDbInfo() {
	$appDir=realpath(dirname(__FILE__).'/../');
        $dbXmlPath = $appDir.'/config/dbconf.xml';
        $xmlparse = new ParseXML($dbXmlPath);
        $dbinfo0 = $xmlparse->parseXML();
        $dbinfo1 = array();
        foreach($dbinfo0 as $k=>$v)
        {
            $dbinfo1[$k] = array();
            foreach($v[0] as $s=>$t)
            {
                $dbinfo1[$k][$s] = $t[0];
            }
        }
        return $dbinfo1;
    }
}
/*$db = db_base::getInstance("tmac");
$res = $db->get_data("select * from user");
var_dump($res);*/
