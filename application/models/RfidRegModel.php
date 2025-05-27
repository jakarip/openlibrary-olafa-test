<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RfidRegModel extends CI_Model {  
	 
	private $table 	= 'rfid_not_same_with_igracias';
	private $id		= 'id';
	private $active = 'room_active';
	 
	function __construct()
	{
		parent::__construct();
		$this->mn = $this->load->database('menu', TRUE);
		$this->md = $this->load->database('oracle', TRUE);
	}  
	
	function getall()
	{	
		$this->db->order_by("room_id","asc");
		return $this->db->get($this->table); 
	}	
	
	function getbyid($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->get($this->table);
	} 

    public function save($data){
        return $this->db->insert($this->table, $data);
    }
	
	function add($item)
	{
		return $this->db->insert($this->table, $item);
	}
	
	function deletes($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->delete($this->table);
	}
	   
	function edit($id, $item)
	{
		$this->db->where($this->id, $id);
		return $this->db->update($this->table, $item);
	}

    public function update($where, $data){
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }    
	
	function getbyactiveid()
	{
		// $this->db->order_by("room_id","asc");
		// $this->db->where($this->active, '0');
		// return $this->db->get($this->table);
		return $this->db->query("select * from room.room left join room.room_gallery on rg_room_id=room_id where room_active='0' group by room_id order by room_capacity,room_name");
	} 
	
	public function member($username){
        return $this->db->query(" 
		select * from member where (master_data_user like '%$username%' or master_data_fullname like '%$username%')");
    }
	
	public function checkInsert($item){
        return $this->db->query(" 
		select * from rfid_not_same_with_igracias where username='".$item['username']."' or rfid='".$item['rfid']."' ");
    }
	
	public function checkEdit($item,$id){
        return $this->db->query(" 
		select * from rfid_not_same_with_igracias where rfid='".$item['rfid']."' and id!='$id'");
    }
	
	
	
	function addNotDb($item)
	{
		return $this->db->insert('rfid_not_in_db', $item);
	}
	 
	
	function deleteNotDb($id)
	{
		$this->db->where('id', $id);
		return $this->db->delete('rfid_not_in_db');
	}
	
	public function checkInsertNotDb($item){
        return $this->db->query(" 
		select * from rfid_not_in_db where rfid='".$item['rfid']."' ");
    }
}
