<?php
class UserEducationManualModel extends CI_Model {
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	} 
	function getYear()
	{ 
		return $this->db->query("select useredu_year from usereducation group by useredu_year desc");
	} 
	 
	
	function totalpresent($jurusan,$tahun)
	{ 
		return $this->db->query("select count(*) total from usereducation where useredu_prodi='$jurusan' and useredu_year='$tahun' and useredu_date is not null");
	} 
	function datastudent($jurusan,$tahun)
	{ 
		return $this->db->query("select * from usereducation where useredu_prodi='$jurusan' AND useredu_year = '$tahun'");
	} 
	function totalstudent($jurusan,$tahun)
	{ 
		return $this->db->query("select count(*) total from usereducation where useredu_prodi='$jurusan' and useredu_year='$tahun'");
	} 
	 
	
	function getjurbykodejur($jurusan)
	{
		return $this->db->query("select * from usereducation where useredu_id = '$jurusan'");
	}   
	
	function present($id)
	{
		return $this->db->query("update usereducation set useredu_date='".date('Y-m-d H:i:s')."' where useredu_id='$id'"); 
	}
	
	function getProdi()
	{
		return $this->db->query("select * from t_mst_prodi order by nama_prodi"); 
	}
	
	
	
	function add($item)
	{
		return $this->db->insert('useredu_manual', $item);
	}
	 
}
?>