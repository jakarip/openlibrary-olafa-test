<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BlacklistModel extends CI_Model {  
	 
	private $table 	= 'blacklist';
	private $id		= 'bl_username';
	 
	function __construct()
	{
		parent::__construct();
		$this->mn = $this->load->database('menu', TRUE);
	}  
	
	function getall()
	{	
		$this->mn->order_by("room_id","asc");
		return $this->mn->get($this->table); 
	}	
	
	function getbyid($id)
	{
		$this->mn->where($this->id, $id);
		return $this->mn->get($this->table);
	} 

    public function save($data){
        return $this->mn->insert($this->table, $data);
    }
	
	function add($item)
	{
		return $this->mn->insert($this->table, $item);
	}  

    public function update($where, $data){
        $this->mn->update($this->table, $data, $where);
        return $this->mn->affected_rows();
    }     
	
	function delete($id)
	{
		$this->mn->where($this->id, $id);
		return $this->mn->delete($this->table);
	}
	function edit($item)
	{
		 return $this->mn->query("update booking set bk_status='Cancel' where bk_status='Request' and bk_username='$item'");
	}
}
