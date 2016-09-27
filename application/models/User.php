<?php
	class User extends CI_Model {
		public function __construct() {
			parent::__construct();
			$this->load->database();
		}
		public function UserQuery($userid = ""){
			if ($userid != "") {
				$this->db->where('userid', $userid);
			}
		        $this->db->select('*');
			$query = $this->db->get('user');
			$res = $query->result();
			return  $res;
		}
		public function User($userid = ""){
			if ($userid != "") {
				$this->db->where('userid', $userid);
			}
		        $this->db->select('*');
			$query = $this->db->get('user');
			$res = $query->result();
			return  $res;
		}
	}
