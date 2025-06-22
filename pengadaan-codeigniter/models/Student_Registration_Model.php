<?php
class Student_Registration_Model extends CI_Model 
{
	
	private $table = 'student_registration';
	private $id    = 'sreg_id';
	
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
								 LEFT JOIN ms_school ON sreg_id_school = school_id
								 LEFT JOIN ms_kec ON sreg_id_kec = kec_id
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
	function update_pass($id)
	{
		return $this->db->query("UPDATE ".$this->table." SET sreg_status = 'Y' WHERE sreg_id IN ($id)");
	}
	
	
	
	function getsimulation() 
	{
		return $this->db->query("SELECT * FROM participant_registration
								 JOIN participant ON par_id = sreg_id_participant
								 LEFT JOIN ms_school ON par_id_school = school_id
								 WHERE sreg_status_pass = 'N' AND sreg_status_print = 'Y'
								 ORDER BY par_fullname ASC, school_name ASC");
	}
	
	function count_complete()
	{
		$this->db->where('sreg_status_print', 'Y');
		$this->db->where('sreg_status_pass', 'N');
		return $this->db->count_all_results('participant_registration');
	}
	
	function reset_status_byid($id)
	{
		return $this->db->query("UPDATE ".$this->table." SET sreg_print_status = 'N', sreg_step = '5' WHERE sreg_id_student = '{$id}'");
	}

	function getCountRegClosed($iduser)
    {
        return $this->db->query("SELECT COUNT(*) as jml 
                                 FROM participant_registration
								 WHERE sreg_status_print = 'Y' AND sreg_id_participant = '$iduser'");
    }
}
?>