<?php

class KaryaIlmiah extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		$this->load->model('KaryaIlmiahModel');
		if(!$this->session->userdata('login')) redirect('');
    }

    function index() {   
		$data['total'] 	= $this->KaryaIlmiahModel->totalkaryailmiah(); 
		$data['menu'] 	= 'karyailmiah/karyailmiah'; 
		$this->load->view('theme', $data);
    }
	
	public function ajax_index()
	{
		$jurusan 	= $this->input->post('jurusan'); 
		$tahun 		= $this->input->post('tahun');
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$year 		= $this->input->post('year');
		// $table 		= "select c_kode_prodi, nama_prodi,nama_fakultas, count(kt.id) jml_ta from t_mst_prodi tp 
					// left join t_mst_fakultas tf using(c_kode_fakultas) 
					// left join knowledge_item kt on kt.course_code=tp.c_kode_prodi
					// left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					// left join knowledge_type kp on kt.knowledge_type_id=kp.id 
					// where ks.active='1' and kp.active='1' and kt.knowledge_type_id
					// in (4,5,6) group by nama_prodi order by nama_fakultas, nama_prodi";
					
		$table 		= "select c_kode_prodi, nama_prodi,nama_fakultas, (
						select count(*) total 
						from knowledge_item kt 
						left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
						left join knowledge_type kp on kt.knowledge_type_id=kp.id 
						where ks.active='1' and kp.active='1' and kt.knowledge_type_id
						in (4,5,6) and course_code=tp.c_kode_prodi)jml_ta 
					from t_mst_prodi tp 
					left join t_mst_fakultas tf using(c_kode_fakultas) where (nama_prodi not like '%Pindahan%' and nama_prodi not like '%International%' and nama_prodi not like '%Internasional%') and nama_fakultas!=''
					order by nama_fakultas, nama_prodi";
		
		$colOrder 	= array(null,'nama_fakultas','nama_prodi','jml_ta',null); //set column field database for datatable orderable
		$colSearch 	= array('nama_fakultas','nama_prodi','jml_ta'); //set column field database for datatable
		$order 		= "order by nama_fakultas,nama_prodi"; // default order 
		
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
			$row[] = $dt->nama_fakultas;
			$row[] = $dt->nama_prodi;
			//if ($dt->c_kode_prodi==93402) $row[] = '<div class="td_right">8 '.getLang('title').'</div>';
			//else
				$row[] = '<div class="td_right">'.$dt->jml_ta.' '.getLang('title').'</div>';
			$row[] = '<div><a class="btn btn-sm btn-default btn-embossed" target="_blank" href="index.php/karyailmiah/detail/'.strtolower($dt->c_kode_prodi).'" title="'.getLang('scientific_paper_detail').'"><i class="fa fa-file"></i></a></div>';
		
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
	
	function detail($kodejurusan)
	{ 
		
		$data['jurusan']	= $this->KaryaIlmiahModel->getjurusanbyid($kodejurusan)->row(); 
		if ($this->uri->segment(3)=='93402') $data['menu'] 		= 'karyailmiah/karyailmiah_detail_sementara';
		else $data['menu'] 		= 'karyailmiah/karyailmiah_detail';
		$this->load->view('theme',$data);
	} 
	
	public function ajax_detail()
	{
		$jurusan 	= $this->input->post('jurusan'); 
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$year 		= $this->input->post('year');
		$table 		= "select author,title,published_year FROM knowledge_item kt 
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id 
					where ks.active='1' and kp.active='1' and kt.knowledge_type_id
					in (4,5,6) and course_code= '$jurusan' group by kt.id order by published_year desc";
		$colOrder 	= array(null,'author','title','published_year',null); //set column field database for datatable orderable
		$colSearch 	= array('author','title','published_year'); //set column field database for datatable
		$order 		= "order by pubished_year desc"; // default order 
		
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
			$row[] = ucwords(strtolower($dt->author));
			$row[] = ucwords(strtolower($dt->title));
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