<?php
class DigitalaccessModel extends CI_Model {
	
	/** 
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	function getalljurusan()
	{ 
		return $this->db->query("select c_kode_prodi, nama_prodi,nama_fakultas from t_mst_prodi left join t_mst_fakultas using(c_kode_fakultas) order by nama_fakultas, nama_prodi");
	} 
	
	function getallfakultas()
	{ 
		return $this->db->query("select * from t_mst_fakultas order by nama_fakultas");
	} 
	
	function getmembertype()
	{ 
		return $this->db->query("select * from member_type where id in (1,2,3,4,5,6,7,8,9,10,25,19,20,21,22,23,24,26,27,28,29,30,31) order by name");
	} 
	
	function getCurriculum()
	{ 
		return $this->db->query("select curriculum_code from master_subject group by curriculum_code desc");
	} 
	
	function getBahanPustaka($year,$grow_year)
	{ 
		return $this->db->query("select c_kode_prodi,nama_fakultas,nama_prodi,
					(select count(kt.id) from master_subject msu
					left join knowledge_item_subject kis on kis.master_subject_id=msu.id
					left join knowledge_item kt on knowledge_item_id=kt.id 
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id
					where ks.active='1' and kp.active='1'
					and knowledge_type_id not in (4,5,6) and curriculum_code='$year' and msu.course_code=tmp.c_kode_prodi 
					and entrance_date between '1900-01-01' and '$grow_year-12-31 23:59:59') judul,
					(select count(kk.id) from master_subject msu
					left join knowledge_item_subject kis on kis.master_subject_id=msu.id 
					left join knowledge_item kt on kt.id = kis.knowledge_item_id 
					left join knowledge_stock kk on kk.knowledge_item_id = kt.id
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id 
					where ks.active='1' and kp.active='1' and kk.status not in(4,5)
					and kk.knowledge_type_id not in (4,5,6) and curriculum_code='$year' and msu.course_code=tmp.c_kode_prodi
					and kk.entrance_date between '1900-01-01' and '$grow_year-12-31 23:59:59') eks,
					(select count(*) from master_subject where curriculum_code='$year' and course_code=tmp.c_kode_prodi) mk,
					(select count(*) from master_subject where curriculum_code='$year' and course_code=tmp.c_kode_prodi and master_subject.id in(select master_subject_id from knowledge_item_subject)) mkadabuku
					from t_mst_fakultas tmf left join t_mst_prodi tmp on tmp.c_kode_fakultas=tmf.c_kode_fakultas");
	} 
	
	function getStudyProgram($id)
	{ 
		return $this->db->query("select c_kode_prodi,nama_fakultas,nama_prodi
					from t_mst_fakultas tmf left join t_mst_prodi tmp on tmp.c_kode_fakultas=tmf.c_kode_fakultas where c_kode_prodi='$id'");
	} 
	
	function getMK($id,$year)
	{ 
		return $this->db->query("select *,SUBSTR(msu.code,-1) sks 
					from master_subject msu where course_code ='$id' AND msu.curriculum_code = '$year'");
	} 
	
	function totalcollection($year,$grow_year,$faculty)
	{ 
		return $this->db->query("select sum(judul)judul,sum(eks)eks,sum(judul_fisik)judul_fisik,sum(eks_fisik)eks_fisik,sum(mk)mk,sum(mkadabuku)mkadabuku  from(select c_kode_prodi,nama_fakultas,nama_prodi,
					(select count(kt.id) from master_subject msu
					left join knowledge_item_subject kis on kis.master_subject_id=msu.id
					left join knowledge_item kt on knowledge_item_id=kt.id 
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id
					where ks.active='1' and kp.active='1'
					and knowledge_type_id=21 and curriculum_code='$year' and msu.course_code=tmp.c_kode_prodi 
					and entrance_date between '1900-01-01' and '$grow_year-12-31 23:59:59') judul,
					(select count(kk.id) from master_subject msu
					left join knowledge_item_subject kis on kis.master_subject_id=msu.id 
					left join knowledge_item kt on kt.id = kis.knowledge_item_id 
					left join knowledge_stock kk on kk.knowledge_item_id = kt.id
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id 
					where ks.active='1' and kp.active='1' and kk.status not in(4,5)
					and kk.knowledge_type_id=21 and curriculum_code='$year' and msu.course_code=tmp.c_kode_prodi
					and kk.entrance_date between '1900-01-01' and '$grow_year-12-31 23:59:59') eks,
					(select count(kt.id) from master_subject msu
					left join knowledge_item_subject kis on kis.master_subject_id=msu.id
					left join knowledge_item kt on knowledge_item_id=kt.id 
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id
					where ks.active='1' and kp.active='1'
					and knowledge_type_id  in (1, 2, 3, 33, 40, 41, 59, 65) and curriculum_code='$year' and msu.course_code=tmp.c_kode_prodi 
					and entrance_date between '1900-01-01' and '$grow_year-12-31 23:59:59') judul_fisik,
					(select count(kk.id) from master_subject msu
					left join knowledge_item_subject kis on kis.master_subject_id=msu.id 
					left join knowledge_item kt on kt.id = kis.knowledge_item_id 
					left join knowledge_stock kk on kk.knowledge_item_id = kt.id
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id 
					where ks.active='1' and kp.active='1' and kk.status not in(4,5)
					and kk.knowledge_type_id  in (1, 2, 3, 33, 40, 41, 59, 65) and curriculum_code='$year' and msu.course_code=tmp.c_kode_prodi
					and kk.entrance_date between '1900-01-01' and '$grow_year-12-31 23:59:59') eks_fisik,
					(select count(*) from master_subject where curriculum_code='$year' and course_code=tmp.c_kode_prodi) mk,
					(select count(*) from master_subject where curriculum_code='$year' and course_code=tmp.c_kode_prodi and master_subject.id in(select master_subject_id from knowledge_item_subject)) mkadabuku
					from t_mst_fakultas tmf left join t_mst_prodi tmp on tmp.c_kode_fakultas=tmf.c_kode_fakultas where (nama_prodi not like '%Pindahan%' and nama_prodi not like '%International%' and nama_prodi not like '%Internasional%')  and tmf.c_kode_fakultas='$faculty')a");
	} 
	
	function totalsubject($jurusan,$tahun)
	{ 
		return $this->db->query("
		select count(*) totalmk, sum(book) mk, sum(buku) judul, sum(buku_fisik) judul_fisik from (
		select *, CASE  WHEN buku='0' THEN '0' ELSE '1' END as book, CASE  WHEN buku_fisik='0' THEN '0' ELSE '1' END as book_fisik from (
		select *, (select count(*) from knowledge_item_subject 	
					left join knowledge_item kt on knowledge_item_id=kt.id 
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id
					where ks.active='1' and kp.active='1' and knowledge_type_id=21 and master_subject_id=msu.id) buku, 
					(select count(*) from knowledge_item_subject 	
					left join knowledge_item kt on knowledge_item_id=kt.id 
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id
					where ks.active='1' and kp.active='1' and knowledge_type_id  in (1, 2, 3, 33, 40, 41, 59, 65) and master_subject_id=msu.id) buku_fisik
		from  master_subject msu where course_code ='$jurusan' AND msu.curriculum_code = '$tahun')a)b");
	} 
	
	function getbpkulbykodejur($jurusan,$tahun)
	{ 
		return $this->db->query("
			select count(judul) judul, sum(eks) eks from (select knowledge_item_id,(select count(id) from knowledge_item where id=kis.knowledge_item_id) judul, 
			(select count(ks.id) from knowledge_item ki left join knowledge_stock ks on ks.knowledge_item_id=ki.id where ki.id=kis.knowledge_item_id group by ki.id) eks from knowledge_item_subject kis 
											LEFT JOIN master_subject ms ON kis.master_subject_id = ms.id WHERE ms.course_code = '$jurusan' and ms.curriculum_code = '$tahun' order by knowledge_item_id) a");
	}
	
	function getbpkultambahanbykodejur($jurusan)
	{ 
		return $this->db->query("SELECT SUM(b.NJMLTOTAL) as EKS, count(*) as JUDUL FROM tbbpkuliahtambahan a LEFT JOIN tbkolEKSi b ON a.TNOINDUK = substr(b.TNOINDUK,1,7)
								LEFT JOIN tbmatakuliah c on a.KD_KULIAH = c.ID_KULIAH WHERE c.KD_JURUSAN = '$jurusan' AND a.TH_KURIKULUM = 2008");
	}
	
	function getjurbykodejur($jurusan)
	{
		return $this->db->query("select c_kode_prodi, nama_prodi from t_mst_prodi where c_kode_prodi = '$jurusan'");
	} 
	
	function getmkbykodejur($jurusan,$tahun)
	{
		return $this->db->query("select ms.code kode_mk,ms.semester,ms.name nama_mk, SUBSTR(ms.code,-1) sks, (SELECT COUNT(*) FROM knowledge_item_subject WHERE master_subject_id = ms.id) as jmljudul,ms.id id_kuliah FROM t_mst_prodi tp left join master_subject ms on tp.c_kode_prodi=ms.course_code WHERE tp.c_kode_prodi ='$jurusan' AND ms.curriculum_code = '$tahun' ORDER BY semester asc");
	
	} 
	
	// function getmkpilihanbykodejur($jurusan)
	// {
		// return $this->db->query("SELECT b.KODEMK, b.NAMAMK, SUBSTR(b.KODEMK,-1) as SKS, (SELECT COUNT(*) FROM tbbpkuliah WHERE KD_KULIAH = a.KD_KULIAH) as JMLJUDULREF, 
							// (SELECT COUNT(*) FROM tbbpkuliahtambahan WHERE KD_KULIAH = a.KD_KULIAH) as JMLJUDULTAMBAHAN, b.ID_KULIAH
							// FROM tbkurikulum_pem a LEFT JOIN tbmatakuliah b ON a.KD_KULIAH = b.ID_KULIAH 
							// WHERE upper(a.KD_JURUSAN) = upper('$jurusan') AND a.TH_KURIKULUM = '2008' ORDER BY b.NAMAMK ASC");
	// } 
	
	function getbukuref($kode, $where, $limit, $type)
	{
		// echo "select ki.code kode_buku, cc.code klasifikasi, ki.title, ki.author,eks, (select count(*) from knowledge_stock ks where status='1' and knowledge_item_id=ki.id) tersedia 
		// from knowledge_item_subject kis 
		 // join (SELECT ki.id,ki.code, ki.title,ki.classification_code_id,ki.knowledge_subject_id,ki.knowledge_type_id,ki.author, count(ki.id) eks
		// FROM knowledge_item ki
		// JOIN knowledge_stock ks ON ki.id = ks.knowledge_item_id group by ki.id) ki on kis.knowledge_item_id=ki.id 
		// left join classification_code cc on cc.id=ki.classification_code_id
		 // left join master_subject ms on kis.master_subject_id=ms.id
// left join knowledge_subject ks on ki.knowledge_subject_id=ks.id
					// left join knowledge_type kp on ki.knowledge_type_id=kp.id
					// where ks.active='1' and kp.active='1' and ki.knowledge_type_id not in (4,5,6) 
		 	// and $where kis.master_subject_id='$kode' order by ki.title
		// ";

		
		$temp = "ki.knowledge_type_id  in (1, 2, 3, 33, 40, 41, 59, 65, 21)";
		if($type=='book'){
			$temp = "ki.knowledge_type_id  in (1, 2, 3, 33, 40, 41, 59, 65)";
		}
		else if($type=='ebook'){
			$temp = "ki.knowledge_type_id ='21'";
		}

		
		return $this->db->query("select ki.id kiid,kis.master_subject_id, ki.code kode_buku, ki.knowledge_type_id,cc.code klasifikasi, ki.title, ki.author,eks,
	ki.published_year,
	ki.isbn, (select count(*) from knowledge_stock ks where status='1' and knowledge_item_id=ki.id) tersedia 
		from knowledge_item_subject kis 
		 join (SELECT  ki.id,ki.code, ki.title,
	ki.published_year,
	ki.isbn, ki.classification_code_id,ki.knowledge_subject_id,ki.knowledge_type_id,ki.author, count(ki.id) eks
		FROM knowledge_item ki
		JOIN knowledge_stock ks ON ki.id = ks.knowledge_item_id group by ki.id) ki on kis.knowledge_item_id=ki.id 
		left join classification_code cc on cc.id=ki.classification_code_id
		 left join master_subject ms on kis.master_subject_id=ms.id
left join knowledge_subject ks on ki.knowledge_subject_id=ks.id
					left join knowledge_type kp on ki.knowledge_type_id=kp.id
					where ks.active='1' and kp.active='1' and $temp
		 	and $where kis.master_subject_id='$kode' order by ki.published_year desc, ki.title
		");
							
		// return $this->db->query("SELECT a.TNOINDUK, b.TNOKELASLKP, b.TJUDUL, b.TPENGARANGLKP, b.NJMLTOTAL, b.NJMLTERSEDIA, a.NO
							// FROM tbbpkuliah a LEFT JOIN tbkolEKSi b on a.TNOINDUK = substr(b.TNOINDUK,1,7)
							// WHERE $where a.KD_KULIAH='$kode' and a.TH_KURIKULUM='2008' ORDER BY a.JUDUL ASC");
	} 
	
	// function getbukurefplus($kode, $where, $limit)
	// {
		// return $this->db->query("SELECT a.TNOINDUK, b.TNOKELASLKP, b.TJUDUL, b.TPENGARANGLKP, b.NJMLTOTAL, b.NJMLTERSEDIA, a.NO
							// FROM tbbpkuliahtambahan a LEFT JOIN tbkolEKSi b on a.TNOINDUK = substr(b.TNOINDUK,1,7)
							// WHERE $where a.KD_KULIAH='$kode' and a.TH_KURIKULUM='2008' ORDER BY a.JUDUL ASC ");
	// } 
	function delete_bukuref($id)
	{
		return $this->db->query("DELETE FROM tbbpkuliah WHERE NO = '$id'");
	}
	// function delete_bukurefplus($id)
	// {
		// return $this->db->query("DELETE FROM tbbpkuliahtambahan WHERE NO = '$id'");
	// }
	 
	// function getmaxidbukurefplus()
	// {
		// return $this->db->query("SELECT MAX(NO) as NO FROM TBBPKULIAHTAMBAHAN");
	// }
	
	function cekNOinduk($NOinduk)
	{
		return $this->db->query("SELECT TNOINDUK, TJUDUL, TPENGARANG FROM TBKOLEKSI WHERE TNOINDUK LIKE '$NOinduk.%'");
	}
	function cekduplikat($NOinduk, $mk)
	{
		return $this->db->query("SELECT * FROM ( 
						  SELECT NO FROM TBBPKULIAH WHERE TNOINDUK = '$NOinduk' AND KD_KULIAH = '$mk' 
						  UNION 
						  SELECT NO FROM TBBPKULIAHTAMBAHAN WHERE TNOINDUK = '$NOinduk' AND KD_KULIAH = '$mk')");
	}
	
	function add_bukurefplus($query)
	{
		return $this->db->query($query);
	}
	
	
	
	
}
?>