<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Guest extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Guest_Model', 'dm', TRUE);
		
		if (!$this->session->userdata('login') or $this->session->userdata('user')['membertype']!='1') {
			header("Location: /");
			die();
		}
	}
	
	public function index()
	{
		$data['view'] 	= 'frontend/guest/index';
		$data['title']	= 'Buku Tamu';		
		$data['icon']	= 'icon-address-book';
		
		$this->load->helper('form');
		
		$this->load->view('frontend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'guestbook_fullname', 'dt' => 0 ),
			array( 'db' => 'guestbook_email', 'dt' => 1 ),
			array( 'db' => 'guestbook_mobile_phone', 'dt' => 2 ), 
			array( 'db' => 'guestbook_institution', 'dt' => 3 ),
			array( 'db' => 'guestbook_date', 'dt' => 4 )
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
				$row->guestbook_fullname,
				$row->guestbook_email, 
				$row->guestbook_mobile_phone, 
				$row->guestbook_institution, 
				$row->guestbook_date, 
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