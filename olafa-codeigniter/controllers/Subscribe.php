<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Subscribe extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->model('Member_Model', 'dm', TRUE); 
		$this->load->model('Usermodel', 'um', TRUE); 
        $this->load->model('Common_Model', 'cm', TRUE);
        $this->load->model('Subscribe_Model', 'sm', TRUE);

		if (!$this->session->userdata('user_login')) redirect('login');
	}
	
	public function index()
	{
        $this->load->helper('form');

        $data['view'] = 'frontend/subscribe/index';  
        $iuser = $this->session->userdata();   
        $data['subs'] = $this->um->getsubscribe(array('subscribe_id_member' => 	$iuser['member_id']),array('0','1','2'))->row();
  
        $subscribe = subscribes();
        foreach($subscribe[$iuser['usergroup']] as $key=> $row){ 
            $data['member'][$key] = $key.' Bulan ('.y_num_idr($row).')'; 
        } 

		$this->load->view('frontend/tpl', $data);
	}

    public function json()
    {
        if(!$this->input->is_ajax_request()) return false;

        $columns = array( 
            array( 'db' => 'subscribe_transaction', 'dt' => 0 ),
            array( 'db' => 'subscribe_month', 'dt' => 1 ),
            array( 'db' => 'subscribe_payment_date', 'dt' => 2 ),
            array( 'db' => 'subscribe_payment_code', 'dt' => 3 ),
            array( 'db' => 'subscribe_start_date', 'dt' => 4 ), 
            array( 'db' => 'subscribe_status', 'dt' => 5 ),
            array( 'db' => 'subscribe_id', 'dt' => 6 )
        );

        $this->datatables->set_cols($columns);
        $param	= $this->datatables->query(); 

        $iuser = $this->session->userdata();  
        $id = 	$iuser['member_id'];
        if(empty($param['where']))
            $param['where'] = " WHERE subscribe_id_member = '$id' ";
        else
            $param['where'] .= " AND (subscribe_id_member = '$id') ";

        $result = $this->sm->dtquery($param)->result();
        $filter = $this->sm->dtfiltered();
        $total	= $this->sm->dtcount();
        $output = $this->datatables->output($total, $filter);

        foreach($result as $row)
        {
            $btn = "";
            if($row->subscribe_status == '1')
            {
                $label = '<label class="label label-success"><strong>Aktif</strong></label>';
            }
            else if($row->subscribe_status == '0')
            {
                $label = '<label class="label label-danger"><strong>Belum Konfirmasi Pembayaran</strong></label>';
                $btn = '<a href="javascript:validate_online('.$row->subscribe_id.',\''.$row->subscribe_transaction.'\')">Konfirmasi Pembayaran</a>';
            }
            else if($row->subscribe_status == '2')
            {
                $label = '<label class="label label-primary"><strong>Menunggu Validasi Admin</strong></label>';
            }
            else if($row->subscribe_status == '3')
            {
                $label = '<label class="label label-default"><strong>Non Aktif</strong></label>';
            }  

            if($row->subscribe_status == '0'){
                $payment ='<label class="label label-success" style="font-size:16px"><strong>'. y_num_idr($row->subscribe_payment_code).'</strong></label>';
            }
            else 
            $payment ='<label class="label label-default" style="font-size:16px"><strong>'. y_num_idr($row->subscribe_payment_code).'</strong></label>';

            $rows = array (
                $row->subscribe_transaction,
                $row->subscribe_month.' Bulan <br><span class="text-muted">('.y_num_idr($row->subscribe_payment).')</span>',
                ($row->subscribe_payment_date!=""?y_date_text($row->subscribe_payment_date):''),
                $payment,
                ($row->subscribe_start_date!=""?convert_format_dates($row->subscribe_start_date).' - '.convert_format_dates($row->subscribe_end_date):''), 
                $label,
                $btn
            );

            $output['data'][] = $rows;
        }

        echo json_encode( $output );
    }

    public function save()
    {
        if(!$this->input->is_ajax_request()) return false;
        if(!$this->input->post('subscribes')) return false;

        $iuser = $this->session->userdata();   

        $subscribe = subscribes();
        $id = $this->input->post('id');
        $data['subscribe_id_member'] = $id; 
        $data['subscribe_month'] = $this->input->post('subscribes');
        $data['subscribe_payment'] = $subscribe[$iuser['usergroup']][$this->input->post('subscribes')];
        $data['subscribe_date'] = date('Y-m-d H:i:s');

        $status = 'false';
        do{
            $code = rand(0,999);
            $code = sprintf("%04d", $code);
            $data['subscribe_payment_code'] 	= $data['subscribe_payment'] + $code;
            $payment =  $this->sm->checkPaymentCode($data['subscribe_payment_code'])->row();
            if(!$payment) $status='true'; 
        }
        while ($status=='false');
        
        
        
        $number 		= $this->sm->getTransactionNumber()->row(); 
        if($number){
            $temp = substr($number->max,-4,4);
            $temp = (int)$temp;
            $temp = $temp+1; 
            $data['subscribe_transaction'] 	= date('ymd').sprintf("%04d", $temp);
        }
        else 	$data['subscribe_transaction'] 	= date('ymd')."0001";

        if($this->sm->add($data)){
            // $this->send_email($pin_free);
            $member = $this->sm->member($id)->row();
            $data['code']   = $code;
            $data['name']   = ucwords(strtolower($member->master_data_fullname));
            $data['no']   = ucwords(strtolower($member->master_data_number));
            $content 	    = $this->load->view('email_template_request_berlangganan', $data, true);
            $subject 	    = "Berlangganan Keanggotaan Telkom University Open Library";  
            $state = SendEmail($member->master_data_email,$subject,$content,'Telkom University Open Library',ucwords(strtolower($member->master_data_fullname)));
            echo json_encode(array('status' => 'ok;', 'text' => ''));
        } 
        else
            echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data')); 
    } 

    
    public function save_validate()
    {
        if(!$this->input->is_ajax_request()) return false;
        if(!$this->input->post('id')) return false;
 
        $iuser = $this->session->userdata();   

        $id = $this->input->post('id'); 
        $no = $this->input->post('no'); 
        $data['subscribe_status'] = '2';
        $data['subscribe_payment_date'] = date('Y-m-d H:i:s'); 

        if($this->sm->edit($id,$data)){ 
            $jns_anggota = $this->cm->form_jenis_anggota();
            $state = SendEmail('library@telkomuniversity.ac.id','Validasi Pembayaran '.$jns_anggota[$iuser['usergroup']],'Validasi Pembayaran '.$jns_anggota[$iuser['usergroup']].' dengan No. Transaksi '.$no,'Telkom University Open Library','');
            echo json_encode(array('status' => 'ok;', 'text' => ''));
        } 
        else
            echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data')); 
    }
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/

    public function getpin()
    {
        if(!$this->input->is_ajax_request()) return false;

        $pin = $this->pin->getPinByPeriode($this->input->post('pin'))->result();

        echo json_encode($pin);
    }

    public function getpinaff()
    {
        if(!$this->input->is_ajax_request()) return false;

        $pin = $this->pin->getPinAffByPeriode($this->input->post('pin'))->result();

        echo json_encode($pin);
    }

    public function get_data_validate_online()
    {
        if(!$this->input->is_ajax_request()) return false;

        $pin = $this->dm->getValidateOnline($this->input->post('id'))->result();

        echo json_encode($pin);
    }

    public function send_email($pin)
    {
        $data['setting'] = y_load_setting();
        $data['pin']     = $pin;

        $body = $this->load->view('frontend/pin/email_request_template', $data, true); 
        echo y_send_email($this->session->userdata('participant_login_info')->par_email, '[NOMOR TRANSAKSI] Request Nomor Transaksi Online '.$data['setting']['website_name'], $body);
    }
}

?>