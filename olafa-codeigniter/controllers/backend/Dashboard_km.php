<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_km extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Dashboard_Model', '', TRUE);
		$this->load->model('Ms_School_Model', '', TRUE);
		$this->load->model('Ms_Kec_Model', '', TRUE);
		$this->load->model('Settings_Model', '', TRUE);
		$this->load->model('Periode_Model', '', TRUE);
		$this->load->model('Setting_Km_Model', 'sk', TRUE);
		$this->load->model('Common_Model', 'cm', TRUE);
		
		// if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function report($start="",$end="")
	{
		$data['view'] 			= 'backend/dashboard_km/index';
		$data['title']			= 'Dashboard KM';		
		$data['icon']			= 'icon-meter-fast';
		$data['dash_periode'] 	= '<li><a href=""><i class="icon-home2 position-left"></i> Home</a></li><li class="active">Dashboard</li>';

		// echo $start_date." ".$end_date;
		if($start=="" and $end==""){
			$start_date = date("Y-m", strtotime("-11 months")).'-01';
			$end_date = date('Y-m').'-31'; 
			$date1 = new DateTime($start_date);
			$date2 = new DateTime($end_date); 
			$diff = $date1->diff($date2); 
			$tot = 11;
		}  
		else {    
			$start_date = date("Y-m", strtotime($start)).'-01';
			$end_date = date("Y-m", strtotime($end)).'-31';  
			$date1 = new DateTime($start_date);
			$date2 = new DateTime($end_date); 
			$diff = $date1->diff($date2); 

			if($start==$end)	$tot = 0; 
			else $tot = (($diff->format('%y') * 12) + $diff->format('%m'));  
		}  
		
		if($tot>=0 and $tot<=11 and $diff->invert==0) { 
			$km_db = $this->cm->form_type_km();
			$target 		= array();
			$realisasi 	= array();
			$target2 		= array(); 
			$realisasi2	= array();
			foreach($km_db as $key=>$row){
				$target[$key]	 		= $this->sk->get_target($key,$start_date,$end_date)->result();
				$realisasi[$key] 	= $this->sk->get_realisasi($key,$start_date,$end_date)->result();
				$target2[$key]    = array();
				$realisasi2[$key] = array();
			} 
			
			$total_target 		= array(); 
			$total_realisasi 	= array();
			foreach($km_db as $key=>$row){
				$total_target[$key]    = 0;
				$total_realisasi[$key] = 0;

				foreach($target[$key] as $dts){
					$total_target[$key] = $total_target[$key]+$dts->km_target;
					$target2[$key][ date("Y-m", strtotime($dts->km_date))] = $dts->km_target; 
				}

				foreach($realisasi[$key] as $dts){
					$total_realisasi[$key] = $total_realisasi[$key]+$dts->total;
					$realisasi2[$key][ date("Y-m", strtotime($dts->date))] = $dts->total; 
				} 
			} 

			$data['start_date'] 	 		= $start_date;
			$data['end_date'] 	   		= $end_date;
			$data['km_db'] 	 					= $km_db;
			$data['month'] 				 		= $tot+1;
			$data['total_target'] 		= $total_target;
			$data['total_realisasi'] 	= $total_realisasi; 
			$data['target'] 			 		= $target2;
			$data['realisasi'] 		 		= $realisasi2;  
			$data['error']						= "";
		}
		else { 
			$data['start_date'] 	 		= $start_date;
			$data['end_date'] 	   		= $end_date;
			$data['error'] 		 		= "Maksimal range bulan adalah 12 bulan";  
		} 

		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'school_name', 'dt' => 0 ),
			array( 'db' => 'kec_name', 'dt' => 1 ),
			array( 'db' => 'school_score', 'dt' => 2 )
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
				$row->school_name.'<br><span class="text-primary-800">'.$row->school_status.'</span>',
				'Kec. '.$row->kec_name.', '.$row->kec_kab.', Prov. '.$row->kec_prov.'<br><span class="text-primary-800">NPSN: '.$row->school_npsn.'</span>',
				$row->school_score,
				'<a href="javascript:edit('.$row->school_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a>
				<a href="javascript:del('.$row->school_id.', \''.$row->school_name.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>'
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
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