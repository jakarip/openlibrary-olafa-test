<?php
class Setting_Km_Model extends CI_Model 
{
	
	private $table = 'setting_km';
	private $id    = 'km_id';
	
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
	function get_target($type,$start,$end)
	{
		return $this->db->query("SELECT * FROM ".$this->table." where km_type='$type' and km_date between '$start' and '$end'");
	} 
	function get_realisasi($type,$start,$end)
	{
		if($type=='register'){
			return $this->db->query("SELECT DATE_FORMAT(par_input_date,'%Y-%m')date,count(*) total from participant where par_input_date between '$start 00:00:00' and '$end 23:59:59'
			group by DATE_FORMAT(par_input_date,'%Y-%m')");
		}
		else if($type=='request_token'){
			return $this->db->query("SELECT DATE_FORMAT(sreg_input_date,'%Y-%m')date,count(*) total from participant_registration where sreg_input_date between '$start 00:00:00' and '$end 23:59:59'
			group by DATE_FORMAT(sreg_input_date,'%Y-%m')");
		}
		else if($type=='token'){
			return $this->db->query("SELECT DATE_FORMAT(sreg_active_date,'%Y-%m')date,count(*) total from participant_registration where sreg_active_date between '$start 00:00:00' and '$end 23:59:59'
			group by DATE_FORMAT(sreg_active_date,'%Y-%m')");
		} 
		else if($type=='up3'){
			return $this->db->query("SELECT DATE_FORMAT(sreg_active_date,'%Y-%m')date,count(*) total from participant_registration where sreg_active_date between '$start 00:00:00' and '$end 23:59:59'
			group by DATE_FORMAT(sreg_active_date,'%Y-%m')");
		} 
	} 
}
?>