<?php
class Subscribe_Admin_Model extends CI_Model 
{
	
	private $table = 'member_subscribe';
	private $id    = 'subscribe_id';

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
		return $this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM ".$this->table." left join member on subscribe_id_member=id
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
		$this->db->from($this->table);
		$this->db->join('member', 'member.id = subscribe_id_member');
		$this->db->where($this->id, $id); 
		return $this->db->get();
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
	
	function edit_t_mst_user_login($id, $item)
	{
		$this->db->where('C_USERNAME', $id);
		return $this->db->update('masterdata.t_mst_user_login', $item);
	}
	
	function delete($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->delete($this->table);
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/ 
	
	function aktivasi($where, $item)
	{
		$this->db->where($where);
		return $this->db->update($this->table, $item);
	}
	
	function getMember($id)
	{
		return $this->db->query("SELECT * FROM ".$this->table." m left join masterdata.t_mst_user_login on master_data_user=c_username WHERE m.id = '$id' ");
	}
	
	
}
?>