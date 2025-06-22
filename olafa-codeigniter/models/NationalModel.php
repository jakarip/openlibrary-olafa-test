<?php
class NationalModel extends CI_Model {
	
	/**
	 * Constructor
	 */
	 
	 function getall()
	{ 	
		// $this->db->from($this->table);  
		// $this->db->order_by('jd_judul ASC, jd_edisi ASC'); 
		// return $this->db->get();
		
		return $this->db->query("SELECT *, count(ki.id) eks   
			FROM knowledge_item ki
			LEFT JOIN knowledge_stock ks ON ki.id = ks.knowledge_item_id where ki.knowledge_type_id='42' group by ki.id order by ki.id");
	}
	 
	
	function getbyquery($where="", $limit="")
	{
		//return $this->db->query("SELECT * FROM jurnaldikti $where ORDER BY jd_judul ASC, jd_edisi ASC $limit");
		return $this->db->query("SELECT *, count(ki.id) eks  FROM knowledge_item ki LEFT JOIN knowledge_stock ks ON ki.id = ks.knowledge_item_id where ki.knowledge_type_id='42'   $where group by ki.id  ORDER BY publisher_name asc, published_year desc, title ASC");
	} 
	
	function __construct()
	{
		parent::__construct();
	}
	function getjitbyquery($where, $limit)
	{
		return $this->db->query("SELECT * FROM jurnalinternational_trans $where ORDER BY jit_name $limit");
	}
	
	function countjitbyquery($where)
	{
		return $this->db->query("SELECT * FROM jurnalinternational_trans $where ORDER BY jit_name")->num_rows;
	}
	
	function getalljiofisik()
	{ 
		return $this->db->query("SELECT * FROM jurnalinternational_online WHERE jio_fisik = 1 ORDER BY jio_name ASC");
	}
	
	function getjurnaltrans($kode)
	{ 
		return $this->db->query("SELECT jit_id, jit_name FROM jurnalinternational_trans WHERE jit_jio_id = '$kode' ORDER BY jit_name");
	}
	
	function getbyid($id)
	{ 
		return $this->db->query("SELECT * FROM jurnalinternational WHERE jif_id = '$id'");
	}
	
	function getjurnalbyid($id)
	{ 
		return $this->db->query("SELECT * FROM jurnalinternational_online WHERE jio_id = $id");
	}
	
	function getjurnalintbyid($code, $th)
	{ 
		return $this->db->query("SELECT jif_id, jit_name, jif_bulan, jif_volume FROM jurnalinternational WHERE jio_id = '$code' AND jif_tahun = '$th' ORDER BY jit_name ASC, jif_bulan ASC");
	}
	
	function cekjurnal($jif_jit_id, $bulan, $tahun)
	{ 
		return $this->db->query("SELECT jif_jit_id FROM jurnalinternational_fisik WHERE jif_jit_id = '$jif_jit_id' AND jif_bulan = '$bulan' AND jif_tahun = '$tahun'");
	}
	
	function getminandmaxtahunbyid($id)
	{ 
		return $this->db->query("SELECT MIN(jif_tahun) as maxtahun, MAX(jif_tahun) as mintahun FROM jurnalinternational WHERE jio_id = '$id'");
	} 
	
	function add($id, $bulan, $volume, $tahun)
	{ 
		return $this->db->query("INSERT INTO jurnalinternational_fisik(jif_jit_id, jif_bulan, jif_volume, jif_tahun) VALUES ('$id', '$bulan', '$volume', '$tahun')");
	} 
	
	function update($jif_jit_id, $bulan, $volume, $tahun, $id)
	{ 
		return $this->db->query("UPDATE jurnalinternational_fisik SET jif_jit_id = '$jif_jit_id', jif_bulan = '$bulan', jif_volume = '$volume', jif_tahun = '$tahun' WHERE jif_id = '$id'");
	}
	
	function delete($id)
	{
		$this->db->where('jif_id', $id);
		$this->db->delete('jurnalinternational_fisik');
	} 
	
	function addjurnal($item)
	{ 	
		$this->db->insert('jurnalinternational_trans', $item);
	} 
	
	function deletejurnal($id)
	{
		$this->db->where('jit_id', $id);
		$this->db->delete('jurnalinternational_trans');
	}
	
}
?>