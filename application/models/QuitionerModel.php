<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class QuitionerModel extends CI_Model {  
	 
	private $table 	= 'quitioner_responder';
	private $id		= 'responder_id'; 
	 
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
		return $this->db->query("select * from telu8381_room.room left join telu8381_room.room_gallery on rg_room_id=room_id where room_active='0' group by room_id order by room_capacity,room_name");
	}  
	
	function quitioner()
	{ 
		return $this->db->query("select * from telu8381_room.quitioner");
	} 
	 
	function checkQuitioner()
	{ 
		return $this->db->query("select * from telu8381_room.quitioner_responder where id='".$this->session->userdata('memberid')."'");
	} 
}
