<?php
class Ms_Kab_Model extends CI_Model 
{
	
	private $table = 'ms_kab';
	private $id    = 'kab_id';
	
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
								LEFT JOIN ms_prov ON kab_id_prov = prov_id
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
	
	function getmax($prov)
	{
		return $this->db->query("SELECT kab_code FROM ".$this->table." WHERE kab_id_prov = '$prov' ORDER BY kab_code DESC LIMIT 0, 1");
	}
	
	function getbyidview($id)
	{
		return $this->db->query("SELECT * FROM ".$this->table." 
								 LEFT JOIN ms_prov ON kab_id_prov = prov_id
								 WHERE kab_id = '$id'");
	}
	
	function update_view($id)
	{
		return $this->db->query("UPDATE ms_kec a LEFT JOIN ".$this->table." b ON a.kec_kab_id = b.kab_id
								 SET a.kec_kab_code = b.kab_code, a.kec_kab = b.kab_name
								 WHERE a.kec_kab_id = '$id'");
	}
	
	function update_view_with_prov($id)
	{
		return $this->db->query("UPDATE ms_kec a LEFT JOIN ".$this->table." b ON a.kec_kab_id = b.kab_id LEFT JOIN ms_prov c ON b.kab_id_prov = c.prov_id
								 SET a.kec_kab_code = b.kab_code, a.kec_kab = b.kab_name, 
								     a.kec_prov_id = c.prov_id, a.kec_prov_code = c.prov_code, a.kec_prov = c.prov_name
								 WHERE a.kec_kab_id = '$id'");
	}
}
?>