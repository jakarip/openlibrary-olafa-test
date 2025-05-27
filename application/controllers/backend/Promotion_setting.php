<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Promotion_setting extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Promotion_Setting_Model', 'dm', TRUE); 
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/promotion_setting/index';
		$data['title']	= 'Setting Promosi';		
		$data['icon']	= 'icon-gear';
		$data['promo']		= $this->cm->form_promotion_setting();
		
		$this->load->helper('form'); 
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array( 
			array( 'db' => 'set_status', 'dt' => 0 ),
			array( 'db' => 'set_cost', 'dt' => 1 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		$promo		= $this->cm->form_promotion_setting();
		foreach($result as $row)
		{
			if($row->set_active == 1)
			{
				$label = '<span class="label label-success">Aktif</span>';
				$btn = '<a href="javascript:active(0, '.$row->set_id.', \''.$row->set_status.'\', \''.$promo[$row->set_status].'\')" title="Non Aktifkan Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-eye-blocked"></i></a>';
			}
			else
			{
				$label = '<span class="label label-danger">Non Aktif</span>';
				$btn = '<a href="javascript:active(1, '.$row->set_id.', \''.$row->set_status.'\', \''.$promo[$row->set_status].'\')" title="Aktifkan Data" class="btn btn-xs btn-icon btn-success"><i class="icon-eye"></i></a>';
			}
			
			$rows = array (
				$promo[$row->set_status],
				'Rp. '.number_format($row->set_cost, 0, ',', '.'),
				$label,
				'<a href="javascript:edit('.$row->set_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a> '.$btn
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
		$find 	= array(",", "."); 
		$item['set_cost'] 		= str_replace($find, "",$item['set_cost']);

		$where = "where set_status='".$item['set_status']."' and set_active='1'";
		$check = $this->dm->getbyquery2($where)->row();
		if(!$check){ 
			if( $this->dm->add($item) )
				echo json_encode(array('status' => 'ok;', 'text' => ''));
			else
				echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
		}
		else echo json_encode(array('status' => 'error;', 'text' => 'Data Aktif dengan "Registrasi Status" yang dipilih sudah ada. Si	lahkan memilih "Registrasi Status" yang lain'));
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

		$find 	= array(",", "."); 
		$item['set_cost'] 		= str_replace($find, "",$item['set_cost']);
		
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
		$set_status = $this->input->post('set_status');

		if($sts==1) {
			$where = "where set_status='".$set_status."' and set_active='1'";
			$check = $this->dm->getbyquery2($where)->row();
			if(!$check){  
				if( $this->dm->edit($id, array('set_active' => $sts)) )
					echo json_encode(array('status' => 'ok;', 'text' => ''));
				else
					echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
			}
			else echo json_encode(array('status' => 'error;', 'text' => 'Tidak dapat mengaktifkan data tersebut, karena sudah ada Data Aktif dengan "Registrasi Status" yang sama'));
		}
		else {
			if( $this->dm->edit($id, array('set_active' => $sts)) )
				echo json_encode(array('status' => 'ok;', 'text' => ''));
			else
				echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
		}
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
}

?>