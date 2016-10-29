<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$appDir=realpath(dirname(__FILE__).'/../');
$imageDir=realpath(dirname(__FILE__).'/../image');

class CRegister extends CI_Controller {

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
		$this->load->model('MRedis');
		$this->load->model("MUser");
		$this->redis = $this->MRedis->_getInstance();
		
	}
	public function index()
	{
		#$this->load->view('welcome_message');
		echo "hello huyong!";
	}
	
	
	/*
	beego.Router("/register/verifycode", &controllers.RegisterController{}, "get:RegisterDeal")
	*/
	public function RegisterDeal(){
		$phone = $_GET["phone"];
		$retRegInfo = array();
		$retRegInfo["ret"] = "0";
		$this->redis->set($phone, "123456"); 	
		$retRegInfo["ret"] = "1";
		$jsonstr = json_encode($retRegInfo);
		echo $jsonstr;
	}	
	public function RegisterPasswdUpLoad(){
		//$phone =  $_GET["phone"];
		//$vc =  $_GET["verifycode"];
		//$passwd1 = $_GET["passwd1"];
		//$passwd2 = $_GET["passwd2"];
		$type = $_POST["type"]; #1:new 2:reset
		$phone =  $_POST["phone"];
		$vc =  $_POST["verifycode"];
		$passwd1 = $_POST["passwd1"];
		$passwd2 = $_POST["passwd2"];
	
		$cell = array();
		
		#password regular check
		$ret = JudgePasswd($passwd1, $passwd2, $this->config);
		$cell["type"] = $ret;
		//verify code check
		$verifycode = $this->redis->get($phone); 	
		if ($vc != $verifycode || $verifycode == "") {
			$cell["type"] = $this->config->item('MY_REGISTER_VERIFYCODEERR');
		}
		
		//ru ku check
		$dbret = false;
		$ok = $this->config->item('MY_REGISTER_PASSWDOK');
		if ($cell["type"] == $ok) {
			$time = GetTime(0);   
			if($type == 1) {
        			$dbret = $this->MUser->InserUserInfo($phone, $passwd1, $time);
				if (!$dbret) {
					$cell["type"] = $this->config->item('MY_REGISTER_PASSWDINSERTERROR'); 
				}
			} 
			else if($type == 2) {
				$dbret = $this->MUser->GetUserInfo($phone);
				if(!$dbret) {
					$cell["type"] = $this->config->item('MY_RESET_USERNOEXIST');
				} else {
					$dbret = $this->MUser->UpdateUserPassword($phone, $passwd1);
					$cell["type"] = $this->config->item('MY_RESET_OK');
				}
			}
		}

		$jsonstr = json_encode($cell);
		echo $jsonstr;
	}
}

	
