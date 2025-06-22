<?php
class UserModel extends CI_Model 
{
	
	private $table  = 'batik.member';
	private $id     = 'id';
	private $mn 	= '';
   
	/**
	 * Constructor
	 */
	public function __construct()
	{ 
		$this->masterdb = $this->load->database('oracle', true);
	}	
	
	function getall()
	{
		return $this->mn->get($this->table);
	} 
	
	function getby($user,$pass)
	{ 	 
		return $this->masterdb->query("select * from t_mst_user_login mst left join t_tem_userlogin_igracias_for_rfid igra on mst.c_username=igra.c_username where mst.c_username='$user' and password='$pass' and status_user='1'");
	}  
	
	function getbymemberAll($user,$pass)
	{ 	  
		return $this->masterdb->query("select * from batik.member where master_data_user='$user' and master_data_password='$pass' and status='1'");
	} 
	
	function getbymember($user,$pass)
	{ 	
		return $this->masterdb->query("select *, member.id memberid from batik.member where master_data_user='$user' and master_data_password='$pass' and status='1' and member_type_id!='19'");
	} 

	
	// function getbymember($user,$pass)
	// {
	// 	$this->masterdb->where('master_data_password', $pass);
	// 	$this->masterdb->where('master_data_user',$user);
	// 	$this->masterdb->where('status','1');
	// 	$this->masterdb->where('member_type_id','!=','19'); 
	// 	return $this->masterdb->get('batik.member');
	// }
	
	function checkUser($user)
	{ 	 
		return $this->db->query("select *, member.id memberid from member where master_data_user='$user' and member_type_id!='19' and status='1'");
	}   
	
	// function checkUser($user)
	// { 
	// 	$this->db->where('master_data_user',$user);
	// 	$this->db->where('status','1');
	// 	$this->db->where('member_type_id','!=','19'); 
	// 	return $this->masterdb->get('batik.member');
	// }
	
	function checkstatus($user,$pass)
	{ 	  
		return $this->masterdb->query("select * from batik.member where master_data_user='$user'");
	} 
	
	function checkUserinTemUserLoginIgracias($user)
	{ 	  
		return $this->masterdb->query("select * from masterdata.t_tem_userlogin_igracias where c_username='$user'");
	}  
	
	function getbyid($id)
	{
		$this->mn->where($this->id, $id);
		return $this->mn->get($this->table);
	}

	
	
	function getbyone($item)
	{ 
		$this->masterdb->where($item);
		return $this->masterdb->get('batik.member');
	}
	function getsubscribe($item,$status)
	{ 
		
		$this->masterdb->where($item);
		$this->masterdb->where_in('subscribe_status', $status);
		return $this->masterdb->get('batik.member_subscribe');
	}
	
	
	function add($item)
	{
		$this->masterdb->insert($this->table, $item);
	} 
	
	function addItem($table,$item)
	{ 
		$this->masterdb->insert($table, $item);
	}
	
	function updateItem($table,$data,$where)
	{ 
		$this->masterdb->update($table, $data, $where);
	} 
	
	function update($data,$where)
	{ 
		$this->masterdb->update('batik.member', $data, $where);
	}
	
	
	function edit($id, $item)
	{
		$this->mn->where($this->id, $id);
		$this->mn->update($this->table, $item);
	}
	
	function delete($id)
	{
		$this->mn->where($this->id, $id);
		$this->mn->delete($this->table);
	}
	
	/**		FOR ADDITONAL FUNCTIONj
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
	
}
?>