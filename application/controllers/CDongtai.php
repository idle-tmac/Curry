<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$appDir=realpath(dirname(__FILE__).'/../');
require_once($appDir."/libraries/util.php");

class CDongtai extends CI_Controller {

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
	public function index()
	{
		#$this->load->view('welcome_message');
		echo "hello huyong!";
	}
	
	
	/*
	 beego.Router("/dongtai/cells?module=xxx&flag=xxx&num=xxx&ticket=xxx", &controllers.DongtaiController{}, "get:ReqRecommendCells") 
	*/
	public function cells() {
		
		$module = $_GET['module'];
        	$flag = $_GET["flag"];
        	$num = $_GET["num"];
        	$ticket = $_GET["ticket"];

		$this->load->model("MDongtai");
        	
		if ($ticket == 0 && $flag == 0 ){
                	$time = GetTime(-60 * 60 * 24 * 90);
			$ticket = $this->MDongtai->GetTicket($time, $module);
       		 }

        	$ticketinfo = GetInfoByTicket($ticket);
        	$id = $ticketinfo[1];

        	if ($flag == 1) {
                	$id = $id - $num - 1;
        	}
        	$cells = $this->MDongtai->GetDongtaiInfo($id, $module, $num);
		#var_dump($cells);
		$jsonstr = json_encode($cells);
		echo $jsonstr;
	}
}
