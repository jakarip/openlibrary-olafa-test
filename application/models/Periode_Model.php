<?php
class Periode_Model extends CI_Model 
{
	
	private $table = 'periode';
	private $id    = 'periode_id';
	
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
		
		return $this->db->query("SELECT SQL_CALC_FOUND_ROWS *, (select count(*) from periode_fee where fee_id_periode=periode_id)total, (select group_concat(test_date  ORDER BY test_date ASC) from periode_test where test_id_periode=periode_id)exam FROM ".$this->table." 
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
	function getLastData($format)
	{
		return $this->db->query("SELECT * FROM periode where periode_code like '".$format."%' order by periode_code desc limit 1");
	}

	function getActive()
	{
		return $this->db->query("SELECT * FROM periode where periode_status='1'");
	}

	function getActiveNow()
	{
			return $this->db->query("SELECT * FROM periode WHERE periode_status = '1' and periode_end_date >= now()");
	}
	
	function getsettingbyid($id)
	{
		return $this->db->query("SELECT * FROM periode_fee where fee_id_periode='$id'");
	}

	function getComponentActive()
	{
		return $this->db->query("SELECT * FROM ms_component where component_status='1'");
	}

	function getProdiActive()
	{
		return $this->db->query("SELECT * FROM ms_prodi left join ms_faculty on faculty_id=prodi_faculty_id where faculty_status='1' and prodi_status='1'");
	} 
	
	function addFee($item)
	{
		$this->db->insert('periode_fee', $item);
		return $this->db->insert_id();
	}
	
	function deletefeebyid($id)
	{
		return $this->db->query("delete from periode_fee where fee_id_periode='$id'");
	}   
	
	function getfeebyprodi($prodi,$periode)
	{
		return $this->db->query("select * from periode_fee
		join ms_component on fee_id_component=component_id 
		join periode on fee_id_periode=periode_id 
		where component_status='1' and periode_status='1' and fee_id_periode='$periode' and fee_id_prodi='$prodi' order by component_id");
	} 
	
	function getfeebyid($id)
	{
		return $this->db->query("select * from periode_fee join ms_component on fee_id_component=component_id  where fee_id='$id'");
	}  

	function getAllOrderByDate()
	{
		return $this->db->query("SELECT * FROM periode order by periode_start_date DESC");
	}

	
	function dtquery_test($param)
	{
		
		return $this->db->query("SELECT SQL_CALC_FOUND_ROWS * from periode_test
								 $param[where] $param[order] $param[limit]");
	}
	
	function dtfiltered_test()
	{
		$result = $this->db->query('SELECT FOUND_ROWS() as jumlah')->row();
		
		return $result->jumlah;
	}
	
	function dtcount_test()
	{
		return $this->db->count_all('periode_test');
	}  
	
	function getbyid_test($id)
	{
		$this->db->where('test_id_periode', $id);
		return $this->db->get('periode_test');
	}
	
	function add_test($item)
	{
		$this->db->insert('periode_test', $item);
		return $this->db->insert_id();
	}
	
	function delete_test($id)
	{
		$this->db->where('test_id', $id);
		return $this->db->delete('periode_test');
	}
}
?>