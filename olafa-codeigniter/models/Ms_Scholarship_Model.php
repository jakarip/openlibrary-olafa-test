<?php
class Ms_Scholarship_Model extends CI_Model 
{
	
	private $table = 'ms_scholarship';
	private $id    = 'scholarship_id';
	
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
		return $this->db->query("SELECT * FROM ".$this->table." where scholarship_id='$id'");
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
	
	function getlogin($username, $password)
	{
		return $this->db->query("SELECT * FROM ".$this->table." WHERE admin_username = '$username' AND admin_password = '$password'");
	} 
	
	function getprodischolarship()
	{
		return $this->db->query("SELECT * FROM prodi_scholarship 
		left join ms_prodi pro on ps_id_prodi=pro.prodi_id
		left join ms_scholarship sch on ps_id_scholarship=sch.scholarship_id");
	}
	
	function deleteprodischolarship()
	{
		return $this->db->query("delete from prodi_scholarship");
	}  
	
	function addprodischolarship($item)
	{
		$this->db->insert('prodi_scholarship', $item);
		return $this->db->insert_id();
	} 
	
	function deleteprodischolarshipbyid($id)
	{
		$this->db->where('ps_id_scholarship', $id);
		return $this->db->delete('prodi_scholarship');
	}  
	
	function getbyidprodischolarship($id)
	{
		return $this->db->query("SELECT * FROM prodi_scholarship where ps_id_scholarship='$id'");
	}
}
?>