<?php
class Student_Simulation_Temp_Model extends CI_Model 
{
	
	private $table = 'student_simulation_temp';
	private $id    = 'sst_id';
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	// Query for new datatables purpose ;
	//--------------------------------------------------------------------------------------------------------------------------
	function dtquery($param)
	{
		return $this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM ".$this->table."
								 LEFT JOIN participant_registration ON sst_id_sreg = sreg_id
								 JOIN participant ON par_id = sreg_id_participant
								 LEFT JOIN ms_school ON par_id_school = school_id
								 LEFT JOIN ms_prodi ON sst_choice_prodi = prodi_id
								 $param[where] $param[order] $param[limit]");
	}
	
	function dtfiltered()
	{
		$result = $this->db->query('SELECT FOUND_ROWS() as jumlah')->row();
		
		return $result->jumlah;
	}
	
	function dtcount()
	{
		return $this->db->count_all($this->table);
	}
	//--------------------------------------------------------------------------------------------------------------------------
	
	function getall()
	{
		return $this->db->get($this->table);
	}
	
	function getbyquery($param)
	{
		return $this->db->query("SELECT * FROM ".$this->table." $param[where] $param[order] $param[limit]");
	}
	
	function countbyquery($param)
	{
		$result = $this->db->query("SELECT COUNT(".$this->id.") as jumlah FROM ".$this->view." $param[where]")->row();
		
		if(!empty($result))
			return $result->jumlah;
		else
			return 0;
	}
	
	function countall()
	{
		return $this->db->count_all($this->table);
	}
	
	function getby($item)
	{
		$this->db->where($item);
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
		return $this->db->insert_id();
	}
	
	function add_batch($item)
	{
		return $this->db->insert_batch($this->table, $item);
	}
	
	function edit($id, $item)
	{
		$this->db->where($this->id, $id);
		return $this->db->update($this->table, $item);
	}
	
	function delete($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->delete($this->table);
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
	
	function getall_view() 
	{
		return $this->db->query("SELECT * FROM ".$this->table."
								 LEFT JOIN participant_registration ON sst_id_sreg = sreg_id
								 JOIN participant ON par_id = sreg_id_participant
								 LEFT JOIN ms_school ON par_id_school = school_id");
	}
	
	function truncate()
	{
		return $this->db->truncate($this->table);
	}
	
	function count()
	{
		return $this->db->count_all_results($this->table);
	}
}
?>