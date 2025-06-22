<?php
class Dashboard_Model extends CI_Model 
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
		return $this->db->query("SELECT SQL_CALC_FOUND_ROWS *, DATEDIFF(book_date_logistic_submission, book_date_prodi_submission) proses_pengajuan, DATEDIFF(book_date_acceptance, book_date_logistic_process) proses_pengadaan, DATEDIFF(book_date_available, book_date_acceptance) proses_ketersediaan, DATEDIFF(book_date_email_confirmed, book_date_available) proses_email FROM ".$this->table." 
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
	
	function total_pengajuan($start,$end)
	{     
		return $this->db->query("
		select count(*) total, 
		(select count(*) from book_procurement
		where book_status='pengajuan' and book_date_prodi_submission between '$start' and '$end' )pengajuan, 
		(select count(*) from book_procurement 
		where book_status='logistik' and book_date_prodi_submission between '$start' and '$end')logistik, 
		(select count(*) from book_procurement 
		where book_status='penerimaan' and book_date_prodi_submission between '$start' and '$end' )penerimaan,
		(select count(*) from book_procurement
		where book_status='r_ketersediaan' and book_date_prodi_submission between '$start' and '$end' )available,
		(select count(*) from book_procurement
		where book_status='s_email' and book_date_prodi_submission between '$start' and '$end' )email_confirmed
		from book_procurement where book_date_prodi_submission between '$start' and '$end'"); 
	} 
	
	function total_pengajuan_faculty($start,$end,$facid)
	{     
		return $this->db->query("
		select count(*) total, 
		(select count(*) from book_procurement 
		join t_mst_prodi on c_kode_prodi=book_id_prodi
		where book_status='pengajuan' and book_date_prodi_submission between '$start' and '$end' and c_kode_fakultas='$facid')pengajuan,
		(select count(*) from book_procurement 
		join t_mst_prodi on c_kode_prodi=book_id_prodi
		where book_status='logistik' and book_date_prodi_submission between '$start' and '$end' and c_kode_fakultas='$facid')logistik, 
		(select count(*) from book_procurement 
		join t_mst_prodi on c_kode_prodi=book_id_prodi
		where book_status='penerimaan' and book_date_prodi_submission between '$start' and '$end' and c_kode_fakultas='$facid')penerimaan,
		(select count(*) from book_procurement
		join t_mst_prodi on c_kode_prodi=book_id_prodi
		where book_status='r_ketersediaan' and book_date_prodi_submission between '$start' and '$end' and c_kode_fakultas='$facid')available,
		(select count(*) from book_procurement
		join t_mst_prodi on c_kode_prodi=book_id_prodi
		where book_status='s_email' and book_date_prodi_submission between '$start' and '$end' and c_kode_fakultas='$facid')email_confirmed
		from book_procurement 
		join t_mst_prodi on c_kode_prodi=book_id_prodi
		where book_date_prodi_submission between '$start' and '$end' and c_kode_fakultas='$facid'"); 
	}    
	
	function total_pengajuan_prodi($start,$end,$prodiid)
	{     
		return $this->db->query("
		select count(*) total, 
		(select count(*) from book_procurement  
		where book_status='pengajuan' and book_date_prodi_submission between '$start' and '$end' and book_id_prodi='$prodiid')pengajuan,
		(select count(*) from book_procurement  
		where book_status='logistik' and book_date_prodi_submission between '$start' and '$end' and book_id_prodi='$prodiid')logistik, 
		(select count(*) from book_procurement  
		where book_status='penerimaan' and book_date_prodi_submission between '$start' and '$end' and book_id_prodi='$prodiid')penerimaan,
		(select count(*) from book_procurement 
		where book_status='r_ketersediaan' and book_date_prodi_submission between '$start' and '$end' and book_id_prodi='$prodiid')available,
		(select count(*) from book_procurement 
		where book_status='s_email' and book_date_prodi_submission between '$start' and '$end' and book_id_prodi='$prodiid')email_confirmed
		from book_procurement  
		where book_date_prodi_submission between '$start' and '$end' and book_id_prodi='$prodiid'"); 
	}    
	
	function rerata_hari_status_penerimaan($start,$end)
	{     
		return $this->db->query("
		select (proses_pengadaan/total)rerata_penerimaan from (SELECT count(*) total, sum(DATEDIFF(book_date_acceptance, book_date_logistic_process)) proses_pengadaan FROM book_procurement
		left join t_mst_prodi tmp on c_kode_prodi=book_id_prodi
		left join t_mst_fakultas tmf on tmp.c_kode_fakultas=tmf.c_kode_fakultas 
		where book_date_prodi_submission between '$start' and '$end' and book_status='penerimaan')a"); 
	} 
	
	function rerata_hari_status_penerimaan_faculty($start,$end,$facid)
	{     
		return $this->db->query("
		select (proses_pengadaan/total)rerata_penerimaan from (SELECT count(*) total, sum(DATEDIFF(book_date_acceptance, book_date_logistic_process)) proses_pengadaan FROM book_procurement
		left join t_mst_prodi tmp on c_kode_prodi=book_id_prodi
		left join t_mst_fakultas tmf on tmp.c_kode_fakultas=tmf.c_kode_fakultas 
		where book_date_prodi_submission between '$start' and '$end' and book_status='penerimaan'  and tmf.c_kode_fakultas='$facid')a"); 
	} 
	
	function rerata_hari_status_penerimaan_prodi($start,$end,$prodiid)
	{     
		return $this->db->query("
		select (proses_pengadaan/total)rerata_penerimaan from (SELECT count(*) total, sum(DATEDIFF(book_date_acceptance, book_date_logistic_process)) proses_pengadaan FROM book_procurement
		left join t_mst_prodi tmp on c_kode_prodi=book_id_prodi
		left join t_mst_fakultas tmf on tmp.c_kode_fakultas=tmf.c_kode_fakultas 
		where book_date_prodi_submission between '$start' and '$end' and book_status='penerimaan'  and book_id_prodi='$prodiid')a"); 
	} 
	
	function total_pengajuan_telupress($start,$end)
	{     
		return $this->db->query("
		select count(*) total, 
		(select count(*) from book_telupress
		where book_status='1' and book_startdate_realization_step_1 between '$start' and '$end' )step1, 
		(select count(*) from book_telupress
		where book_status='2' and book_startdate_realization_step_2 between '$start' and '$end' )step2, 
		(select count(*) from book_telupress
		where book_status='3' and book_startdate_realization_step_3 between '$start' and '$end' )step3, 
		(select count(*) from book_telupress
		where book_status='4' and book_startdate_realization_step_4 between '$start' and '$end' )step4, 
		(select count(*) from book_telupress
		where book_status='5' and book_startdate_realization_step_5 between '$start' and '$end' )step5, 
		(select count(*) from book_telupress
		where book_status='6' and book_startdate_realization_step_6 between '$start' and '$end' )step6, 
		(select count(*) from book_telupress
		where book_status='7' and book_received_date between '$start' and '$end' )step7
		from book_telupress where book_startdate_realization_step_1 between '$start' and '$end'"); 
	}
	
	function getTransactionNumber()
	{   
		return $this->db->query("select max(subscribe_transaction)max from member_subscribe where subscribe_transaction like '".date('ymd')."%'"); 
	} 
	
	function getFaculty()
	{   
		return $this->db->query("select c_kode_fakultas,nama_fakultas from t_mst_fakultas order by nama_fakultas"); 
	} 
	
	function getProdiByFacId($id)
	{   
		return $this->db->query("select * from t_mst_prodi where c_kode_fakultas = '$id' and nama_prodi NOT LIKE '%Pindahan%' AND nama_prodi NOT LIKE '%International%'  order by nama_prodi"); 
	} 
	
	function getProdiByProdId($id)
	{   
		return $this->db->query("select * from t_mst_prodi where c_kode_prodi = '$id'"); 
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
		return $this->db->query("SELECT * FROM ".$this->table."  
								left join member m on m.id=book_id_member where book_id='$id'");
	}

	function getbook($id)
	{  
		return $this->db->query("SELECT book_id,book_title,master_data_fullname,book_date_logistic_submission,book_memo_logistic_number FROM ".$this->table." left join member m on m.id=book_id_member where book_id in ($id)");
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