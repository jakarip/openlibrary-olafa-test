<?php

class SumberPustaka extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
    }

    function index() {
		$data['view'] 	= 'sumberpustaka/sumberpustaka';
		$data['site'] 	= 'sumber pustaka'; 		
		$this->load->view('main',$data);
    }
}

?>