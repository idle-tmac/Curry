<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$appDir=realpath(dirname(__FILE__).'/../');
$imageDir=realpath(dirname(__FILE__).'/../image');

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
		$this->redis = $this->MRedis->_getInstance();
		
	}
	public function index()
	{
		#$this->load->view('welcome_message');
		echo "hello umvp!";
	}
	
	
	/*
	 beego.Router("/login", &controllers.LoginController{}, "get:LoginCheck")
	*/
	public function LoginCheck(){
		if(!isset($_POST["loginway"]) || !isset($_POST["loginid"]) || !isset($_POST["passwd"])) {
			$message = $this->config->item('MY_BAD_PARAMETER');
            $code = $this->config->item('MY_ECHO_FAIL');
            MessageEcho($code, $message);
			echo $jsonstr;
			return;
		}
		$loginway = $_POST["loginway"];
		$value = $_POST["loginid"];
		$passwd = $_POST["passwd"];
		$code = $this->config->item('MY_ECHO_FAIL');
        	$data = $this->MUser->GetUserInfoPlus($loginway, $value);
      		
		$cell = array();
		$message = $this->config->item('MY_USERNOEXIST');
		if(!empty($data)) {
			$message = $this->config->item('MY_USERWRONGPASSWD');
			if (base64_encode($passwd) == base64_encode($data['passwd'])) {
				$code = $this->config->item('MY_ECHO_OK');
				$message = $this->config->item('MY_USERLESSINFO');
				$cell["schoolid"] = $data["schoolno"];
				$cell["userid"] = $data["userid"];
				if ($data["schoolno"] != "") {
					$message = $this->config->item('MY_USERPERFECT');
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
		MessageEcho($code, $message, $cell);
	}	
}
