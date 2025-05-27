<?php

class Sumberjurnal extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		//if(!$this->session->userdata('login')) redirect('');
    }
	
	function index() { 
		$data['menu'] 	= 'sumberjurnal/sumberjurnal'; 
		$this->load->view('theme', $data);
    }  
}

?>