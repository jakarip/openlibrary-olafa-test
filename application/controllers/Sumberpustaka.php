<?php

class Sumberpustaka extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		if(!$this->session->userdata('login')) redirect('');
    }
	
	function index() { 
		$data['menu'] 	= 'sumberpustaka/sumberpustaka'; 
		$this->load->view('theme', $data);
    }  
}

?>