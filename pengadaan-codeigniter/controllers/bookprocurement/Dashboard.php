<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		// $this->load->model('Participant_Model', 'par', TRUE);
		// $this->load->model('Participant_Registration_Model', 'dm', TRUE);
        $this->load->model('bookprocurement/Dashboard_Model', 'pm', TRUE);
        // $this->load->model('Pin_Model', 'pin', TRUE);
        $this->load->model('Common_Model', 'cm', TRUE); 
		
		if (!$this->session->userdata('user_login_apps')) redirect('login');
	}

    public function index($filter="")
    {
        $this->load->helper('form');
		
		if($filter!=''){ 
			$temp = explode('_-_',$filter);

			$date1 = y_convert_date($temp[0], 'Y-m-d');
			$date2 = y_convert_date($temp[1], 'Y-m-d 23:59:59');
			$option = "(book_date_prodi_submission between '$date1' and '$date2')";
            $data['filter'] = $filter;
		}
		else {
			$date1 = date('Y').'-01-01';
			$date2 = date('Y-m-d 23:59:59');
            
            $data['filter'] = '01-01-'.date('Y')."_-_".date('d-m-Y');
		}
		
		$data['date1'] = y_convert_date($date1, 'd/m/Y');;
		$data['date2'] = y_convert_date($date2, 'd/m/Y');
		
        $data['view'] 	= 'bookprocurement/dashboard/index';
		$dt = $this->pm->total_pengajuan($date1,$date2)->row();
		$data['rerata_penerimaan'] = $this->pm->rerata_hari_status_penerimaan($date1,$date2)->row()->rerata_penerimaan;
        //$data['check']  = $this->par->check($this->session->userdata('participant_login_info')->par_id);
        //$data['check_active'] = $this->dm->checkPinActive($this->session->userdata('participant_login_info')->par_id);
 
        $data['total'] = $dt;  

        
		$dt = $this->pm->total_pengajuan_telupress($date1,$date2)->row();

        
        $data['total_telupress'] = $dt;  

        $this->load->view('frontend/tpl', $data);
    }

    public function faculty($filter="",$faculty="",$prodi="")
    {
        $this->load->helper('form');
		
		if($filter!=''){ 
			$temp = explode('_-_',$filter);

			$date1 = y_convert_date($temp[0], 'Y-m-d');
			$date2 = y_convert_date($temp[1], 'Y-m-d 23:59:59');
			$option = "(book_date_prodi_submission between '$date1' and '$date2')";
            $data['filter'] = $filter;
		}
		else {
			$date1 = date('Y').'-01-01';
			$date2 = date('Y-m-d 23:59:59');
 
            $data['filter'] = '01-01-'.date('Y')."_-_".date('d-m-Y');
		} 
		$data['date1'] = y_convert_date($date1, 'd/m/Y');
		$data['date2'] = y_convert_date($date2, 'd/m/Y');
		$data['facid'] = $faculty;
		$data['prodiid'] = $prodi; 
        $data['view'] 	= 'bookprocurement/dashboard/faculty';

		$data['faculty'] = $this->pm->getFaculty()->result();
        if($faculty=='all' || $faculty=='') {
            foreach($data['faculty'] as $row){

                $dt = $this->pm->total_pengajuan_faculty($date1,$date2,$row->c_kode_fakultas)->row();
                $data['total'][$row->c_kode_fakultas] = $dt; 
                $data['rerata_penerimaan'][$row->c_kode_fakultas] = $this->pm->rerata_hari_status_penerimaan_faculty($date1,$date2,$row->c_kode_fakultas)->row()->rerata_penerimaan;
            } 
        }
        else {
            $data['prodi'] = $this->pm->getProdiByFacId($faculty)->result();  

            if($prodi=='all' || $prodi=='') {
                $prod = $data['prodi'];
                $data['prod'] =  $data['prodi'];
            }
            else {
                $prod = $this->pm->getProdiByProdId($prodi)->result();  
                $data['prod'] =  $prod;
            }

            foreach($prod as $row){
                $dt = $this->pm->total_pengajuan_prodi($date1,$date2,$row->C_KODE_PRODI)->row();
                $data['total'][$row->C_KODE_PRODI] = $dt; 
                $data['rerata_penerimaan'][$row->C_KODE_PRODI] = $this->pm->rerata_hari_status_penerimaan_prodi($date1,$date2,$row->C_KODE_PRODI)->row()->rerata_penerimaan;
            } 
        } 




        $this->load->view('frontend/tpl', $data);
    }

    public function getProdi()
    {
		if(!$this->input->is_ajax_request()) return false;

        $facultyId = isset($_POST['facultyId']) ? $_POST['facultyId'] : '';

        
		$data['prodi'] = $this->pm->getProdiByFacId($facultyId)->result();  

        echo json_encode($data);
    }

	
	public function indexx()
	{
	    // if($this->session->userdata('pin'))
	    // {
        //     $check = $this->dm->check($this->session->userdata('participant_login_info')->par_id);

        //     if($check['qr']) {
        //         if($check['biodata']) {
        //             $pin_step = $this->session->userdata('pin_step');
        //             $pin_type = $this->session->userdata('pin_type');

        //             $c = 'reg/'.$this->cm->get_form_step('url', $pin_type, $pin_step);

        //             redirect($c);
        //         } else {
        //             redirect('pendaftaran/biodata');
        //         }
        //     } else {
        //         redirect('questionnaire');
        //     }
        // }
	    // else
        // {
            $pin = $this->pin->getPinByUser($this->session->userdata('participant_login_info')->par_id)->row();

            if(empty($pin)) {
                redirect('pin_reg');
            } else {
                redirect('pin');
            }
            /*$reg = $this->pin->getPinActiveByUser($this->session->userdata('participant_login_info')->par_id)->row();

            if(empty($reg)) {
                $pin = $this->pin->getPinByUser($this->session->userdata('participant_login_info')->par_id)->row();

                if(empty($pin)) {
                    redirect('pin_reg');
                } else {
                    redirect('pin_reg/validate');
                }
            } else {
                $check = $this->dm->check($this->session->userdata('participant_login_info')->par_id);

                if($check['qr']) {
                    if($check['biodata']) {
                        $step = ($reg->sreg_step == 1) ? 2 : $reg->sreg_step;

                        $item = array(
                            'pin' => $reg->pin_transaction_number,
                            'pin_type' => $reg->periode_track_type,
                            'pin_step' => $step,
                            'pin_print' => $reg->sreg_status_print,
                        );

                        $c = 'reg/'.$this->cm->get_form_step('url', $item['pin_type'], $item['pin_step']);

                        $this->session->set_userdata($item);

                        redirect($c);
                    } else {
                        redirect('pendaftaran/biodata');
                    }
                } else {
                    redirect('questionnaire');
                }
            }*/
        // }
	}

    public function info()
    {
        $data['view'] 	= 'frontend/dashboard/index';
        $data['check']  = $this->dm->check($this->session->userdata('participant_login_info')->par_id);

        $this->load->view('frontend/tpl', $data);
    }




	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
}

?>