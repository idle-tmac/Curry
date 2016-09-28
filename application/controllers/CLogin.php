<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$appDir=realpath(dirname(__FILE__).'/../');
$imageDir=realpath(dirname(__FILE__).'/../image');
require_once($appDir."/libraries/util.php");

class CLogin extends CI_Controller {

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
		$this->load->helper('url');
		$this->redis = $this->MRedis->_getInstance();
		
	}
	public function index()
	{
		#$this->load->view('welcome_message');
		echo "hello huyong!";
	}
	
	
	/*
	 beego.Router("/login", &controllers.LoginController{}, "get:LoginCheck")
	*/
	public function LoginCheck(){
		//$loginway = $_GET["loginway"];
		//$value = $_GET["value"];
		//$passwd = $_GET["passwd"];
		$loginway = $_POST["loginway"];
		$value = $_POST["value"];
		$passwd = $_POST["passwd"];
		
        	$data = $this->MUser->GetUserInfoPlus($loginway, $value);
      
		$cell = array();
		$cell["type"] = $this->config->item('MY_USERNOEXIST');
		if(!empty($data)) {
			$cell["type"] = $this->config->item('MY_USERWRONGPASSWD');
			if (base64_encode($passwd) == base64_encode($data['passwd'])) {
				$cell["type"] = $this->config->item('MY_USERLESSINFO');
				$cell["schoolid"] = $data["schoolno"];
				if ($data["schoolno"] != "") {
					$cell["type"] = $this->config->item('MY_USERPERFECT');
				}
				global $appDir;
				$path = $appDir . "/views/image/head/$value.jpg";
				if(file_exists($path)) {
					$cell["picture"] = base_url("views/image/head/$value.jpg");
				} else {
					$cell["picture"] = base_url("views/image/head/default.jpeg");
				}
			}
		}
		$jsonstr = json_encode($cell);
		echo $jsonstr;
	}	
}
