<?php
class KatalogMkModel extends CI_Model {
	private $table = 'knowledge_item';
	private $id    = 'id';

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}

	
	// Query for new datatables purpose ;
	//--------------------------------------------------------------------------------------------------------------------------
	function dtquery($param)
	{ 

		return $this->db->query("SELECT *,(
	SELECT
		COUNT( ms.id ) 
	FROM
		knowledge_item_subject kis
		JOIN master_subject ms ON ms.id = kis.master_subject_id 
	WHERE
		kis.knowledge_item_id = a.id 
		AND ms.curriculum_code = '2020' 
	) AS total  from (select kt.id, cc.name klasifikasi,ks.name subjek,kt.title,kt.published_year,kt.code codes,author,kp.name tipe,'0' total
from knowledge_item kt
		left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		left join classification_code cc on kt.classification_code_id=cc.id
		left join knowledge_type kp on kt.knowledge_type_id=kp.id where ks.active='1' and kp.active='1' 
		$param[where] $param[order] $param[limit] )a");
	}
	
	function dtfiltered($param)
	{
		$result = $this->db->query("SELECT count(kt.id) jumlah
		from knowledge_item kt
		left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		left join classification_code cc on kt.classification_code_id=cc.id
		left join knowledge_type kp on kt.knowledge_type_id=kp.id where ks.active='1' and kp.active='1' 
		$param[where]")->row();
		
		return $result->jumlah;
	}
	
	function dtcount()
	{
		return $this->db->count_all($this->table);
	}
	//--------------------------------------------------------------------------------------------------------------------------
	

	
	function getbyid($id)
	{ 
		return $this->db->query("SELECT * FROM knowledge_item where id='$id'");
	}
	
	function get_type()
	{ 
		return $this->db->query("SELECT * FROM knowledge_type where active='1' and id in (1, 2, 3,10, 21, 33, 40, 41, 59, 65, 12, 77, 78, 45, 46, 44, 42) order by name ");
	}
	
	function get_location()
	{ 
		return $this->db->query("SELECT id, name FROM item_location where show_as_footer='1' order by name ");
	}
	function getcurriculumyear()
	{ 
		return $this->db->query("select * from master_subject group by curriculum_code order by curriculum_code desc");
	}
	function getstudyprogram()
	{ 
		return $this->db->query("select * from t_mst_prodi tmp left join t_mst_fakultas tmf on tmp.C_KODE_FAKULTAS=tmf.C_KODE_FAKULTAS order by NAMA_FAKULTAS,NAMA_PRODI");
	} 
	
	function add($item)
	{
		$this->db->insert('knowledge_item_subject', $item);
	}
	
	function delete($master_subject_id,$knowledge_item_id)
	{	
		return $this->db->query("delete from knowledge_item_subject where master_subject_id='$master_subject_id' and knowledge_item_id='$knowledge_item_id'");
	}
	
	function checkExisting($master_subject_id,$knowledge_item_id)
	{	
		return $this->db->query("select * from knowledge_item_subject where master_subject_id='$master_subject_id' and knowledge_item_id='$knowledge_item_id'");
	}

	
	 
} 
?>