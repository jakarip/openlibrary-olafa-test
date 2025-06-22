<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_km extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Setting_Km_Model', 'dm', TRUE); 
		$this->load->model('Settings_Model', 'sm', TRUE);
		$this->load->model('Common_Model', 'cm', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/setting_km/index';
		$data['title']	= 'Data KM';		
		$data['icon']	= 'icon-watch2';
		
		$this->load->helper('form');

		$km_db = $this->cm->form_type_km();
		$km = array('' => 'Pilih Jenis KM');
		foreach ($km_db as $key=>$row)
				$km[$key] = $row;
		$data['km'] = $km; 
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'km_type', 'dt' => 0 ),
			array( 'db' => 'km_date', 'dt' => 1 ), 
			array( 'db' => 'km_target', 'dt' => 2 ), 
			array( 'db' => 'km_id', 'dt' => 3 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);


		$km_db = $this->cm->form_type_km();
		foreach($result as $row)
		{ 			  
	 
			$temp  = array();  
			
			$rows = array (
				$km_db[$row->km_type],
				date('M Y',strtotime($row->km_date)), 
				$row->km_target,  
				'<a href="javascript:deletes('.$row->km_id.', \''.$row->km_type.'/'.$row->km_date.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>'
			);
			
			$output['data'][] = $rows; 
		}
		
		echo json_encode( $output );
	}
	
	public function insert()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		$km_db 							= $this->cm->form_type_km();
		$item 							= $this->input->post('inp'); 
		$item['km_date']		= y_convert_date('01'.'-'.$item['km_date'],'Y-m-d'); 
	
		if(!$this->dm->getby(array("km_date"=>$item['km_date'],"km_type"=>$item['km_type']))->row()){ 
			if( $this->dm->add($item) )
				echo json_encode(array('status' => 'ok;', 'text' => ''));
			else
				echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data')); 
		}
		else echo json_encode(array('status' => 'error;', 'text' => "Data KM dengan Jenis KM ".$km_db[$item['km_type']].' Bulan '.date('M Y',strtotime($item['km_date']))." sudah ada.")); 
		
	}
	
	public function edit()
	{
		if(!$this->input->post('id')) return false;
		$dt = $this->dm->getbyid($this->input->post('id'))->row(); 
		$dt->km_start_date		= y_convert_date($dt->km_start_date,'d-m-Y');
		$dt->km_end_date		= y_convert_date($dt->km_end_date,'d-m-Y');
		echo json_encode($dt);
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
		
		if( $this->dm->edit($id, array('km_status' => $sts)) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
	 
}

?>