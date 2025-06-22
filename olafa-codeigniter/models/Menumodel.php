<?php
class Menumodel extends CI_Model 
{
	/**
	 * Constructor
	 */
	 
	private $table 	= 'md_menu';
	private $id		= 'menu_id';
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
	
	function getMappingUserGroup($menu_id)
	{
		return $this->mn->query("select * from md_ugmenu left join md_usergroup on ug_id=um_ug_id where um_menu_id='$menu_id' order by ug_name");
	}
	
	function delete($id)
	{
		$this->mn->where($this->id, $id);
		$this->mn->delete($this->table);
		
		$this->mn->where('um_menu_id', $id);
		$this->mn->delete('md_ugmenu');
	}
	
	
}
?>