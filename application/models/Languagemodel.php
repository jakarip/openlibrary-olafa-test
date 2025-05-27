<?php
class Languagemodel extends CI_Model 
{
	/**
	 * Constructor
	 */
	 
	private $table 	= 'md_language';
	private $id		= 'lang_id';
	 
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
	
	function checkLanguage($lang)
	{
		return $this->db->query("select * from md_language where lang_var='$lang'");
	}
	
	
}
?>