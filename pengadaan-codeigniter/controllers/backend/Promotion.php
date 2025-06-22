<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Promotion extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Promotion_Model', 'dm', TRUE); 
		
		if (!$this->session->userdata('login') and !$this->session->userdata('referral_login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/promotion/index';
		$data['title']	= 'Text Promosi';		
		$data['icon']	= 'icon-percent';
		
		$this->load->helper('form'); 
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'promotion_date', 'dt' => 0 ),
			array( 'db' => 'promotion_text', 'dt' => 1 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{

			$action = "";
			if($this->session->userdata('usergroup')=='admin'){
				$action = '<a href="javascript:edit('.$row->promotion_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a><a href="javascript:del('.$row->promotion_id.', \''.$row->promotion_date.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>';
			}

			$rows = array (
				date_format(date_create($row->promotion_date),'d F Y'),
				'<span id="texter'.$row->promotion_id.'">'.$row->promotion_text.'</span>', 
				'<a href="javascript:copies('.$row->promotion_id.')" title="Copy to Clipboard" class="btn btn-xs btn-icon btn-success"><i class="icon-copy4"></i></a>'.$action
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
		$item['promotion_date'] = date('Y-m-d');
		
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