<?php
class KM_Model extends CI_Model 
{
	
	private $table = 'member';
	private $id    = 'id';

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
		return $this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM ".$this->table." left join member_subscribe on subscribe_id_member=id  and subscribe_status='1'
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
	 
	
	function getAll($sql_tit)
	{
		return $this->db->query("SELECT id,title,abstract_content FROM knowledge_item $sql_tit");
	} 
	
	function getAllLimit($sql_tit,$offset,$dataPerPage)
	{
		return $this->db->query("SELECT id,title,author,abstract_content FROM `knowledge_item` $sql_tit LIMIT $offset,$dataPerPage");
	}
	
	 
	
	
}
?>