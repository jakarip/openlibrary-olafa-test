<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Km extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config);
		$this->load->model("Km_Model","km"); 
    }
 
   
     function index_post() {
		
		$dataPerPage = 100; 
		
		$private_key 	= $this->input->post('private_key');
		$tit1 			= $this->input->post('tit1');
		$limit 			= $this->input->post('limit');
		$page 			= $this->input->post('page');
		
		if($private_key=='brin5420c7fed9fe74569f580e0f924f9b734574a03b'){
			
			$tit = isset($tit1) ? $tit1 : '';
			$limit = isset($limit) ? $limit : '';
			$sql_tit ='';
				if (isset($page)){
					$noPage = $page;
				} else {
					$noPage=1; 
				}
			$offset = ($noPage - 1) * $limit;	
					
			$dataPerPage = $offset + $limit;

			if (!empty($tit)) {
				$sql_tit = 'where title LIKE \'%'.$tit.'%\'';
			}

			$sql_limit = '';
			if (!empty($limit)) {
				$sql_limit = ' LIMIT 0,'.$limit;
			}
			
			$result = $this->km->getAll($sql_tit)->num_rows();

 
			$data['jumlah_data'] = $result;
			$data['jumlah_page'] = ceil($result/$limit);

			$result = $this->km->getAllLimit($sql_tit,$offset,$dataPerPage)->result_array(); 
					
		 
			$temp = array();
			foreach($result as $row) {  
				$row['url'] = "https://rin.bppt.go.id/home/catalog/id/3".$row['id']."/slug/".$row['id'].".html";
		 
				$data['data'][]=$row;	
			} 
			 
			
			$this->response(array('status' => "true",'data'=>$data), 200); 
		}
		else $this->response(array('status' => "false",'message'=>'Tidak ada hak akses'), 200); 
  }
}

?>