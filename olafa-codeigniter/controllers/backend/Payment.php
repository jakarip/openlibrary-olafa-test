<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Student_Model', 'dm', TRUE);
		$this->load->model('Participant_Registration_Model', 'sreg', TRUE);
		$this->load->model('Ms_Course_Model', '', TRUE); 
		$this->load->model('Ms_Prodi_Model', '', TRUE);
		$this->load->library('PHPExcel');
		$this->load->model('Common_Model', 'cm', TRUE);
		$this->load->model('Periode_Model', 'pm', TRUE);
		$this->load->model('Pin_Model', 'pin', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/payment/index';
		$data['title']	= 'Data Pembayaran';		
		$data['icon']	= 'icon-cash4';
		$data['periode'] = $this->pm->getAllOrderByDate()->result();
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;

		$columns = array( 
			array( 'db' => 'sreg_id', 'dt' => 0 ),
			array( 'db' => 'sreg_payment_register_status', 'dt' => 1 ),
			array( 'db' => 'pin_transaction_number', 'dt' => 2 ),
			array( 'db' => 'periode_code', 'dt' => 3 ),
			array( 'db' => 'par_fullname', 'dt' => 4 ),
			array( 'db' => 'sreg_status_pass', 'dt' =>5 ),
			array( 'db' => 'sreg_payment_date', 'dt' => 6 )
		); 
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();

		
		$periode 	= $this->input->post('periode');    
 
		if ($periode!=0){
			if(empty($param['where'])) $param['where'] = "WHERE (pin_id_periode='".$periode."') and pin_type='Online' and (pin_booking_by != '' OR pin_booking_by != '0' OR pin_booking_by is not NULL)";
			else $param['where'] .= "AND (pin_id_periode='".$periode."') and pin_type='Online' and (pin_booking_by != '' OR pin_booking_by != '0' OR pin_booking_by is not NULL)";
		}
		else {
			if(empty($param['where'])) $param['where'] = "WHERE pin_type='Online' and (pin_booking_by != '' OR pin_booking_by != '0' OR pin_booking_by is not NULL)";
			else $param['where'] .= "AND pin_type='Online' and (pin_booking_by != '' OR pin_booking_by != '0' OR pin_booking_by is not NULL)";
		}
	  
		$result = $this->sreg->dtquery($param)->result();
		$filter = $this->sreg->dtfiltered();
		$total	= $this->sreg->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		$i=0;
		foreach($result as $row)
		{ 
			$reg = "";
			if($row->sreg_active == 'Y'){  
				$reg 				= "";
				$label 			= '<span class="label label-success">Sudah dibayar</span>';
				$reg_status = '<span class="label label-success">registered</span>';
				if($row->sreg_payment_register_status==""){   
					$reg_status = '<span class="label label-warning">not register</span>';
				}
				else if($row->sreg_payment_register_status=='registered'){ 
					$reg_status = '<span class="label label-success">registered</span>';
				}
			}
			else { 
				$label 	= '<span class="label label-warning">Belum dibayar</span>';

				if($row->sreg_payment_register_status==""){  
					$reg 		= '<input type="checkbox" class="chk-location" value="'.$row->sreg_id.'-'.$row->pin_transaction_number.'-'.$row->sreg_id_pin.'">';
					$reg_status = '<span class="label label-warning">not register</span>';
				}
				else if($row->sreg_payment_register_status=='registered'){ 
					$reg_status = '<span class="label label-success">registered</span>';
				}
			}  

			$rows = array ( 
				$reg,
				$reg_status,
				$row->pin_transaction_number,
				$row->periode_name.' / '.$row->periode_track_type.' ('.y_convert_date($row->periode_start_date,'d/m/Y').' - '.y_convert_date($row->periode_end_date,'d/m/Y').')',
				$row->par_fullname,
				$label,
				y_convert_date($row->sreg_payment_date,'d/m/Y'), 
				'<button title="Reset '.$row->sreg_id_pin.'" class="btn btn-xs btn-icon btn-primary" onClick="log(\''.$row->sreg_id_pin.'\')"><i class="icon-file-text2"></i></a>'
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	} 

	public function generate()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;

		$this->load->library('parser');  
		
		$id  = $this->input->post('id');   
		
		$dbs 	= explode(",",$id); 
		$string = "";
		$i			= 0;
		if(!empty($dbs))
		{  	
			foreach($dbs as $row){ 
				$temp 	= explode('-',$row);
				$pin 		= $this->pin->getby(array('pin_id'=>$temp[2]))->row();
				$date 	= date('Y-m-d',  strtotime('+1 month')); 
				$bill 	= array(
						'name' => $temp[1],
						'bill_key_value' => $temp[1]
				);
				$bill['bill_upload_list'][] = array( 
						'account_code' 					=> 'BCA',
						'bill_component_name' 	=> 'Biaya Pendaftaran',
						'expiry_date' 					=> $date.'T00:00:00Z',
						'due_date' 							=> $date.'T00:00:00Z', 
						'Amount' 								=> floatval($pin->pin_price)
				);    
				$json = json_encode($bill); 
				$result = json_decode($this->curl('bill/checkout',$json),true);      
				$i++;
				if($result['status_code']=='201'){  
					$dt['sreg_payment_register_status'] 	= 'registered';
					$dt['sreg_payment_bill_id'] 					= $result['data']['bill_id'];
					$dt['sreg_payment_bill_component_id'] = $result['data']['bill_component_list'][0];
					$this->sreg->edit($temp[0], $dt);    
					$string .= "$i. $temp[1] : Berhasil  \n";
				}
				else { 
					$string .= "$i. $temp[1] : Gagal  \n";
				}				 

			} 
			echo json_encode(array('status' => 'ok;', 'text' => $string));
		}
		else echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}  

	function curl($url,$data,$method=""){ 
		$date = date('Ymd'); 
		$dt     =  hash( 'sha256',hash( 'sha256','akatel_idn@2019').$date);    
		$ch = curl_init('https://10042:'.$dt.'@testclient.infradigital.io/'.$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data))
		); 
		$result = curl_exec($ch); 
		curl_close($ch);
		return $result;
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
}

?>