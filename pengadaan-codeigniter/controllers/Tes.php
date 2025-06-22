<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tes extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		//$this->load->model('Student_Model', 'dm', TRUE);
		//if (!$this->session->userdata('student_login')) redirect('login');	
	}
	
	public function index()
	{  
		$dbs = $this->db->query("SELECT *, group_concat(id) as ids FROM tbl_kodepos WHERE kec_id IS NULL GROUP BY kec ORDER BY provinsi ASC, kabupaten ASC, kecamatan ASC   LIMIT 0,200")->result();
		
		$data['html'] = '';
		
		foreach($dbs as $db)
		{
			$data['html'] .= '<div id="s_'.$db->id.'"><br><br><p>'.ucwords(strtolower($db->kecamatan)).'; '.$db->kabupaten.'; '.$db->provinsi.'</p>
			<select class="form-control input-sm kec" id="input_kec_'.$db->id.'" style="width:300px; float:left"></select><button type="button" onClick="save2('.$db->id.', \''.$db->ids.'\')">simpan ...</button>';
			
			//$data['html'] .= $this->algorithm($db->kec, $db->id, $db->ids, $db->kab);
			
			$data['html'] .= '</div>';
		}
		
		$data['view'] 		= 'frontend/student/tes';
		$data['title']		= 'Setting User';		
		$data['icon']		= 'icon-user';		
		$data['add']		= true;
		
		$this->load->view('frontend/tpl', $data); 
	}
	
	private function algorithm($str, $id, $ids, $kab)
	{
		$return = '';
		
		$kab = str_replace(array('-', 'kota', 'kab.'), '', $kab);
		
		$rels = $this->db->query("SELECT *, (MATCH(kec) AGAINST ('{$str}' IN BOOLEAN MODE)) AS relevance 
								  FROM ms_kec
								  WHERE MATCH(kec) AGAINST ('{$str}')
								  ORDER BY relevance DESC LIMIT 0,5")->result();
								  
		if(empty($rels))
		{
			if( strpos($str, '(') !== false )
			{
				$exp1 = explode('(', $str);
				
				$rels = $this->db->query("SELECT * 
										  FROM ms_kec
										  WHERE kec LIKE '%{$exp1[0]}%' AND kab LIKE '%{$kab}%'
										  LIMIT 0,5")->result();	
				$return .= $this->f($rels, $id, $ids);
				
				$rels2 = $this->db->query("SELECT * 
										  FROM ms_kec
										  WHERE kec LIKE '%".str_replace($exp1[1], ')', '')."%' AND kab LIKE '%{$kab}%'
										  LIMIT 0,5")->result();	
				$return .= $this->f($rels2, $id, $ids);
			}
			else
			{
				$len = strlen($str);
				
				if($len > 5)
				{
					$center = ceil($len/2);
					$exp = substr($str, 0, $center);
					
					$rels3 = $this->db->query("SELECT * 
											  FROM ms_kec
											  WHERE kec LIKE '%{$exp}%' AND kab LIKE '%{$kab}%'
											  LIMIT 0,5")->result();	
					$return .= $this->f($rels3, $id, $ids);
					
					$exp1 = substr($str, $center, $len-$center);
					
					$rels3 = $this->db->query("SELECT * 
											  FROM ms_kec
											  WHERE kec LIKE '%{$exp1}%' AND kab LIKE '%{$kab}%'
											  LIMIT 0,5")->result();	
					$return .= $this->f($rels3, $id, $ids);
				}
				else
				{
					$rels4 = $this->db->query("SELECT * 
											  FROM ms_kec
											  WHERE kec LIKE '%{$str}%' AND kab LIKE '%{$kab}%'
											  LIMIT 0,5")->result();	
					$return .= $this->f($rels4, $id, $ids);
				}
			}
		}
		else
		{
			$return .= $this->f($rels, $id, $ids);	
		}
		
		return $return;
	}
	
	private function f($rels, $id, $ids)
	{
		$return = '<ul>';
		
		foreach($rels as $rel)
			$return .= '<li>'.$rel->kec_name.'; '.$rel->kec_kab.'; '.$rel->kec_prov.' &nbsp; 
			<button type="button" class="xxx'.$id.'" onClick="save('.$id.', '.$rel->kec_id.', \''.$ids.'\')">simpan</button></li>';	
			
		$return .= '</ul><br>';
		
		return $return;
	}
	
	public function save()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$kec = $this->input->post('kec');
		$id   = $this->input->post('id');
		$ids   = $this->input->post('ids');
		
		if(!empty($ids))
			$this->db->query("UPDATE tbl_kodepos SET kec_id = '{$kec}' WHERE id IN ({$ids})");
		
		echo json_encode(array('status' => 'ok;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
}

?>