<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ms_Kab extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Ms_Kab_Model', 'dm', TRUE);
		$this->load->model('Ms_Prov_Model', '', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/ms_kab/index';
		$data['title']	= 'Master Data Kabupaten Kota';		
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
			array( 'db' => 'prov_name', 'dt' => 0 ),
			array( 'db' => 'kab_code', 'dt' => 1 ),
			array( 'db' => 'kab_name', 'dt' => 2 )
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
				$row->prov_name,
				$row->kab_code,
				$row->kab_name,
				'<a href="javascript:edit('.$row->kab_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a>
				<a href="javascript:del('.$row->kab_id.', \''.$row->kab_name.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>'
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
		$code = (int) substr($this->dm->getmax($item['kab_id_prov'])->row()->kab_code, 2, 2) + 1;
		$item['kab_code'] = y_pad($item['kab_id_prov'], 2).y_pad($code, 2).'00';
		
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
		$kab  = $this->input->post('kab_id_prov_ori');
		
		if($item['kab_id_prov'] != $kab)
		{
			$code = (int) substr($this->dm->getmax($item['kab_id_prov'])->row()->kab_code, 2, 2) + 1;
			$item['kab_code'] = y_pad($item['kab_id_prov'], 2).y_pad($code, 2).'00';	
		}
		
		if( $this->dm->edit($id, $item) )
		{
			if($item['kab_id_prov'] != $kab)
				$this->dm->update_view_with_prov($id);
			else
				$this->dm->update_view($id);
			
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		}
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
}

?>