<?php
class CreamerModel extends CI_Model {
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		//$this->koleksi = $this->load->database('koleksi', true); 
	}
	
	function DeleteFileTotal()
	{ 
		return $this->db->query("delete from file_total"); 
	}  
	
	function InsertFileTotal($data)
	{ 
		return $this->db->insert('file_total', $data);
	}  
	
	function knowledge_item()
	{ 
		return $this->db->query("Select * from knowledge_items where id>=103955")->result(); 
	}  
	
	function knowledge_stock($id)
	{ 
		return $this->db->query("Select * from knowledge_stocks where knowledge_item_id='$id'")->result(); 
	}  
	
	
	function koleksiAudioVisual()
	{ 
		return $this->koleksi->query("Select * from audiovisual limit 3")->result(); 
	}
	
	function lastKodeKatalog()
	{ 
		return $this->db->query("Select code from knowledge_item where knowledge_type_id='15' order by id desc limit 1")->row(); 
	}  
	
	function InsertKatalog($item)
	{
		$this->db->insert('knowledge_item', $item);
		$last_id = $this->db->insert_id();
		return $last_id;
	} 
	
	function InsertEksKatalog($item)
	{
		$this->db->insert('knowledge_stock', $item);
	}
	
	function koleksiAudioVisualEks($id)
	{ 
		return $this->koleksi->query("Select * from audiovisual_eks where aveks_id_av='$id'")->result();  
	}  
	
	
	function lastKodeEksKatalog($id)
	{ 
		return $this->db->query("Select code from knowledge_stock where knowledge_item_id='$id' order by id desc limit 1"); 
	}  
	
	
	function getKnowledgeItem($supplier)
	{ 
		return $this->db->query("Select * from knowledge_item where supplier ='$supplier'")->result(); 
	} 
	
	function getUploadType()
	{ 
		return $this->db->query("Select * from upload_type order by name")->result(); 
	} 
	
	function getKnowledgeType()
	{ 
		return $this->db->query("Select * from knowledge_type")->result(); 
	} 
	
	function getKatalogByType($kt)
	{ 
		return $this->db->query("Select * from knowledge_item where knowledge_type_id='$kt'")->result(); 
	} 
	
	function getMember()
	{ 
		return $this->db->query("Select * from member")->result(); 
	} 
	 
	
	function getSSO($name)
	{ 
		return $this->db->query("Select * from t_tem_userlogin_igracias where c_username='$name'"); 
	} 
	
	function updateMember($prodi,$name)
	{ 
		return $this->db->query("update member set master_data_course='$prodi' where master_data_user='$name'"); 
	} 
	
	function getNIM()
	{ 
		return $this->db->query("Select location from tes left join on workflow_document wd on wd.id=tes.id left join workflow_document_file wdf on document_id=wd.id group by wd.id")->result(); 
	} 
	
	function getUsenameByWorkflow()
	{ 
		return $this->db->query("Select master_data_user user, (select location from workflow_document_file 
where document_id=wd.id group by wd.id) loc 
from workflow_document wd left join member m on 
wd.member_id=m.id where abs(master_data_user) = 0 group by master_data_user order by master_data_user")->result(); 
	} 
	
	function getDocument()
	{ 
		return $this->db->query("select id,workflow_id,course_code, created_by, created_at, updated_by, updated_at, (select location from workflow_document_file wf where wf.upload_type_id='16' and document_id=wd.id) 
 lokasi from workflow_document wd  
 where id>'6165'  and workflow_id='1' group by created_by order by id")->result(); 
	} 
	
	function getKnowledgeItemByProdi($prodi)
	{ 
		return $this->db->query("
		SELECT ki.code code,ki.id,  
		(select master_data_user from member where master_data_fullname=author group by master_data_fullname) nim, author,title, editor, ki.created_by,ki.created_at
		FROM knowledge_item ki
		LEFT JOIN t_mst_prodi tp ON tp.c_kode_prodi = ki.course_code
		LEFT JOIN t_mst_fakultas tf ON tf.c_kode_fakultas = tp.c_kode_fakultas
		WHERE ki.knowledge_type_id
		IN ( 4, 5, 6 ) and ki.course_code='$prodi'")->result(); 
	} 
	 
	
	function getAllData()
	{ 
		return $this->db->query("SELECT ki.code code,  
			  tp.c_kode_fakultas, nama_fakultas, course_code,abstract_content abstrak, nama_prodi, title, author,
			(select master_data_email from member where master_data_fullname=author group by master_data_fullname) email,editor, 
			published_year, ki.created_at, softcopy_path, REPLACE(SUBSTRING(SUBSTRING_INDEX(editor, ',', 1),
				   LENGTH(SUBSTRING_INDEX(editor, ',', 1 -1)) + 1),
				   ',', '')  AS pembimbing1, 
			REPLACE(SUBSTRING(SUBSTRING_INDEX(editor, ',', 2),
				   LENGTH(SUBSTRING_INDEX(editor, ',', 2 -1)) + 1),
				   ',', '') AS pembimbing2 
			FROM knowledge_item ki
			LEFT JOIN t_mst_prodi tp ON tp.c_kode_prodi = ki.course_code
			LEFT JOIN t_mst_fakultas tf ON tf.c_kode_fakultas = tp.c_kode_fakultas
			WHERE ki.softcopy_path not in (select nim from test_jurnal_eproc) and ki.knowledge_type_id
			IN ( 4, 5, 6 )
			AND (softcopy_path NOT LIKE '%.%' OR  (ki.created_at between  '2000-06-30  00:00:00' and  '2014-06-30 00:00:00'))
			ORDER BY code asc")->result(); 
	} 

	function getSomeData()
	{ 
		return $this->db->query("SELECT ki.code code, softcopy_path,  
		  tp.c_kode_fakultas, nama_fakultas, knowledge_type_id tipe, nama_prodi,published_year, title, author,
		published_year
		FROM knowledge_item ki
		LEFT JOIN t_mst_prodi tp ON tp.c_kode_prodi = ki.course_code
		LEFT JOIN t_mst_fakultas tf ON tf.c_kode_fakultas = tp.c_kode_fakultas
		WHERE ki.softcopy_path not in (select nim from test_jurnal_eproc) and ki.knowledge_type_id
		IN ( 4, 5, 6 )
		AND (softcopy_path NOT LIKE '%.%' OR  (ki.created_at between  '2000-06-30  00:00:00' and  '2014-06-30 00:00:00')) 
		ORDER BY published_year desc limit 0,50 ")->result(); 
	}  
	
	function getPembimbing($nama)
	{ 	
		$nama = strtoupper($nama);
		return $this->db->query('select master_data_fullname,master_data_email,NAMA_FAKULTAS fakultas, NAMA_PRODI prodi from member m left join t_mst_prodi mp on master_data_course=C_KODE_PRODI left join t_mst_fakultas mf using(c_kode_fakultas) where UPPER(master_data_fullname) like "%$nama%"')->row(); 
	} 
}
?>