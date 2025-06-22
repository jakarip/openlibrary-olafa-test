<?php
class Submission2_Model extends CI_Model 
{

	private $table = 'book_procurement';
	private $id    = 'book_id';

	/**
	 * Constructor
	 */ 
	
	// Query for new datatables purpose ;
	//--------------------------------------------------------------------------------------------------------------------------
	function dtquery($param)
	{  

		return $this->db->query("SELECT SQL_CALC_FOUND_ROWS *, DATEDIFF(book_date_logistic_submission, book_date_prodi_submission) proses_pengajuan, DATEDIFF(book_date_acceptance, book_date_logistic_process) proses_pengadaan, DATEDIFF(book_date_email_confirmed, book_date_acceptance) proses_email, DATEDIFF(book_date_available, book_date_email_confirmed) proses_ketersediaan FROM ".$this->table." 
								left join t_mst_prodi tmp on c_kode_prodi=book_id_prodi
								left join t_mst_fakultas tmf on tmp.c_kode_fakultas=tmf.c_kode_fakultas
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
	
	function checkPaymentCode($code)
	{    
		return $this->db->query("select * from member_subscribe where subscribe_status in (0,2) and subscribe_payment_code='".$code."'"); 
	} 
	
	function member($id)
	{   
		return $this->db->query("select * from member where id='$id'"); 
	} 
	
	function getTransactionNumber()
	{   
		return $this->db->query("select max(subscribe_transaction)max from member_subscribe where subscribe_transaction like '".date('ymd')."%'"); 
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
		return $this->db->query("SELECT * FROM ".$this->table." where book_id='$id'");
	}

	function getbook($id)
	{  
		return $this->db->query("SELECT book_id,book_title,book_member,book_date_logistic_submission,book_memo_logistic_number FROM ".$this->table." where book_id in ($id)");
	}

	function get_email_confirmed_and_available($id)
	{  
		return $this->db->query("SELECT book_status,book_id,book_title,book_date_email_confirmed,book_date_available,book_catalog_number FROM ".$this->table." where book_id= '$id'");
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
	
	function add_batch($item)
	{
		return $this->db->insert_batch($this->table, $item);
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/  
	
	function getprodi()
	{
		return $this->db->query("SELECT *	from t_mst_fakultas tmf left join t_mst_prodi tmp on tmp.c_kode_fakultas=tmf.c_kode_fakultas where (nama_prodi not like '%Pindahan%' and nama_prodi not like '%International%' and nama_prodi not like '%Internasional%') order by nama_fakultas,nama_prodi");
	} 

	function getmemberbyname($name)
	{
			return $this->db->query("SELECT * FROM member where member_type_id in (4,7) and master_data_fullname like '%$name%' and status='1' order by master_data_fullname limit 20");
	} 

	function edit_logistic($id, $item)
	{
		$this->db->where_in($this->id, $id);
		return $this->db->update($this->table, $item);
	}
}
?>