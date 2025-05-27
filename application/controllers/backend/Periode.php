<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Periode extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Periode_Model', 'dm', TRUE); 
		$this->load->model('Settings_Model', 'sm', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/periode/index';
		$data['title']	= 'Data Periode';		
		$data['icon']	= 'icon-watch2';
		
		$this->load->helper('form');
		$data['jns']	= $this->cm->form_type_track();	
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'periode_code', 'dt' => 0 ),
			array( 'db' => 'periode_name', 'dt' => 1 ),
			array( 'db' => 'periode_track_type', 'dt' => 2 ),
			array( 'db' => 'periode_start_date', 'dt' => 3 ), 
			array( 'db' => 'periode_id', 'dt' => 4 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{ 			
			if($row->periode_status == 1)
			{
				$label = '<span class="label label-success">Aktif</span>';
				$btn = '<a href="javascript:active(0, '.$row->periode_id.', \''.$row->periode_name.'\')" title="Non Aktifkan Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-eye-blocked"></i></a>';
			}
			else
			{
				$label = '<span class="label label-danger">Non Aktif</span>';
				$btn = '<a href="javascript:active(1, '.$row->periode_id.', \''.$row->periode_name.'\')" title="Aktifkan Data" class="btn btn-xs btn-icon btn-success"><i class="icon-eye"></i></a>';
			}
			
			if($row->total == 0) 
				$setting = '<span class="label label-danger">FALSE</span>';  
			else  
				$setting = '<span class="label label-success">TRUE</span>'; 
 
			$temp  = array(); 
			if($row->exam!=""){ 
				$tests = explode(",",$row->exam); 
				foreach($tests as $test){
					$date 	= explode("-",$test);
					$temp[] = $date[2]."-".$date[1]."-".$date[0];
				}
			}
			
			$rows = array (
				$row->periode_code,
				$row->periode_name,
				ucwords($row->periode_track_type), 
				y_convert_date($row->periode_start_date).'<br>'. 
				y_convert_date($row->periode_end_date), 
				$label, 
				implode("<br>",$temp), 
				$setting,
				'<a href="itpanel/periode/setting/'.$row->periode_id.'" title="Setting Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-cog"></i></a> <a href="itpanel/periode/test/'.$row->periode_id.'" title="Jadwal Tes" class="btn btn-xs btn-icon btn bg-grey"><i class="icon-calendar2"></i></a> '.$btn
			);
			
			$output['data'][] = $rows; 
		}
		
		echo json_encode( $output );
	}
	
	public function insert()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		$item 							= $this->input->post('inp');
		$start_date 					= strtotime($item['periode_start_date']);
		$end_date    					= strtotime($item['periode_end_date']);
		$diff   						= $end_date - $start_date; 
		$item['periode_start_date']		= y_convert_date($item['periode_start_date'],'Y-m-d');
		$item['periode_end_date']		= y_convert_date($item['periode_end_date'],'Y-m-d');
 
		$format = $this->sm->getvalue('format_code');

		if($diff<0){
			echo json_encode(array('status' => 'error;', 'text' => 'Tanggal Awal tidak boleh melewati Tanggal Akhir'));
		}
		else {
			$existing = $this->dm->getLastData($format)->row();
			if($existing){
				$split = intval(substr($existing->periode_code,-2));
				$split++;
				$item['periode_code']	= 	$format.sprintf('%02d',$split); 
			}
			else $item['periode_code']	= $format.'01';
			
			$item['periode_status']	= 1; 
			
			if( $this->dm->add($item) )
				echo json_encode(array('status' => 'ok;', 'text' => ''));
			else
				echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
		}
		
	}
	
	public function edit()
	{
		if(!$this->input->post('id')) return false;
		$dt = $this->dm->getbyid($this->input->post('id'))->row(); 
		$dt->periode_start_date		= y_convert_date($dt->periode_start_date,'d-m-Y');
		$dt->periode_end_date		= y_convert_date($dt->periode_end_date,'d-m-Y');
		echo json_encode($dt);
	}
	
	public function update()
	{ 
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');
		 
		$start_date 					= strtotime($item['periode_start_date']);
		$end_date    					= strtotime($item['periode_end_date']);
		$diff   						= $end_date - $start_date; 
		$item['periode_start_date']		= y_convert_date($item['periode_start_date'],'Y-m-d');
		$item['periode_end_date']		= y_convert_date($item['periode_end_date'],'Y-m-d');
		
		
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
		
		if( $this->dm->edit($id, array('periode_status' => $sts)) )
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
		$data['view'] 	= 'backend/periode/setting';
		$data['title']	= 'Setting';		
		$data['icon']	= 'icon-cog';
		
		$data['periode'] = $this->dm->getbyid($id)->row();
		if(empty($data['periode'])) redirect('itpanel/periode');
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
				$item['fee_id_periode'] 	= $id; 
				$item['fee_payment'] 		= str_replace($find, "",$fee[$key][$key2]);   
				$this->dm->addFee($item);
			}
		} 
		echo json_encode(array('status' => 'ok;', 'text' => ''));
	} 
	
	public function test($id)
	{
		$data['view'] 	= 'backend/periode/periode_test';
		$data['title']	 = 'Data Jadwal Tes';		
		$data['icon']		= 'icon-calendar2';	
		$data['id']			= $id;
 
		if(!$this->dm->getbyid($id)->row()) redirect(y_url_admin().'/periode');
		
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
 
		if(empty($param['where'])) 		$param['where'] = "WHERE (test_id_periode='".$id."')";
		else $param['where'] .= "AND (test_id_periode='".$id."')";    

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