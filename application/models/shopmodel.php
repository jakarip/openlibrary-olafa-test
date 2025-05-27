<?php
class ShopModel extends CI_Model 
{
	
	private $table = 'tb_shop';
	private $id    = 'shop_id';
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}	
	
	// Query for new datatables purpose ;
	//--------------------------------------------------------------------------------------------------------------------------
	function dtquery($param,$table)
	{
			
		return $this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM $table $param[where] $param[order] $param[limit]");
		
	}
	
	function dtfiltered()
	{
		$result = $this->db->query('SELECT FOUND_ROWS() as jumlah')->row();
		
		return $result->jumlah;
	}
	
	function dtcount($table)
	{
	return $this->db->query("SELECT * from $table")->num_rows();
		//return $this->db->count_all($table);
		
	}
	//--------------------------------------------------------------------------------------------------------------------------
	 
}
?>