<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AmnestyModel extends CI_Model {  
	 
	private $table 	= 'amnesty_denda';
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
	 
	public function member($username){
        return $this->db->query(" 
		select * from member join t_mst_prodi on master_data_course=c_kode_prodi where master_data_user like '%$username%' or master_data_fullname like '%$username%' order by master_data_fullname ");
    }
	
	public function checkInsert($item){
        return $this->db->query(" 
		select * from amnesty_denda where username_id='".$item['username_id']."'");
    }
	 
	
	 
}
