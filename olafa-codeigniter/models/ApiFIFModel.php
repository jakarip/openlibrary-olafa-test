<?php
class ApiFIFModel extends CI_Model {
	
	/**
	 * Constructor
	 */
	var $md;
	function __construct()
	{
		parent::__construct(); 
		
		$this->md = $this->load->database('oracle', TRUE);
	}
	
	function checkPegawaiFIF($username,$password) 
	{ 
		return $this->md->query(" select * from t_tem_userlogin_igracias_for_rfid where c_kode_jenis_user='pegawai' and unit like '%(FIF)%' and c_username='$username' and pwd='$password'");  
	}   
}
?>