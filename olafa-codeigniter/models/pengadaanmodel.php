<?php
class PengadaanModel extends CI_Model {
	
	private $table 	= 'prosiding';
	private $id		= 'jd_id';
	function __construct()
	{
		parent::__construct(); 
	} 
	 
	
	function getall()
	{ 	 
		return $this->db->query("SELECT *   
			FROM pengadaan_pengajuan_detail pd 
			left join pengadaan_pengajuan on pj_id=pd_pj_id 
			left join pengadaan_nodin on nd_id=pd_nd_id 
			left join pengadaan_bast on bs_id=pd_bs_id order by pj_id,pj_tanggal");
	} 
	
	
	
	function getallpengajuannd($where="")
	{ 	 
		return $this->db->query("SELECT *   
			FROM pengadaan_pengajuan pj left join pengadaan_pengajuan_detail pd on pj_id=pd_pj_id where pd_nd_id=0 $where order by pj_id,pj_tanggal");
	} 

	function getallpengajuanbs($where="")
	{ 	 
		return $this->db->query("SELECT *   
			FROM pengadaan_pengajuan pj left join pengadaan_pengajuan_detail pd on pj_id=pd_pj_id where pd_bs_id=0 and pd_nd_id!=0 $where order by pj_id,pj_tanggal");
	}  	
	
	function getpengajuan()
	{ 	
		// $this->db->from($this->table);  
		// $this->db->order_by('jd_judul ASC, jd_edisi ASC'); 
		// return $this->db->get();
		
		return $this->db->query("select *, count(pd_id) total from (SELECT *   
			FROM pengadaan_pengajuan pj left join pengadaan_pengajuan_detail pd on pj_id=pd_pj_id order by pj_tanggal) a group by pj_id ");
	}  
	
	function getpengajuandetailbypj($id)
	{
		return $this->db->query("SELECT * FROM pengadaan_pengajuan_detail where pd_pj_id='$id'");
	}
	
	
	function getpengajuandetailbynd($id)
	{
		return $this->db->query("SELECT * FROM pengadaan_pengajuan_detail left join pengadaan_pengajuan on pj_id=pd_pj_id  where pd_nd_id='$id'");
	}
	
	function getpengajuandetailbybs($id)
	{
		return $this->db->query("SELECT * FROM pengadaan_pengajuan_detail left join pengadaan_pengajuan on pj_id=pd_pj_id  where pd_bs_id='$id'");
	}
	
	function getnodin()
	{ 	
		return $this->db->query("select *, count(pd_id) total from (SELECT *   
			FROM pengadaan_nodin pj left join pengadaan_pengajuan_detail pd on nd_id=pd_nd_id order by nd_tanggal) a group by nd_id ");
	}
	
	function getbast()
	{ 	
		return $this->db->query("select *, count(pd_id) total from (SELECT *   
			FROM pengadaan_bast pj left join pengadaan_pengajuan_detail pd on bs_id=pd_bs_id order by bs_tanggal) a group by bs_id ");
	}
	  
	
	function getpengajuanbyid($id)
	{
		return $this->db->query("SELECT * FROM pengadaan_pengajuan where pj_id='$id'");
	} 
	
	function getpengajuandetailbyid($id)
	{
		return $this->db->query("SELECT * FROM pengadaan_pengajuan_detail where pd_id='$id'");
	}

	function getnodinbyid($id)
	{
		return $this->db->query("SELECT * FROM pengadaan_nodin where nd_id='$id'");
	} 	
	
	function getbastbyid($id)
	{
		return $this->db->query("SELECT * FROM pengadaan_bast where bs_id='$id'");
	} 
	
	function addPengajuan($item)
	{
		$this->db->insert('pengadaan_pengajuan', $item);
		$last_id = $this->db->insert_id();
		return $last_id;
	}
	
	function addPengajuanDetail($item)
	{
		$this->db->insert('pengadaan_pengajuan_detail', $item);
	}
	
	function addNodin($item)
	{
		$this->db->insert('pengadaan_nodin', $item);
		$last_id = $this->db->insert_id();
		return $last_id;
	}
	
	function addBast($item)
	{
		$this->db->insert('pengadaan_bast', $item);
		$last_id = $this->db->insert_id();
		return $last_id;
	}
	
	function editPengajuan($id, $item)
	{
		$this->db->where('pj_id', $id);
		$this->db->update('pengadaan_pengajuan', $item);
	}
	
	function editPengajuanDetail($id, $item)
	{
		$this->db->where('pd_id', $id);
		$this->db->update('pengadaan_pengajuan_detail', $item);
	}
	
	function editNodin($id, $item)
	{
		$this->db->where('nd_id', $id);
		$this->db->update('pengadaan_nodin', $item);
	}
	
	function editBast($id, $item)
	{
		$this->db->where('bs_id', $id);
		$this->db->update('pengadaan_bast', $item);
	}
	
	
	function delPengajuan($id)
	{
		$this->db->where('pj_id', $id);
		$this->db->delete('pengadaan_pengajuan');
		
		$this->db->where('pd_pj_id', $id);
		$this->db->delete('pengadaan_pengajuan_detail');
	}
	
	function delPengajuanDetail($id)
	{
		$this->db->where('pd_id', $id);
		$this->db->delete('pengadaan_pengajuan_detail');
	}
	
	function delNodin($id)
	{
		$this->db->where('nd_id', $id);
		$this->db->delete('pengadaan_nodin');
		
		return $this->db->query("update pengadaan_pengajuan_detail set pd_nd_id=0,pd_status='Diajukan Dosen',pd_eks_awal='0' where pd_nd_id='$id'");
	} 
	
	function delBast($id)
	{
		$this->db->where('bs_id', $id);
		$this->db->delete('pengadaan_bast');
		
		return $this->db->query("update pengadaan_pengajuan_detail set pd_bs_id=0,pd_status='Diajukan ke Logistik',pd_eks_akhir='0' where pd_bs_id='$id'");
	} 
	
	
	 
}
?>