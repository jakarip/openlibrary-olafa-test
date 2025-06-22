<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StudentcaseModel extends CI_Model {  
	 
	private $table 	= 'room';
	private $id		= 'room_id';
	private $active = 'room_active';
	 
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
	
	function addImage($item)
	{
		return $this->mn->insert('room_gallery', $item);
	}
	   
	function edit($id, $item)
	{
		$this->mn->where($this->id, $id);
		return $this->mn->update($this->table, $item);
	}

    public function update($where, $data){
        $this->mn->update($this->table, $data, $where);
        return $this->mn->affected_rows();
    }    
	
	function getbyactiveid()
	{
		// $this->mn->order_by("room_id","asc");
		// $this->mn->where($this->active, '0');
		// return $this->mn->get($this->table);
		return $this->db->query("select * from room.room left join room.room_gallery on rg_room_id=room_id where room_active='0' group by room_id order by room_capacity,room_name");
	} 
	
	function getbyimage($id)
	{
		return $this->db->query("select * from room.room_gallery where rg_id='$id'");
	} 
	
	
	function getimagebyroomid($id)
	{
		return $this->db->query("select * from room.room_gallery where rg_room_id='$id'");
	} 
	
	function deleteImage($id)
	{
		$this->mn->where('rg_id', $id);
		return $this->mn->delete('room_gallery');
	}
}
