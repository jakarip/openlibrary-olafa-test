<?php

class Cumlaude extends CI_Controller {
 
    function __construct() {
        parent::__construct(); 
		$this->load->model('CumlaudeModel');
		// $this->load->library('PHPExcel');
		//$this->load->library('PHPExcel/IOFactory');

    }

    function index() {  
		$data['menu'] 		= 'cumlaude/cumlaude'; 
		$this->load->view('theme',$data);
    }
	 
	public function ajax_index()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$grow_year 		= $this->input->post('grow_year');
		$access 		= $this->input->post('access');

		if($access=='yes') $where = "and master_data_uuid is not null"; 
		else if($access=='no')  $where = "and master_data_uuid is null";

		$table 		= "select mc.*, master_data_fullname, master_data_number, nama_prodi,nama_fakultas,master_data_uuid,
					(select count(*) from member_attendance where member_id=m.id)visitor,
					(select count(*) from rent where member_id=m.id)peminjaman_buku,
					(select count(*) from room.booking where bk_memberid=m.id)peminjaman_ruangan,
					(select count(*) from knowledge_item_file_readonly wd where wd.member_id=m.id)akses_digital 
					from member_cumlaude mc
					left join member m on id=cmld_id_member
					left join t_mst_prodi tmp on c_kode_prodi=master_data_course
					left join t_mst_fakultas tmf on tmp.C_KODE_FAKULTAS=tmf.C_KODE_FAKULTAS where cmld_year='".$grow_year."' $where
					order by master_data_fullname
					";

					// echo $table;
		$colOrder 	= array(null,'nama_fakultas','nama_prodi','master_data_number','master_data_fullname','cmld_ipk','cmld_yudisium','cmld_status_akhir','visitor','peminjaman_buku','peminjaman_ruangan','akses_digital','master_data_uuid',null); //set column field database for datatable orderable
		$colSearch 	= array('nama_fakultas','nama_prodi','master_data_number','master_data_fullname','cmld_ipk','cmld_yudisium','cmld_status_akhir','visitor','peminjaman_buku','peminjaman_ruangan','akses_digital','master_data_uuid'); //set column field database for datatable
		$order 		= "order by cmld_yudisium asc,cmld_ipk desc"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array(); 
		foreach ($list as $dt) {
			 
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->nama_fakultas;
			$row[] = $dt->nama_prodi;
			$row[] = $dt->master_data_number;
			$row[] = $dt->master_data_fullname;
			$row[] = $dt->cmld_ipk;
			$row[] = $dt->cmld_yudisium;
			$row[] = $dt->cmld_status_akhir;
			$row[] = '<div><a class="btn btn-sm btn-primary btn-embossed" target="_blank" href="index.php/cumlaude/total/visitor/'.$dt->cmld_id_member.'" title="'.getLang('Pengunjung Fisik').'">'.$dt->visitor.'</a></div>';
			$row[] = '<div><a class="btn btn-sm btn-primary btn-embossed" target="_blank" href="index.php/cumlaude/total/rent/'.$dt->cmld_id_member.'" title="'.getLang('Peminjaman Buku').'">'.$dt->peminjaman_buku.'</a></div>';
			$row[] = '<div><a class="btn btn-sm btn-primary btn-embossed" target="_blank" href="index.php/cumlaude/total/room/'.$dt->cmld_id_member.'" title="'.getLang('Peminjaman Ruangan').'">'.$dt->peminjaman_ruangan.'</a></div>';
			$row[] = '<div><a class="btn btn-sm btn-primary btn-embossed" target="_blank" href="index.php/cumlaude/total/digital/'.$dt->cmld_id_member.'" title="'.getLang('Akses Koleksi Digital').'">'.$dt->akses_digital.'</a></div>';

			if($dt->master_data_uuid!="") $row[] = '<div><button class="btn btn-sm btn-success btn-embossed" ><i class="fa fa-check"></i></button></div>'; 
			else  $row[] = '';
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

    function total($type,$id) {
		$data['id'] 		= $id;
		$data['member']		= $this->CumlaudeModel->getmember($id)->row();
		$data['menu'] 		= 'cumlaude/cumlaude_'.$type; 
		$this->load->view('theme',$data);
    }


	public function ajax_visitor()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 		= $this->input->post('id');

		$table 		= "select name,attended_at from member_attendance 
		left join item_location il on il.id=item_location_id
		where member_id='$id'
		order by attended_at desc";

					// echo $table;
		$colOrder 	= array(null,'name','attended_at',null); //set column field database for datatable orderable
		$colSearch 	= array('name','attended_at'); //set column field database for datatable
		$order 		= "order by attended_at desc"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array(); 
		foreach ($list as $dt) {
			 
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->name;
			
			$temp = explode(" ",$dt->attended_at);
			$begintime = new DateTime($temp[1]);
			$endtime = new DateTime('08:00');

			if($begintime < $endtime) {
				$time = date('Y-m-d H:i',strtotime('+7 hours',strtotime($dt->attended_at)));
			}
			else $time = $dt->attended_at;
			
			$row[] = $time;
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

	public function ajax_rent()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 		= $this->input->post('id');

		$table 		= "select kit.title,author,ks.code,rent_date,return_date from rent  
		left join knowledge_item kit on kit.id=ks.knowledge_item_id
		where member_id='$id'
		order by rent_date desc";

					// echo $table;
		$colOrder 	= array(null,'title','author','code','rent_date','return_date',null); //set column field database for datatable orderable
		$colSearch 	= array('title','author','code','rent_date','return_date'); //set column field database for datatable
		$order 		= "order by rent_date desc"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array(); 
		foreach ($list as $dt) {
			 
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->title;
			$row[] = $dt->author;
			$row[] = $dt->code;
			$row[] = $dt->rent_date;
			$row[] = $dt->return_date;
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

	public function ajax_room()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 		= $this->input->post('id');

		$table 		= "
		select room_name, bk_reason, bk_startdate, bk_enddate from room.booking
		left join room.room r on room_id=bk_room_id
		where bk_memberid='$id'
		order by bk_startdate desc";

					// echo $table;
		$colOrder 	= array(null,'room_name','bk_reason','bk_startdate','bk_enddate',null); //set column field database for datatable orderable
		$colSearch 	= array('room_name','bk_reason','bk_startdate','bk_enddate'); //set column field database for datatable
		$order 		= "order by bk_startdate desc"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array(); 
		foreach ($list as $dt) {
			 
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->room_name; 
			$row[] = $dt->bk_reason; 
			$row[] = $dt->bk_startdate; 
			$row[] = $dt->bk_enddate; 
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

	public function ajax_digital()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 		= $this->input->post('id');

		$table 		= " 
		select kt.name,kit.title,author,wd.name nama_file,wd.created_at
		from knowledge_item_file_readonly wd
		left join knowledge_item kit on kit.id=knowledge_item_id
		left join knowledge_type kt on kt.id=kit.knowledge_type_id
		where member_id='$id'
		order by wd.created_at desc";

					// echo $table;
		$colOrder 	= array(null,'name','title','author','nama_file','created_at',null); //set column field database for datatable orderable
		$colSearch 	= array('name','title','author','nama_file','created_at'); //set column field database for datatable
		$order 		= "order by created_at desc"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array(); 
		foreach ($list as $dt) {
			 
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->name; 
			$row[] = $dt->title; 
			$row[] = $dt->author; 
			$row[] = $dt->nama_file; 
			$row[] = $dt->created_at; 
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