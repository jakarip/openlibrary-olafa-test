<?php
class Ms_Kec_Model extends CI_Model 
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
	
	function getmax($kab)
	{
		return $this->db->query("SELECT kec_code FROM ".$this->table." WHERE kec_kab_id = '$kab' ORDER BY kec_code DESC LIMIT 0, 1");
	}
	
	function getaddress($param)
	{
		return $this->db->query("SELECT kec_id, kec_prov, kec_kab, kec_name FROM ".$this->table."
								 WHERE kec_prov LIKE '%$param%' OR kec_kab LIKE '%$param%' OR kec_name LIKE '%$param%'
								 LIMIT 0, 30");
	}

    function getkecbyname($name)
    {
        return $this->db->query("SELECT * FROM ms_kec  where kec_name like'%$name%' or kec_kab like'%$name%' or kec_prov like'%$name%' order by kec_prov,kec_kab,kec_name limit 20");
    }

    function getkodekec($id)
    {
        return $this->db->query("SELECT * FROM ms_kec where kec_kab_id='$id' order by kec_name");
    }
}
?>