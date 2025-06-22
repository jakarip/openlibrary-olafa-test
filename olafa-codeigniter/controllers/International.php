<?php

class International extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		$this->load->model('InternationalFisikModel'); 
		if(!$this->session->userdata('login')) redirect(''); 
    }

   function index() { 
		$data['total'] 	= $this->InternationalFisikModel->getall()->num_rows(); 
		$data['dikti'] 	= $this->InternationalFisikModel->getbyquery('','')->result();
		$data['view'] 	= 'internationalfisik/internationalfisik'; 
		$data['site'] 	= 'jurnal international fisik'; 
		$this->load->view('main',$data); 
    }    
}

?>