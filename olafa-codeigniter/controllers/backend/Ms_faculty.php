<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ms_Faculty extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Ms_Faculty_Model', 'dm', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/ms_faculty/index';
		$data['title']	= 'Master Data Fakultas';		
		$data['icon']	= 'icon-location4';
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'faculty_name', 'dt' => 0 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			if($row->faculty_status == 1)
			{
				$label = '<span class="label label-success">Aktif</span>';
				$btn = '<a href="javascript:active(0, '.$row->faculty_id.', \''.$row->faculty_name.'\')" title="Non Aktifkan Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-eye-blocked"></i></a>';
			}
			else
			{
				$label = '<span class="label label-danger">Non Aktif</span>';
				$btn = '<a href="javascript:active(1, '.$row->faculty_id.', \''.$row->faculty_name.'\')" title="Aktifkan Data" class="btn btn-xs btn-icon btn-success"><i class="icon-eye"></i></a>';
			}
			
			$rows = array (
				$row->faculty_name,
				$label,
				'<a href="javascript:edit('.$row->faculty_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a> '.$btn
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
		{
			$this->dm->update_view($id);
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		}
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
	
	public function active()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		$sts = $this->input->post('sts');
		
		if( $this->dm->edit($id, array('faculty_status' => $sts)) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
}

?>