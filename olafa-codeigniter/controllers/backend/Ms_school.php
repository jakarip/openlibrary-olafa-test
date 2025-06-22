<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ms_School extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Ms_School_Model', 'dm', TRUE);
		$this->load->model('Ms_Kec_Model', '', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/ms_school/index';
		$data['title']	= 'Master Data Sekolah';		
		$data['icon']	= 'icon-city';
		
		$this->load->helper('form');
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'school_npsn', 'dt' => 0 ),
			array( 'db' => 'school_name', 'dt' => 1 ),
			array( 'db' => 'kec_name', 'dt' => 2 ),
			array( 'db' => 'school_score', 'dt' => 3 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			$rows = array (
				$row->school_npsn,
				$row->school_name.'<br><span class="text-primary-800">'.$row->school_status.'</span>',
				'Kec. '.$row->kec_name.', '.$row->kec_kab.', Prov. '.$row->kec_prov,
				$row->school_score,
				'<a href="javascript:edit('.$row->school_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a>
				<a href="javascript:del('.$row->school_id.', \''.$row->school_name.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>'
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