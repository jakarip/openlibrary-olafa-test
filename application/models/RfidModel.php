<?php
class RfidModel extends CI_Model {
	
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
			return $this->md->query("Select * from batik.member where master_data_user='$username'"); 
		}
		else 
			return $this->md->query("Select * from batik.member where (rfid1='$rfid' or rfid2='$rfid')"); 
	} 
	
	function GetRfidScanner($rfid,$username="") 
	{  //'GRADUATED, 'NON-ACTIVE' 
		if($username!=""){
			return $this->md->query("Select id, id memberid, master_data_course,member_type_id,master_data_user,master_data_number,master_data_fullname from batik.member where master_data_user='$username' and status='1'"); 
		}
		else 
			return $this->md->query("Select id, id memberid, master_data_course,member_type_id,master_data_user,master_data_number,master_data_fullname from batik.member where (rfid1='$rfid' or rfid2='$rfid') and status='1'"); 
	} 
	
	function GetMember($username) 
	{  
		return $this->db->query("select *,id memberid from member where master_data_user='$username'"); 
	}  
	
	function GetBook($barcode) 
	{  
		return $this->db->query("select ks.id,kit.id itemid, title, author, kt.name type,penalty_cost,rent_cost,
		case 
			when status='1' then 'tersedia'
			when status='2' then 'dipinjam'
			else 'lainnya'
		end status,
		case  
			when rentable='0' then 'false'
			when status='1' then 'true'
		end rentable
		from knowledge_stock ks left join knowledge_item kit on kit.id=knowledge_item_id 
		left join knowledge_type kt on kt.id=kit.knowledge_type_id
		where ks.code='$barcode'"); 
	}  
	
	function GetMemberScanner($username) 
	{  
		return $this->db->query("select id, id memberid, member_type_id,master_data_user,master_data_number,master_data_fullname  from member where master_data_user='$username'  status='1'"); 
	}  
	
	function GetMemberAndRent($memberid,$itemid) 
	{   
		return $this->db->query("select m.id memberid, member_type_id,master_data_user,master_data_number,master_data_fullname,mt.*,
		(select count(*) from rent where return_date is null and member_id=m.id) total_pinjam, 
		(select count(*) from rent 
		left join knowledge_stock ks on ks.id=knowledge_stock_id 
		where return_date is null and member_id=m.id and knowledge_item_id='$itemid') total_judul_sama
		from member m left join member_type mt on mt.id=member_type_id where m.id='$memberid'"); 
	}   
	
	function GetRent($id) 
	{   
		return $this->db->query("select rent.*,master_data_user from rent 
		left join member m on m.id=member_id
		where knowledge_stock_id='$id' and rent.status='1'"); 
	}  
	
	function GetUser($option,$username)
	{  
		if ($option=='mahasiswa') return $this->md->query("select c_kode_prodi from t_mst_mahasiswa where c_npm='$username'"); 
		else return $this->md->query("select c_kode_status_pegawai from t_mst_pegawai where c_nip='$username'"); 
	}    
	 
	
	function GetRfidNotInDb()
	{  
		return $this->db->query("Select * from rfid_not_in_db"); 
	} 
	function GetRfidNotInDbByRfid($rfid)
	{  
		return $this->db->query("Select * from rfid_not_in_db where rfid='$rfid'"); 
	} 
	
	function GetRfidNotSameWithIgracias() 
	{  
		return $this->db->query("Select * from rfid_not_same_with_igracias"); 
	} 
	
	function GetMemberTypeApi($api_base_type,$api_key_value)
	{  
		return $this->db->query("select * from member_type_api where api_base_type='$api_base_type' and  api_key_value='$api_key_value'"); 
	}   
	
	function addAttendanceCheckout($item)
	{ 
		$this->db->insert('member_attendance_checkout', $item);
		return $this->db->insert_id();
	} 
	
	function addAttendance($item)
	{ 
		$this->db->insert('member_attendance', $item);
		return $this->db->insert_id();
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
	 
	function add_shelfloan_new_log($item)
	{
		$this->db->insert('shelfloan_new_log', $item);
		return $this->db->insert_id();
	}
	
	function add_rent_cart($item)
	{
		$this->db->insert('rent_cart', $item);
		return $this->db->insert_id();
	}
	
	function add_rent($item)
	{
		$this->db->insert('rent', $item);
		return $this->db->insert_id();
	}
	
	function edit_rent($id, $item)
	{
		$this->db->where('id', $id);
		return $this->db->update('rent', $item);
	}
	
	function edit_knowledge_stock($id, $item)
	{
		$this->db->where('id', $id);
		return $this->db->update('knowledge_stock', $item);
	}
}
?>