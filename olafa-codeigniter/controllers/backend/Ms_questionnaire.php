<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ms_Questionnaire extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Questionnaire_Model', 'dm', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/ms_questionnaire/index';
		$data['title']	= 'Master Data Kuesioner';		
		$data['icon']	= 'icon-stack-text';
		
		$this->load->helper('form');
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'q_name', 'dt' => 0 ), 
			array( 'db' => 'q_id', 'dt' => 1 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			if($row->q_active == 1)
			{
				$label = '<span class="label label-success">Aktif</span>';
				$btn = '';
			}
			else
			{
				$label = '<span class="label label-danger">Non Aktif</span>';
				$btn = '<a href="javascript:active(1, '.$row->q_id.', \''.$row->q_name.'\')" title="Aktifkan Data" class="btn btn-xs btn-icon btn-success"><i class="icon-eye"></i></a>';
			}
			
			$rows = array (
				$row->q_name, 
				$label,
				'<a href="javascript:edit('.$row->q_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a>
				<a href="javascript:detail('.$row->q_id.')" title="Detail" class="btn btn-xs btn-icon bg-orange"><i class="icon-file-check"></i></a>
				'.$btn,
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	public function detail($id)
	{
		$data['id']		= $id;
		$this->load->helper('form'); 
		$datas = $this->dm->getbyid($id)->row();
		$data['view'] 	= 'backend/ms_questionnaire/detail';
		$data['title']	= 'Master Data Kuesioner : '.($datas?$datas->q_name:'');		
		$data['icon']	= 'icon-file-check';
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json_detail($id)
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'rownumber', 'dt' => 0), 
			array( 'db' => 'qq_question', 'dt' => 1), 
			array( 'db' => 'qq_type', 'dt' => 2 ),
			array( 'db' => 'total', 'dt' => 3),
			array( 'db' => 'qq_active', 'dt' => 4),
			array( 'db' => 'qq_id', 'dt' => 5 )
		); 
		
		
		$this->datatables->set_cols($columns);	
		$param	= $this->datatables->query();	
		$param['order'] 	= 'order by qq_active, qq_id asc';
		if(empty($param['where']))
			$param['where'] = " WHERE (qq_id_questionnaire = '$id') ";
		else
			$param['where'] .= " AND (qq_id_questionnaire = '$id') ";
		
		$result = $this->dm->dtquery_detail($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount_detail();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{ 
			
			if($row->qq_active == 1)
			{
				$label = '<span class="label label-success">Aktif</span>';
				$btn = '<a href="javascript:active(0, '.$row->qq_id.', \''.$row->qq_question.'\')" title="Non Aktifkan Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-eye-blocked"></i></a>';
			}
			else
			{
				$label = '<span class="label label-danger">Non Aktif</span>';
				$btn = '<a href="javascript:active(1, '.$row->qq_id.', \''.$row->qq_question.'\')" title="Aktifkan Data" class="btn btn-xs btn-icon btn-success"><i class="icon-eye"></i></a>';
			} 
			
			$rows = array (
				($row->qq_active == 1)?$row->rownumber:'-',  
				$row->qq_question,  
				$row->qq_type,  
				($row->qq_type!='text'?$row->total:'-'),    
				$label,  
				'<a href="javascript:edit('.$row->qq_id_questionnaire.','.$row->qq_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a>
				'.$btn,
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	} 
	
	public function option($q_id,$qq_id="")
	{
		$data['view'] 	= 'backend/ms_questionnaire/option';
		$data['title']	= ($qq_id==""?'Tambah Pertanyaan':'Ubah Pertanyaan');		
		$data['icon']	= 'icon-file-check';
		$data['q_id']		= $q_id;
		$data['qq_id']		= $q_id;
		 
		$data['detail'] 	= $this->dm->getbyid_detail($qq_id)->row();
		$data['option'] 	= $this->dm->getbyid_option($qq_id)->result();
		$this->load->helper('form');
		
		$this->load->view('backend/tpl', $data);
	} 
	 
	public function insert()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		
		if( $this->dm->add($item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function insert_detail()
	{
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item 		= $this->input->post('inp'); 
		$id 		= $this->input->post('id'); 
		$inputan 	= $this->input->post('inputan');
		$check 		= $this->input->post('check');
		$text 		= $this->input->post('text');
		$exist 		= $this->input->post('existing'); 
		
		if(empty($id))$id = $this->dm->add_detail($item); 
		else $this->dm->edit_detail($id,$item); 
		 
		if(!empty($inputan)){
			foreach($inputan as $key=> $row){ 
				$item2['qo_id_question'] 	= $id;
				$item2['qo_option'] 		= $row;
				$item2['qo_text'] 		= (empty($text[$key])?'0':'1');
				$item2['qo_active'] 		= (empty($check[$key])?'0':'1');
				
				if(!empty($exist[$key])) $this->dm->edit_option($exist[$key],$item2);
				else $this->dm->add_option($item2);
			}
			echo json_encode(array('status' => 'ok;', 'text' => '')); 
		}
		else echo json_encode(array('status' => 'ok;', 'text' => '')); 
	} 
	
	public function edit()
	{
		if(!$this->input->post('id')) return false;
		
		echo json_encode($this->dm->getbyid($this->input->post('id'))->row());
	}
	
	public function edit_detail()
	{
		if(!$this->input->post('id')) return false;
		
		echo json_encode($this->dm->getbyid_detail($this->input->post('id'))->row());
	}
	
	public function update()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');
		
		if( $this->dm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	} 
	
	public function update_detail()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');
		
		if( $this->dm->edit_detail($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	} 
	
	public function active()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		$sts = $this->input->post('sts');
		
		$this->dm->edit_status(array('q_active' => '0'));
		
		if($this->dm->edit($id, array('q_active' => $sts)) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}  
	
	public function active_detail()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		$sts = $this->input->post('sts'); 
		
		if($this->dm->edit_detail($id, array('qq_active' => $sts)) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	
	
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
	
	public function get_address()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('searchTerm')) return false;
		
		$s = $this->input->post('searchTerm');
		$dbs = $this->Ms_Kec_Model->getaddress($s)->result();
		
		$result = array();
		foreach($dbs as $db)
			$result[] = array('id' => $db->kec_id,
							  'text' => 'Kec. '.$db->kec_name.', '.$db->kec_kab.', Prov. '.$db->kec_prov);
		
		echo json_encode($result);
	}
}

?>