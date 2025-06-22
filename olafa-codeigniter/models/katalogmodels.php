<?php
class KatalogModel extends CI_Model {
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	function getallknowledgetype()
	{ 
		return $this->db->query("select kt.name nama, 
(select count(*) from knowledge_item where knowledge_type_id=kt.id) judul, 
count(*) eksemplar, 
(select count(*) from knowledge_item left join knowledge_stock on knowledge_item.id=knowledge_item_id where status='1' and knowledge_item.knowledge_type_id=kt.id) tersedia, 
(select count(*) from knowledge_item left join knowledge_stock on knowledge_item.id=knowledge_item_id where status='2' and knowledge_item.knowledge_type_id=kt.id) dipinjam, 
(select count(*) from knowledge_item left join knowledge_stock on knowledge_item.id=knowledge_item_id where status='3' and knowledge_item.knowledge_type_id=kt.id) rusak, 
(select count(*) from knowledge_item left join knowledge_stock on knowledge_item.id=knowledge_item_id where status='4' and knowledge_item.knowledge_type_id=kt.id) hilang, 
(select count(*) from knowledge_item left join knowledge_stock on knowledge_item.id=knowledge_item_id where status='5' and knowledge_item.knowledge_type_id=kt.id) expired,
(select count(*) from knowledge_item left join knowledge_stock on knowledge_item.id=knowledge_item_id where status='6' and knowledge_item.knowledge_type_id=kt.id) hilang_diganti,
(select count(*) from knowledge_item left join knowledge_stock on knowledge_item.id=knowledge_item_id where status='7' and knowledge_item.knowledge_type_id=kt.id) diolah
from knowledge_stock ks left join knowledge_item kn on kn.id=knowledge_item_id left join knowledge_type kt on kt.id=kn.knowledge_type_id  group by kt.id order by kt.name");
		 
	} 
	
	function getbookonprocess($where="")
	{  
			return $this->db->query("SELECT kl.name tipe, ks.id id, kt. CODE catalog, ks. CODE barcode, cc. CODE klasifikasi, title,
			author, publisher_name,kl.name tipe FROM knowledge_item kt
			LEFT JOIN knowledge_stock ks ON kt.id = knowledge_item_id
			LEFT JOIN classification_code cc ON cc.id = kt.classification_code_id
			LEFT JOIN knowledge_type kl ON kl.id = kt.knowledge_type_id where status='7' $where 
			ORDER BY status,kl.id,kt.id ");
	}
	
	function getbook($where)
	{ 
			return $this->db->query("SELECT ks.id id, kt. CODE catalog, ks. CODE barcode, cc. CODE klasifikasi, title,
			author, publisher_name,kl.name tipe FROM knowledge_item kt
			LEFT JOIN knowledge_stock ks ON kt.id = knowledge_item_id
			LEFT JOIN classification_code cc ON cc.id = kt.classification_code_id
			LEFT JOIN knowledge_type kl ON kl.id = kt.knowledge_type_id where kt.knowledge_type_id='1' $where
			ORDER BY status,kl.id,kt.id ");
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
	
}
?>