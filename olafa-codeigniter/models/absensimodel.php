<?php
class AbsensiModel extends CI_Model {
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	function getallProdi()
	{ 
		return $this->db->query("select c_kode_prodi, nama_prodi,nama_fakultas from t_mst_prodi left join t_mst_fakultas using(c_kode_fakultas) order by nama_fakultas, nama_prodi");
	} 
	
	
	function getJumlahByTanggal($kode,$tanggal)
	{
		$tgl 	= explode('-',$tanggal);
		$data	= $tgl[1].'-'.$tgl[0];
		return $this->db->query("Select count(*) total from member_attendance ma left join member m on m.id=member_id 
			where m.master_data_course='$kode' and ma.created_at like '$data%' ");
	}						
	 
	
	
	
	
	
}
?>