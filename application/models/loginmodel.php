<?php
class LoginModel extends CI_Model {
	
	private $table 	= 'prosiding';
	private $id		= 'jd_id';
	function __construct()
	{
		parent::__construct();
		$this->masterdb = $this->load->database('oracle', true);
	} 
	
	function login($user,$pass)
	{ 	 
		return $this->masterdb->query("select * from t_mst_user_login where c_username='$user' and password='$pass' and status_user='1'");
	} 
	
	function checkUser($user)
	{ 	 
		return $this->db->query("select * from member where master_data_user='$user' and member_type_id='1'");
	} 
	
	
}
?>