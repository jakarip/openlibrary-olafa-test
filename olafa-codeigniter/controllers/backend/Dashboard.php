<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	
	function __construct() {
        parent::__construct(); 		
		if(!$this->session->userdata('login')) redirect(url_admin(), 'refresh');

		$this->load->model('Dashboardmodel','dash');
        $this->load->helper('custom_helper');
    }
	
	public function index()
	{	 
        $data['widgetPengadaan'] = $this->dash->widgetPengadaanCounter();

		$data['startDate'] = date('Y/m/01');
		$data['endDate'] = date('Y/m/t');

		$data['menu'] = "backend/dashboard";
		$this->load->view('backend/theme',$data);
	}
	
	public function size()
	{
		$data['menu'] = "backend/dashboard";
		$this->load->view('backend/theme',$data);
	}
	
	public function account($aa="")
	{
		$data['menu'] = "backend/dashboard";
		$this->load->view('backend/theme',$data);
	}
	
	public function home()
	{
		$data['menu'] = "backend/dashboard";
		$this->load->view('backend/theme',$data);
	}


    public function daterange(){
         $month = array( '1'=> 'Jan',
                    '2'=> 'Feb',
                    '3'=> 'Mar',
                    '4'=> 'Apr',
                    '5'=> 'Mei',
                    '6'=> 'Jun',
                    '7'=> 'Jul',
                    '8'=> 'Agu',
                    '9'=> 'Sep',
                    '10'=> 'Okt',
                    '11'=> 'Nov',
                    '12'=> 'Des');
        
        $first = $_POST['first'];
        $second = $_POST['second'];
        $d      = new DateTime($first); 
         
        $date1= explode("/",$first);
        $date2= explode("/",$second);
            
        $label 			= array();
        $income 		= array();
        $outcome 		= array();

        
        if ($_POST['status']!='default'){
            if ($date1[0]!=$date2[0]){
                
                $awals  = intval($date1[0]);
                $akhirs = intval($date2[0]); 
                
                for ($i=$awals;$i<=$akhirs;$i++){
                    if ($i==$awals){
                        $awal   = $date1[0]."-".$date1[1]."-".$date1[2]." 00:00:00";
                        $akhir  = $date1[0]."-12-31 23:59:59";
                    }
                    else if ($i==$akhirs){
                        $awal =  $date2[0]."-01-01 00:00:00";
                        $akhir = $date2[0]."-".$date2[1]."-".$date2[2]." 23:59:59";
                    }
                    else {
                        $awal =  $i."-01-01 00:00:00";
                        $akhir = $i."-12-31 23:59:59";
                    } 
                    
                    $data = $this->dash->dataStatistikKeuanganByMonth($awal,$akhir);
                    $label[]    =  $i;
                    $income[]   =  $data->income_penjualan;
                    $outcome[]  =  $data->outcome_peg_gaji + $data->outcome_peg_kasbon + $data->outcome_peg_debt;
                }
            }
            else {
                if ($date1[1]!=$date2[1]){
                    $awals  = intval($date1[1]);
                    $akhirs = intval($date2[1]); 
                    for ($i=$awals;$i<=$akhirs;$i++){
                        $m = sprintf("%02d", $i);
                        if ($i==$awals){
                            $awal   = $date1[0]."-".$date1[1]."-".$date1[2]." 00:00:00";
                            $d      = new DateTime($first); 
                            $akhir  = $d->format( 'Y-m-t' )." 23:59:59";
                        }
                        else if ($i==$akhirs){
                            $awal =  $date2[0]."-".$date2[1]."-"."01"." 00:00:00";
                            $akhir = $date2[0]."-".$date2[1]."-".$date2[2]." 23:59:59";
                        }
                        else {
                            $awal   =  $date1[0]."-".$m."-"."01"." 00:00:00";
                            $d      = new DateTime($awal); 
                            $akhir  = $d->format( 'Y-m-t' )." 23:59:59";
                        } 
                        //echo $awal." ".$akhir."<br>";
                        
                        $data = $this->dash->dataStatistikKeuanganByMonth($awal,$akhir);
                        $label[]    =  $month[$i]." ".$date1[0];
	                    $income[]   =  $data->income_penjualan;
    	                $outcome[]  =  $data->outcome_peg_gaji + $data->outcome_peg_kasbon + $data->outcome_peg_debt;
                    }
                }
                else {
                    $awals  = intval($date1[2]);
                    $akhirs = intval($date2[2]); 
                    
                    for ($i=$awals;$i<=$akhirs;$i++){ 
                        $d = sprintf("%02d", $i);
                        $awal   = $date1[0]."-".$date1[1]."-".$d." 00:00:00";
                        $akhir = $date1[0]."-".$date1[1]."-".$d." 23:59:59";
                        
                        $data = $this->dash->dataStatistikKeuanganByMonth($awal,$akhir);
                        $label[]    =  $d." ".$month[intval($date1[1])]." ".$date1[0];
                        
	                    $income[]   =  $data->income_penjualan;
    	                $outcome[]  =  $data->outcome_peg_gaji + $data->outcome_peg_kasbon + $data->outcome_peg_debt;
                    }
                }
                     
            }
        }
        else { 
            for ($i=1;$i<=7;$i++){ 
                $d = sprintf("%02d", $i);
                if ($i==1) $awal    = date('Y-m-d', strtotime('-6 days'));
                $akhir  = $awal." 23:59:59";
                $data   = $this->dash->dataStatistikKeuanganByMonth($awal." 00:00:00",$akhir);
                $dates  = explode("-",$awal);
                $awal   = date('Y-m-d', strtotime($awal . ' +1 day'));
                
                $label[]    =  $dates[2]." ".$month[intval($dates[1])]." ".$dates[0];
                $income[]   =  $data->income_penjualan;
                $outcome[]  =  $data->outcome_peg_gaji + $data->outcome_peg_kasbon + $data->outcome_peg_debt;
            }
        } 
        
        $label2 = array();
        $result = array();

		$dt = array();
		array_push($dt,$label,$income,$outcome,$label2,$result);
		echo json_encode($dt);
    }

    public function notification(){
        $data = $this->dash->notifier();
        if($data->lowStockeQual != 0 ){
            $html = '<li>
                        <i class="fa fa-cubes p-r-10 f-18 c-orange"></i> <small>'.$data->lowStockeQual.' item stock mencapai batas minimal</small>
                    </li>';
            echo $html;
        }

        if($data->lowStockLess != 0 ){
            $html = '<li>
                        <i class="fa fa-cubes p-r-10 f-18 c-red"></i> <small>'.$data->lowStockLess.' item stock dibawah jumlah minimal</small>
                    </li>';
            echo $html;
        }

        if($data->expiredPoReminderInTwo != 0 ){
            $html = '<li>
                        <i class="fa fa-shopping-cart p-r-10 f-18 c-orange"></i> <small>'.$data->expiredPoReminderInTwo.' transaksi PO akan segera expired</small>
                    </li>';
            echo $html;
        }

        if($data->expiredPoReminderTom != 0 ){
            $html = '<li>
                        <i class="fa fa-shopping-cart p-r-10 f-18 c-red"></i> <small>'.$data->expiredPoReminderTom.' transaksi PO akan expired esok hari</small>
                    </li>';
            echo $html;
        }
    }
}
