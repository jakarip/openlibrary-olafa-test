<?php
class DiktiModel extends CI_Model {

	private $table 	= 'jurnaldikti';
	private $id		= 'jd_id';
	
	function __construct()
	{
		parent::__construct();
	}  
	function getall()
	{ 	
		// $this->db->from($this->table);  
		// $this->db->order_by('jd_judul ASC, jd_edisi ASC'); 
		// return $this->db->get();
		
		return $this->db->query("SELECT *, count(ki.id) eks   
			FROM knowledge_item ki
			LEFT JOIN knowledge_stock ks ON ki.id = ks.knowledge_item_id where ki.knowledge_type_id='43' group by ki.id order by ki.id");
	}
	
	function getbyid($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->get($this->table); 
	}
	
	function getbyquery($where, $limit)
	{
		//return $this->db->query("SELECT * FROM jurnaldikti $where ORDER BY jd_judul ASC, jd_edisi ASC $limit");
		return $this->db->query("SELECT *, count(ki.id) eks  FROM knowledge_item ki LEFT JOIN knowledge_stock ks ON ki.id = ks.knowledge_item_id where ki.knowledge_type_id='43' $where group by ki.id  ORDER BY published_year desc, title ASC $limit");
	}
	
	function countbyquery($where)
	{
		return $this->db->query("SELECT * FROM jurnaldikti $where ORDER BY jd_judul ASC, jd_edisi ASC")->num_rows;
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
}
?>