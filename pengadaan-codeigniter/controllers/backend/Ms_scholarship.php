<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ms_Scholarship extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Ms_Prodi_Model', 'pm', TRUE);  
		$this->load->model('Ms_Scholarship_Model', 'dm', TRUE); 
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/ms_scholarship/index';
		$data['title']	= 'Master Data Beasiswa';		
		$data['icon']	= 'icon-price-tag';
		$data['prodi']  = $this->pm->getby(array('prodi_status' => 1))->result(); 
		$this->load->helper('form');
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'scholarship_name', 'dt' => 0 ),
			array( 'db' => 'scholarship_status', 'dt' => 1 ),
			array( 'db' => 'scholarship_id', 'dt' => 2 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			if($row->scholarship_status == 1)
			{
				$label = '<span class="label label-success">Aktif</span>';
				$btn = '<a href="javascript:active(0, '.$row->scholarship_id.', \''.$row->scholarship_name.'\')" title="Non Aktifkan Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-eye-blocked"></i></a>';
			}
			else
			{
				$label = '<span class="label label-danger">Non Aktif</span>';
				$btn = '<a href="javascript:active(1, '.$row->scholarship_id.', \''.$row->scholarship_name.'\')" title="Aktifkan Data" class="btn btn-xs btn-icon btn-success"><i class="icon-eye"></i></a>';
			}
			
			$rows = array (
				$row->scholarship_name, 
				$label,
				'<a href="javascript:edit('.$row->scholarship_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a>'.$btn,
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	public function insert()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item 		= $this->input->post('inp');
		$ps_amount 	= $this->input->post('ps_amount');
		$id = $this->dm->add($item);
		if($id){ 
			
			$find 	= array(",", "."); 
			foreach($ps_amount as $prodi=> $row){
				$item2['ps_id_scholarship'] = $id;
				$item2['ps_id_prodi'] 		= $prodi;
				$item2['ps_amount'] 		= str_replace($find, "",$row);
				
				$this->dm->addprodischolarship($item2);
			}
			
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		
		}else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function edit()
	{
		if(!$this->input->post('id')) return false;
		$data['scholar'] 		= $this->dm->getbyid($this->input->post('id'))->row();  
		$data['prodi_scholar'] 	= $this->dm->getbyidprodischolarship($this->input->post('id'))->result(); 
		echo json_encode($data);
	}
	
	public function update()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$ps_amount 	= $this->input->post('ps_amount');
		$id   = $this->input->post('id');
		
		if( $this->dm->edit($id, $item) ){
			$this->dm->deleteprodischolarshipbyid($id);
			$find 	= array(",", "."); 
			foreach($ps_amount as $prodi=> $row){
				$item2['ps_id_scholarship'] = $id;
				$item2['ps_id_prodi'] 		= $prodi;
				$item2['ps_amount'] 		= str_replace($find, "",$row);
				
				$this->dm->addprodischolarship($item2);
			}
			
			
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		}else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	} 
	
	public function active()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		$sts = $this->input->post('sts');
		
		if( $this->dm->edit($id, array('scholarship_status' => $sts)) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
	
	public function get_address()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('searchTerm')) return false;
		
		$s = $this->input->post('searchTerm');
		$dbs = $this->Ms_Kec_Model->getaddress($s)->result();
		
		$result = array();
		foreach($dbs as $db)
			$result[] = array('id' => $db->kec_id,
							  'text' => 'Kec. '.$db->kec_name.', '.$db->kec_kab.', Prov. '.$db->kec_prov);
		
		echo json_encode($result);
	}
}

?>