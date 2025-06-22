<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ms_Prodi extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Ms_Prodi_Model', 'dm', TRUE);
		$this->load->model('Ms_Faculty_Model', '', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/ms_prodi/index';
		$data['title']	= 'Master Data Program Studi (Prodi)';		
		$data['icon']	= 'icon-location4';
		
		$this->load->helper('form');
		
		$faculty = $this->Ms_Faculty_Model->getby(array('faculty_status' => '1'))->result();
		$data['faculty'] = array('' => 'Pilih Fakultas');
		foreach($faculty as $fac)
			$data['faculty'][$fac->faculty_id.'__'.$fac->faculty_name] = $fac->faculty_name;
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'prodi_faculty', 'dt' => 0 ),
			array( 'db' => 'prodi_name', 'dt' => 1 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			if($row->prodi_status == 1)
			{
				$label = '<span class="label label-success">Aktif</span>';
				$btn = '<a href="javascript:active(0, '.$row->prodi_id.', \''.$row->prodi_name.'\')" title="Non Aktifkan Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-eye-blocked"></i></a>';
			}
			else
			{
				$label = '<span class="label label-danger">Non Aktif</span>';
				$btn = '<a href="javascript:active(1, '.$row->prodi_id.', \''.$row->prodi_name.'\')" title="Aktifkan Data" class="btn btn-xs btn-icon btn-success"><i class="icon-eye"></i></a>';
			}
			
			$rows = array (
				$row->prodi_faculty,
				$row->prodi_name,
				$label,
				'<a href="javascript:edit('.$row->prodi_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a> '.$btn
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
		$faculty = explode('__', $this->input->post('faculty'));
		$item['prodi_faculty_id'] = $faculty[0];
		$item['prodi_faculty'] = $faculty[1];
		
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
		
		$faculty = explode('__', $this->input->post('faculty'));
		$item['prodi_faculty_id'] = $faculty[0];
		$item['prodi_faculty'] = $faculty[1];
		
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
		
		if( $this->dm->edit($id, array('prodi_status' => $sts)) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
}

?>