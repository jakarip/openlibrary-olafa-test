<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ms_Kec extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Ms_Kec_Model', 'dm', TRUE);
		$this->load->model('Ms_Kab_Model', '', TRUE);
		$this->load->model('Ms_Prov_Model', '', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/ms_kec/index';
		$data['title']	= 'Master Data Kecamatan';		
		$data['icon']	= 'icon-location4';
		
		$this->load->helper('form');
		
		$prov_db = $this->Ms_Prov_Model->getall()->result();
		$data['prov'] = array('' => 'Pilih Provinsi');
		foreach($prov_db as $prov)
			$data['prov'][$prov->prov_id] = $prov->prov_name;
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'kec_prov', 'dt' => 0 ),
			array( 'db' => 'kec_kab', 'dt' => 1 ),
			array( 'db' => 'kec_code', 'dt' => 2 ),
			array( 'db' => 'kec_name', 'dt' => 3 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			$rows = array (
				$row->kec_prov,
				$row->kec_kab,
				$row->kec_code,
				$row->kec_name,
				'<a href="javascript:edit('.$row->kec_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a>
				<a href="javascript:del('.$row->kec_id.', \''.$row->kec_name.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>'
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	public function insert()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$kab  = $this->input->post('kab');
		
		$code = $this->dm->getmax($kab)->row()->kec_code;
		$code_last = (int) substr($code, 4, 2) + 1;
		$item['kec_code'] = substr($code, 0, 4).y_pad($code_last, 2);
		
		$kab_db = $this->Ms_Kab_Model->getbyidview($kab)->row();
		
		$item['kec_prov_id'] 	= $kab_db->prov_id;
		$item['kec_prov_code'] 	= $kab_db->prov_code;
		$item['kec_prov']		= $kab_db->prov_name;
		$item['kec_kab_id'] 	= $kab_db->kab_id;
		$item['kec_kab_code'] 	= $kab_db->kab_code;
		$item['kec_kab'] 		= $kab_db->kab_name;
		
		if( $this->dm->add($item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function edit()
	{
		if(!$this->input->post('id')) return false;
		
		echo json_encode($this->dm->getbyid($this->input->post('id'))->row());
	}
	
	public function update()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');
		$kab  = $this->input->post('kab');
		$kab_ori = $this->input->post('kab_ori');
		
		if($kab != $kab_ori)
		{
			$code = $this->dm->getmax($kab)->row()->kec_code;
			$code_last = (int) substr($code, 4, 2) + 1;
			$item['kec_code'] = substr($code, 0, 4).y_pad($code_last, 2);
			
			$kab_db = $this->Ms_Kab_Model->getbyidview($kab)->row();
			
			$item['kec_prov_id'] 	= $kab_db->prov_id;
			$item['kec_prov_code'] 	= $kab_db->prov_code;
			$item['kec_prov']		= $kab_db->prov_name;
			$item['kec_kab_id'] 	= $kab_db->kab_id;
			$item['kec_kab_code'] 	= $kab_db->kab_code;
			$item['kec_kab'] 		= $kab_db->kab_name;
		}
		
		if( $this->dm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function delete()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		
		if( $this->dm->delete($id) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menghapus Data'));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
	
	public function getkab()
	{
		if(!$this->input->post('id')) return false;
		
		echo json_encode($this->Ms_Kab_Model->getby(array('kab_id_prov' => $this->input->post('id')))->result());
	}
}

?>