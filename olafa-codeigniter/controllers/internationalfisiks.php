<?php

class InternationalFisik extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		$this->load->model('InternationalFisikModel');  
    }

   function index() { 
		$data['total'] 	= $this->InternationalFisikModel->getall()->num_rows();  
		$data['view'] 	= 'internationalfisik/internationalfisik'; 
		$data['site'] 	= 'jurnal international fisik'; 
		$this->load->view('main',$data); 
    }   

	public function ajaxFisik(){ 
		$dt 	= $this->InternationalFisikModel->getbyquery()->result_array(); 
		 
		$data = array(
									array('Name'=>'parvez', 'Empid'=>11, 'Salary'=>101),
									array('Name'=>'alam', 'Empid'=>1, 'Salary'=>102),
									array('Name'=>'phpflow', 'Empid'=>21, 'Salary'=>103)							);
		 
			$results = array(
					"sEcho" => 1,
				"iTotalRecords" => count($dt),
				"iTotalDisplayRecords" => count($dt),
				  "aaData"=>$dt);
		/*while($row = $result->fetch_array(MYSQLI_ASSOC)){
		  $results["data"][] = $row ;
		}*/

		echo json_encode($results);

	}	
}

?>