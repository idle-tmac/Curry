<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Huyong extends CI_Controller {

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
		#$url = 'http://120.76.130.252:8000/application/views/15652352290.jpg';
		$this->load->helper('url');
		$this->load->view('index.html');
		#$this->load->model('MRedis');
		#$redis = $this->MRedis->_getInstance();
		#$redis -> set("tutorial-name", "Redis tutorial");
	        #echo $redis->get("tutorial-name");
		echo base_url("abc");
	}
	public function getAge()
	{
		#$this->load->view('welcome_message');
		//$this->load->model("Dongtai");
		//$res = $this->Dongtai->GetTicket("2015-01-01 00:00:00","NBA");
		//$res = $this->Dongtai->GetDongtaiInfo(2, "NBA", 4);
		//$this->load->model("Match");
		//$res = $this->Match->GetMatchInfo(1, 1);
		//$this->load->model("Match_Result");
		//$res = $this->Match_Result->GetMatchResultInfo(1);
		//$this->load->model("Team");
		//$res = $this->Team->GetTeamInfo(1);
		echo $_GET['id']; 
		$this->load->model("League");
		$res = $this->League->GetLeagueInfo(1, 3, 0);
		var_dump($res);
		echo "hello 8!";
	}
}
