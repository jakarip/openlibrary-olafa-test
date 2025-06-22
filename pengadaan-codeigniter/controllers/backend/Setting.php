<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Ms_Course_Model', 'com', TRUE);
		$this->load->model('Settings_Model', 'sm', TRUE);
		$this->load->model('Ms_Component_Model', 'mcm', TRUE);
		$this->load->model('Ms_Scholarship_Model', 'msm', TRUE);
		$this->load->model('Ms_Prodi_Model', 'mpm', TRUE); 
		$this->load->model('Common_Model', 'cm', TRUE);
		$this->load->model('Periode_Model', 'dm', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/setting/index';
		$data['title']	= 'Setting';		
		$data['icon']	= 'icon-cog52';
		
		$this->load->helper('form');
		
		$settings = $this->sm->getall()->result();
		$data['setting'] = array();
		foreach($settings as $setting)
		{
			$data['setting'][$setting->setting_option] = $setting->setting_value;
		}
		$data['prodi'] 			= $this->dm->getProdiActive()->result();  
		

		$data['bobot'] = array('0.1' => '0.1 (10%)', '0.2' => '0.2 (20%)', '0.3' => '0.3 (30%)', '0.4' => '0.4 (40%)', '0.5' => '0.5 (50%)', '0.6' => '0.6 (60%)', 
							   '0.7' => '0.7 (70%)', '0.8' => '0.8 (80%)', '0.9' => '0.9 (90%)', '1' => '1 (100%)' );
							   
		$jml_prodi = $this->mpm->count_active();
		for($i=1; $i<=$jml_prodi; $i++)
			$data['max'][$i] = $i;
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function update_setting()
	{	
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('fee')) return false; 
		
		$fee 	= $this->input->post('fee');
		$id 	= $this->input->post('id');
		$find 	= array(",", "."); 
		
		$this->msm->deleteprodischolarship();
		foreach($fee as $key => $row){
			foreach($row as $key2 => $r){
				$item['ps_id_prodi'] 		= $key;
				$item['ps_id_scholarship'] 	= $key2; 
				$item['ps_amount'] 			= str_replace($find, "",$fee[$key][$key2]);   
				$this->msm->addprodischolarship($item);
			}
		} 
		echo json_encode(array('status' => 'ok;', 'text' => ''));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
	
	public function course_json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'course_status', 'dt' => 0 ),
			array( 'db' => 'course_name', 'dt' => 1 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->com->dtquery($param)->result();
		$filter = $this->com->dtfiltered();
		$total	= $this->com->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			if($row->course_status == 1)
				$label = '<span class="label label-success">Aktif</span>';
			else
				$label = '<span class="label label-danger">Non Aktif</span>';
				
			if($row->course_status == 1)
				$btn = '<a href="javascript:active('.$row->course_id.', \''.$row->course_name.'\', 0, \'course\')" title="Non Aktifkan Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-database-remove"></i></a>';
			else
				$btn = '<a href="javascript:active('.$row->course_id.', \''.$row->course_name.'\', 1, \'course\')" title="Aktifkan Data" class="btn btn-xs btn-icon btn-success"><i class="icon-database-check"></i></a>';
				
			$rows = array (
				$label,
				$row->course_name,
				$row->course_score,
				$row->course_type,
				$row->course_tags,
				'<a href="javascript:edit('.$row->course_id.', \'course\')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a> '.$btn
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	public function course_insert()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		
		if( $this->com->add($item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function course_edit()
	{
		if(!$this->input->post('id')) return false;
		
		echo json_encode($this->com->getbyid($this->input->post('id'))->row());
	}
	
	public function course_update()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');
		
		if( $this->com->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function course_active()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		$mode = $this->input->post('mode');
		
		if( $this->com->edit($id, array('course_status' => $mode)) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	/* Setting 2 */
	
	public function save_setting()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('option')) return false;
		
		$settings 	= $this->sm->getall()->result();
		$setting 	= array();
		foreach($settings as $set)
		{
			$setting[$set->setting_option] = $set->setting_value;
		}
		
		$options = $this->input->post('option');
		foreach($options as $option => $value)
		{
			$this->sm->update_value($option, $value);
		}
		
		$status			= "success"; 
		$error			= "";
		$logo			= $setting['image_logo'];
		$background		= $setting['image_background'];
		$file			= $setting['file_payment_method'];
		$upPath			= "cdn/environment/";   
		//logo
		if(isset($_FILES['logo']) && $_FILES['logo']['error'] != UPLOAD_ERR_NO_FILE) {
			$config = array(
				'file_name'		=> 'logo',
				'upload_path' 	=> $upPath,
				'allowed_types' => "jpg|png|jpeg",
				'overwrite'		=> TRUE
			);
			$this->load->library('upload', $config);
			$this->upload->initialize($config); 
			
			$this->load->library('upload', $config);
			if(!$this->upload->do_upload('logo')) $data['imageError']['logo'] =  $this->upload->display_errors(); 
			else
			{  
				$ud  	 = $this->upload->data();
				$ext 	 = str_replace('.', '', $ud['file_ext']); 
				$logo	 = $config['upload_path'].$ud['file_name'];
				$this->sm->update_value('image_logo', $config['upload_path'].$ud['file_name']);
			}
			
			if (ISSET($data['imageError']['logo'])) {
				$status='failed';
				$error .= "- Upload Logo : \n".strip_tags($data['imageError']['logo'])."\n\n"; 
			}
		}  
		//====================================================================================
		//background
		if(isset($_FILES['background']) && $_FILES['background']['error'] != UPLOAD_ERR_NO_FILE) {
			$config = array(
				'file_name'		=> 'background',
				'upload_path' 	=> $upPath,
				'allowed_types' => "jpg|png|jpeg",
				'overwrite'		=> TRUE
			); 
			$this->load->library('upload', $config);
			$this->upload->initialize($config); 
			
			$this->load->library('upload', $config);
			if(!$this->upload->do_upload('background')) $data['imageError']['background'] =  $this->upload->display_errors(); 
			else
			{  
				$ud  		 = $this->upload->data();
				$ext 		 = str_replace('.', '', $ud['file_ext']); 
				$background	 = $config['upload_path'].$ud['file_name']; 
				$this->sm->update_value('image_background', $config['upload_path'].$ud['file_name']);
			}
			
			if (ISSET($data['imageError']['background'])) {
				$status='failed';
				$error .= "- Upload Background :\n".strip_tags($data['imageError']['background'])."\n\n";
			}
		}  
		//====================================================================================
		//background
		if(isset($_FILES['file']) && $_FILES['file']['error'] != UPLOAD_ERR_NO_FILE) {
			$config = array(
				'file_name'		=> 'tata_cara_pembayaran',
				'upload_path' 	=> $upPath,
				'allowed_types' => "docx|pdf",
				'overwrite'		=> TRUE
			);
			$this->load->library('upload', $config);
			$this->upload->initialize($config); 
			
			$this->load->library('upload', $config);
			if(!$this->upload->do_upload('file')) $data['imageError']['file'] =  $this->upload->display_errors(); 
			else
			{  
				$ud  	 = $this->upload->data();
				$ext 	 = str_replace('.', '', $ud['file_ext']);  
				$file	 = $config['upload_path'].$ud['file_name']; 
				$this->sm->update_value('file_payment_method', $config['upload_path'].$ud['file_name']);
			}
			
			if (ISSET($data['imageError']['file'])) {
				$status='failed'; 
				$error .= "- Upload Tata Cara Pembayaran :\n".strip_tags($data['imageError']['file'])."\n\n";
			}
		}  
		//====================================================================================
		
		if ($status=="success") echo json_encode(array('status'=> 'ok;','logo'=>$logo,'background'=>$background,'file'=>$file));
		else echo json_encode(array('status'=> 'failed',"text"=>"Ada error saat upload : \n".$error)); 
	}
	
	public function save_setting_submit()
	{
		if(!$this->input->post('option')) return false;
		
		$options = $this->input->post('option');
		foreach($options as $option => $value)
		{
			$this->sm->update_value($option, $value);
		}
		
		redirect(y_url_admin().'/setting');
	}
	 
}

?>