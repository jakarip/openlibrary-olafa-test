<?php
class Katalog_Model extends CI_Model 
{
	
	private $table = 'so_edition';
	private $id    = 'so_id';
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	// Query for new datatables purpose ;
	//--------------------------------------------------------------------------------------------------------------------------
	function dtquery($param)
	{
		return $this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM ".$this->table."
								$param[where] $param[order] $param[limit]");
	}
	
	function dtfiltered()
	{
		$result = $this->db->query('SELECT FOUND_ROWS() as jumlah')->row();
		
		return $result->jumlah;
	}
	
	function dtcount()
	{
		return $this->db->count_all($this->table);
	}
	//--------------------------------------------------------------------------------------------------------------------------
	
	function getall()
	{
		return $this->db->get($this->table);
	}
	
	function getbyquery($param)
	{
		return $this->db->query("SELECT * FROM ".$this->table." $param[where] $param[order] $param[limit]");
	}
	
	function getadmin()
	{
		return $this->db->query("SELECT id,master_data_user,master_data_fullname FROM member where member_type_id='1' and member_class_id in (1,7) and status='1' order by master_data_fullname");
	}
	
	function getlocation()
	{
		return $this->db->query("SELECT id,name FROM item_location where show_as_footer='1' order by name");
	}
	
	function getknowledge_type_id()
	{
		return $this->db->query("SELECT id,name FROM knowledge_type where active='1' and id not in (4,5,6,21) order by name");
	}
	
	function countbyquery($param)
	{
		$result = $this->db->query("SELECT COUNT(".$this->id.") as jumlah FROM ".$this->view." $param[where]")->row();
		
		if(!empty($result))
			return $result->jumlah;
		else
			return 0;
	}
	
	function countall()
	{
		return $this->db->count_all($this->table);
	}
	
	function getby($item)
	{
		$this->db->where($item);
		return $this->db->get($this->table);
	}
	
	function getbyid($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->get($this->table);
	}
	
	function add($item)
	{
		$this->db->insert($this->table, $item);
		return $this->db->insert_id();
	}
	
	function edit($id, $item)
	{
		$this->db->where($this->id, $id);
		return $this->db->update($this->table, $item);
	}
	
	function editNotId($id, $item)
	{
		$this->db->where($this->id."!=", $id);
		return $this->db->update($this->table, $item);
	}
	
	function delete($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->delete($this->table);
	}
	
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
	
	function addTable($table,$item)
	{
		$this->db->insert($table, $item);
		return $this->db->insert_id();
	}
	
	function editTable($table,$colomn,$id, $item) 
	{
		$this->db->where($colomn, $id);
		return $this->db->update($table, $item);
	} 
	
	function deleteTable($table,$colomn,$id)
	{
		$this->db->where($colomn, $id);
		return $this->db->delete($table);
	}
	
	function deleteAllSOStock($memberid,$id)
	{ 
		$this->db->where('sos_id_user', $memberid);  
		$this->db->where('sos_id_so', $id);
		return $this->db->delete('so_stock'); 
	} 

	function getstock($temp)
	{
		$sql = "select code from knowledge_stock where lower(code) in ($temp)"; 
		return $this->db->query($sql);
	} 

	function getbystock($temp,$item)
	{    
		// echo "select id from knowledge_stock where code in ($temp)"; 
		$delete = "delete from so_stock where sos_id_so='".$item['sos_id_so']."' and sos_id_user='".$item['sos_id_user']."'  and sos_id_stock in (select id from knowledge_stock where lower(code) in ($temp) )";
		// echo $delete;
		$this->db->query($delete);
		
		$insert = "insert into so_stock select '' sos_id,'".$item['sos_id_so']."' sos_id_so, id sos_id_stock,'".$item['sos_id_user']."' sos_id_user,'".date("Y-m-d")."' sos_date,'".$item['sos_status']."' sos_status,'".$item['sos_filename']."' sos_filaname,'".$item['sos_id_location']."' sos_id_location
		from knowledge_stock where lower(code) in ($temp)"; 

		// $insert = "insert into so_stock select '' sos_id,'".$item['sos_id_so']."' sos_id_so, id sos_id_stock,'".$item['sos_id_user']."' sos_id_user,'".date("Y-m-d")."' sos_date,'".$item['sos_status']."' sos_status,'".$item['sos_filaname']."' sos_filaname
		// from knowledge_stock where code in ($temp) and id not in (select sos_id_stock from so_stock where sos_id_so='".$item['sos_id_so']."' and sos_id_user='".$item['sos_id_user']."')"; 
		// echo $insert;
 
		return $this->db->query($insert);
	}

	function getbystockOne($temp,$item)
	{   
		$temp = "'".implode("','",$temp)."'"; 
		$temp =  str_replace(array("\r", "\n"), '', $temp);
		// echo "select id from knowledge_stock where code in ($temp)";
		$delete = "delete from so_stock where sos_id_so='".$item['sos_id_so']."' and sos_id_user='".$item['sos_id_user']."'  and sos_id_stock in (select id from knowledge_stock where lower(code)=$temp )";
		// echo $delete;
		$this->db->query($delete);
		
		$insert = "insert into so_stock select '' sos_id,'".$item['sos_id_so']."' sos_id_so, id sos_id_stock,'".$item['sos_id_user']."' sos_id_user,'".date("Y-m-d")."' sos_date,'".$item['sos_status']."' sos_status,'".$item['sos_filename']."' sos_filaname,'".$item['sos_id_location']."' sos_id_location
		from knowledge_stock where lower(code)=$temp"; 

		// $insert = "insert into so_stock select '' sos_id,'".$item['sos_id_so']."' sos_id_so, id sos_id_stock,'".$item['sos_id_user']."' sos_id_user,'".date("Y-m-d")."' sos_date,'".$item['sos_status']."' sos_status,'".$item['sos_filaname']."' sos_filaname
		// from knowledge_stock where code in ($temp) and id not in (select sos_id_stock from so_stock where sos_id_so='".$item['sos_id_so']."' and sos_id_user='".$item['sos_id_user']."')"; 
		// echo $insert;
 
		return $this->db->query($insert);
	}


	function GetRentBook($id)
	{
		return $this->db->query("select rent_date,return_date_expected,title, ks.code from rent left join knowledge_stock ks on ks.id=knowledge_stock_id
		left join knowledge_item kit on kit.id=knowledge_item_id
		where member_id='$id'
		and return_date is null");
	}
	
	function CheckBarcode($id)
	{ 
		return $this->db->query("select ks.id from knowledge_stock ks where ks.code='$id'");
	}
	
	function CheckBook($id)
	{ 
		return $this->db->query("select *,ks.id ksid from knowledge_stock ks 
		left join knowledge_item kit on kit.id=knowledge_item_id 
		left join knowledge_type kt on kt.id=kit.knowledge_type_id where ks.code='$id'");
	}
	
	function CheckMemberBook($type,$username)
	{ 
		return $this->db->query("select *,(select count(*) total from member_type_permission where knowledge_type_id='$type' 
		and member_type_id=mt.id) status_pinjam, (select count(*) from rent left join knowledge_stock ks on ks.id=knowledge_stock_id
		left join knowledge_item kit on kit.id=knowledge_item_id
		where member_id=m.id
		and return_date is null)rent_book from member m
		left join member_type mt on mt.id=m.member_type_id where master_data_user='$username'");
	}
	
	function CheckMember($username)
	{ 
		return $this->db->query("select * from member m
		left join member_type mt on mt.id=m.member_type_id where master_data_user='$username'");
	}
}
?>