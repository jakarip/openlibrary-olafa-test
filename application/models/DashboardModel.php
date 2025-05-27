<?php
class DashboardModel extends CI_Model {
	
	/**
	 * Constructor
	 */ 
	 
	  
	function getFileTotal()
	{ 	 
		return $this->db->query("select * from file_total");
	}
	 
	function getTotalJudul()
	{ 	 
		return $this->db->query("select count(id)total from (select kt.id from knowledge_item kt 
				left join knowledge_stock kk on kt.id=kk.knowledge_item_id
				left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
				left join knowledge_type kp on kt.knowledge_type_id=kp.id where ks.active='1' and kp.active='1'  group by kt.id)a");
	}
	function getTotalKoleksi()
	{ 	 
		return $this->db->query("select count(kk.id) total from knowledge_item kt 
			left join knowledge_stock kk on kt.id=kk.knowledge_item_id
			left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
			left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' and kp.active='1'   
			and kk.status not in(4,5)");
			//hilang dan eksemplar
	} 
	
	function getTotalJudulPerBulan($date)
	{ 	 
		return $this->db->query("select count(id)total,DATE_FORMAT(entrance_date,'%Y-%m')tgl from (select kt.id,kt.entrance_date from knowledge_item kt 
				left join knowledge_stock kk on kt.id=kk.knowledge_item_id
				left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
				left join knowledge_type kp on kt.knowledge_type_id=kp.id where ks.active='1' and kp.active='1'
				and YEAR(kt.entrance_date)=$date
							group by kt.id)a 
				group by DATE_FORMAT(entrance_date,'%Y-%m')");
	}
	function getTotalKoleksiPerBulan($date)
	{ 	 
		return $this->db->query("select count(kk.id) total,DATE_FORMAT(kk.entrance_date,'%Y-%m')tgl from knowledge_item kt 
			left join knowledge_stock kk on kt.id=kk.knowledge_item_id
			left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
			left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' and kp.active='1'   
			and kk.status not in(4,5) and YEAR(kk.entrance_date)=$date
			group by DATE_FORMAT(kk.entrance_date,'%Y-%m')");
	} 
	
	function getTotalPengunjungFisik()
	{ 	 
		$tahun = date('Y');
		return $this->db->query("select count(*)total from member_attendance where date_format(attended_at,'%Y') like '$tahun%' ");
	}
	
	function getTotalPengunjungFisikPerBulan($date)
	{ 	 
		return $this->db->query("select count(*)total,DATE_FORMAT(attended_at,'%Y-%m')tgl  from member_attendance  
				where YEAR(attended_at)=$date
				group by DATE_FORMAT(attended_at,'%Y-%m')");
	}
	
	function getTotalPeminjaman()
	{ 	 
		$tahun = date('Y');
		return $this->db->query("select count(*)total from rent where date_format(rent_date,'%Y') like '$tahun%'");
	}
	
	function getTotalPeminjamanPerBulan($date)
	{ 	 
		return $this->db->query(" 
		select * ,IFNULL(totals,1) total from (
			select *,month_name tgl from months left join (
				select count(*)totals,DATE_FORMAT(rent_date,'%Y-%m')tgls from rent 
						where YEAR(rent_date)='$date'
						group by DATE_FORMAT(rent_date,'%Y-%m')) a on month_name=a.tgls
						where month_name like '$date%'
						)b order by month_name");
	}
	 
	function getTotalPengembalian()
	{ 	 
		$tahun = date('Y');
		return $this->db->query("select count(*)total from rent where return_date is not null and date_format(return_date,'%Y') like '$tahun%' ");
	} 
	
	function getTotalPengembalianPerBulan($date)
	{ 	 
		return $this->db->query("select count(*)total,DATE_FORMAT(return_date,'%Y-%m')tgl from rent 
			where YEAR(return_date)=$date
			group by DATE_FORMAT(return_date,'%Y-%m')");
	}
	
	function getRasioMKperProdi()
	{ 	 
		return $this->db->query("select nama_prodi,mk,buku,round(buku/mk *100,2)rasio from (
		select nama_fakultas,nama_prodi,(select count(*)total from master_subject where course_code=c_kode_prodi
		and curriculum_code='2014')mk,
		(
		select count(master_subject_id) from (
		select * from master_subject ms left join knowledge_item_subject kis
		on ms.id=kis.master_subject_id 
		and curriculum_code='2014'  group by master_subject_id )a where  course_code=c_kode_prodi and master_subject_id is not null
		)buku
		from t_mst_fakultas tmf 
		left join t_mst_prodi tmp using (c_kode_fakultas) 
		group by nama_fakultas,nama_prodi)b");
	}
	
}
?>