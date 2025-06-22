<?php
class ApiThetaModel extends CI_Model {
	
	/**
	 * Constructor
	 */
	var $md;
	function __construct()
	{
		parent::__construct(); 
		
		$this->md = $this->load->database('oracle', TRUE);
	}
	
	function CheckStatus($username="") 
	{ 
		return $this->db->query(" 
		select buku,dokumen,peminjaman, IF(ISNULL(lunas) or lunas=0,TRUE,FALSE) lunas from (
		select (denda-bayar)lunas,IF(ISNULL(BUKU),FALSE,TRUE)buku,IF(ISNULL(dokumen),FALSE,TRUE)dokumen,IF(ISNULL(peminjaman),TRUE,FALSE)peminjaman from 
		(select (select id from free_letter where member_number=master_data_user  limit 1) buku, 
		(select id from workflow_document where member_id=mm.id and latest_state_id in (3,4,52,53,64,5,91) limit 1) dokumen, 
		(select id from rent where member_id=mm.id and return_date is null limit 1) peminjaman,
		(select sum(amount) from rent_penalty where member_id=mm.id ) denda,
		(select sum(amount) from rent_penalty_payment where member_id=mm.id ) bayar
		 from member mm where master_data_user='$username')a)b
		");  
	} 
	
	function Amnesty() 
	{ 
		return $this->db->query("select ad.*,master_data_user,master_data_fullname from amnesty_denda ad join member m on m.id=username_id");  
	} 
	
	function Revision() 
	{ 
		return $this->db->query("select ad.*,master_data_user,master_data_fullname from bebaspustaka_revision ad join member m on m.id=username_id");  
	} 
	
	function CheckDataMappingLecturer($student) 
	{ 
		return $this->db->query("select * from masterdata.smk_t_tra_mhs_skripsi where c_npm='$student'");  
	}  

	function addStudentLecture($item)
	{
		return $this->db->insert('masterdata.smk_t_tra_mhs_skripsi', $item);
	}
	
	function editStudentLecture($id, $item)
	{
		$this->db->where('C_NPM', $id);
		return $this->db->update('masterdata.smk_t_tra_mhs_skripsi', $item);
	} 
}
?>