<?php
class Katalogmodel extends CI_Model {
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	} 
	
	function getallknowledgetype($where)
	{ 
	// echo "select ky.*,
	// 	(
	// 		SELECT
	// 			count( * ) total 
	// 		FROM
	// 			(
	// 			SELECT
	// 				kt.knowledge_type_id id 
	// 			FROM
	// 				knowledge_item kt
	// 				LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
	// 				LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
	// 				LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id 
	// 			WHERE
	// 				ks.active = '1' 
	// 				AND kp.active = '1' $where
				
	// 			GROUP BY
	// 				kt.id 
	// 			) a 
	// 		WHERE
	// 			id = ky.id
	// 		) judul,
	// 		(
	// 		SELECT
	// 			count( * ) total 
	// 		FROM
	// 			knowledge_item kt
	// 			 JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
	// 			LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
	// 			LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id 
	// 		WHERE
	// 			ks.active = '1' 
	// 			AND kp.active = '1' $where
	// 			AND kp.id = ky.id 
	// 		) eksemplar
	// 	from (
	// 	SELECT
	// 				kp.name nama, kp.id,
	// 			SUM(CASE WHEN kk.status = '1' THEN 1 ELSE 0 END) AS tersedia,
	// 			SUM(CASE WHEN kk.status = '2' THEN 1 ELSE 0 END) AS dipinjam,
	// 			SUM(CASE WHEN kk.status = '3' THEN 1 ELSE 0 END) AS rusak,
	// 			SUM(CASE WHEN kk.status = '4' THEN 1 ELSE 0 END) AS hilang,
	// 			SUM(CASE WHEN kk.status = '5' THEN 1 ELSE 0 END) AS expired,
	// 			SUM(CASE WHEN kk.status = '6' THEN 1 ELSE 0 END) AS hilang_diganti,
	// 			SUM(CASE WHEN kk.status = '7' THEN 1 ELSE 0 END) AS diolah,
	// 			SUM(CASE WHEN kk.status = '8' THEN 1 ELSE 0 END) AS cadangan,
	// 			SUM(CASE WHEN kk.status = '9' THEN 1 ELSE 0 END) AS weeding
	// 			FROM
	// 				knowledge_item kt
	// 				LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
	// 				LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
	// 				LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id 
	// 			WHERE
	// 				ks.active = '1' 
	// 				AND kp.active = '1' $where 
	// 			GROUP BY
	// 				kp.id order by kp.name)ky ";
		return $this->db->query("select ky.*,
		(
			SELECT
				count( * ) total 
			FROM
				(
				SELECT
					kt.knowledge_type_id id 
				FROM
					knowledge_item kt
					LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
					LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
					LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id 
				WHERE
					ks.active = '1' 
					AND kp.active = '1' $where
				
				GROUP BY
					kt.id 
				) a 
			WHERE
				id = ky.id
			) judul,
			(
			SELECT
				count( * ) total 
			FROM
				knowledge_item kt
				 JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
				LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
				LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id 
			WHERE
				ks.active = '1' 
				AND kp.active = '1' $where
				AND kp.id = ky.id 
			) eksemplar
		from (
		SELECT
					kp.name nama, kp.id,
				SUM(CASE WHEN kk.status = '1' THEN 1 ELSE 0 END) AS tersedia,
				SUM(CASE WHEN kk.status = '2' THEN 1 ELSE 0 END) AS dipinjam,
				SUM(CASE WHEN kk.status = '3' THEN 1 ELSE 0 END) AS rusak,
				SUM(CASE WHEN kk.status = '4' THEN 1 ELSE 0 END) AS hilang,
				SUM(CASE WHEN kk.status = '5' THEN 1 ELSE 0 END) AS expired,
				SUM(CASE WHEN kk.status = '6' THEN 1 ELSE 0 END) AS hilang_diganti,
				SUM(CASE WHEN kk.status = '7' THEN 1 ELSE 0 END) AS diolah,
				SUM(CASE WHEN kk.status = '8' THEN 1 ELSE 0 END) AS cadangan,
				SUM(CASE WHEN kk.status = '9' THEN 1 ELSE 0 END) AS weeding
				FROM
					knowledge_item kt
					LEFT JOIN knowledge_stock kk ON kt.id = kk.knowledge_item_id
					LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
					LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id 
				WHERE
					ks.active = '1' 
					AND kp.active = '1' $where 
				GROUP BY
					kp.id order by kp.name)ky ");
		 
		// return $this->db->query("select ky.name nama, ky.id, 
		// (select count(id)total from (select kt.knowledge_type_id id from knowledge_item kt 
		// left join knowledge_stock kk on kt.id=kk.knowledge_item_id
		// left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		// left join knowledge_type kp on kt.knowledge_type_id=kp.id where ks.active='1' and kp.active='1' $where group by kt.id)a where id=ky.id) judul,
		// (select count(kk.id) total from knowledge_item kt 
		// left join knowledge_stock kk on kt.id=kk.knowledge_item_id
		// left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		// left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' and kp.id=ky.id $where) eksemplar,
		// (select count(*) total from knowledge_item kt 
		// left join knowledge_stock kk on kt.id=kk.knowledge_item_id
		// left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		// left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' and kp.id=ky.id and status='1' $where) tersedia,
		// (select count(*) total from knowledge_item kt 
		// left join knowledge_stock kk on kt.id=kk.knowledge_item_id
		// left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		// left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' and kp.id=ky.id and status='2' $where) dipinjam,
		// (select count(*) total from knowledge_item kt 
		// left join knowledge_stock kk on kt.id=kk.knowledge_item_id
		// left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		// left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' and kp.id=ky.id and status='3' $where) rusak,
		// (select count(*) total from knowledge_item kt 
		// left join knowledge_stock kk on kt.id=kk.knowledge_item_id
		// left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		// left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' and kp.id=ky.id and status='4' $where) hilang,
		// (select count(*) total from knowledge_item kt 
		// left join knowledge_stock kk on kt.id=kk.knowledge_item_id
		// left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		// left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' and kp.id=ky.id and status='5' $where) expired,
		// (select count(*) total from knowledge_item kt 
		// left join knowledge_stock kk on kt.id=kk.knowledge_item_id
		// left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		// left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' and kp.id=ky.id and status='6' $where) hilang_diganti,
		// (select count(*) total from knowledge_item kt 
		// left join knowledge_stock kk on kt.id=kk.knowledge_item_id
		// left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		// left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' and kp.id=ky.id and status='7' $where) diolah ,
		// (select count(*) total from knowledge_item kt 
		// left join knowledge_stock kk on kt.id=kk.knowledge_item_id
		// left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		// left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' and kp.id=ky.id and status='8' $where) cadangan ,
		// (select count(*) total from knowledge_item kt 
		// left join knowledge_stock kk on kt.id=kk.knowledge_item_id
		// left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		// left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' and kp.id=ky.id and status='9' $where) weeding 
		// from knowledge_type ky where ky.active='1' order by ky.name");
		 
	}  
	 
	function getbookonprocess($where="",$where2="")
	{       
			return $this->db->query("select * from (SELECT il.name location_name, kl.name tipe, ks.id id, kt. CODE catalog, ks. CODE barcode, cc. CODE klasifikasi, title,ks.origination,
			author, publisher_name,replace(ltrim(replace(cc.code,'0',' ')),' ','0') codes2 FROM knowledge_item kt
			LEFT JOIN knowledge_stock ks ON kt.id = knowledge_item_id
			LEFT JOIN knowledge_subject kss ON kss.id = kt.knowledge_subject_id
			LEFT JOIN classification_code cc ON cc.id = kt.classification_code_id
			LEFT JOIN item_location il ON il.id = ks.item_location_id
			LEFT JOIN knowledge_type kl ON kl.id = ks.knowledge_type_id where 1=1 
			AND kss.active = '1'  $where 
			ORDER BY status,kl.id,kt.id) a $where2");
	} 
						// echo $table;
						
	function getbook($where,$where2="") 
	{  
	// return $this->db->query("SELECT ks.id id, kt. CODE catalog, ks. CODE barcode, cc. CODE klasifikasi, title,
			// author, publisher_name,kl.name tipe FROM knowledge_item kt
			// LEFT JOIN knowledge_stock ks ON kt.id = knowledge_item_id
			// LEFT JOIN classification_code cc ON cc.id = kt.classification_code_id
			// LEFT JOIN knowledge_type kl ON kl.id = kt.knowledge_type_id where 1=1 $where
			// ORDER BY status,kl.id,kt.id "); 
			return $this->db->query("select * from (SELECT ks.id id, kt. CODE catalog, ks.status, ks. CODE barcode, cc. CODE klasifikasi, title,ks.origination,
						author, publisher_name,kl.name tipe,published_year,replace(ltrim(replace(cc.code,'0',' ')),' ','0') codes2 FROM knowledge_item kt
						LEFT JOIN knowledge_stock ks ON kt.id = knowledge_item_id
						LEFT JOIN classification_code cc ON cc.id = kt.classification_code_id
						LEFT JOIN knowledge_type kl ON kl.id = kt.knowledge_type_id 
						where 1=1 $where order by published_year desc,status,kl.id,kt.id asc)a $where2");
	}
	
	function ubahStatus($id, $item)
	{
		$this->db->where('id', $id);
		return $this->db->update('knowledge_stock', $item);
	}
	
	function getEcatalog($tanggal,$code_awal,$code_akhir)
	{
		$tgl 	= explode('-',$tanggal);
		$data	= $tgl[1].'-'.$tgl[0]; 
		// echo "select count(eks_id) eks,cat_id,cat_code,eks_id,title,publisher_name,published_year, cat,subject,alternate_subject, tipe,
		// abstract_content,author,class_code,class_name,cover_path from (
		// select kt.id cat_id,ks.id eks_id,title,publisher_name,published_year,kt.code cat,cover_path, kb.name subject, kp.name tipe, kt.alternate_subject,
		// abstract_content,author,kt.code cat_code, cc.code class_code,cc.name class_name from knowledge_item kt 
		// LEFT JOIN knowledge_stock ks on kt.id=ks.knowledge_item_id 
		// LEFT JOIN classification_code cc on cc.id=kt.classification_code_id 
		// LEFT JOIN knowledge_subject kb on kb.id=kt.knowledge_subject_id 
		// LEFT JOIN knowledge_type kp on kp.id=kt.knowledge_type_id 
		// where kt.entrance_date like '$data%' and kt.knowledge_type_id='1' 
		// and cc.code >=$code_awal and cc.code<=$code_akhir
		// order by cc.code desc ) a group by cat_id
		//  ";
		return $this->db->query("select count(eks_id) eks,cat_id,cat_code,eks_id,title,publisher_name,published_year, cat,subject,alternate_subject, tipe,
				abstract_content,author,class_code,class_name,cover_path from (
				select kt.id cat_id,ks.id eks_id,title,publisher_name,published_year,kt.code cat,cover_path, kb.name subject, kp.name tipe, kt.alternate_subject,
				abstract_content,author,kt.code cat_code, cc.code class_code,cc.name class_name from knowledge_item kt 
				LEFT JOIN knowledge_stock ks on kt.id=ks.knowledge_item_id 
				LEFT JOIN classification_code cc on cc.id=kt.classification_code_id 
				LEFT JOIN knowledge_subject kb on kb.id=kt.knowledge_subject_id 
				LEFT JOIN knowledge_type kp on kp.id=kt.knowledge_type_id 
				where kt.entrance_date like '$data%' and kt.knowledge_type_id='1' 
				and cc.code >=$code_awal and cc.code<=$code_akhir
				order by cc.code desc ) a group by cat_id
				 ");
	} 
	
	function getknowledgetype()
	{   
			return $this->db->query("select * from knowledge_type where active='1' order by name");
	}
	
	function getcatalog_book($where,$where2)
	{  
			return $this->db->query("select * from (select kt.code catalog,count(ks.code)eksemplar, cc. CODE klasifikasi, title,nama_prodi,
						author, SUBSTRING_INDEX(SUBSTRING_INDEX( author, ',', 1 ),' ',-1) author_code,alternate_subject, publisher_name,kl.name tipe,published_year,sum(ks.price) harga,il.name lokasi,isbn,
						replace(ltrim(replace(cc.code,'0',' ')),' ','0') codes2 
						from knowledge_stock ks 
						left join knowledge_item kt on kt.id=ks.knowledge_item_id  
						LEFT JOIN classification_code cc ON cc.id = kt.classification_code_id 
						LEFT JOIN knowledge_type kl ON kl.id = kt.knowledge_type_id 
						left join item_location il on il.id=kt.item_location_id  
						left join t_mst_prodi tmp on c_kode_prodi=kt.course_code  
						where 1=1 $where group by kt.id,ks.course_code
							order by title) a $where2 order by klasifikasi,author_code,title asc");
	}
	
}
?>