<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$appDir=realpath(dirname(__FILE__).'/../');
$imageDir=realpath(dirname(__FILE__).'/../image');
$dataDir=realpath(dirname(__FILE__).'/../data');
#require_once($appDir."/logs/log.php");

class CImage extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct() {
		parent::__construct();
		$this->log1 = new LogInfo();
		$this->load->model('MRedis');
		$this->redis = $this->MRedis->_getInstance();
		
	}
	public function index()
	{
	}
	
	public function GetImageFromApp(){
		global $dataDir, $imageDir;
		$res = array();
		if(!isset($_POST["type"]) or !isset($_POST["id"]) 
			or !isset($_POST["data"]) or !isset($_POST["suffix"])) {
			$err = $this->config->item('MY_BAD_PARAMETER');	
			$res["ret"] = $err;
			$jsonstr = json_encode($res);
			echo $jsonstr;
			return;
		}
		$type = $_POST["type"];
		$id = $_POST["id"];
		$srcdata = $_POST["data"];
		$suffix = $_POST["suffix"];

		$prefix = $this->config->item('MY_REDIS_IMAGE');
		$key = $prefix . $type . "_" . $id;
                $this->redis->hmset($key, array($srcdata, $suffix));
		//var_dump($this->redis->hgetall($key));
		
		$data = base64_decode($srcdata);
		$filepath = "$imageDir/head/$id.$suffix"; 
		WriteToFile($filepath , $data);	

		$text = $type . "||" . $id . "||" .$srcdata;
		$this->log1->WriteLog("imagedata", $text);
			
		$code = $this->config->item('MY_ECHO_OK');	
		MessageEcho($code);
	}	
}
