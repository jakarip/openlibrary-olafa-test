<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Exports extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Export_Model', 'em', TRUE);
		$this->load->model('Participant_Model', 'dm', TRUE);
		$this->load->model('Participant_Registration_Model', 'sreg', TRUE);
		$this->load->model('Periode_Model', 'pm', TRUE);
		$this->load->model('Settings_Model', '', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/export/index';
		$data['title']	= 'Export to I-Gracias';		
		$data['icon']	= 'icon-lock';
		
		$this->load->helper('form');
		$data['jns']	= $this->cm->form_pin();
		$data['track']	= $this->pm->getAllOrderByDate()->result();
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{ 
		if(!$this->input->is_ajax_request()) return false;
		
		 
		$columns = array(
			array( 'db' => 'sreg_id', 'dt' => 0 ), 
			array( 'db' => 'sreg_sync_status', 'dt' => 1 ),
			array( 'db' => 'par_fullname', 'dt' => 2 ),
			array( 'db' => 'school_name', 'dt' => 3 ),  
			array( 'db' => 'sreg_choice_prodi', 'dt' => 4 ),
			array( 'db' => 'sreg_choice_date', 'dt' => 5 ),
			array( 'db' => 'sreg_sync_date', 'dt' => 6 )
		); 
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();	
		
		$date_choice 		= $this->input->post('date_choice'); 
		$date_sync 			= $this->input->post('date_sync'); 
		$track 				= $this->input->post('track');  

		// print_r($_POST);
		
		if(empty($param['where'])) 		$param['where'] = "WHERE sreg_status_pass='Y'";
		else $param['where'] .= "AND sreg_status_pass='Y'";
		 
		if(!empty($date_choice)) $param['where'] .= " AND (sreg_choice_date = '".y_convert_date($date_choice,'Y-m-d')."') "; 
		if(!empty($date_sync)) $param['where'] .= " AND (sreg_sync_date = '".y_convert_date($date_sync,'Y-m-d')."') "; 
		if($track!="0") $param['where'] .= " AND (pin_id_periode = '$track') ";  

		// print_r($param);
		
		// echo "<pre>";
		// print_r($param);
		// echo "</pre>";
		$result = $this->em->dtquery($param)->result();
		$filter = $this->em->dtfiltered();
		$total	= $this->em->dtcount();
		$output = $this->datatables->output($total, $filter); 
		foreach($result as $row)
		{
			if($row->sreg_sync_status == '0')
			{
				$label 		= '<span class="label label-danger">FALSE</span>'; 
				$checkbox 	= '<input type="checkbox" class="chk" value="'.$row->sreg_id.'">';
				$export 	= '';
			}
			else {
				$label = '<span class="label label-success">TRUE</span>'; 
				$checkbox 	= '<input type="checkbox" class="chks" value="'.$row->sreg_id.'" disabled>';
				$export 	= '<a href="javascript:copy('.$row->sreg_id.')" title="Export to change data" class="btn btn-xs btn-icon btn-warning"><i class="icon-database-edit2"></i></a>';
			}
			 
			$rows = array (
				$checkbox,
				$label, 
				$row->par_fullname."<br>".$row->par_participantnumber,
				$row->school_name."<br>".$row->par_department,  
				$row->prodi_name."<br>".$row->periode_name.' / '.$row->periode_track_type,
				(empty($row->sreg_choice_date)?'':date('m/d/Y', strtotime($row->sreg_choice_date))),
				(empty($row->sreg_sync_date)?'':date('m/d/Y', strtotime($row->sreg_sync_date))), 
				$export
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	public function insert()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('username')) return false;
		
		$user = y_info_login();
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$type_pin = $this->input->post('type_pin');
		$desc = $this->input->post('desc');
		
		$chk = $this->dm->getby(array('student_username' => $username))->row();
		if(!empty($chk))
		{
			echo json_encode(array('status' => 'error;', 'text' => 'Username Duplicate'));
			return false;	
		}
		
		$item = array('student_date' => date('Y-m-d H:i:s'),
					  'student_username' => $username,
					  'student_password_plain' => $password,
					  'student_password' => '$USMBB$'.substr(sha1(md5(md5($password))), 0, 50),
					  'student_by' => $user->admin_id,
					  'student_type_pin' => $type_pin,
					  'student_desc' => $desc);

		
		$id = $this->dm->add($item);
		if( !empty($id) )
		{
			$this->Student_Registration_Model->add(array('sreg_id_student' => $id));
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		}
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
		
		$item['student_password'] = '$USMBB$'.substr(sha1(md5(md5($item['student_password_plain']))), 0, 50);
		
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
	
	public function doexport()
	{
		/*
		SELECT * FROM PARTICIPANT a
		LEFT JOIN COURSEINVOICE b ON a.PARTICIPANTID = b.PARTICIPANTID
		LEFT JOIN INVOICEDETAILS c ON b.COURSEINVOICEID = c.COURSEINVOICEID
		LEFT JOIN INVOICEITEM d ON c.INVOICEITEMID = d.INVOICEITEMID
		LEFT JOIN LISTOFINVOICEITEM e ON d.LISTOFINVOICEITEMID = e.LISTOFINVOICEITEMID
		WHERE a.PARTICIPANTID = '1819342'
		*/
		
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('ta')) return false;
		if(!$this->input->post('id')) return false; 
		
		$ta = $this->input->post('ta');
		$id = $this->input->post('id');
		
		// print_r($_POST);
		
		$component_dbs = $this->db->query("SELECT * FROM ms_component ORDER BY component_id")->result();
		$component = array();		
		foreach($component_dbs as $cdb)
			$component[$cdb->component_name] = $cdb->component_id;
		
		$result = $this->db->query("SELECT * FROM participant 
									 LEFT JOIN participant_registration ON par_id = sreg_id_participant 
									 LEFT JOIN pin ON pin_id = sreg_id_pin 
									 LEFT JOIN periode ON periode_id = pin_id_periode 
									 LEFT JOIN ms_prodi ON sreg_choice_prodi = prodi_id
									 LEFT JOIN ms_kec ON par_id_kec = kec_id
									 WHERE sreg_id = '$id' AND sreg_status_pass = 'Y' AND sreg_choice IS NOT NULL AND sreg_choice_prodi IS NOT NULL")->row();
									 
		if(empty($result))
		{
			echo json_encode(array('status' => 'error;', 'text' => 'Data Pendaftar Tidak Ditemukan'));
			return false;
		}
		
		$return['participant'] = array('PARTICIPANTID' => $ta.$result->par_id,
									   'SELECTIONPATHID' => $result->periode_id,
									   'FULLNAME' => $result->par_fullname,
									   'PARTICIPANTNUMBER' => $result->par_participantnumber,
									   'STUDYPROGRAMID' => $result->prodi_id_igracias,
									   'PAYMENTSTATUS' => 'BELUM LUNAS',
									   'STUDENTSCHOOLYEAR' => $ta,
									   'PASSWORD' => $result->par_password_plain,
									   'REGISTRATIONBY' => 'PMB',
									   'INPUTDATE' => date('Y-m-d H:i:s'),
									   'FINNET' => 'TIDAK');	  
										   
		$return['additional'] = array('PARTICIPANTID' => $ta.$result->par_id,
									   'SEX' => ($result->par_gender == 'L' ? 'PRIA' : 'WANITA'),
									   'BIRTHDATE' => $result->par_birthdate,
									   'BIRTHPLACE' => $result->par_birthplace,
									   'ORIGINADDRESS' => $result->par_address,
									   'ORIGINDISTRICTCITY' => $result->kec_kab_id,
									   'ORIGINZIPCODE' => $result->par_postcode,
									   'ORIGINPROVINCE' => $result->kec_prov_id,
									   'IDENTITYCARDNUMBER' => $result->par_nik,
									   'MARITALSTATUS' => 'BELUM MENIKAH');
									   
		$invoice = $this->invoicedetails($result->sreg_prodi, $result->sreg_choice_prodi, $ta, $result->par_id);
		
		$return['courseinvoice'] = array('COURSEINVOICEID' => $ta.$result->par_id,
									   'PARTICIPANTID' => $ta.$result->par_id,
									   'SEMESTER' => '1',
									   'SCHOOLYEAR' => $ta,
									   'TOTALCOSTINVOICE' => $invoice['total'],
									   'SETTLEDSTATUS' => 'BELUM LUNAS' );  
		
		$return['invoicedetails'] = $invoice['invoicedetails'];
									   
		$return['users'] = array('USERID' => '999'.$ta.$result->par_id,
								 'NAME' => $result->par_fullname,
								 'EMAIL' => $result->par_email,
								 'USERNAME' => $result->par_participantnumber,
								 'PASSWORD' => md5($result->par_password_plain),
								 'INPUTBY' => '1',
								 'INPUTDATE' => date('Y-m-d H:i:s'),
								 'ACTIVATEDSTATUS' => 'yes');
									   
		$return['usermapping'] = array('USERID' => '999'.$ta.$result->par_id,
									   'USERGROUPID' => '2',
									   'INPUTBY' => '1',
									   'INPUTDATE' => date('Y-m-d H:i:s') );
									   
		// echo "<pre>";	
		// print_r($return);
		// echo "</pre>";
		
		$json = json_encode($return);
		$result = $this->curl_api_igracias($json);
		$res = json_decode($result, true);
		// $res['status'] = true;
		if($res and !empty($res) and isset($res['status']))
		{
			if($res['status'] == 'true')
			{
				$this->sreg->edit($id, array('sreg_sync_date' => date('Y-m-d H:i:s'), 'sreg_sync_status' => '1'));
				echo json_encode(array('status' => 'ok;', 'text' => ''));
			}				
			else
				echo json_encode(array('status' => 'error;', 'text' => $res['msg']));
		}
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Tidak Dapat Menghubungi API'));
	}
	
	private function invoicedetails($data, $prodi_choose, $ta, $student_id)
	{
		$json = json_decode($data, true);
		$return = array();
		$total = 0;
		
		if(!empty($json))
		{
			foreach($json as $keyjs => $js)
			{
				if($js['prodi'] == $prodi_choose)
				{
					$fees = $json[$keyjs]; unset($fees['prodi']);
					
					foreach($fees as $feeid => $fee)
					{
						$return[] = array('INVOICEITEMID' => $ta.$feeid,
										  'COURSEINVOICEID' => $ta.$student_id,
										  'INVOICENUM' => '1');
					
						$f = $fee['fee'] + ($fee['n'] * 1000000 * $fee['component_custom']);
						$total += $f;
					}
				}
			}
		}
		
		
		return array('invoicedetails' => $return, 'total' => $total);		
	}

    public function doexport_master()
    {
        /* ms_track => SELECTIONPATHS */

        if(!$this->input->is_ajax_request()) return false;
        //if(!$this->input->post('ta')) return false;
        //if(!$this->input->post('id')) return false;

        //$ta = $this->input->post('ta');
        //$id = $this->input->post('id');

        // print_r($_POST);

        $track_dbs = $this->db->query("SELECT * FROM ms_track WHERE track_status = 1 ORDER BY track_id ASC")->result();
        $tracks = array();
        foreach($track_dbs as $track)
            $tracks[] = array('id' => $track->track_id, 'name' => strtoupper($track->track_name) );

        $return['track'] = $tracks;

        $json = json_encode($return); //echo $json;
        $result = $this->curl_api_igracias($json); //echo '--aa--'; echo $result;
        $res = json_decode($result, true);
         $res['status'] = true;
        if($res and !empty($res) and isset($res['status']))
        {
            if($res['status'] == 'true')
            {
                echo json_encode(array('status' => 'ok;', 'text' => $res['msg']));
            }
            else
                echo json_encode(array('status' => 'error;', 'text' => $res['msg']));
        }
        else
            echo json_encode(array('status' => 'error;', 'text' => 'Tidak Dapat Menghubungi API'));
    }
	
	private function curl_api_igracias($json)
	{
		$url_api =  $this->Settings_Model->getvalue('url_api'); //echo $url_api; echo realpath(APPPATH.'libraries').'/cacert.pem';
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL,$url_api);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, array("json"=> urlencode($json)) );

        //curl_setopt($ch, CURLOPT_CAINFO, realpath(APPPATH.'./libraries').'/cacert.pem');
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		//--- debug -----
        /*curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        //You can then read it after curl has done the request:

        $result = curl_exec($ch);
        if ($result === FALSE) {
            printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
                htmlspecialchars(curl_error($ch)));
        }

        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);

        echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";*/
		
		$result = curl_exec($ch);
		curl_close($ch);
		
		return $result;
	}
}

?>