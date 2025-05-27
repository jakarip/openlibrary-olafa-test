<?php
class ApiModel extends CI_Model {
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	function checkUsername($user)
	{  
		//$md = $this->load->database('oracle', TRUE);
		// return $md->query("select id from t_mst_user_login where c_username='$user'"); 
		return $this->db->query("select * from member where master_data_user='$user' and status='1'"); 
	} 
	
	function checkVerify($user)
	{  
		return $this->db->query("select * from member where master_data_user='$user' and status!='1'"); 
	}
	
	function GetPhone($data)
	{  
		$md = $this->load->database('oracle', TRUE);
		return $md->query("select hp from t_tem_userlogin_igracias where c_username='$data'"); 
	}
	
	function add($table,$item)
	{
		$md = $this->load->database('oracle', TRUE);
		return $md->insert($table, $item);
	} 
	
	function addMember($table,$item)
	{
		return $this->db->insert($table, $item);
	} 
	
	function edit($table,$colomn,$id,$item)
	{
		$md = $this->load->database('oracle', TRUE);
		$md->where($colomn, $id);
		return $md->update($table, $item);
	}
	
	function editMember($table,$colomn,$id,$item)
	{
		$this->db->where($colomn, $id);
		return $this->db->update($table, $item);
	}
	
	function getUploadType()
	{ 
		return $this->db->query("Select * from upload_type order by name")->result(); 
	} 
	
	function DeleteFileTotal()
	{ 
		return $this->db->query("delete from file_total"); 
	}  
	
	function InsertFileTotal($data)
	{ 
		return $this->db->insert('file_total', $data);
	} 
	
	function schedulerLog()
	{ 
		return $this->db->query("select * from scheduler_log where sch_log_date='".date('Y-m-d')."'"); 
	}   
	
	function InsertSchedulerLog($data)
	{ 
		return $this->db->insert('scheduler_log', $data);
	} 
	
	function getMember()
	{ 
		$this->db->query("update member set master_data_mobile_phone=null where master_data_mobile_phone is not null"); 
		return $this->db->query("select * from member where status='1' and id not in (select member_id from member_phone)"); 
	}  
	
	function InsertMemberPhone($data)
	{ 
		return $this->db->insert('member_phone', $data);
	} 
	
	function getMemberPhone()
	{ 
		return $this->db->query("select * from member where status='1'"); 
	}

	function removePhoneMasterData()
	{  
		$md = $this->load->database('oracle', TRUE);
		$md->query("update t_mst_pegawai set no_hp=null where no_hp is not null"); 
		return $md->query("update t_mst_mahasiswa set no_hp=null where no_hp is not null"); 
	} 		

	function getIgracias()
	{  
		$md = $this->load->database('oracle', TRUE);
		return $md->query("select c_username,hp from t_tem_userlogin_igracias"); 
	} 	
	
	function UpdateMemberPhone($data,$id)
	{
		$this->db->where('id', $id);
		return $this->db->update('member', $data);
	}
	
	function getEmailBroadcast()
	{  
		return $this->db->query("select * from email where email_status is null"); 
	}
	
	function UpdateBroadcast($data,$id)
	{
		$this->db->where('email_code', $id);
		return $this->db->update('email', $data);
	}
}
?>