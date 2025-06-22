
<?php

class Prosidingseminar extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		$this->load->model('Prosidingseminarmodel');  
		if(!$this->session->userdata('login')) redirect('');
    }

   function index() { 
		$data['total'] 	= $this->Prosidingseminarmodel->getall()->num_rows();  
		$data['menu'] 	= 'prosidingseminar/prosidingseminar'; 
		$this->load->view('theme', $data);
    }   
	
	public function ajax_index()
	{
		  
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$year 		= $this->input->post('year');
		$table 		= "SELECT ki.id ids, title,publisher_name,isbn,published_year, count(ki.id) eks FROM knowledge_item ki LEFT JOIN knowledge_stock ks ON ki.id = ks.knowledge_item_id where ki.knowledge_type_id='28' group by ki.id order by ki.id";
		$colOrder 	= array(null,'title','publisher_name','eks','isbn','published_year'); //set column field database for datatable orderable
		$colSearch 	= array('title','publisher_name','eks','isbn','published_year'); //set column field database for datatable
		$order 		= "order by ids"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $dt) { 
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->title;
			$row[] = $dt->publisher_name;
			$row[] = $dt->eks;
			$row[] = $dt->isbn; 
			$row[] = $dt->published_year; 
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->datatables->count_all(),
						"recordsFiltered" => $this->datatables->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
}

?>