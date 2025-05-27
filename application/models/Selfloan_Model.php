<?php
class Selfloan_Model extends CI_Model 
{
	
	private $table = 'rent';
	private $id    = 'id';
	
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
	
	function GetRentBook($id)
	{
		return $this->db->query("select rent_date,return_date_expected,title, ks.code from rent left join knowledge_stock ks on ks.id=knowledge_stock_id
		left join knowledge_item kit on kit.id=knowledge_item_id
		where member_id='$id'
		and return_date is null");
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