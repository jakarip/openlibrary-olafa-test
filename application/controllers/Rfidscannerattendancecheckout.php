<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Rfidscannerattendancecheckout extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config);
		$this->load->model("RfidModel","rm");
		$this->load->model('Usermodel', 'um', TRUE);
    } 
   
    function index_get() {
		
		$rfid 				= $this->input->get('rfid');
		$status 			= "success";
		
		if($rfid!=""){ 
			$case 	= $this->rm->GetRfidNotSameWithIgracias()->result(); 

			foreach($case as $ca){
				$case_member[$ca->rfid] = $ca->username;
			}

			$dt = $this->rm->GetRfidScanner($rfid)->row(); 
			if ($dt){ 
				if($dt->member_type_id!='19' || $dt->master_data_user='A0706ED9' || $dt->master_data_user='205A58D9'){   
					$temp = array(
						"member_id" => $dt->id,
						"master_data_course" => $dt->master_data_course,
						"item_location_id" => '9',
						"attended_at" => date('Y-m-d H:i:s'),
						"created_at" => date('Y-m-d H:i:s'),
						"created_by" => "checkout", 
						"updated_by" => "checkout",
						"updated_at" => date('Y-m-d H:i:s')
					); 
					$this->rm->addAttendanceCheckout($temp);
					$this->response(array('status' => 'success', 'username'=>$dt->master_data_user, 'nim_nip'=>$dt->master_data_number), 200); 
				} 
				else $this->response(array('status' => 'failed'), 502);  
			} 
			else {
				if (array_key_exists($rfid, $case_member)){
					$member = $this->rm->GetMemberScanner($case_member[$rfid])->row(); 
					if ($member){  
						if($member->member_type_id!='19' || $dt->master_data_user='A0706ED9' || $dt->master_data_user='205A58D9'){    
							$temp = array(
								"member_id" => $member->id,
								"master_data_course" => $member->master_data_course,
								"item_location_id" => '9',
								"attended_at" => date('Y-m-d H:i:s'),
								"created_at" => date('Y-m-d H:i:s'),
								"created_by" => "checkout", 
								"updated_by" => "checkout",
								"updated_at" => date('Y-m-d H:i:s')
							);
							$this->rm->addAttendanceCheckout($temp); 
							$this->response(array('status' => 'success', 'username'=>$member->master_data_user, 'nim_nip'=>$member->master_data_number), 200);
						} 
						else $this->response(array('status' => 'failed'), 502);  
						
					} else $this->response(array('status' => 'failed'), 502); 
				}
				else $this->response(array('status' => 'failed'), 502); 
			} 
		}
		else $this->response(array('status' => 'failed'), 502);
    }
} 

?> 