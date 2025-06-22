<?php
class Documentselectionmodel extends CI_Model {
	
	private $table  = 'documentselection';
	private $id 	= 'ds_id';
	function __construct()
	{
		parent::__construct();
	} 
	
	function getall()
	{
		$this->db->from($this->table);  
		$this->db->order_by("ds_name","ASC"); 
		return $this->db->get();
	}
	
	function getbyid($id)
	{	$this->db->where($this->id, $id);
		return $this->db->get($this->table); 
	}
	
	function getbyquery($where, $limit)
	{
		return $this->db->query("SELECT * FROM documentselection $where ORDER BY ds_id ASC $limit");
	}
	
	function countbyquery($where)
	{
		return $this->db->query("SELECT * FROM documentselection $where ORDER BY ds_id ASC")->num_rows;
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
	function delete($id)
	{
		$this->db->where($this->id, $id);
		$this->db->delete($this->table);
	}
	
}
?>