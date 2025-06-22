<?php
class Promotion_Setting_Model extends CI_Model 
{
	
	private $table = 'promotion_setting';
	private $id    = 'set_id';
	
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
		$this->db->order_by('prodi_name', 'asc');
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
		return $this->db->insert($this->table, $item);
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
	
	function getmax()
	{
		return $this->db->query("SELECT MAX(prov_id) as maks FROM ".$this->table);
	}
	
	function count_active()
	{
		$this->db->where('prodi_status', '1');
		return $this->db->count_all($this->table);
	} 
	
	function getprodi()
	{
		return $this->db->query("SELECT * FROM ms_prodi order by prodi_name");
	} 
	
	function getprodiactive()
	{
		return $this->db->query("SELECT * FROM ms_prodi left join ms_faculty on faculty_id=prodi_faculty_id   where prodi_status='1' and  faculty_status='1' order by prodi_name");
	}    

	function getbyquery2($where)
	{
			return $this->db->query("SELECT * FROM ".$this->table." $where");
	}
}
?>