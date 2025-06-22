<?php

class Lecturerbook extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		$this->load->model('Lecturerbookmodel');  
		if(!$this->session->userdata('login')) redirect('');
    }

   function index() { 
		$data['total'] 	= $this->Lecturerbookmodel->getall()->num_rows();  
		$data['menu'] 	= 'lecturerbook/index'; 
		$this->load->view('theme', $data);
    }   
	
	public function ajax_index()
	{
		 
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed'); 
		
		$excel = $this->input->post('excel');
		$table 		= "SELECT kit.id,press_barcode, press_type,press_title,press_author,press_publisher,press_published_year,press_faculty_unit,press_isbn,press_id
		FROM telu_press left join knowledge_item kit on kit.id=press_id_knowledge_item";
		$colOrder 	= array(null,'press_barcode','press_type','press_title','press_author','press_publisher','press_published_year','press_faculty_unit','press_isbn'); //set column field database for datatable orderable
		$colSearch 	= array('press_barcode','press_type','press_title','press_author','press_publisher','press_published_year','press_faculty_unit','press_isbn'); //set column field database for datatable
		$order 		= "order by press_faculty_unit, press_author"; // default order 
		
		 
		
		if($excel=='1'){
			$_POST['length'] = '-1'; 
			$this->datatables->set_table($table);
			$this->datatables->set_col_order($colOrder);
			$this->datatables->set_col_search($colSearch);
			$this->datatables->set_order($order);
			$list = $this->datatables->get_datatables(); 
			echo json_encode($list);
		}
		else { 
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
				$row[] = '<a href="https://openlibrary.telkomuniversity.ac.id/home/catalog/id/'.$dt->id.'/slug/book.html" target="_blank">'.$dt->press_barcode.'</a>';
				$row[] = $dt->press_type;
				$row[] = $dt->press_title;
				$row[] = $dt->press_author;
				$row[] = $dt->press_publisher; 
				$row[] = $dt->press_published_year; 
				$row[] = $dt->press_faculty_unit; 
				$row[] = $dt->press_isbn; 
				if($this->session->userdata("user_id")=='1'){
					$row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="'.getLang("edit").'" onclick="edit('."'".$dt->press_id."'".')"><i class="fa fa-pencil-square-o"></i></button><button type="button" class="btn btn-sm btn-danger" title="'.getLang("delete_data").'" onclick="del('."'".$dt->press_id."'".','."'".$dt->press_title."'".')"><i class="fa fa-trash-o"></i></button></div>'; 
				}
				else $row[] = "";
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

    function insert(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $item = $_POST['inp']; 
	
		$item['press_id_knowledge_item'] = $this->Lecturerbookmodel->getItemId($item['press_barcode']);
		if ($this->Lecturerbookmodel->add($item)) echo json_encode(array("status" => TRUE)); 
    }

    function update(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $item   = $_POST['inp'];    
		$item['press_id_knowledge_item'] = $this->Lecturerbookmodel->getItemId($item['press_barcode']);
		if ($this->Lecturerbookmodel->edit($id, $item)) echo json_encode(array("status" => TRUE)); 
    }

    function edit(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $data 	= $this->Lecturerbookmodel->getbyid($id)->row();
        echo json_encode($data);
    }

    function deletes(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id'); 
        $this->Lecturerbookmodel->deletes($id);
        echo json_encode(array("status" => TRUE));
    } 
}

?>