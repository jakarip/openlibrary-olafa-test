<?php
class Lokermodel extends CI_Model 
{
	/**
	 * Constructor
	 */
	 
	private $table 	= 'loker_history';
	private $id		= 'history_id';
	private $mn 	= '';
	 
	function __construct()
	{
		parent::__construct(); 
	}	
	
	function getall()
	{
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
	}
	
	function edit($id, $item)
	{
		$this->db->where($this->id, $id);
		$this->db->update($this->table, $item);
	}
	
	function checkRfid($rfid)
	{
		return $this->db->query("select * from member where rfid1='$rfid' or rfid2='$rfid'");
	}
	
	function checkLoker($id)
	{
		return $this->db->query("select * from loker_history h left join member m on m.id=history_id_member where m.id='$id' and history_keluar is null");
	}
	
	function getnmrloker()
	{
		return $this->db->query("select * from loker_kunci where kunci_waktu is null");
	}
	
	function add_loker($item)
	{
		$this->db->insert($this->table, $item);
	}
	
	function edit_kunci($id, $item)
	{
		$this->db->where('kunci_nomor', $id);
		$this->db->update('loker_kunci', $item);
	}
	
	function edit_loker($id, $item)
	{
		$this->db->where($this->id, $id);
		$this->db->update($this->table, $item);
	}
	
	function delete($id)
	{
		$this->db->where($this->id, $id);
		$this->db->delete($this->table);
		
		$this->db->where('um_menu_id', $id);
		$this->db->delete('md_ugmenu');
	}
	
	
}
?>