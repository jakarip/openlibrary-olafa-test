<?php
class BahanPustakaModel extends CI_Model {
	
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
		//return $this->db->query("SELECT KD_JURUSAN, NAMA_JURUSAN FROM TBPROGRAM WHERE KD_JURUSAN NOT LIKE 'PD%' ORDER BY NAMA_JURUSAN ASC");
	} 
	function getbpkulbykodejur($jurusan)
	{ 
		return $this->db->query("
			select count(judul) judul, sum(eks) eks from (select knowledge_item_id,(select count(id) from knowledge_item where id=kis.knowledge_item_id) judul, 
			(select count(ks.id) from knowledge_item ki left join knowledge_stock ks on ks.knowledge_item_id=ki.id where ki.id=kis.knowledge_item_id group by ki.id) eks from knowledge_item_subject kis 
											LEFT JOIN master_subject ms ON kis.master_subject_id = ms.id WHERE ms.course_code = '$jurusan' and ms.curriculum_code = '2014' order by knowledge_item_id) a");
		// return $this->db->query("SELECT SUM(b.NJMLTOTAL) as EKS, count(*) as JUDUL FROM tbbpkuliah a LEFT JOIN tbkolEKSi b ON a.TNOINDUK = substr(b.TNOINDUK,1,7)
								// LEFT JOIN tbmatakuliah c on a.KD_KULIAH = c.ID_KULIAH WHERE c.KD_JURUSAN = '$jurusan' AND a.TH_KURIKULUM = 2008" );
	}
	
	function getbpkultambahanbykodejur($jurusan)
	{ 
		return $this->db->query("SELECT SUM(b.NJMLTOTAL) as EKS, count(*) as JUDUL FROM tbbpkuliahtambahan a LEFT JOIN tbkolEKSi b ON a.TNOINDUK = substr(b.TNOINDUK,1,7)
								LEFT JOIN tbmatakuliah c on a.KD_KULIAH = c.ID_KULIAH WHERE c.KD_JURUSAN = '$jurusan' AND a.TH_KURIKULUM = 2008");
	}
	
	function getjurbykodejur($jurusan)
	{
		return $this->db->query("select c_kode_prodi, nama_prodi from t_mst_prodi where c_kode_prodi = '$jurusan'");
		//return $this->db->query("SELECT NAMA_JURUSAN, KD_JURUSAN FROM tbprogram WHERE KD_JURUSAN = '$jurusan'");
	} 
	
	function getmkbykodejur($jurusan)
	{
		return $this->db->query("select ms.code kode_mk,ms.semester,ms.name nama_mk, SUBSTR(ms.code,-1) sks, (SELECT COUNT(*) FROM knowledge_item_subject WHERE master_subject_id = ms.id) as jmljudul,ms.id id_kuliah FROM t_mst_prodi tp left join master_subject ms on tp.c_kode_prodi=ms.course_code WHERE tp.c_kode_prodi ='$jurusan' AND ms.curriculum_code = '2014' ORDER BY jmljudul desc");
	
		
		// return $this->db->query("SELECT a.SEMESTER, b.KODEMK, b.NAMAMK, SUBSTR(b.KODEMK,-1) as SKS, (SELECT COUNT(*) FROM tbbpkuliah WHERE KD_KULIAH = a.KD_KULIAH) as JMLJUDULREF, 
							// (SELECT COUNT(*) FROM tbbpkuliahtambahan WHERE KD_KULIAH = a.KD_KULIAH) as JMLJUDULTAMBAHAN, b.ID_KULIAH
							// FROM tbkurikulum_sem a LEFT JOIN tbmatakuliah b ON a.KD_KULIAH = b.ID_KULIAH 
							// WHERE upper(a.KD_JURUSAN) = upper('$jurusan') AND a.TH_KURIKULUM = '2008' ORDER BY a.SEMESTER ASC, b.NAMAMK ASC");
	} 
	
	// function getmkpilihanbykodejur($jurusan)
	// {
		// return $this->db->query("SELECT b.KODEMK, b.NAMAMK, SUBSTR(b.KODEMK,-1) as SKS, (SELECT COUNT(*) FROM tbbpkuliah WHERE KD_KULIAH = a.KD_KULIAH) as JMLJUDULREF, 
							// (SELECT COUNT(*) FROM tbbpkuliahtambahan WHERE KD_KULIAH = a.KD_KULIAH) as JMLJUDULTAMBAHAN, b.ID_KULIAH
							// FROM tbkurikulum_pem a LEFT JOIN tbmatakuliah b ON a.KD_KULIAH = b.ID_KULIAH 
							// WHERE upper(a.KD_JURUSAN) = upper('$jurusan') AND a.TH_KURIKULUM = '2008' ORDER BY b.NAMAMK ASC");
	// } 
	
	function getbukuref($kode, $where, $limit)
	{
		return $this->db->query("select ki.code kode_buku, cc.code klasifikasi, ki.title, ki.author,eks, (select count(*) from knowledge_stock ks where status='1' and knowledge_item_id=ki.id) tersedia 
		from knowledge_item_subject kis 
		left join (SELECT ki.id,ki.code, ki.title,ki.classification_code_id,ki.author, count(ki.id) eks
		FROM knowledge_item ki
		LEFT JOIN knowledge_stock ks ON ki.id = ks.knowledge_item_id group by ki.id) ki on kis.knowledge_item_id=ki.id 
		left join classification_code cc on cc.id=ki.classification_code_id
		 left join master_subject ms on kis.master_subject_id=ms.id
		where $where kis.master_subject_id='$kode' and ms.curriculum_code = '2014' order by ki.title
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