<?php
class UserModel extends CI_Model 
{
	
	private $table  = 'batik.member';
	private $id     = 'id';
	private $mn 	= '';
   
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->mn = $this->load->database('menu', TRUE);
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
		return $this->masterdb->query("select * from batik.member where master_data_user='$user' and master_data_password='$pass' and status='1' and member_type_id!='19'");
	} 
	
	function checkUser($user)
	{ 	 
		return $this->db->query("select *, member.id memberid from member where master_data_user='$user' and member_type_id!='19' and status='1'");
	}   
	
	function listCatalog($id)
	{ 	 
		return $this->db->query(" 
		select *,
		CASE
					WHEN jeniskatalog like '%reference%' then 
					(select maps_no from maps where maps_range_start <=cckode  and maps_range_end >=cckode and maps_type='reference' limit 1)
					else 	(select maps_no from maps where maps_range_start <=cckode  and maps_range_end >=cckode and maps_type is null limit 1)
				END rak
		from (
		select kt.id, cc.code cckode, kt.code kat, ks.name subjek, kp.name jeniskatalog,  kt.title judul,author,editor , published_year tahunterbit,publisher_city,publisher_name,group_concat(il.name) lokasi from knowledge_item kt  
				left join knowledge_stock kk on kt.id=kk.knowledge_item_id
				left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
				left join classification_code cc on cc.id=kt.classification_code_id
				left join item_location il on il.id=kk.item_location_id
				left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' 
				and kk.status not in (4,5,9) and kp.id in ($id)
				group by kt.id) a");
	}  
	
	function editDb($table, $data, $id, $iddata)
	{
		$this->mn->where($id, $iddata);
		$this->mn->update($table, $data);
	}
	
	
	function getRent($stockid)
	{ 	 
		return $this->db->query("select * from rent where knowledge_stock_id='$stockid' and return_date is null");
	}  
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