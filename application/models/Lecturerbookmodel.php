<?php
class Lecturerbookmodel extends CI_Model {
	 
	private $table 	= 'telu_press';
	private $id		= 'press_id';
	private $active = 'room_active';
	
	/**
	 * Constructor
	 */
	 
	 function getall()
	{ 	
		// $this->db->from($this->table);  
		// $this->db->order_by('jd_judul ASC, jd_edisi ASC'); 
		// return $this->db->get();
		
		return $this->db->query("SELECT *   
			FROM telu_press left join knowledge_item kit on kit.id=press_id_knowledge_item order by press_faculty_unit, press_author");
	}
	 
	
	function getbyquery($where="", $limit="")
	{
		//return $this->db->query("SELECT * FROM jurnaldikti $where ORDER BY jd_judul ASC, jd_edisi ASC $limit");
		return $this->db->query("SELECT *   
		FROM telu_press   $where order by press_faculty_unit, press_author ");
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
	
	function getItemId($code)
	{  
		$db = $this->db->query("select kt.id from knowledge_item kt where code='".trim($code)."'")->row();
		// print_r($db);
		if($db) return $db->id;
		else return '';
	}
}
?>