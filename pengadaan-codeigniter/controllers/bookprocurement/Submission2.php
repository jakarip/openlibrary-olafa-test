<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Submission2 extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Member_Model', 'dm', TRUE); 
		$this->load->model('bookprocurement/Submission2_Model', 'sm', TRUE); 
		$this->load->library('PHPExcel');
		$this->load->model('Common_Model', 'cm', TRUE);
		$this->load->helper('form');
		ini_set('memory_limit', '-1'); 
		
		// y_is_login('bookprocurement');
	}
	
	public function index()
	{ 
		$data['view'] 	= 'bookprocurement/submission2/index';
		$data['title']	= 'Data Pengajuan Buku';		
		$data['icon']	= 'icon-book3';
		
		$prodi 	= $this->sm->getprodi()->result(); 
		$temp[''] = "Semua Prodi"; 
		$temp2[''] = "Pilih Prodi"; 
		foreach($prodi as $row){
			$temp[$row->C_KODE_PRODI] = ucwords(strtolower($row->NAMA_FAKULTAS)).' - '.ucwords(strtolower($row->NAMA_PRODI));
			$temp2[$row->C_KODE_PRODI] = ucwords(strtolower($row->NAMA_FAKULTAS)).' - '.ucwords(strtolower($row->NAMA_PRODI));
		}
		$data['prodi'] = $temp;
		$data['prodi_input'] = $temp2;

		
		$data['status'] 	= $this->cm->form_status(); 
		$data['type'] 	= $this->cm->form_book_type(); 
		$data['book_type'] 	= $this->cm->form_book_type_option(); 
		
		$this->load->view('frontend/tpl', $data);
	}
 
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;

		
		$prodi 	 			 			= $this->input->post('prodi');   
		$type 	  						= $this->input->post('type');   
		$status 	  					= $this->input->post('status');   
		$dates_submission_option 		= $this->input->post('dates_submission_option'); 
		$dates_logistic_option 			= $this->input->post('dates_logistic_option'); 
		$dates_acceptance_option 		= $this->input->post('dates_acceptance_option'); 
		$dates_email_confirmed_option 	= $this->input->post('dates_email_confirmed_option'); 
		$dates_available_option 		= $this->input->post('dates_available_option'); 
		$dates_submission 				= $this->input->post('dates_submission'); 
		$dates_logistic 				= $this->input->post('dates_logistic'); 
		$dates_acceptance 				= $this->input->post('dates_acceptance'); 
		$dates_email_confirmed 				= $this->input->post('dates_email_confirmed'); 
		$dates_available 				= $this->input->post('dates_available'); 

		$option = "";
		if($dates_submission_option!='all'){
			$temp = explode(' - ',$dates_submission);

			$date1 = y_convert_date($temp[0], 'Y-m-d');
			$date2 = y_convert_date($temp[1], 'Y-m-d');
			$option = "(book_date_prodi_submission between '$date1' and '$date2')";
		}
		
		if($dates_logistic_option!='all'){
			$temp = explode(' - ',$dates_logistic);

			$date1 = y_convert_date($temp[0], 'Y-m-d');
			$date2 = y_convert_date($temp[1], 'Y-m-d');
			$option .= ($option!="" ? " and" : "")." (book_date_logistic_submission between '$date1' and '$date2')";
		}
		
		if($dates_acceptance_option!='all'){
			$temp = explode(' - ',$dates_acceptance);

			$date1 = y_convert_date($temp[0], 'Y-m-d');
			$date2 = y_convert_date($temp[1], 'Y-m-d');
			$option .= ($option!="" ? " and" : "")." (book_date_acceptance between '$date1' and '$date2')";
		} 
		
		if($dates_email_confirmed_option!='all'){
			$temp = explode(' - ',$dates_email_confirmed);

			$date1 = y_convert_date($temp[0], 'Y-m-d');
			$date2 = y_convert_date($temp[1], 'Y-m-d');
			$option .= ($option!="" ? " and" : "")." (book_date_email_confirmed between '$date1' and '$date2')";
		} 
		
		if($dates_available_option!='all'){
			$temp = explode(' - ',$dates_available);

			$date1 = y_convert_date($temp[0], 'Y-m-d');
			$date2 = y_convert_date($temp[1], 'Y-m-d');
			$option .= ($option!="" ? " and" : "")." (book_date_available between '$date1' and '$date2')";
		} 
	
		$columns = array( 
			array( 'db' => 'book_status', 'dt' => 0 ),
			array( 'db' => 'book_status', 'dt' => 1 ),
			array( 'db' => 'book_status', 'dt' => 2 ),
			array( 'db' => 'NAMA_PRODI', 'dt' =>3 ),
			array( 'db' => 'book_member', 'dt' => 4 ),
			array( 'db' => 'book_subject', 'dt' => 5 ),
			array( 'db' => 'book_type', 'dt' => 6 ),
			array( 'db' => 'book_title', 'dt' => 7 ),
			array( 'db' => 'book_author', 'dt' =>8 ),
			array( 'db' => 'book_publisher', 'dt' => 9),
			array( 'db' => 'book_published_year', 'dt' => 10 ),
			array( 'db' => 'book_date_prodi_submission', 'dt' => 11 ),
			array( 'db' => 'book_date_logistic_submission', 'dt' => 12 ),
			array( 'db' => 'book_memo_logistic_number', 'dt' => 13 ),
			array( 'db' => 'book_status', 'dt' => 14 ),
			array( 'db' => 'book_date_logistic_process', 'dt' => 15 ),
			array( 'db' => 'book_date_acceptance', 'dt' => 16 ),
			array( 'db' => 'book_status', 'dt' => 17 ),
			array( 'db' => 'book_procurement_price', 'dt' => 18 ),
			array( 'db' => 'book_total_price', 'dt' => 19 ),
			array( 'db' => 'book_copy', 'dt' => 20 ),
			array( 'db' => 'book_date_email_confirmed', 'dt' => 21 ),
			array( 'db' => 'book_date_available', 'dt' => 22 ),
			array( 'db' => 'book_catalog_number', 'dt' => 23 )
	);
		$this->datatables->set_cols($columns);

		$param	= $this->datatables->query();  

		if ($type!=""){ 
			if(empty($param['where'])) 	$param['where'] = "WHERE (book_type='".$type."')";
			else $param['where'] .= "AND (book_type='".$type."')"; 
		}   

		if ($status!=""){ 
			if(empty($param['where'])) 	$param['where'] = "WHERE (book_status='".$status."')";
			else $param['where'] .= "AND (book_status='".$status."')"; 
		} 

		if ($prodi!=""){ 
			if(empty($param['where'])) 	$param['where'] = "WHERE (book_id_prodi='".$prodi."')";
			else $param['where'] .= "AND (book_id_prodi='".$prodi."')"; 
		} 

		
		if ($option!=""){ 
			if(empty($param['where'])) 	$param['where'] = "WHERE (".$option.")";
			else $param['where'] .= "AND (".$option.")";
		}

		$result = $this->sm->dtquery($param)->result();
		$filter = $this->sm->dtfiltered();
		$total	= $this->sm->dtcount();
		$output = $this->datatables->output($total, $filter);

		$status_type 		= $this->cm->form_status();
		$book_type 	= $this->cm->form_book_type();
		$iuser = $this->session->userdata(); 
		foreach($result as $row)
		{
			$btn = "";
			$accept = "";
			$logistic = "";
			$email_confirmed = "";
			$available = "";
			$checkbox = "";
			if($row->book_status == 'r_ketersediaan')
			{
					$status = '<label class="label label-success"><strong>'.$status_type[$row->book_status].'</strong></label>';
					$available = ' <a href="javascript:edit_available('.$row->book_id.')" title="Input Tanggal Ketersediaan Buku" class="btn btn-xs btn-icon btn-success"><i class="icon-calendar"></i></a>'; 
					$email_confirmed = ' <a href="javascript:edit_email_confirmed('.$row->book_id.')" title="Input Tanggal Konfirmasi Email" class="btn btn-xs btn-icon btn-primary"><i class="icon-calendar"></i></a>'; 
					$accept = ' <a href="javascript:edit_accept('.$row->book_id.')" title="Input Tanggal Penerimaan" class="btn btn-xs btn-icon btn-info"><i class="icon-calendar"></i></a>'; 
					$logistic = ' <a href="javascript:logistics('.$row->book_id.',\'edit\')" title="Ubah Data Pengajuan ke Logistik" class="btn btn-xs btn-icon btn-warning"><i class="icon-calendar3"></i></a>';  
			} 
			else if($row->book_status == 'q_email')
			{
					$status = '<label class="label label-primary"><strong>'.$status_type[$row->book_status].'</strong></label>';
					$available = ' <a href="javascript:edit_available('.$row->book_id.')" title="Input Tanggal Ketersediaan Buku" class="btn btn-xs btn-icon btn-success"><i class="icon-calendar"></i></a>'; 
					$email_confirmed = ' <a href="javascript:edit_email_confirmed('.$row->book_id.')" title="Input Tanggal Konfirmasi Email" class="btn btn-xs btn-icon btn-primary"><i class="icon-calendar"></i></a>'; 
					$accept = ' <a href="javascript:edit_accept('.$row->book_id.')" title="Input Tanggal Penerimaan" class="btn btn-xs btn-icon btn-info"><i class="icon-calendar"></i></a>'; 
					$logistic = ' <a href="javascript:logistics('.$row->book_id.',\'edit\')" title="Ubah Data Pengajuan ke Logistik" class="btn btn-xs btn-icon btn-warning"><i class="icon-calendar3"></i></a>';  
			}
			else if($row->book_status == 'penerimaan')
			{
					$status = '<label class="label label-info"><strong>'.$status_type[$row->book_status].'</strong></label>'; 
					$email_confirmed = ' <a href="javascript:edit_email_confirmed('.$row->book_id.')" title="Input Tanggal Konfirmasi Email" class="btn btn-xs btn-icon btn-primary"><i class="icon-calendar"></i></a>'; 
					$accept = ' <a href="javascript:edit_accept('.$row->book_id.')" title="Input Tanggal Penerimaan" class="btn btn-xs btn-icon btn-info"><i class="icon-calendar"></i></a>'; 
					$logistic = ' <a href="javascript:logistics('.$row->book_id.',\'edit\')" title="Ubah Data Pengajuan ke Logistik" class="btn btn-xs btn-icon btn-warning"><i class="icon-calendar3"></i></a>';  
			}
			else if($row->book_status == 'logistik')
			{ 
				$status = '<label class="label label-warning"><strong>'.$status_type[$row->book_status].'</strong></label>'; 
				$accept = ' <a href="javascript:edit_accept('.$row->book_id.')" title="Input Tanggal Penerimaan" class="btn btn-xs btn-icon btn-info"><i class="icon-calendar"></i></a>'; 
				$logistic = ' <a href="javascript:logistics('.$row->book_id.',\'edit\')" title="Ubah Data Pengajuan ke Logistik" class="btn btn-xs btn-icon btn-warning"><i class="icon-calendar3"></i></a>';  
			}
			else if($row->book_status == 'pengajuan')
			{
				$status = '<label class="label label-danger"><strong>'.$status_type[$row->book_status].'</strong></label>';  
				$checkbox = '<input type="checkbox" class="chk-logistic" value="'.$row->book_id.'">';
			}   

			$btn = '<a href="javascript:edit('.$row->book_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a> <a href="javascript:del('.$row->book_id.', \''.$row->book_title.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>';

			
			if ($iuser['usergroup']!='superadmin'){  
				$btn = "";
				$accept = "";
				$logistic = "";
				$checkbox = "";
			}

			// else if($row->subscribe_status == '2')
			// {
			// 		$label = '<label class="label label-primary"><strong>Menunggu Verifikasi Admin</strong></label>';
			// 		$btn = 
			// 		'<a href="javascript:reject_form('.$row->subscribe_id.','.$row->subscribe_id_member.')" title="Reject Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-cross3"></i></a>
			// 		<a href="javascript:approval('.$row->subscribe_id.',\''.$row->subscribe_transaction.'\',\''.$row->subscribe_id_member.'\',\''.$row->master_data_type.'\')" title="Approve Data" class="btn btn-xs btn-icon btn-success"><i class="icon-checkmark2"></i></a>';
			// }
			// else if($row->subscribe_status == '3')
			// {
			// 		$label = '<label class="label label-default"><strong>Non Aktif</strong></label>';
			// }  

			// if($row->subscribe_status == '0'){
			// 		$payment ='<label class="label label-success" style="font-size:16px"><strong>'. y_num_idr($row->subscribe_payment_code).'</strong></label>';
			// }
			// else 
			// $payment ='<label class="label label-default" style="font-size:16px"><strong>'. y_num_idr($row->subscribe_payment_code).'</strong></label>';

			$rows = array (
				// $btn,
				// $label,
				// ($row->subscribe_payment_date!=""?y_date_text($row->subscribe_payment_date):''),
				// $payment,
				// $jenis_anggota[$row->master_data_type].'<br>'.$row->master_data_institution, 
					$checkbox,
					$btn.$logistic.$accept.$email_confirmed.$available,
					$status,
					$row->NAMA_PRODI, 
					$row->book_member, 
					$row->book_subject, 
					$row->book_type, 
					$row->book_title, 
					$row->book_author, 
					$row->book_publisher, 
					$row->book_published_year, 
					$row->book_date_prodi_submission, 
					$row->book_date_logistic_submission, 
					$row->book_memo_logistic_number, 
					$row->proses_pengajuan, 
					$row->book_date_logistic_process, 
					$row->book_date_acceptance, 
					$row->proses_pengadaan, 
					"Rp " . number_format($row->book_procurement_price,0,',','.'),
					"Rp " . number_format($row->book_total_price,0,',','.'), 
					$row->book_copy, 
					$row->proses_email,
					$row->book_date_email_confirmed, 
					$row->proses_ketersediaan,
					$row->book_date_available, 
					$row->book_catalog_number 
			); 

			$output['data'][] = $rows;
		}

		echo json_encode( $output );
	}

	
	public function save_upload()
	{
		 
		$this->load->library('phpexcel');

		$prodi 	= $this->sm->getprodi()->result(); 
		$temp[''] = "semua prodi";  
		foreach($prodi as $row){
			$temp[] =  $row->C_KODE_PRODI;
		}

		$inputfilename = $_FILES['file']['tmp_name'];
		
		//  read your excel workbook
        try {
            $inputfiletype = PHPExcel_IOFactory::identify($inputfilename);
            $objreader = PHPExcel_IOFactory::createreader($inputfiletype);
            $objphpexcel = $objreader->load($inputfilename);
        } catch(exception $e) {
            die('error loading file "'.pathinfo($inputfilename,pathinfo_basename).'": '.$e->getmessage());
        }

        //  get worksheet dimensions
        $sheet              = $objphpexcel->getsheet(0); 
        $highestrow         = $sheet->gethighestrow(); 
        $highestcolumn      = $sheet->gethighestcolumn();
		
		$rowdata = $sheet->rangetoarray('b8:'.$highestcolumn.$highestrow, null, true, false);
		
		$item  = array();
		$rowno = 1;
		$error = array();

		// print_r($rowdata);
		foreach($rowdata as $row)
		{
			$id = explode(" - ",$row[0]); 

			if(in_array(!$id[0],$temp)){
				$error[] = $rowno;
			}
			else
			{
 
 
				// $index 		= count($row);
				// $scholar 	= (int) trim($row[$index-2]);
				// $choice 	= (int) trim($row[$index-3]);
				// $status 	= (int) trim($row[$index-4]);

				$status = "pengajuan";  

				
				if($row[7]!=""){ 
					$status = "pengajuan";
				}
				else $row[7] = null;

				if($row[8]!=""){ 
					$status = "logistik";
				}
				else $row[8] = null;

				if($row[10]!=""){ 
					$status = "penerimaan";
				}
				else $row[10] = null;

				if($row[15]!=""){ 
					$status = "q_email";
				}
				else $row[15] = null;

				if($row[16]!=""){ 
					$status = "r_ketersediaan";
				}
				else $row[16] = null;
 
				
				$item[] = array('book_id_prodi' => $id[0],
								'book_member' => $row[1],
								'book_subject' => $row[2],
								'book_title' => $row[3], 
								'book_author' => $row[4], 
								'book_publisher' => $row[5], 
								'book_published_year' => $row[6], 
								'book_date_prodi_submission' => $row[7], 
								'book_date_logistic_submission' => $row[8], 
								'book_date_logistic_process' => $row[8], 
								'book_memo_logistic_number' => $row[9], 
								'book_date_acceptance' => $row[10], 
								'book_type' => $row[11], 
								'book_copy' => $row[12], 
								'book_procurement_price' => $row[13], 
								'book_total_price' => $row[14],
								'book_status' => $status,
								'book_date_email_confirmed' => $row[15],
								'book_date_available' => $row[16],
								'book_catalog_number' => $row[17]
							);
			}
			
			$rowno++;
		}  
		
		if(empty($error))
		{ 
			$this->sm->add_batch($item); 
			
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		}
		else
		{
			echo json_encode(array('status' => 'error;', 'text' => "terdapat data yang kosong pada baris: \n".implode(', ', $error)));
		}
	}

	
	
	public function getmember()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('searchTerm')) return false;
		
		$s = $this->input->post('searchTerm');
		$dbs = $this->sm->getmemberbyname($s)->result();
		
		$result = array(); 
		foreach($dbs as $db)
			$result[] = array('id' => $db->id,
							  'text' => $db->master_data_fullname);
		
		echo json_encode($result);
	} 
	
	public function insert()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		
		$item['book_date_prodi_submission'] = date('Y-m-d', strtotime($item['book_date_prodi_submission']));
		$item['book_status'] = 'pengajuan';
		
		if( $this->sm->add($item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function edit()
	{
		if(!$this->input->post('id')) return false; 
		$dt = $this->sm->getbyid($this->input->post('id'))->row();
		$dt->book_date_prodi_submission = date('d-m-Y', strtotime($dt->book_date_prodi_submission));
		echo json_encode($dt);
	}
	
	public function edit_accept()
	{
		if(!$this->input->post('id')) return false; 
		$dt = $this->sm->getbyid($this->input->post('id'))->row();
		if($dt->book_date_acceptance!="") $dt->book_date_acceptance = date('d-m-Y', strtotime($dt->book_date_acceptance));
		echo json_encode($dt);
	}
	
	public function logistics()
	{
		if(!$this->input->post('id')) return false; 
		$book = $this->sm->getbook($this->input->post('id'))->result();

		$type = $this->input->post('type');
		$html = array();
		$dt   = array();
		foreach($book as $key=> $row){
			$tmp = $key + 1;
			$html[] = $tmp.'. '.$row->book_member.' - '.$row->book_title;

			if($type=='edit'){
				$dt['book_date_logistic_submission'] = date('d-m-Y', strtotime($row->book_date_logistic_submission));
				$dt['book_memo_logistic_number'] = $row->book_memo_logistic_number;
			}
		}
		$data['list'] = implode("\n",$html); 
		$data['dt'] = $dt; 
		echo json_encode($data);
	} 
	
	public function edit_email_confirmed_and_available()
	{ 
		if(!$this->input->post('id')) return false; 
		$dt = $this->sm->get_email_confirmed_and_available($this->input->post('id'))->row(); 
		
		if($dt->book_date_email_confirmed!="") $dt->book_date_email_confirmed = date('d-m-Y', strtotime($dt->book_date_email_confirmed));
		if($dt->book_date_available!="") $dt->book_date_available = date('d-m-Y', strtotime($dt->book_date_available));
		
		echo json_encode($dt);
	}
	
	public function update()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');  

		$item['book_date_prodi_submission'] = date('Y-m-d', strtotime($item['book_date_prodi_submission']));
		
		if( $this->sm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	} 

	public function save_logistic()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item 	= $this->input->post('inp');
		$id   	= $this->input->post('id');
		$type   = $this->input->post('type');

		$temp = explode(",",$id);
		
		$arr = array();
		foreach($temp as $row){
			$arr[] = $row;
		}
		// $this->db->where_in('username', $names);

		$item['book_date_logistic_submission'] = date('Y-m-d', strtotime($item['book_date_logistic_submission']));
		$item['book_date_logistic_process'] 	 = $item['book_date_logistic_submission'];
		if($type=='input') $item['book_status'] = 'logistik';
 
		
		if( $this->sm->edit_logistic($arr, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}

	
	
	public function save_accept()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		

		$item = $this->input->post('inp');
		$id   = $this->input->post('id');  
		
		$dt = $this->sm->get_email_confirmed_and_available($id)->row(); 
		if($dt->book_status=='logistik') $item['book_status'] = 'penerimaan';

		$item['book_date_acceptance'] = date('Y-m-d', strtotime($item['book_date_acceptance'])); 
		
		$item['book_total_price'] = 	$item['book_copy']*$item['book_procurement_price'];
 
		
		if( $this->sm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	} 
	
	public function save_email_confirmed()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		

		$item = $this->input->post('inp');
		$id   = $this->input->post('id');  
		
		$dt = $this->sm->get_email_confirmed_and_available($id)->row(); 
		if($dt->book_status=='penerimaan') $item['book_status'] = 'q_email';

		$item['book_date_email_confirmed'] = date('Y-m-d', strtotime($item['book_date_email_confirmed']));
		
		if( $this->sm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function save_available()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		

		$item = $this->input->post('inp');
		$id   = $this->input->post('id');  
		
		$dt = $this->sm->get_email_confirmed_and_available($id)->row(); 
		if($dt->book_status=='q_email') $item['book_status'] = 'r_ketersediaan';

		$item['book_date_available'] = date('Y-m-d', strtotime($item['book_date_available']));
		
		if( $this->sm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function delete()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		
		if( $this->sm->delete($id) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menghapus Data'));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/

	public function aktivasi()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		 
		$id   							= $this->input->post('id');
		$pass   						= $this->input->post('pass');
		$item['par_active'] = '1';
		$where 							= "par_participantnumber='$id' and par_password_plain='$pass'";
		if( $this->sm->aktivasi($where, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
} 

?>