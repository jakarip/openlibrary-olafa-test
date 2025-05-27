<?php
class TulwModel extends CI_Model {
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	function checkEmailExist($email)
	{ 
		return $this->db->query("select * from tulw_reg where tulw_reg_email='$email'");
	} 
	
	function getEvent()
	{ 
		return $this->db->query("select * from tulw_event");
	} 
	
	function insertReg($data)
	{ 
		$this->db->insert('tulw_reg', $data);
	} 
	 				
	 
	
	
	
	
	
}
?>