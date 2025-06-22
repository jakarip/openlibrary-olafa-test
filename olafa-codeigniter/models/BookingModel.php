<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingModel extends CI_Model {  
	 
	private $table 	= 'booking';
	private $id		= 'bk_id'; 
	 
	function __construct()
	{
		parent::__construct();
		$this->mn = $this->load->database('menu', TRUE);
	}  
	
	function getall()
	{	
		$this->mn->order_by("room_id","asc");
		return $this->mn->get($this->table); 
	}	
	
	// function getbyid($id)
	// {
	// 	$this->mn->where($this->id, $id);
	// 	return $this->mn->get($this->table);
	// } 
	
    public function getbyid($id){
        return $this->mn->query("select * from telu8381_room.booking left join telu8381_room.room on room_id=bk_room_id where bk_id='$id'");
    } 
	
    public function getroombyid($id){  
        return $this->mn->query("select * from telu8381_room.room where room_id='$id'");
    }   

    public function save($data){
        return $this->mn->insert($this->table, $data);
    }
	
	function add($item)
	{
		$this->mn->insert($this->table, $item);
		return $this->mn->insert_id();
	}
	
	function addDummy($item)
	{
		$this->mn->insert('booking_copy', $item);
		return $this->mn->insert_id();
	}
	
	function addBookingMember($item)
	{
		return $this->mn->insert('booking_member', $item);
	}
	
	function addNotificationMobile($item)
	{
		$this->mn->insert('telu8381_openlibrarys.notification_mobile', $item);
		return $this->mn->insert_id();
	}  
	
	function getTokenNotificationMobile($id){ 
        return $this->mn->query("select master_data_token from telu8381_openlibrarys.member where id='$id'");
    }   
	
	function edit($id, $item)
	{
		$this->mn->where($this->id, $id);
		return $this->mn->update($this->table, $item);
	}

    public function update($where, $data){
        $this->mn->update($this->table, $data, $where);
        return $this->mn->affected_rows();
    }    
	
    public function holiday(){
        return $this->mn->query("select holiday_date from telu8381_openlibrarys.holiday where holiday_date >= DATE_FORMAT(NOW(),'%Y-%m-%d') order by holiday_date");
    }    
	
    public function member($data){
        return $this->mn->query("select * from telu8381_openlibrarys.member where LOWER(master_data_user) like '%$data%' or LOWER(master_data_fullname) like '%$data%' and status='1' and member_type_id in (1,2,3,4,5,6,7,9,10,25) order by master_data_fullname");
    } 
	
    public function getMobilePhone(){
        return $this->mn->query("select bk_mobile_phone from telu8381_room.booking where bk_username='".$this->session->userdata('username')."' order by bk_id desc limit 1");
    }
	
    public function checkExistBooking($startdate,$enddate,$room_id){
	 
        return $this->mn->query("select * from telu8381_room.booking where bk_room_id='$room_id' and bk_status!='Cancel' 
		and bk_status!='Not Approved' and bk_status!='Not Attend'
		and ((bk_startdate<'$startdate' and bk_enddate>'$startdate')  
		or (bk_startdate<'$enddate' and bk_enddate>'$enddate') 
		or (bk_startdate>'$startdate' and bk_startdate<'$enddate')
		or (bk_enddate>'$startdate' and bk_enddate<'$enddate'))
		union
		select * from telu8381_room.booking where bk_room_id='$room_id' and bk_status!='Cancel' 
		and bk_status!='Not Approved' and bk_status!='Not Attend'
		and bk_startdate='$startdate' and bk_enddate='$enddate'");
    } 
    public function checkExistBookingDummy($startdate,$enddate,$room_id){
		echo "";
        return $this->mn->query("select * from telu8381_room.booking_copy where bk_room_id='$room_id' and bk_status!='Cancel' 
		and bk_status!='Not Approved' and bk_status!='Not Attend'
		and ((bk_startdate<'$startdate' and bk_enddate>'$startdate')  
		or (bk_startdate<'$enddate' and bk_enddate>'$enddate') 
		or (bk_startdate>'$startdate' and bk_startdate<'$enddate')
		or (bk_enddate>'$startdate' and bk_enddate<'$enddate'))
		union
		select * from telu8381_room.booking_copy where bk_room_id='$room_id' and bk_status!='Cancel' 
		and bk_status!='Not Approved' and bk_status!='Not Attend'
		and bk_startdate='$startdate' and bk_enddate='$enddate'");
    } 
	
	function getSchedule($room_id,$date)
	{   
		return $this->mn->query("select * from telu8381_room.booking 
		left join telu8381_openlibrarys.member on master_data_user=bk_username 
		left join telu8381_openlibrarys.t_mst_prodi on c_kode_prodi=master_data_course 
		where  bk_room_id='$room_id' and bk_startdate like '$date%' and bk_status!='Cancel' and bk_status!='Not Approved' and bk_status!='Not Attend'");
	}  
	
	function ExportRoom($where)
	{   
		$startdate = date("Y-m-01", strtotime("-3 months"));
		return $this->mn->query("select *,DATE_FORMAT(bk_startdate,'%Y-%m-%d') tanggal,DATE_FORMAT(bk_startdate,'%H:%i') starthour,DATE_FORMAT(bk_enddate,'%H:%i') endhour, bk_total member_name 
		from telu8381_room.booking  
		left join telu8381_openlibrarys.member on member.id=bk_memberid 
		left join telu8381_room.room on bk_room_id=room_id where bk_status='Attend' and bk_startdate between '".$startdate."' and '".date('Y-m-d')."' $where order by bk_startdate asc");
	}  
	
	
	function getBookingDetail($bk_id)
	{   
		return $this->mn->query("select * from telu8381_room.booking 
		left join telu8381_openlibrarys.member on master_data_user=bk_username 
		left join telu8381_openlibrarys.t_mst_prodi on c_kode_prodi=master_data_course 
		where  bk_id='$bk_id'");
	}  
	
	function getbyaprrovedid($id,$startdate,$enddate)
	{  
		return $this->mn->query("select * from telu8381_room.booking 
		left join telu8381_openlibrarys.member on master_data_user=bk_username 
		left join telu8381_openlibrarys.t_mst_prodi on c_kode_prodi=master_data_course 
		where  bk_room_id='$id' and bk_startdate between '$startdate' and '$enddate' and bk_status!='Cancel' and bk_status!='Not Approved' and bk_status!='Not Attend'");
	}  

    public function checkCountBookingRoomStatus(){
		return $this->mn->query("select count(*) total from booking where bk_status='Request' and bk_username='".$this->session->userdata('username')."'");
    }  

    public function checkBannedPerMonthStatus(){
		$date = date("Y-m");
		return $this->mn->query("select count(*) total from booking where bk_status='Not Attend' and bk_startdate like '$date%' and bk_username='".$this->session->userdata('username')."'");
    } 
	
	function getBlacklist()
	{   
		return $this->mn->query("select * from telu8381_room.blacklist where bl_username='".$this->session->userdata('username')."'");
	} 

    public function getMember($bk_id){
		return $this->mn->query("select * from telu8381_room.booking_member left join telu8381_openlibrarys.member on id =bm_userid where bm_bk_id='$bk_id'");
    }  

    public function getMemberByUsername($username){
		return $this->mn->query("select id from telu8381_openlibrarys.member where  master_data_user='$username'");
    } 

    public function getListNameMember($list){
		return $this->mn->query("SELECT
			group_concat(
				master_data_fullname
				ORDER BY
					master_data_fullname ASC SEPARATOR ', '
			) nama
			from telu8381_openlibrarys.member where  id in ($list)");
    }  

    public function editRequestToCancel(){
		return $this->mn->query("update telu8381_room.booking set bk_status='Cancel' where bk_status='Request' and bk_startdate < '".date('Y-m-d')."'");
    } 
}
