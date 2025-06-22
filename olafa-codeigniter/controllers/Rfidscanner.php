<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Rfidscanner extends REST_Controller {
 
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
				if($dt->member_type_id!='19'){   
					$this->response(array('status' => 'success', 'username'=>$dt->master_data_user, 'nim_nip'=>$dt->master_data_number), 200); 
				} 
				else $this->response(array('status' => 'failed'), 502);  
			} 
			else {
				if (array_key_exists($rfid, $case_member)){
					$member = $this->rm->GetMemberScanner($case_member[$rfid])->row(); 
					if ($member){  
						if($member->member_type_id!='19'){    
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

	function webcam_get() {
		
		$token 				= $this->input->get('rfid');
		$status 			= "success"; 

		if (strpos($token,  'mytelumobile') !== false) {

			$response = KTMDigitalMyTelU($token);
			
			$dt = json_decode($response,true);  

			if($dt['success']==true){
				$this->response(array('status' => 'success', 'username'=>$dt['data']['username'], 'nim_nip'=>$dt['data']['number_id']), 200); 
			}
			else $this->response(array('status' => 'failed'), 200); 
		}
		else {

			$this->load->helper('jwt'); 
		

			$jwt = new JWT();

			$payload = $jwt->decode($token, 'qrcode'); 
 
		
			$date = date('Y-m-d H:i:s');
			$timenow = strtotime($date);
			$timetoken = strtotime($payload->expired); 
			
		
			//pake qrcode
			if($timenow>$timetoken)  { 
				$this->response(array('status' => 'failed'), 200); 
			}
			else { 
				$dt = $this->rm->GetRfid('',$payload->username)->row();  
				if ($dt){    

					$this->response(array('status' => 'success', 'username'=>$dt->master_data_user, 'nim_nip'=>$dt->master_data_number), 200); 
				}  
				else $this->response(array('status' => 'failed'), 200); 
			} 
		}
    }
} 

?> 