<?php
class Dashboard_Model extends CI_Model 
{
	
	private $table = 'ms_kec';
	private $id    = 'kec_id';
	
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
	
	function get_dashboard_token($id)
	{
		return $this->db->query("SELECT
									SUM( CASE WHEN pin_status = 'Y' THEN 1 ELSE 0 END ) AS ya,
									SUM( CASE WHEN pin_status = 'N' THEN 1 ELSE 0 END ) AS tidak 
								 FROM pin where pin_id_periode='$id' "); 
	}
	
	function get_dashboard_participant()
	{
		return $this->db->query("
				SELECT
						SUM( CASE WHEN par_active = '1' THEN 1 ELSE 0 END ) AS ya,
						SUM( CASE WHEN par_active = '0' THEN 1 ELSE 0 END ) AS tidak 
				from	participant "); 
	}  
	
	function get_dashboard_used($id)
	{
		return $this->db->query("SELECT
									SUM( CASE WHEN sreg_status_Print = 'Y' THEN 1 ELSE 0 END ) AS ya,
									SUM( CASE WHEN sreg_status_Print = 'N' THEN 1 ELSE 0 END ) AS tidak 
								 FROM participant_registration 
								 LEFT JOIN participant ON sreg_id_participant = par_id
								 LEFT JOIN pin ON sreg_id_pin = pin_id
								 WHERE pin_status = 'Y' and pin_id_periode='$id'"); 
	}  
	
	function get_dashboard_lulus($id)
	{
		return $this->db->query("SELECT
									SUM( CASE WHEN sreg_status_pass = 'Y' and sreg_status_Print = 'Y' THEN 1 ELSE 0 END ) AS ya,
									SUM( CASE WHEN sreg_status_pass = 'N' and sreg_status_Print = 'Y' THEN 1 ELSE 0 END ) AS tidak 
								 FROM participant_registration join pin on pin_id=sreg_id_pin where pin_id_periode='$id'"); 
	}  
	
	function get_dashboard_ke($id)
	{
		return $this->db->query("SELECT sreg_choice, COUNT(*) as jml
								 FROM participant_registration join pin on pin_id=sreg_id_pin where pin_id_periode='$id'
								 GROUP BY sreg_choice"); 
	}  
	
	function get_dashboard_prodi($id)
	{
		return $this->db->query("SELECT prodi_name, COUNT(*) as jml
								 FROM participant_registration LEFT JOIN ms_prodi ON sreg_choice_prodi = prodi_id
								join pin on pin_id=sreg_id_pin where pin_id_periode='$id'
								 and prodi_name != ''
								 GROUP BY sreg_choice_prodi"); 
	} 
	
	function get_dashboard_affiliate()
	{
		return $this->db->query("select 
		SUM( CASE WHEN total > 0 THEN 1 ELSE 0 END ) AS ya,
		SUM( CASE WHEN total = '0' THEN 1 ELSE 0 END ) AS tidak from (
		SELECT
		(select count(sreg_referral) from participant_registration where sreg_referral=ref.ref_id) total
		from	referral ref) a
		"); 
	}  
}
?>