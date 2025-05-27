<?php
class Usergroupmodel extends CI_Model 
{
	/**
	 * Constructor
	 */
	 
	private $table 	= 'md_usergroup';
	private $id		= 'ug_id';
	private $mn 	= '';
	 
	function __construct()
	{
		parent::__construct();
		$this->mn = $this->load->database('menu', TRUE);
	}	
	
	function getall()
	{
		return $this->mn->get($this->table);
	}
	
	function getbyid($id)
	{
		$this->mn->where($this->id, $id);
		return $this->mn->get($this->table);
	}
	
	function add($item)
	{
		$this->mn->insert($this->table, $item);
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
		
		$this->mn->where('um_ug_id', $id);
		$this->mn->delete('md_ugmenu');
		
		$this->mn->where('uu_ug_id', $id);
		$this->mn->delete('md_uguser');
	}
	
	
}
?>