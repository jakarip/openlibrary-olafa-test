<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ms_Component extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Ms_Component_Model', 'dm', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/ms_component/index';
		$data['title']	= 'Master Data Komponen Pembiayaan';		
		$data['icon']	= 'icon-puzzle';
		
		$this->load->helper('form');
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'component_status', 'dt' => 0 ),
			array( 'db' => 'component_name', 'dt' => 1 ),
			array( 'db' => 'component_bank', 'dt' => 2 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			if($row->component_status == 1)
				$label = '<span class="label label-success">Aktif</span>';
			else
				$label = '<span class="label label-danger">Non Aktif</span>';
				
			if($row->component_custom == 1)
				$label_custom = '<span class="label label-primary">Ya</span>';
			else
				$label_custom = '<span class="label label-warning">Tidak</span>';
				
			if($row->component_status == 1)
				$btn = '<a href="javascript:active('.$row->component_id.', \''.$row->component_name.'\', 0)" title="Non Aktifkan Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-database-remove"></i></a>';
			else
				$btn = '<a href="javascript:active('.$row->component_id.', \''.$row->component_name.'\', 1)" title="Aktifkan Data" class="btn btn-xs btn-icon btn-success"><i class="icon-database-check"></i></a>';
				
			$rows = array (
				$label,
				$row->component_name,
				$row->component_bank,
				$label_custom,
				'<a href="javascript:edit('.$row->component_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a> '.$btn
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
		$item['component_status'] = '1';		
		
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
		
		if(!isset($item['component_custom']))
			$item['component_custom'] = '0';
		
		if( $this->dm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function active()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		$mode = $this->input->post('mode');
		
		if( $this->dm->edit($id, array('component_status' => $mode)) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
}

?>