<?php

class Sumberproceeding extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		if(!$this->session->userdata('login')) redirect('');
    }
	
	function index() { 
		$data['menu'] 	= 'sumberproceeding/sumberproceeding'; 
		$this->load->view('theme', $data);
    }  
}

?>