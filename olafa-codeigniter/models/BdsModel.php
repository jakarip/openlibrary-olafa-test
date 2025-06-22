<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BdsModel extends CI_Model {  
	 
	private $table 	= 'telu8381_openlibrarys.book_delivery_service';
	private $id		= 'bds_id'; 
	 
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
        return $this->mn->query("select *, m.id memberid from telu8381_openlibrarys.book_delivery_service left join telu8381_openlibrarys.member m on m.id=bds_idmember where bds_id='$id'");
    } 
	
    function checkEksemplar($item,$barcode,$memberid){ 
        return $this->mn->query("select ks.id from telu8381_openlibrarys.rent r 
		left join telu8381_openlibrarys.knowledge_stock ks on ks.id=knowledge_stock_id  
		left join telu8381_openlibrarys.knowledge_item kit on kit.id=knowledge_item_id  
		where ks.code='$barcode' and kit.code='$item' and member_id='$memberid' and ks.status='2'");
    } 
	
    public function getDataExport($where){
        return $this->mn->query("select  bds.*,master_data_user,master_data_fullname,master_data_number,
		GROUP_CONCAT(bdsb_item_code SEPARATOR ' / ') item_code, 
		GROUP_CONCAT(bdsb_stock_code SEPARATOR ' / ') stock_code,
		count(*) total_buku,
		DATE_FORMAT(bds_createdate,'%Y-%m-%d') tanggal from telu8381_openlibrarys.book_delivery_service bds
		left join telu8381_openlibrarys.member on member.id=bds_idmember
		left join telu8381_openlibrarys.book_delivery_service_book book on bdsb_idbds=bds_id
		left join telu8381_openlibrarys.knowledge_item kit on kit.id=bdsb_item_id $where
		group by bds_id order by bds_createdate desc");
    } 
	
    public function getroombyid($id){  
        return $this->mn->query("select * from telu8381_room.room where room_id='$id'");
    }   
	
    public function getBdsStatus($id){  
        return $this->db->query("select * from book_delivery_service_status left join book_delivery_service on bds_id=bdss_idbds where bdss_idbds='$id' order by bdss_id desc");
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
		return $this->mn->insert('telu8381_openlibrarys.notification_mobile', $item);
	} 
	
    function getTokenNotificationMobile($id){ 
        return $this->mn->query("select master_data_token from telu8381_openlibrarys.member where id='$id'");
    }   

	function addBdsStatus($item)
	{
		return $this->mn->insert('telu8381_openlibrarys.book_delivery_service_status', $item);
	}

	function edit($id, $item)
	{
		$this->mn->where($this->id, $id);
		return $this->mn->update($this->table, $item);
	}

	function editBook($idbds,$item_code, $item)
	{
		$this->mn->where('bdsb_idbds', $idbds);
		$this->mn->where('bdsb_item_code', $item_code); 
		return $this->mn->update('telu8381_openlibrarys.book_delivery_service_book', $item);
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
