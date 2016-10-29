<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$appDir=realpath(dirname(__FILE__).'/../');
$logDir=$appDir . "/logs";
class LogInfo{

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
	
	public function WriteLog($prefix, $text){
		$appDir=realpath(dirname(__FILE__).'/../');
		$logDir=$appDir . "/logs/";
		$filename = $logDir . date("Y_m_d") . ".log";
		$f = fopen($filename, 'a+');
		$text = date("Y-m-d H:i:s") .  "||" . $prefix . "||" . $text . "\n";
		fwrite($f, $text);
		fclose($f);
	}	
}
