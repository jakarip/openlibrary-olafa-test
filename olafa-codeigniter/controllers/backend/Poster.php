<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Poster extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Poster_Model', 'dm', TRUE); 
		
		if (!$this->session->userdata('login') and !$this->session->userdata('referral_login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{

		$data['title']	= 'Poster';		
		$data['icon']	= 'icon-images3';

        if($this->session->userdata('usergroup') == 'referral') {
            $data['data'] = $this->dm->getall()->result();
            $data['view'] = 'backend/poster/grid';
        } else {
            $data['view'] = 'backend/poster/index';
        }

		
		$this->load->helper('form'); 
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'poster_date', 'dt' => 0 ),
			array( 'db' => 'poster_title', 'dt' => 1 ),
			array( 'db' => 'poster_image', 'dt' => 2 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			$delete = "";
			if($this->session->userdata('usergroup')=='admin'){
				$delete = '<a href="javascript:del('.$row->poster_id.', \''.$row->poster_title.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>';
			}

			$rows = array (
				$row->poster_date,
				$row->poster_title,  
				'<img src="'.base_url().$row->poster_image.'" width="50%">',  
				'<a href="'.base_url().$row->poster_image.'" title="Download Data" target="_blank" class="btn btn-xs btn-icon btn-primary"><i class="icon-file-download"></i></a>'.$delete
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	public function insert()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp'); 
		$item['poster_date'] = date('Y-m-d');


		//gambar & file
		$upPath = "cdn/poster/";  
		if(!file_exists($upPath))
		{
				mkdir($upPath, 0777, true);
		}

		if(isset($_FILES['photos']) && $_FILES['photos']['name'] != '' && $_FILES['photos']['error'] != UPLOAD_ERR_NO_FILE)
		{
				$config = array(
						'file_name'		=> 'poster_'.time(),
						'upload_path' 	=> $upPath,
						'allowed_types' => "jpg|png|jpeg",
						'overwrite'		=> TRUE
				);

				$this->load->library('upload', $config);
				if(!$this->upload->do_upload('photos'))
				{
						$error = strip_tags($this->upload->display_errors())."\n";
						echo json_encode(array('status'=> 'failed', 'error' => $error));
						return false;
				}
				else
				{
						//$manager = new ImageManager(array('driver' => 'imagick'));
						$ud  	 = $this->upload->data();
						$ext 	 = str_replace('.', '', $ud['file_ext']);

						//$manager->make($ud['full_path'])->encode($ext, 50)->fit(150, 225)->save($ud['full_path']);

						$item['poster_image'] 	= $config['upload_path'].$ud['file_name'];
				}
		}

		
		if( $this->dm->add($item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function edit()
	{
		if(!$this->input->post('id')) return false;
		
		echo json_encode($this->dm->getbyid($this->input->post('id'))->row());
	}
	
	public function update()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');
		 
		if( $this->dm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function delete()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		
		if( $this->dm->delete($id) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menghapus Data'));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
	
	public function getkab()
	{
		if(!$this->input->post('id')) return false;
		
		echo json_encode($this->Ms_Kab_Model->getby(array('kab_id_prov' => $this->input->post('id')))->result());
	}
}

?>