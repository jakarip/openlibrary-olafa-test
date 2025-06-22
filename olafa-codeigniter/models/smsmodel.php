<?php
class SmsModel extends CI_Model {
	
	private $table 	= 'prosiding';
	private $id		= 'jd_id';
	function __construct()
	{
		parent::__construct();  
		$this->masterdb = $this->load->database('oracle', true);
	} 
	 
	
	function getall()
	{ 	 
		return $this->masterdb->query("SELECT *   
			FROM sms_grup_detail pd 
			left join sms_grup on sg_id=sg_id 
			left join sms_nodin on nd_id=pd_nd_id 
			left join sms_bast on bs_id=pd_bs_id order by sg_id,pj_tanggal");
	} 
	
	function getAnggota($username)
	{ 	 
		return $this->masterdb->query("SELECT * from t_tem_userlogin_igracias where c_username='$username'");
	} 
	
	
	function searchAnggota($username)
	{ 	 
		return $this->masterdb->query("SELECT * from t_tem_userlogin_igracias where LOWER(fullname) like '%".strtolower($username)."%' or LOWER(c_username) like '%".strtolower($username)."%'");
	} 
		
	function getallgrupnd($where="")
	{ 	 
		return $this->db->query("SELECT *   
			FROM sms_grup pj left join sms_grup_detail pd on sg_id=sg_id where pd_nd_id=0 $where order by sg_id,pj_tanggal");
	} 

	function getallgrupbs($where="")
	{ 	 
		return $this->db->query("SELECT *   
			FROM sms_grup pj left join sms_grup_detail pd on sg_id=sg_id where pd_bs_id=0 and pd_nd_id!=0 $where order by sg_id,pj_tanggal");
	}  	
	
	function getgrup()
	{ 	
		// $this->db->from($this->table);  
		// $this->db->order_by('jd_judul ASC, jd_edisi ASC'); 
		// return $this->db->get();
		
		return $this->db->query("select *, count(sgd_id) total from (SELECT *   
			FROM sms_grup pj left join sms_grup_detail using(sg_id) order by sg_name) a group by sg_id ");
	}  
	
	function getgrupdetailbysg($id)
	{
		return $this->db->query("SELECT * FROM sms_grup_detail where sg_id='$id'");
	}
	
	
	function getgrupdetailbynd($id)
	{
		return $this->db->query("SELECT * FROM sms_grup_detail left join sms_grup on sg_id=sg_id  where pd_nd_id='$id'");
	}
	
	function getgrupdetailbybs($id)
	{
		return $this->db->query("SELECT * FROM sms_grup_detail left join sms_grup on sg_id=sg_id  where pd_bs_id='$id'");
	}
	
	function getnodin()
	{ 	
		return $this->db->query("select *, count(sgd_id) total from (SELECT *   
			FROM sms_nodin pj left join sms_grup_detail pd on nd_id=pd_nd_id order by nd_tanggal) a group by nd_id ");
	}
	
	function getbast()
	{ 	
		return $this->db->query("select *, count(sgd_id) total from (SELECT *   
			FROM sms_bast pj left join sms_grup_detail pd on bs_id=pd_bs_id order by bs_tanggal) a group by bs_id ");
	}
	  
	
	function getgrupbyid($id)
	{
		return $this->db->query("SELECT * FROM sms_grup where sg_id='$id'");
	} 
	
	function getgrupdetailbyid($id)
	{
		return $this->db->query("SELECT * FROM sms_grup_detail where sgd_id='$id'");
	}

	function getnodinbyid($id)
	{
		return $this->db->query("SELECT * FROM sms_nodin where nd_id='$id'");
	} 	
	
	function getbastbyid($id)
	{
		return $this->db->query("SELECT * FROM sms_bast where bs_id='$id'");
	} 
	
	function addGrup($item)
	{
		$this->db->insert('sms_grup', $item);
		$last_id = $this->db->insert_id();
		return $last_id;
	}
	
	function addGrupDetail($item)
	{
		$this->db->insert('sms_grup_detail', $item);
	}
	
	function addNodin($item)
	{
		$this->db->insert('sms_nodin', $item);
		$last_id = $this->db->insert_id();
		return $last_id;
	}
	
	function addBast($item)
	{
		$this->db->insert('sms_bast', $item);
		$last_id = $this->db->insert_id();
		return $last_id;
	}
	
	function editGrup($id, $item)
	{
		$this->db->where('sg_id', $id);
		$this->db->update('sms_grup', $item);
	}
	
	function editGrupDetail($id, $item)
	{
		$this->db->where('sgd_id', $id);
		$this->db->update('sms_grup_detail', $item);
	}
	
	function editNodin($id, $item)
	{
		$this->db->where('nd_id', $id);
		$this->db->update('sms_nodin', $item);
	}
	
	function editBast($id, $item)
	{
		$this->db->where('bs_id', $id);
		$this->db->update('sms_bast', $item);
	}
	
	
	function delGrup($id)
	{
		$this->db->where('sg_id', $id);
		$this->db->delete('sms_grup');
		
		$this->db->where('sg_id', $id);
		$this->db->delete('sms_grup_detail');
	}
	
	function delGrupDetail($id)
	{
		$this->db->where('sgd_id', $id);
		$this->db->delete('sms_grup_detail');
	}
	
	function delNodin($id)
	{
		$this->db->where('nd_id', $id);
		$this->db->delete('sms_nodin');
		
		return $this->db->query("update sms_grup_detail set pd_nd_id=0,pd_status='Diajukan Dosen',pd_eks_awal='0' where pd_nd_id='$id'");
	} 
	
	function delBast($id)
	{
		$this->db->where('bs_id', $id);
		$this->db->delete('sms_bast');
		
		return $this->db->query("update sms_grup_detail set pd_bs_id=0,pd_status='Diajukan ke Logistik',pd_eks_akhir='0' where pd_bs_id='$id'");
	} 
	
	
	 
}
?>