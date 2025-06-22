<?php
class Payment_validate_Model extends CI_Model 
{
	
	private $table = 'participant_registration';
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
								join participant on par_id = sreg_id_participant
								left join pin on pin_id=sreg_id_pin
								left join periode on periode_id=pin_id_periode
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

	function getByUserPin($user, $pin)
    {
        return $this->db->query("SELECT * FROM ".$this->table."
								 LEFT JOIN pin ON sreg_id_pin = pin_id
								 WHERE sreg_id_participant = '$user' AND pin_transaction_number = '$pin'");
    }

    function editActiveByPin($user, $pin)
    {
        return $this->db->query("UPDATE ".$this->table." SET sreg_active = 'Y'
								 WHERE sreg_id_participant = '$user' AND sreg_id_pin = '$pin'");
    }

    function getValidateOnline($id)
    {
        return $this->db->query("SELECT * FROM ".$this->table."
								 LEFT JOIN pin ON sreg_id_pin = pin_id
								 LEFT JOIN periode ON pin_id_periode = periode_id
								 WHERE sreg_id = '$id'");
    }

	function update_pass($id)
	{
		return $this->db->query("UPDATE ".$this->table." SET sreg_status = 'Y' WHERE sreg_id IN ($id)");
	}
	
	function getall_view($id) 
	{ 
		return $this->db->query("SELECT * FROM ".$this->table."
								 LEFT JOIN participant ON sreg_id_participant = par_id 
								 LEFT JOIN pin ON sreg_id_pin= pin_id
								 LEFT JOIN periode ON pin_id_periode = periode_id
								 LEFT JOIN ms_school ON par_id_school = school_id
								 LEFT JOIN ms_location ON sreg_id_location = location_id
								 LEFT JOIN ms_kec ON par_id_kec = kec_id where sreg_id_pin in ($id)");
	}
	
	function getsimulation_utbk() 
	{ 
		return $this->db->query("SELECT * FROM ".$this->table."
								 LEFT JOIN participant ON sreg_id_participant = par_id
								 LEFT JOIN ms_school ON par_id_school = school_id 
								 LEFT JOIN pin ON pin_id= sreg_id_pin
								 LEFT JOIN periode ON periode_id= pin_id_periode
								 WHERE sreg_status_print = 'Y' and (sreg_status_pass is null)
								 and periode_track_type='utbk' 
								 ORDER BY par_fullname ASC, school_name ASC"); 
	}
	
	function getsimulation_nonutbk() 
	{ 
		return $this->db->query("SELECT * FROM ".$this->table."
								 LEFT JOIN participant ON sreg_id_participant = par_id
								 LEFT JOIN ms_school ON par_id_school = school_id 
								 LEFT JOIN pin ON pin_id= sreg_id_pin
								 LEFT JOIN periode ON periode_id= pin_id_periode
								 WHERE sreg_status_print = 'Y' and (sreg_status_pass is null)
								 and periode_track_type!='utbk' 
								 ORDER BY par_fullname ASC, school_name ASC"); 
	}
	
	function count_complete()
	{ 
		$this->db->where('sreg_status_print', 'Y');
		// $this->db->where('sreg_status_pass', 'N');
		return $this->db->count_all_results($this->table);
	}
	
	function reset_status_byid($id)
	{
		// echo "UPDATE ".$this->table." SET sreg_status_print = 'N', sreg_status_pass = NULL, WHERE sreg_id_pin = '{$id}'";
		return $this->db->query("UPDATE ".$this->table." SET sreg_status_print = 'N', sreg_status_pass = NULL WHERE sreg_id_pin = '{$id}'");
	}
	
	function getAllByUserPin($user,$pin)
	{
		return $this->db->query("select * from participant_registration
			LEFT JOIN participant on par_id = sreg_id_participant
			LEFT JOIN ms_school on par_id_school = school_id
			LEFT JOIN ms_kec ON par_id_kec = kec_id 
			LEFT JOIN pin on sreg_id_pin = pin_id
			LEFT JOIN periode on periode_id = pin_id_periode
			LEFT JOIN ms_location ON sreg_id_location = location_id
			WHERE sreg_id_participant = '$user' AND pin_transaction_number = '$pin'");
	}

	function checkPinActive($user)
	{
			return $this->db->query("SELECT COUNT(*) as jml 
																FROM participant_registration 
																WHERE sreg_id_participant = '$user' AND (sreg_status_pass = '' OR sreg_status_pass IS NULL)")->row()->jml;
	}

	function getCountRegClosed($iduser)
	{
			return $this->db->query("SELECT COUNT(*) as jml 
																FROM participant_registration
								WHERE sreg_status_print = 'Y' AND sreg_id_participant = '$iduser'");
	}

	function getParticipantReg($pin_id)
	{
			return $this->db->query("SELECT * FROM participant_registration pr
			LEFT JOIN participant p on sreg_id_participant=par_id
			LEFT JOIN pin on sreg_id_pin=pin_id
			LEFT JOIN periode on periode_id=pin_id_periode
			LEFT JOIN ms_school on school_id=par_id_school
			LEFT JOIN ms_prodi on prodi_id=sreg_choice_prodi
			WHERE sreg_id_pin='".$pin_id."' AND sreg_status_pass='Y'")->row();
	} 
	
	function deletebypin($id)
	{
		$this->db->where('sreg_id_pin', $id);
		return $this->db->delete($this->table);
	}

	function getLetterNumber($year)
	{
			return $this->db->query("SELECT sreg_letter_number from participant_registration where sreg_letter_number like'%".$year."'
			order by sreg_letter_number desc");
	}
}
?>