<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Discount extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Discount_Model', 'dm', TRUE); 
		$this->load->model('Settings_Model', 'sm', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/discount/index';
		$data['title']	= 'Data Discount';		
		$data['icon']	= 'icon-watch2';
		
		$this->load->helper('form');
		$data['jns']	= $this->cm->form_type_track();	
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'discount_code', 'dt' => 0 ),
			array( 'db' => 'discount_title', 'dt' => 1 ), 
			array( 'db' => 'discount_start_date', 'dt' => 2 ), 
			array( 'db' => 'discount_id', 'dt' => 3 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{ 			
			if($row->discount_status == 1)
			{
				$label = '<span class="label label-success">Aktif</span>';
				$btn = '<a href="javascript:active(0, '.$row->discount_id.', \''.$row->discount_title.'\')" title="Non Aktifkan Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-eye-blocked"></i></a>';
			}
			else
			{
				$label = '<span class="label label-danger">Non Aktif</span>';
				$btn = '<a href="javascript:active(1, '.$row->discount_id.', \''.$row->discount_title.'\')" title="Aktifkan Data" class="btn btn-xs btn-icon btn-success"><i class="icon-eye"></i></a>';
			} 
			 
			
			$rows = array (
				$row->discount_code,
				$row->discount_title, 
				y_convert_date($row->discount_start_date).'<br>'. 
				y_convert_date($row->discount_end_date), 
				$label,  
				'<a href="javascript:edit('.$row->discount_id.')" title="Ubah Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a>'.$btn
			);
			
			$output['data'][] = $rows; 
		}
		
		echo json_encode( $output );
	}
	
	public function insert()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		$item 								= $this->input->post('inp');
		$start_date 					= strtotime($item['discount_start_date']);
		$end_date    					= strtotime($item['discount_end_date']);
		$diff   							= $end_date - $start_date; 
		$item['discount_start_date']	= y_convert_date($item['discount_start_date'],'Y-m-d');
		$item['discount_end_date']		= y_convert_date($item['discount_end_date'],'Y-m-d'); 
		$item['discount_code']				= strtoupper($item['discount_code']); 
	 
		if($this->dm->getby(array('discount_code'=>$item['discount_code']))->row()){
			echo json_encode(array('status' => 'error;', 'text' => 'Kode diskon sudah ada.'));
		}
		else {
			if( $this->dm->add($item))
				echo json_encode(array('status' => 'ok;', 'text' => ''));
			else
				echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
		}

		
		
	}
	
	public function edit()
	{
		if(!$this->input->post('id')) return false;
		$dt = $this->dm->getbyid($this->input->post('id'))->row(); 
		$dt->discount_start_date		= y_convert_date($dt->discount_start_date,'d-m-Y');
		$dt->discount_end_date		= y_convert_date($dt->discount_end_date,'d-m-Y');
		echo json_encode($dt);
	}
	
	public function update()
	{ 
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');
		 
		$start_date 					= strtotime($item['discount_start_date']);
		$end_date    					= strtotime($item['discount_end_date']);
		$diff   						= $end_date - $start_date; 
		$item['discount_start_date']		= y_convert_date($item['discount_start_date'],'Y-m-d');
		$item['discount_end_date']		= y_convert_date($item['discount_end_date'],'Y-m-d');
		
		
		if($diff<0){
			echo json_encode(array('status' => 'error;', 'text' => 'Tanggal Awal tidak boleh melewati Tanggal Akhir'));
		}
		else { 
			if( $this->dm->edit($id, $item) )
				echo json_encode(array('status' => 'ok;', 'text' => ''));
			else
				echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
		} 
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
		
		if( $this->dm->edit($id, array('discount_status' => $sts)) )
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
	
	public function setting($id)
	{
		$data['view'] 	= 'backend/discount/setting';
		$data['title']	= 'Setting';		
		$data['icon']	= 'icon-cog';
		
		$data['discount'] = $this->dm->getbyid($id)->row();
		if(empty($data['discount'])) redirect('itpanel/discount');
		else { 
			$data['component'] 	= $this->dm->getComponentActive()->result();
			$data['prodi'] 		= $this->dm->getProdiActive()->result();
			$setting 			= $this->dm->getsettingbyid($id)->result();
			
			$temp = array();
			foreach($setting as $row){
				$temp[$row->fee_id_prodi][$row->fee_id_component] = $row->fee_payment;
			}
			$data['setting'] 	= $temp;
			$this->load->view('backend/tpl', $data);
		} 
	} 
	
	public function update_setting()
	{	
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('fee')) return false; 
		
		$fee 	= $this->input->post('fee');
		$id 	= $this->input->post('id');
		$find 	= array(",", "."); 
		
		$this->dm->deletefeebyid($id);
		foreach($fee as $key => $row){
			foreach($row as $key2 => $r){
				$item['fee_id_prodi'] 		= $key;
				$item['fee_id_component'] 	= $key2;
				$item['fee_id_discount'] 	= $id; 
				$item['fee_payment'] 		= str_replace($find, "",$fee[$key][$key2]);   
				$this->dm->addFee($item);
			}
		} 
		echo json_encode(array('status' => 'ok;', 'text' => ''));
	} 
	
	public function test($id)
	{
		$data['view'] 	= 'backend/discount/discount_test';
		$data['title']	 = 'Data Jadwal Tes';		
		$data['icon']		= 'icon-calendar2';	
		$data['id']			= $id;
 
		if(!$this->dm->getbyid($id)->row()) redirect(y_url_admin().'/discount');
		
		$this->load->helper('form'); 
		
		$this->load->view('backend/tpl', $data);
	}

	public function json_test()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'test_date', 'dt' => 0 ),
			array( 'db' => 'test_id', 'dt' => 1 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		
		$id 	= $this->input->post('id'); 
 
		if(empty($param['where'])) 		$param['where'] = "WHERE (test_id_discount='".$id."')";
		else $param['where'] .= "AND (test_id_discount='".$id."')";    

		$result = $this->dm->dtquery_test($param)->result();
		$filter = $this->dm->dtfiltered_test();
		$total	= $this->dm->dtcount_test();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{ 			 
			$date 	= explode("-",$row->test_date);
			$temp 	= $date[2]."-".$date[1]."-".$date[0];
			
			$rows = array (
				$temp, 
				'<a href="javascript:delete_test('.$row->test_id.', \''.$row->test_date.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>'
			);
			
			$output['data'][] = $rows; 
		}
		
		echo json_encode( $output );
	}

	public function insert_test()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		$item 							= $this->input->post('inp'); 
		$item['test_date']	= y_convert_date($item['test_date'],'Y-m-d');
  
		if( $this->dm->add_test($item))
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data')); 
		
	}

	public function delete_test()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		
		if( $this->dm->delete_test($id) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menghapus Data'));
	}
}

?>