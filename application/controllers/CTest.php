<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$appDir=realpath(dirname(__FILE__).'/../');
$imageDir=realpath(dirname(__FILE__).'/../image');
$dataDir=realpath(dirname(__FILE__).'/../data');
#require_once($appDir."/logs/log.php");

class CTest extends CI_Controller {

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
		#MessageEcho('0', '吃顶了', '斯科特');
		echo MY_REDIS_IMAGE;
		#MessageEcho('0', '吃顶了', array("1"=>'fasdfs'));
		f();
	}
}
