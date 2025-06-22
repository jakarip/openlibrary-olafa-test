<?php
class Telupress_Model extends CI_Model 
{

	private $table = 'book_telupress';
	private $id    = 'book_id';

	/**
	 * Constructor
	 */ 
	
	// Query for new datatables purpose ;
	//--------------------------------------------------------------------------------------------------------------------------
	function dtquery($param)
	{  
 
		return $this->db->query("SELECT SQL_CALC_FOUND_ROWS bt.*, 
		IFNULL(DATEDIFF(book_enddate_target_step_1, book_enddate_realization_step_1),0) proses_step1, 
		IFNULL(DATEDIFF(book_enddate_target_step_2, book_enddate_realization_step_2),0) proses_step2, 
		IFNULL(DATEDIFF(book_enddate_target_step_3, book_enddate_realization_step_3),0) proses_step3, 
		IFNULL(DATEDIFF(book_enddate_target_step_4, book_enddate_realization_step_3),0) proses_step4, 
		IFNULL(DATEDIFF(book_enddate_target_step_5, book_enddate_realization_step_5),0) proses_step5, 
		IFNULL(DATEDIFF(book_enddate_target_step_6, book_enddate_realization_step_6),0) proses_step6, 
		IFNULL(DATEDIFF(book_enddate_realization_step_6, book_startdate_realization_step_1),0) total_proses_naskah_cetak, 
		master_data_fullname,NAMA_FAKULTAS,NAMA_PRODI FROM ".$this->table." bt
								left join member m on m.id=book_id_user
								left join t_mst_prodi tmp on c_kode_prodi=book_id_prodi
								left join t_mst_fakultas tmf on tmp.c_kode_fakultas=tmf.c_kode_fakultas
								 $param[where] $param[order] $param[limit]");
	} 
	
	function getlecturer($term)
	{ 
		return $this->db->query("SELECT * FROM member WHERE (master_data_user like '%$term%' OR master_data_fullname like '%$term%') and (member_type_id='3' or  member_type_id='4' or  member_type_id='7' or  member_type_id='1') and status='1' order by master_data_fullname limit 0,25 ");
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
		return $this->db->query("SELECT bt.*, master_data_number,master_data_fullname FROM ".$this->table." bt
		left join member on member.id=book_id_user where book_id='$id'");
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