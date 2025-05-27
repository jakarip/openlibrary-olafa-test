<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Api_fif extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config); 
    }
 
   
     function index_post() {
		
			$this->response(array('status' => 'yes'), 200);   
    }
	
	 // function index_post() {
		
		// $session 	= $this->input->post('session');
		// echo $session();
		
		// // $password 	= md5($this->input->post('password'));
		// // if ($this->fif->checkpegawaifif($username,$password)->result()){
			// // $this->response(array('status' => 'yes'), 200);  
		// // }
		// // else $this->response(array('status' => 'no'), 200); 
		
		
    // }
}

?>