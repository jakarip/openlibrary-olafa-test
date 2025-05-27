<?php
class RfidDummyModel extends CI_Model {
	
	/**
	 * Constructor
	 */
	var $md;
	function __construct()
	{
		parent::__construct(); 
		
		$this->md = $this->load->database('oracle', TRUE);
	}
	
	function GetRfid($rfid,$username="") 
	{  //'GRADUATED, 'NON-ACTIVE' 
		if($username!=""){
			return $this->md->query("Select * from t_tem_userlogin_igracias where c_username='$username' and c_status_user not in ('DROP OUT', 'LEAVE', 'PASSED', 'RESIGN')"); 
		}
		else 
			return $this->md->query("Select * from t_tem_userlogin_igracias where (rfid1='$rfid' or rfid2='$rfid') and c_status_user not in ('DROP OUT', 'LEAVE', 'PASSED', 'RESIGN')"); 
	} 
	
	function GetMember($username)
	{  
		return $this->db->query("select * from member where master_data_user='$username'"); 
	}  
	
	function GetUser($option,$username)
	{  
		if ($option=='mahasiswa') return $this->md->query("select c_kode_prodi from t_mst_mahasiswa where c_npm='$username'"); 
		else return $this->md->query("select c_kode_status_pegawai from t_mst_pegawai where c_nip='$username'"); 
	}   
	
	function GetMemberTypeApi($api_base_type,$api_key_value)
	{  
		return $this->db->query("select * from member_type_api where api_base_type='$api_base_type' and  api_key_value='$api_key_value'"); 
	}   
	
	function add($item)
	{
		$this->db->insert('member', $item);
		return $this->db->insert_id();
	}
	
	function add_attendance($item)
	{
		$this->db->insert('member_attendance', $item);
		return $this->db->insert_id();
	}
}
?>