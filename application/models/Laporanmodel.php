<?php
class Laporanmodel extends CI_Model {
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	} 
	
	function pengunjung($awal,$akhir,$prodi="")
	{   
		if($prodi!="") {
			$temp = explode("-",$prodi);
			if($temp[1]=="") {
				$prodi = " and master_data_course in (select c_kode_prodi from t_mst_prodi where c_kode_fakultas='$temp[0]')"; 
			}
			else $prodi = " and master_data_course='$temp[1]'"; 
		} 
		return $this->db->query("select count(member_id) total from member_attendance wd where  wd.attended_at between '$awal' and '$akhir' $prodi");
	} 
	
	function prodi()
	{   
			return $this->db->query("select tmp.c_kode_fakultas,c_kode_prodi,nama_fakultas, nama_prodi from t_mst_fakultas tmf left join t_mst_prodi tmp on tmp.c_kode_fakultas=tmf.c_kode_fakultas where (nama_prodi not like '%Pindahan%' and nama_prodi not like '%International%' and nama_prodi not like '%Internasional%') and nama_fakultas!='' order by nama_fakultas, nama_prodi");
	}  
	
	function peminjaman($awal,$akhir,$prodi="")
	{   
		if($prodi!="") {
			$temp = explode("-",$prodi);
			if($temp[1]=="") {
				$prodi = " and master_data_course in (select c_kode_prodi from t_mst_prodi where c_kode_fakultas='$temp[0]')"; 
			}
			else $prodi = " and master_data_course='$temp[1]'"; 
		}
 
		return $this->db->query("select count(member_id)total from rent wd
		left join member m on m.id=member_id
		where  wd.rent_date between '$awal' and '$akhir' $prodi");
	} 
	
	function pengembalian($awal,$akhir,$prodi="")
	{    
		if($prodi!="") {
			$temp = explode("-",$prodi);
			if($temp[1]=="") {
				$prodi = " and master_data_course in (select c_kode_prodi from t_mst_prodi where c_kode_fakultas='$temp[0]')"; 
			}
			else $prodi = " and master_data_course='$temp[1]'"; 
		}

		return $this->db->query("select count(member_id)total from rent wd
			left join member m on m.id=member_id
			where  wd.return_date between '$awal' and '$akhir' $prodi");
	}  
	
	function bebaspustaka($awal,$akhir,$prodi="")
	{      
		if($prodi!="") {
			$temp = explode("-",$prodi);
			if($temp[1]=="") {
				$prodi = " and course_code in (select c_kode_prodi from t_mst_prodi where c_kode_fakultas='$temp[0]')"; 
			}
			else $prodi = " and course_code='$temp[1]'"; 
		}
		 
		return $this->db->query("select count(distinct member_number)total from free_letter wd
			where wd.created_at between '$awal' and '$akhir' and is_member='1' $prodi");
	}  
	
	function ruangan($awal,$akhir,$prodi="")
	{    
		
		if($prodi!="") {
			$temp = explode("-",$prodi);
			if($temp[1]=="") {
				$prodi = " and master_data_course in (select c_kode_prodi from t_mst_prodi where c_kode_fakultas='$temp[0]')"; 
			}
			else $prodi = " and master_data_course='$temp[1]'"; 
		}

		return $this->db->query("select count(*)total from room.booking
		left join member m on m.id=bk_memberid
		where bk_startdate between '$awal' and '$akhir' $prodi");
	}  
	
	function sumbangan_buku($awal,$akhir,$prodi="")
	{    
		
		if($prodi!="") {
			$temp = explode("-",$prodi);
			if($temp[1]=="") {
				$prodi = " and kk.course_code in (select c_kode_prodi from t_mst_prodi where c_kode_fakultas='$temp[0]')"; 
			}
			else $prodi = " and kk.course_code='$temp[1]'"; 
		}

			return $this->db->query(
			"select  kk.code, kt.title,author from knowledge_item kt 
				left join knowledge_stock kk on kt.id=kk.knowledge_item_id
				left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
				left join classification_code cc on cc.id=kt.classification_code_id
				left join knowledge_type kp on kt.knowledge_type_id=kp.id  where kp.active='1' 
				and ks.active='1'  and  kp.id  in (4,5,6)and kk.entrance_date 
				between '$awal' and '$akhir' and kk.origination='2' $prodi");
	}  
	
	function sumbangan_ebook($awal,$akhir,$prodi="")
	{    
		
		if($prodi!="") {
			$temp = explode("-",$prodi);
			if($temp[1]=="") {
				$prodi = " and kk.course_code in (select c_kode_prodi from t_mst_prodi where c_kode_fakultas='$temp[0]')"; 
			}
			else $prodi = " and kk.course_code='$temp[1]'"; 
		}

			return $this->db->query(
			"select  kk.code, kt.title,author from knowledge_item kt 
				left join knowledge_stock kk on kt.id=kk.knowledge_item_id
				left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
				left join classification_code cc on cc.id=kt.classification_code_id
				left join knowledge_type kp on kt.knowledge_type_id=kp.id  where kp.active='1' 
				and ks.active='1'  and  kp.id  in (21)and kk.entrance_date 
				between '$awal' and '$akhir' and kk.origination='2' $prodi");
	}    
	
	function tapa_perjudul_download($awal,$akhir,$prodi="")
	{   
		if($prodi!="") $prodi = " and master_data_course='$prodi'"; 
			return $this->db->query("select count(*) total from(
			select count(kit.id) from knowledge_item_file_download wd
			left join knowledge_item kit on kit.id=knowledge_item_id
			left join member m on m.id=member_id
			where  wd.created_at between '$awal' and '$akhir' $prodi
			and knowledge_type_id in (4,5,6)
			group by kit.id )a");
	}  
	
	function tapa_transaksi_download($awal,$akhir,$prodi="")
	{   
		if($prodi!="") $prodi = " and master_data_course='$prodi'"; 
			return $this->db->query("select count(kit.id)total from knowledge_item_file_download wd
					left join knowledge_item kit on kit.id=knowledge_item_id
					left join member m on m.id=member_id
					where  wd.created_at between '$awal' and '$akhir' $prodi
					and knowledge_type_id in (4,5,6) $prodi");
	}  
	
	function ebook_perjudul_download($awal,$akhir,$prodi="")
	{   
		if($prodi!="") $prodi = " and master_data_course='$prodi'"; 
			return $this->db->query("select count(*) total from(
			select count(kit.id) from knowledge_item_file_download wd
			left join knowledge_item kit on kit.id=knowledge_item_id
			left join member m on m.id=member_id
			where  wd.created_at between '$awal' and '$akhir' $prodi
			and knowledge_type_id in (21)
			group by kit.id )a");
	}  
	
	function ebook_transaksi_download($awal,$akhir,$prodi="")
	{   
		if($prodi!="") $prodi = " and master_data_course='$prodi'"; 
			return $this->db->query("select count(kit.id)total from knowledge_item_file_download wd
					left join knowledge_item kit on kit.id=knowledge_item_id
					left join member m on m.id=member_id
					where  wd.created_at between '$awal' and '$akhir' $prodi
					and knowledge_type_id in (21)");
	} 
	
	
	
	function tapa_perjudul_readonly($awal,$akhir,$prodi="")
	{   
		if($prodi!="") $prodi = " and master_data_course='$prodi'";
 
			return $this->db->query("select count(*) total from(
			select count(kit.id) from knowledge_item_file_readonly wd
			left join knowledge_item kit on kit.id=knowledge_item_id
			left join member m on m.id=member_id
			where  wd.created_at between '$awal' and '$akhir' $prodi
			and knowledge_type_id in (4,5,6)
			group by kit.id )a");
	}  
	
	function tapa_transaksi_readonly($akhir)
	{   
		$temp = explode("-",$akhir);
		return $this->db->query("select * from online_access where year='$temp[0]' and type='karyailmiah'");
	}   
	
	function ebook_transaksi_readonly($akhir)
	{   
		$temp = explode("-",$akhir);
		return $this->db->query("select * from online_access where year='$temp[0]' and type='ebook'");
	}   
	
	function visitor_openlib($akhir)
	{   
		$temp = explode("-",$akhir);
		return $this->db->query("select * from online_visitor where year='$temp[0]' order by type desc");
	}  
	
	function visitor_eproc($akhir)
	{   
		$temp = explode("-",$akhir);
		return $this->db->query("select * from online_visitor_eproc where year='$temp[0]' order by type desc");
	}  
	
	function tapa_based_on_bebaspustaka_date($awal,$akhir,$status,$prodi="")
	{      
		
		if($prodi!="") {
			$temp = explode("-",$prodi);
			if($temp[1]=="") {
				$prodi = " and fl.course_code in (select c_kode_prodi from t_mst_prodi where c_kode_fakultas='$temp[0]')"; 
			}
			else $prodi = " and fl.course_code='$temp[1]'"; 
		}

			return $this->db->query("select count(*)total from (select count(wdd.member_id) total from free_letter fl
			left join workflow_document wdd on wdd.member_id=registration_number 
			left join workflow_document_state wds on wds.document_id=wdd.id
			where fl.created_at between '$awal' and '$akhir' and is_member='1' $prodi 
			and state_id='$status' 
			 AND wds.id = (
				SELECT
					max(id)
				FROM
					workflow_document_state  
				WHERE
					document_id = wdd.id and state_id!='5' 
			)
			group by wdd.member_id)a
			 ");
	} 
	
	 
	
}
?>