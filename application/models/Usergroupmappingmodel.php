<?php
class Usergroupmappingmodel extends CI_Model 
{
	/**
	 * Constructor
	 */
	 
	private $table 	= 'md_uguser';
	private $id		= 'uu_id';
	 
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
	
	function delete($id)
	{
		$this->db->where($this->id, $id);
		$this->db->delete($this->table); 
	}
	
	function getMappingUserGroup($ug_id,$user_id)
	{
		return $this->db->query("select * from md_uguser where uu_user_id='$user_id' and uu_ug_id!='$ug_id'");
	}
	
	
}
?>