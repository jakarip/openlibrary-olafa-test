<?php
class KaryaIlmiahModel extends CI_Model {
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	function totalkaryailmiah()
	{
		$total = $this->db->query("select sum(jml_ta) total from (select c_kode_prodi, nama_prodi,nama_fakultas, (
						select count(*) total 
						from knowledge_item kt 
						left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
						left join knowledge_type kp on kt.knowledge_type_id=kp.id 
						where ks.active='1' and kp.active='1' and kt.knowledge_type_id
						in (4,5,6) and course_code=tp.c_kode_prodi)jml_ta 
					from t_mst_prodi tp 
					left join t_mst_fakultas tf using(c_kode_fakultas) 
					order by nama_fakultas, nama_prodi)a")->row();
		return $total->total;
	} 
	
	function getjurusanbyid($kodejurusan)
	{ 
		return $this->db->query("SELECT * FROM t_mst_prodi where c_kode_prodi='$kodejurusan'");
	}
	
	
	
	function get_ta_pa2($kodejurusan, $search, $limit)
	{
		$sql = $this->load->database('repository', true);
		return $sql->query("SELECT * FROM tbta_pa WHERE (SUBSTR(nim,1,3) = '$kodejurusan' OR SUBSTR(nim,1,3) = '212') $search 
							ORDER BY tahun DESC, nim ASC $limit");
	}
	
	function get_ta_pa_ref($kodejurusan, $search, $limit)
	{
		$sql = $this->load->database('repository', true);
		return $sql->query("SELECT * FROM tbta_pa_ref a LEFT JOIN tbta_pa b ON a.nim = b.nim
							WHERE a.kd_jurusan = '$kodejurusan' $search ORDER BY b.tahun DESC, b.nim ASC $limit");
	} 
	
	function ceknim($nim)
	{
		$sql = $this->load->database('repository', true);
		return $sql->query("SELECT nama FROM tbta_pa WHERE nim = '$nim'");
	}

	function cekduplikat($nim, $kodejurusan)
	{
		$sql = $this->load->database('repository', true);
		return $sql->query("SELECT id_ta_ref FROM tbta_pa_ref WHERE kd_jurusan = '$kodejurusan' AND nim = '$nim'");
	}	
	
	function add_ta_pa_ref($query)
	{
		$sql = $this->load->database('repository', true);
		return $sql->query($query);
	} 
	
	function delete_ta_pa_ref($id)
	{
		$sql = $this->load->database('repository', true);
		return $sql->query("DELETE FROM tbta_pa_ref WHERE id_ta_ref = '$id'");
	} 
} 
?>