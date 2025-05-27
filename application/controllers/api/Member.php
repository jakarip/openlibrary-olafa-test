<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Member extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config); 
		$this->load->model('ApiMemberModel', 'amm', TRUE); 
    }
  
   
   function login_post() { 
		$public_key = $this->input->post('public_key'); 
		 
		  
		if($public_key!='OPENLIB14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		
		$username = strtolower($this->input->post('username'));
		$password = $this->input->post('password');
			
		$sso 	  = SSO($username,$password);  
		if($sso=='false'){
			$dt = $this->amm->getbymember($username,md5($password))->row();
		}
		else {  
			$dt = $this->amm->checkuser($username)->row(); 
		}
		
		if($dt){ 
			print_r($dt); 
		} 
		
		 $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		 
		
		
}
	
	  
}

?>