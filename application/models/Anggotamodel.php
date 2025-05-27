<?php
class Anggotamodel extends CI_Model {
	
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
	
	
	function getJumlahPenunjungByTanggal($kode,$tanggal,$time)
	{
		// $tgl 	= explode('-',$tanggal);
		// $data	= $tgl[1].'-'.$tgl[0];
		
		if ($time=='day') return $this->db->query("Select count(*) total from member_attendance ma left join member m on m.id=member_id 
			where m.master_data_course='$kode' and m.member_type_id in (5,6,9,10) and ma.attended_at between $tanggal and date_format(ma.attended_at,'%H:%i:%s') between '06:00:00' and '16:30:00' ");
		else return $this->db->query("Select count(*) total from member_attendance ma left join member m on m.id=member_id 
			where m.master_data_course='$kode' and m.member_type_id in (5,6,9,10) and ma.attended_at between $tanggal and date_format(ma.attended_at,'%H:%i:%s') between '16:30:01' and '22:00:00' ");
	}
	
	
	function getListPenunjungByTanggal($kode,$tanggal,$time)
	{
		// $tgl 	= explode('-',$tanggal);
		// $data	= $tgl[1].'-'.$tgl[0];
		
		if ($time=='day') return $this->db->query("Select master_data_number,master_data_user,master_data_fullname,ma.attended_at from member_attendance ma left join member m on m.id=member_id 
			where m.master_data_course='$kode' and m.member_type_id in (5,6,9,10,25) and ma.attended_at between $tanggal and date_format(ma.attended_at,'%H:%i:%s') between '06:00:00' and '16:30:00' order by attended_at");
		else return $this->db->query("Select  master_data_number,master_data_user,master_data_fullname,ma.attended_at from member_attendance ma left join member m on m.id=member_id 
			where m.master_data_course='$kode' and m.member_type_id in (5,6,9,10,25) and ma.attended_at between $tanggal and date_format(ma.attended_at,'%H:%i:%s') between '16:30:01' and '22:00:00' order by attended_at");
	}	  
	
	function getJumlahPengunjungDosenPegawaiByTanggal($tanggal,$time)
	{
		// $tgl 	= explode('-',$tanggal);
		// $data	= $tgl[1].'-'.$tgl[0];
		
		if ($time=='day') return $this->db->query("Select count(*) total from member_attendance ma left join member m on m.id=member_id 
			where m.member_type_id in (1,4,7) and ma.attended_at between $tanggal  and date_format(ma.attended_at,'%H:%i:%s') between '06:00:00' and '16:30:00' ");
		else return $this->db->query("Select count(*) total from member_attendance ma left join member m on m.id=member_id 
			where m.member_type_id in (1,4,7) and ma.attended_at between $tanggal and date_format(ma.attended_at,'%H:%i:%s') between '16:30:01' and '22:00:00' ");	
	}	
	
	function getListPengunjungDosenPegawaiByTanggal($tanggal,$time)
	{
		// $tgl 	= explode('-',$tanggal);
		// $data	= $tgl[1].'-'.$tgl[0];
		if ($time=='day') return $this->db->query("Select master_data_number,master_data_user,master_data_fullname,ma.attended_at from member_attendance ma left join member m on m.id=member_id 
			where m.member_type_id in (1,4,7) and ma.attended_at between $tanggal  and date_format(ma.attended_at,'%H:%i:%s') between '06:00:00' and '16:30:00' order by attended_at");
		else return $this->db->query("Select master_data_number,master_data_user,master_data_fullname,ma.attended_at from member_attendance ma left join member m on m.id=member_id 
			where m.member_type_id in (1,4,7) and ma.attended_at between $tanggal and date_format(ma.attended_at,'%H:%i:%s') between '16:30:01' and '22:00:00' order by attended_at");	
	}	
	
	function getJumlahPengunjungPublicByTanggal($tanggal,$time)
	{
		$tgl 	= explode('-',$tanggal);
		$data	= $tgl[1].'-'.$tgl[0];
		
		if ($time=='day') { 
			return $this->db->query("Select count(*) total from member_attendance ma left join member m on m.id=member_id 
			where m.member_type_id in (19) and ma.attended_at between $tanggal  and date_format(ma.attended_at,'%H:%i:%s') between '06:00:00' and '16:30:00' ");
		}
		else { 
			return $this->db->query("Select count(*) total from member_attendance ma left join member m on m.id=member_id 
			where m.member_type_id in (19) and ma.attended_at between $tanggal and date_format(ma.attended_at,'%H:%i:%s') between '16:30:01' and '22:00:00' ");	
		}
	}	   
	
	function getListPengunjungPublicByTanggal($tanggal,$time)
	{
		$tgl 	= explode('-',$tanggal);
		$data	= $tgl[1].'-'.$tgl[0];
		if ($time=='day') return $this->db->query("Select master_data_number,master_data_user,master_data_fullname,ma.attended_at from member_attendance ma left join member m on m.id=member_id 
			where m.member_type_id in (19) and ma.attended_at like '$data%'  and date_format(ma.attended_at,'%H:%i:%s') between '06:00:00' and '16:30:00' order by attended_at");
		else return $this->db->query("Select master_data_number, master_data_user,master_data_fullname,ma.attended_at from member_attendance ma left join member m on m.id=member_id 
			where m.member_type_id in (19) and ma.attended_at like '$data%' and date_format(ma.attended_at,'%H:%i:%s') between '16:30:01' and '22:00:00' order by attended_at");	
	}
	
	function getNim($username)
	{  	
		return $this->db->query("select * from masterdata.t_tem_userlogin_igracias_for_rfid where c_username='$username'");
			
	}

	
	function getJumlahPeminjamanByTanggal($kode,$tanggal)
	{
		$tgl 	= explode('-',$tanggal);
		$data	= $tgl[1].'-'.$tgl[0];
		
		
		return $this->db->query("
			select sum(total)total,anggota from (	
				select sum(total) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
				left join member m on m.id=member_id where m.member_type_id in (5,6,9,10) and m.master_data_course='$kode' and ma.rent_date like '$data%' group by member_id)a 
				union 
				select sum(total) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
				left join member m on m.id=member_id where m.member_type_id in (5,6,9,10) and m.master_data_course='$kode' and ma.rent_date like '$data%' and extended_count='1' group by member_id)a 
				union
				select sum(total*2) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
				left join member m on m.id=member_id where m.member_type_id in (5,6,9,10) and m.master_data_course='$kode' and ma.rent_date like '$data%' and extended_count='2' group by member_id)a
			)b");
			
	}

	function getJumlahPeminjamanNonMahasiswaByTanggal($tanggal)
	{
		$tgl 	= explode('-',$tanggal);
		$data	= $tgl[1].'-'.$tgl[0];
		
		
		return $this->db->query("
			select sum(total)total,anggota from (	
				select sum(total) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
				left join member m on m.id=member_id where m.member_type_id in (1,4,7) and ma.rent_date like '$data%' group by member_id)a
				union  
				select sum(total) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
				left join member m on m.id=member_id where m.member_type_id in (1,4,7) and ma.rent_date like '$data%' and extended_count='1' group by member_id)a
				union  
				select sum(total*2) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
				left join member m on m.id=member_id where m.member_type_id in (1,4,7) and ma.rent_date like '$data%' and extended_count='2' group by member_id)a 
			)b");
			
	}		
	
	function getJumlahPengembalianByTanggal($kode,$tanggal)
	{
		$tgl 	= explode('-',$tanggal);
		$data	= $tgl[1].'-'.$tgl[0];
		
		
		return $this->db->query("
			select sum(total)total,anggota from (	
				select sum(total) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
				left join member m on m.id=member_id where m.member_type_id in (5,6,9,10) and m.master_data_course='$kode' and ma.return_date like '$data%' group by member_id)a 
				union 
				select sum(total) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
				left join member m on m.id=member_id where m.member_type_id in (5,6,9,10) and m.master_data_course='$kode' and ma.return_date like '$data%' and extended_count='1' group by member_id)a 
				union
				select sum(total*2) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
				left join member m on m.id=member_id where m.member_type_id in (5,6,9,10) and m.master_data_course='$kode' and ma.return_date like '$data%' and extended_count='2' group by member_id)a
			)b");
			
	}	
	
	function getJumlahPengembalianNonMahasiswaByTanggal($tanggal)
	{
		$tgl 	= explode('-',$tanggal);
		$data	= $tgl[1].'-'.$tgl[0];
		
		
		return $this->db->query("
			select sum(total)total,anggota from (	
				select sum(total) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
				left join member m on m.id=member_id where m.member_type_id in (1,4,7) and ma.return_date like '$data%' group by member_id)a
				union  
				select sum(total) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
				left join member m on m.id=member_id where m.member_type_id in (1,4,7) and ma.return_date like '$data%' and extended_count='1' group by member_id)a
				union  
				select sum(total*2) total, count(id) anggota from (Select count(*) total,m.id id from rent ma 
				left join member m on m.id=member_id where m.member_type_id in (1,4,7) and ma.return_date like '$data%' and extended_count='2' group by member_id)a 
			)b");
			
	}	
	
	function getJumlahDownloadByTanggal($kode, $tanggal)
	{
		$tgl 	= explode('-',$tanggal);
		$data	= $tgl[1].'-'.$tgl[0];
		
		
		return $this->db->query("
			select sum(total) total, count(id) anggota from (Select count(*) total,m.id id from knowledge_item_file_download kf  
			left join knowledge_item kit on kit.id=knowledge_item_id 	
			left join member m on m.id=member_id 
			where m.member_type_id in (5,6,9,10) and m.master_data_course='$kode' and knowledge_type_id='21'
			and kf.created_at like '$data%' group by member_id)a
		");
			
	}
	
	function getJumlahDownloadNonMahasiswaByTanggal($tanggal)
	{
		$tgl 	= explode('-',$tanggal);
		$data	= $tgl[1].'-'.$tgl[0];
		
		
		return $this->db->query("
			select sum(total) total, count(id) anggota from (Select count(*) total,m.id id from knowledge_item_file_download kf  
			left join knowledge_item kit on kit.id=knowledge_item_id 	
			left join member m on m.id=member_id 
			where m.member_type_id in (1,4,7) and knowledge_type_id='21'
			and kf.created_at like '$data%' group by member_id)a
		");
			
	}
	 
	
	
	
	
	
}
?>