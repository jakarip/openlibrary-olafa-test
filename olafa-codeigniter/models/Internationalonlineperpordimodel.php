<?php
class Internationalonlineperprodimodel extends CI_Model {
	
	private $table  = 'internationalonlineperprodi';
	private $id 	= 'io_id';
	function __construct()
	{
		parent::__construct();
	} 
	
	function getall()
	{
		$this->db->from($this->table);  
		$this->db->order_by("io_name","ASC"); 
		return $this->db->get();
	}
	
	function getbyid($id)
	{	$this->db->where($this->id, $id);
		return $this->db->get($this->table); 
	}
	
	function getbyquery($where, $limit)
	{
		return $this->db->query("SELECT * FROM jurnalinternational_online $where ORDER BY jio_name ASC $limit");
	}
	
	function countbyquery($where)
	{
		return $this->db->query("SELECT * FROM jurnalinternational_online $where ORDER BY jio_name ASC")->num_rows;
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
	
	function deletes($io_id,$prodi_code)
	{	
		return $this->db->query("delete from internationalonlineperprodi where io_id='$io_id' and prodi_code='$prodi_code'");
	}
	
	function checkExisting($io_id,$prodi_code)
	{	
		// echo "select * from internationalonlineperprodi where io_id='$io_id' and prodi_code='$prodi_code'";
		// return $this->db->query("select * from internationalonlineperprodi where io_id='$io_id' and prodi_code='$prodi_code'");
	}
	
}
?>