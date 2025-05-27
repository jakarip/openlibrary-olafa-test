<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class ReturnBook extends REST_Controller {
  
    function __construct($config = 'rest') {
        parent::__construct($config); 
		$this->load->model('Usermodel', 'um', TRUE);
    }
  
   
     function index_post() { 
		$stockid = $this->input->post('stockid');
		
		$row = $this->um->getRent($stockid)->row();
		
		$data['return_date'] = date('Y-m-d');
		$data['status'] = '2';
		 
		$this->um->editDb('batik.rent', $data, 'id', $row->id);
		 
		$data2['status'] = '1'; 
		$this->um->editDb('batik.knowledge_stock', $data2, 'id', $stockid);
		 
		 
		$this->response(array('status' => "success"), 200);
    }
	
	  
}

?>