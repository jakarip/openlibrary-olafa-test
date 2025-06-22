<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ms_Location extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Ms_Location_Model', 'dm', TRUE);
		$this->load->model('Ms_Kec_Model', '', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/ms_location/index';
		$data['title']	= 'Master Data Lokasi Seleksi';		
		$data['icon']	= 'icon-direction';
		
		$this->load->helper('form');
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'location_name', 'dt' => 0 ),
			array( 'db' => 'kec_name', 'dt' => 1 ),
			array( 'db' => 'location_score', 'dt' => 2 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			if($row->location_status == 1)
			{
				$label = '<span class="label label-success">Aktif</span>';
				$btn = '<a href="javascript:active(0, '.$row->location_id.', \''.$row->location_name.'\')" title="Non Aktifkan Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-eye-blocked"></i></a>';
			}
			else
			{
				$label = '<span class="label label-danger">Non Aktif</span>';
				$btn = '<a href="javascript:active(1, '.$row->location_id.', \''.$row->location_name.'\')" title="Aktifkan Data" class="btn btn-xs btn-icon btn-success"><i class="icon-eye"></i></a>';
			}
			
			$rows = array (
				$row->location_name,
				$row->location_address.'<br><span class="text-primary-800"> '.'Kec. '.$row->kec_name.', '.$row->kec_kab.', Prov. '.$row->kec_prov.'</span>',
				$label,
				'<a href="javascript:edit('.$row->location_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a>'.$btn,
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
	
	public function active()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		$sts = $this->input->post('sts');
		
		if( $this->dm->edit($id, array('location_status' => $sts)) )
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