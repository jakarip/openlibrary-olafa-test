<?php
class Removejournalmodel extends CI_Model {
	
	/**
	 * Constructor
	 */
	 
	private $table 	= 'journal_eproc_edition';
	private $id		= 'eproc_edition_id';
	 
	function __construct()
	{
		parent::__construct();
	}
	
	
	function getbyid($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->get($this->table);
	} 

    public function save($data){
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
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

    public function update($where, $data){
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }    
}
?>