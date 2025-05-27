<?php
class ApiMobileModel extends CI_Model 
{
	
	private $table  = 'batik.member';
	private $id     = 'id';
	private $mn 	= '';
   
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->mn = $this->load->database('menu', TRUE);
		$this->masterdb = $this->load->database('oracle', true);
	}	

	//start==========scheduler==========
	function getScheduler($date)
	{ 
		$sql 	 = "Select count(*) total from sms_log where sms_type='scheduler' and sms_date='".$date."'"; 

		return $this->db->query($sql);
	} 
	function insertScheduler($date) 
	{  
		$sql = "insert into sms_log values('','scheduler','','','','','".$date."')";  
	 

		$this->db->query($sql);
	}

	function reminder_H_1() 
	{   
		$datetime = new DateTime('tomorrow');
		$sql 	 = "Select m.id memberid,ks.code,r.id,master_data_user,master_data_fullname,master_data_email from rent r 
					left join member m on r.member_id=m.id
					left join knowledge_stock ks on r.knowledge_stock_id=ks.id
					where return_date_expected='".$datetime->format('Y-m-d')."' and return_date is null"; 
					
		return $this->db->query($sql);
	} 
	
	function reminder_H() 
	{    
		$sql 	 = "Select m.id memberid,ks.code,r.id,master_data_user,master_data_fullname,master_data_email from rent r 
							left join member m on r.member_id=m.id 
							left join knowledge_stock ks on r.knowledge_stock_id=ks.id
							where return_date_expected='".date('Y-m-d')."' and return_date is null"; 
					
		return $this->db->query($sql);
	}

	//start==========bookdeliveryservice==========
	
	function getBdsFilter($typeid,$memberid,$limit,$offset,$search)
	{ 	  
		$where = ""; 

		if($typeid!="Semua" and $typeid!="All" and $typeid!=""){
			$where .= "AND bds_status='$typeid'";
		}      

		if($search) $where .= " AND (".implode(" OR ",$search).")";     
		return $this->db->query("
		select * from (select bds.*, GROUP_CONCAT(DISTINCT title ORDER BY title
        SEPARATOR ' ; ') title from book_delivery_service bds 
		left join book_delivery_service_book on bds_id=bdsb_idbds 
		left join knowledge_item kit on kit.id=bdsb_item_id where bds_idmember='$memberid' $where 
		group by bds_id )a
		order by bds_createdate desc 
		limit $limit offset $offset");
	}  
	
	function getBds($typeid,$memberid,$search)
	{ 	  
		$where = "";  

		if($typeid!="Semua" and $typeid!="All"  and $typeid!=""){
			$where .= "AND bds_status='$typeid'";
		}          

		if($search) $where .= " AND (".implode(" OR ",$search).")";  
		
		return $this->db->query("select count(*) total from (select bds_id from book_delivery_service bds 
		left join book_delivery_service_book on bds_id=bdsb_idbds
		
		left join knowledge_item kit on kit.id=bdsb_item_id where bds_idmember='$memberid' $where 
		group by bds_id )a");
	}   
	  
	function getBdsDetail($id)
	{ 	     
		return $this->db->query("
		select * from (select bds.*, GROUP_CONCAT(DISTINCT title ORDER BY title
        SEPARATOR ' ; ') title from book_delivery_service bds 
		left join book_delivery_service_book on bds_id=bdsb_idbds 
		left join knowledge_item kit on kit.id=bdsb_item_id where bds_id='$id'
		group by bds_id )a");
	}   
	  
	function getBdsStatus($id)
	{ 	     
		return $this->db->query("select * from book_delivery_service_status
		where bdss_idbds='$id' order by bdss_id desc");
	}  
	  
	function getBdsBook($id)
	{ 	     
		return $this->db->query("select bdsb.*,title from book_delivery_service_book bdsb
		left join knowledge_item kit on kit.id=bdsb_item_id
		where bdsb_idbds='$id' order by bdsb_id asc");
	}  
	  
	function getBook($id)
	{ 	     
		return $this->db->query("select id,code,title from knowledge_item where id in ($id)");
	}  

	//end==========bookdeliveryservice==========

	//start==========bahanpustaka========== 
	
	function getBahanpustakaFilter($typeid,$memberid,$limit,$offset,$search)
	{ 	  
		$where = ""; 

		if($typeid!="Semua" and $typeid!="All"  and $typeid!=""){
			$where .= "AND bp_status='$typeid'";
		}       

		if($search) $where .= " AND (".implode(" OR ",$search).")";    
		return $this->db->query("select ub.*,nama_fakultas,nama_prodi from usulan_bahanpustaka ub
		left join batik.t_mst_prodi  tmp on c_kode_prodi=bp_prodi_id
		left join batik.t_mst_fakultas tmf on tmf.c_kode_fakultas=bp_faculty_id
		where bp_idmember='$memberid'  and bp_upload_type is null  $where order by bp_createdate desc limit $limit offset $offset");
	}  
	
	function getBahanpustaka($typeid,$memberid,$search)
	{ 	  
		$where = ""; 

		if($typeid!="Semua" and $typeid!="All" and $typeid!=""){
			$where .= "AND bp_status='$typeid'";
		}          

		if($search) $where .= " AND (".implode(" OR ",$search).")";  
		
		return $this->db->query("select count(*) total 
		from usulan_bahanpustaka
		where bp_idmember='$memberid'  and bp_upload_type is null $where");
	}   
	  
	function getBahanpustakaDetail($id)
	{ 	    
		return $this->db->query("select ub.*,nama_fakultas,nama_prodi from usulan_bahanpustaka ub
		left join batik.t_mst_prodi  tmp on c_kode_prodi=bp_prodi_id
		left join batik.t_mst_fakultas tmf on tmf.c_kode_fakultas=bp_faculty_id
		where bp_id='$id'");
	}  
	  
	function getBahanpustakaStatus($id)
	{ 	    
		return $this->db->query("select * from usulan_bahanpustaka_status
		where bps_idbp='$id' order by bps_id desc");
	}  
	//end==========bahanpustaka==========
	
	//start==========room==========
	
	function getRoomFilter($typeid,$memberid,$limit,$offset,$search,$start_date,$end_date)
	{ 	  
		$where = ""; 
 
		if($typeid!="All" and $typeid!=""){
			$where .= "AND bk_status='$typeid'";
		}     

		if($start_date!="" and $start_date!="0"){
			$where .= " AND bk.bk_startdate between '$start_date' and '$end_date'";
		}

		if($search) $where .= " AND (".implode(" OR ",$search).")";    
		return $this->db->query("select bk_id, room_name,bk_status,bk_startdate,bk_enddate, bk_purpose,bk_createdate
		from room.booking bk
		left join room.room r on r.room_id=bk.bk_room_id 
		where bk_memberid='$memberid' $where order by bk_startdate desc limit $limit offset $offset");
	}  
	
	function getRoom($typeid,$memberid,$search,$start_date,$end_date)
	{ 	  
		$where = ""; 

		if($typeid!="All" and $typeid!=""){
			$where .= "AND bk_status='$typeid'";
		}         

		if($start_date!="" and $start_date!="0"){
			$where .= " AND bk.bk_startdate between '$start_date' and '$end_date'";
		}

		if($search) $where .= " AND (".implode(" OR ",$search).")";  
		
		return $this->db->query("select count(*) total 
		from room.booking bk
		left join room.room r on r.room_id=bk.bk_room_id 
		where bk_memberid='$memberid' $where");
	}   
	  
	function getRoomDetail($id)
	{ 	    
		return $this->db->query("select bk_id, room_name,bk_status,bk_startdate,bk_enddate, bk_purpose,bk_createdate,bk_reason,bk_total, bk_name
		from room.booking bk
		left join room.room r on r.room_id=bk.bk_room_id 
		where bk_id='$id'");
	}  
	
	function getroombyactiveid()
	{ 
		return $this->db->query("select * from room.room left join room.room_gallery on rg_room_id=room_id where room_active='0' group by room_id order by room_capacity,room_name");
	}   

    public function checkExistBooking($startdate,$enddate,$room_id){ 
        return $this->mn->query("select * from room.booking where bk_room_id='$room_id' and bk_status!='Cancel' 
		and bk_status!='Not Approved' and bk_status!='Not Attend'
		and (
			(bk_startdate<'$startdate' and bk_enddate>'$startdate') or (bk_startdate<'$enddate' and bk_enddate>'$enddate') or (bk_startdate>'$startdate' and bk_startdate<'$enddate')
			or (bk_enddate>'$startdate' and bk_enddate<'$enddate')
		)
		union
		select * from room.booking where bk_room_id='$room_id' and bk_status!='Cancel' 
		and bk_status!='Not Approved' and bk_status!='Not Attend'
		and bk_startdate='$startdate' and bk_enddate='$enddate'");
    }

    public function checkBannedPerMonthStatus($username){
		$date = date("Y-m"); 
		return $this->db->query("select count(*) total from room.booking where bk_status='Not Attend' and bk_startdate like '$date%' and bk_username='$username'");
    } 
	
	function getBlacklist($username)
	{     
		return $this->db->query("select * from room.blacklist where bl_username='$username'");
	} 

    public function checkCountBookingRoomStatus($username)
	{
		return $this->db->query("select count(*) total from room.booking where bk_status='Request' and bk_username='$username'");
    }  
	
    public function getroombyid($id){  
        return $this->db->query("select * from room.room where room_id='$id'");
    }   
	
	function addRoom($item)
	{
		$this->db->insert('room.room', $item);
	}   
	
	function addBookingMember($item)
	{
		return $this->mn->insert('room.booking_member', $item);
	}  

    public function getMemberByUsername($username){ 
		return $this->db->query("select id,member_type_id from batik.member where  master_data_user='$username'");
    } 

    public function getListNameMember($list){
		return $this->db->query("SELECT
			group_concat(
				master_data_fullname
				ORDER BY
					master_data_fullname ASC SEPARATOR ', '
			) nama
			from batik.member where  id in ($list)");
    }  

	//end==========room==========

	//start==========rent========== 
	
    public function holiday(){
        return $this->db->query("select holiday_date from batik.holiday where holiday_date >= DATE_FORMAT(NOW(),'%Y-%m-%d') order by holiday_date");
    }  

	function getRentIdMemberFilter($id,$limit,$offset,$typeid)
	{ 	    
		$where = "";

		if($typeid=="Dipinjam"){ 
			$where .= "AND rent.status='1'";
		}
		else if($typeid=="Dikembalikan"){ 
			$where .= "AND rent.status='2'";
		}
		else if($typeid=="Rusak"){ 
			$where .= "AND rent.status='3'";
		}
		else if($typeid=="Hilang"){ 
			$where .= "AND rent.status='4'";
		}

		return $this->db->query("select rent.*, title,author,ks.code,rent_extension_count,rent_extension_day
				from rent left join member m on m.id=member_id
				left join member_type mt on mt.id=member_type_id
				left join knowledge_stock ks on ks.id=knowledge_stock_id
				left join knowledge_item kit on kit.id=knowledge_item_id
				where m.id='$id' $where order by status asc, rent_date desc limit $limit offset $offset");
	}  
	
	function getRentIdMember($id,$typeid)
	{ 	  
		$where = "";

		if($typeid=="Dipinjam"){ 
			$where .= "AND rent.status='1'";
		}
		else if($typeid=="Dikembalikan"){ 
			$where .= "AND rent.status='2'";
		}
		else if($typeid=="Rusak"){ 
			$where .= "AND rent.status='3'";
		}
		else if($typeid=="Hilang"){ 
			$where .= "AND rent.status='4'";
		}

		return $this->db->query("select count(*)total from rent where member_id='$id' $where");
	}    
	 
	function getRentIdFilter($id)
	{ 	     
		return $this->db->query("select rent.*, title,author,ks.code,rent_extension_count,rent_extension_day
				from rent left join member m on m.id=member_id
				left join member_type mt on mt.id=member_type_id
				left join knowledge_stock ks on ks.id=knowledge_stock_id
				left join knowledge_item kit on kit.id=knowledge_item_id
				where rent.id='$id'");
	}  
	 
	function doCountHolidayBetween($current_date, $return_date)
	{ 	      
		return $this->db->query("select count(distinct holiday_date)total from holiday where holiday_date > '$current_date' and holiday_date <= '$return_date'")->row()->total; 
	}  

	function getRentNotYetReturnIdMember($id)
	{ 	   
		return $this->db->query("select * from rent where member_id='$id' and status='1'");
	}  

	function getRentNotYetReturnUsername($username)
	{ 	   
		return $this->db->query("select rent.* from rent left join member m on m.id=member_id where master_data_user='$username' and rent.status='1'");
	}  
	 
	 
	function getLastRentPenalty($id) 
	{ 	 
		return $this->db->query("select penalty_date from rent_penalty where rent_id='$id' order by penalty_date desc limit 1");
	}  
	
	function countsumRentPenalty($id)
	{ 	 
		return $this->db->query("select count(*)total, sum(amount) total_amount from rent_penalty where rent_id='$id'");
	}  
	
	function getHoliday($start,$end)
	{ 	  
	
		return $this->db->query("select distinct holiday_date from holiday where holiday_date > '$start' and holiday_date <= '$end'");
	} 
	
	function countHoliday($start,$end) 
	{ 	  
		return $this->db->query("select count(distinct holiday_date)total from holiday where holiday_date > '$start' and holiday_date <= '$end'");
	} 
	
	function updateRent($data,$where)
	{ 
		$this->db->update('rent', $data, $where);
	} 
	
	function addRentPenalty($item)
	{
		$this->db->insert('rent_penalty', $item);
	}
	
	function addKIV($item)
	{
		$this->db->insert('knowledge_item_view', $item);
	}

	function getRentPenaltyPaymentFilter($id,$limit,$offset)
	{ 	  
		return $this->db->query("select payment_date,amount from rent_penalty_payment where member_id='$id' and amount!=0 order by payment_date desc limit $limit offset $offset");
	}  
	
	function getRentPenaltyPayment($id)
	{ 	  
		return $this->db->query("select count(*)total from rent_penalty_payment where member_id='$id' and amount!=0 ");
	}    
	 
	function getRemainingPenalty($id)
	{ 	 
		return $this->db->query("select sum(amount) penalty,(select sum(amount) from rent_penalty_payment where member_id='$id')payment from rent_penalty where member_id='$id'");
	}  

	//end==========rent==========
	 
	//start==========katalog==========
	
	function getCatalogDashboard()
	{ 	  
		return $this->db->query("
			select count(kt.id)total
			from knowledge_item kt   
			left join knowledge_subject ks on kt.knowledge_subject_id=ks.id 
			left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' and kp.active='1' 
		");
	}    
	 
	function getCatalogFilter($type,$typeid,$location,$digital,$limit,$offset,$search,$memberid,$title,$author,$subject,$editor)
	{ 	  
		$where = "";
		$where2 = "";

		if($typeid=="" || $typeid=="0"){
			if($type=='fisik') $where .= "AND kso.knowledge_type_id not in ($digital)";
			else if($type=='bds') $where .= "AND kp.rentable='1' AND kso.status='1'";
			else $where .= "AND kso.knowledge_type_id in ($digital)"; 
		}
		else {
			$where .= "AND kso.knowledge_type_id='$typeid'";
		}

		if($type!='digital'){
			if($location!="" and $location!="0"){
				$where .= "AND kso.item_location_id='$location'";
			}  
		}

		
		if($title!='') $where .= "AND title like '%$title%'";
		if($author!='') $where .= "AND author like '%$author%'";
		if($subject!='') $where .= "AND ks.name like '%$subject%'"; 
		if($editor!='' and $editor!='0') $where .= "AND kt.editor like '%$editor%'"; 

		$where2 = $where;
  
		if($search) $where .= " AND (".implode(" OR ",$search).")"; 
		return $this->db->query("select kt.id,cover_path, softcopy_path, kt.code catalog_code, cc.name classification_name, cc.code classification_code, ks.name subjectname, kp.name typename, kt.title, published_year,publisher_city,publisher_name, author,il.name location
		from knowledge_item kt
		left join knowledge_stock kso on kso.knowledge_item_id=kt.id
		left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		left join classification_code cc on cc.id=kt.classification_code_id
		left join item_location il on il.id=kso.item_location_id
		left join knowledge_type kp on kso.knowledge_type_id=kp.id   where ks.active='1' and kp.active='1'  and  status not in (4,5) $where group by kt.id 
		order by kt.entrance_date desc, kt.title, author, cc.code, ks.name limit $limit offset $offset");
	}  
	
	function getCatalog($type,$typeid,$location,$digital,$search,$title,$author,$subject,$editor)
	{ 	 
		$where = ""; 

		if($typeid=="" || $typeid=="0"){
			if($type=='fisik') $where .= "AND kso.knowledge_type_id not in ($digital)";
			else if($type=='bds') $where .= "AND kp.rentable='1' AND kso.status='1'";
			else $where .= "AND kso.knowledge_type_id in ($digital)";
		}
		else {
			$where .= "AND kso.knowledge_type_id='$typeid'";
		}

		if($type!='digital'){
			if($location!="" and $location!="0"){
				$where .= "AND kso.item_location_id='$location'";
			}  
		}  
		
		if($title!='') $where .= "AND title like '%$title%'";
		if($author!='') $where .= "AND author like '%$author%'";
		if($subject!='') $where .= "AND ks.name like '%$subject%'"; 
		if($editor!='' and $editor!='0') $where .= "AND kt.editor like '%$editor%'"; 

		if($search) $where .= " AND (".implode(" OR ",$search).")";
		
		return $this->db->query("select count(*)total from(
		select kt.id,count(*) tot
		from knowledge_item kt  
		left join knowledge_stock kso on kso.knowledge_item_id=kt.id
		left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		left join classification_code cc on cc.id=kt.classification_code_id
		left join knowledge_type kp on kso.knowledge_type_id=kp.id   where ks.active='1' and kp.active='1'  and  status not in (4,5) $where  group by kt.id) a");
	}   
	
	function getCatalogDetail($type,$typeid,$location,$digital,$id,$memberid)
	{ 	  
		$where = "";
		if($typeid=="" || $typeid=="0"){
			if($type=='fisik') $where .= "AND kso.knowledge_type_id not in ($digital)";
			else if($type=='bds') $where .= "AND kp.rentable='1' AND kso.status='1'";
			else $where .= "AND kso.knowledge_type_id in ($digital)";
		}
		else {
			$where .= "AND kso.knowledge_type_id='$typeid'";
		}

		if($type!='digital'){
			if($location!="" and $location!="0"){
				$where .= "AND kso.item_location_id='$location'";
			}  
		}  
		  
		return $this->db->query("select kt.id,kt.entrance_date, cover_path, softcopy_path, kt.code catalog_code, cc.name classification_name, cc.code classification_code, ks.name subjectname, kp.name typename, kt.title, published_year, publisher_city, publisher_name, author, kt.knowledge_type_id,
		author_type, editor, translator, language, kt.supplier, kt.origination, kt.price, rent_cost, penalty_cost, abstract_content,alternate_subject,isbn,collation,kp.type,kp.rentable,il.name location,
		SUM(CASE WHEN kso.status = '1' THEN 1 ELSE 0 END) AS tersedia,
		count(kt.id) total,
		count(io.id) notif_ketersediaan
		from knowledge_item kt    
		left join knowledge_stock kso on kso.knowledge_item_id=kt.id
		left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		left join classification_code cc on cc.id=kt.classification_code_id
		left join item_location il on il.id=kso.item_location_id
		left join item_order io on io.knowledge_item_id=kt.id and member_id='$memberid' and io.status='1'
		left join knowledge_type kp on kt.knowledge_type_id=kp.id where ks.active='1' and kp.active='1' and kt.id='$id'
		group by kt.id");
	}  
	 
	function addNotifikasiKetersediaan($item)
	{
		$this->db->insert('item_order', $item);
	}   
	
	function getRak($sql)
	{ 	  
		return $this->db->query($sql);
	} 
	 
	//end==========katalog==========

	//start==========karya ilmiah==========  
	
	function getKaryaIlmiahStatus()
	{ 	  
		return $this->db->query("select ws.id,ws.name from workflow_state ws
		left join workflow_state_sort_id wssi on ws.id=id_state
		where workflow_id='1' order by sort");
	} 

	function getKaryaIlmiahFilter($typeid,$memberid,$civitas_type,$limit,$offset,$search,$start_date,$end_date,$status_general)
	{ 	  
		$where = ""; 

		if($typeid!="" and $typeid!="0"){
			$where .= "AND wd.latest_state_id='$typeid'";
		}    
		
		if($civitas_type=='mahasiswa'){ 
			$where .= " AND wd.member_id='$memberid'";
		} 
		else if($civitas_type=='pegawai_dosen'){ 
			$where .= " AND wd.lecturer_id='$memberid'";
		} 

		if($status_general=='Approved'){ 
			$where .= " AND wd.latest_state_id in (3,52,64,53,91,5)";
		} 
		else if($status_general=='On Draft'){ 
			$where .= " AND wd.latest_state_id in (22)";
		}  
		else if($status_general=='Review'){ 
			$where .= " AND wd.latest_state_id in (1)";
		}  
		else if($status_general=='Revision'){ 
			$where .= " AND wd.latest_state_id in (2)";
		}  

		if($start_date!="" and $start_date!="0"){
			$where .= " AND wd.created_at between '$start_date' and '$end_date'";
		}

		if($search) $where .= " AND (".implode(" OR ",$search).")";    
		
		return $this->db->query("select wd.id,wd.title, m.master_data_fullname student, m.master_data_number nim,nama_prodi,
			ks.name subjectname, kt.name typename, m2.master_data_fullname lecturer_name_1, 
			m3.master_data_fullname lecturer_name_2,
			ws.name status, wd.created_at,latest_state_id
			FROM workflow_document wd
			left join workflow w on w.id=wd.workflow_id 
			left join knowledge_subject ks on ks.id=knowledge_subject_id 
			left join knowledge_type kt on kt.id=knowledge_type_id   
			left join member m on m.id=member_id  
			left join t_mst_prodi tmp on c_kode_prodi=m.master_data_course
			left join workflow_state ws on ws.id=latest_state_id 
			left join member m2 on m2.id=wd.lecturer_id
			left join member m3 on m3.id=wd.lecturer2_id
			where ks.active='1' and kt.active='1' and wd.workflow_id='1' $where order by wd.created_at desc limit $limit offset $offset");
	}  
	
	function getKaryaIlmiah($typeid,$memberid,$civitas_type,$search,$start_date,$end_date,$status_general)
	{ 	  
		
		$where = ""; 

		if($typeid!="" and $typeid!="0"){
			$where .= "AND wd.latest_state_id='$typeid'";
		}    
		
		if($civitas_type=='mahasiswa'){ 
			$where .= " AND wd.member_id='$memberid'";
		} 
		else if($civitas_type=='pegawai_dosen'){ 
			$where .= " AND wd.lecturer_id='$memberid'";
		} 

		if($status_general=='Approved'){ 
			$where .= " AND wd.latest_state_id in (3,52,64,53,91,5)";
		} 
		else if($status_general=='On Draft'){ 
			$where .= " AND wd.latest_state_id in (22)";
		}  
		else if($status_general=='Review'){ 
			$where .= " AND wd.latest_state_id in (1)";
		}  
		else if($status_general=='Revision'){ 
			$where .= " AND wd.latest_state_id in (2)";
		}   

		if($start_date!="" and $start_date!="0"){
			$where .= " AND wd.created_at between '$start_date' and '$end_date'";
		}

		if($search) $where .= " AND (".implode(" OR ",$search).")"; 

		return $this->db->query("select count(*) total
		FROM workflow_document wd
		left join member m on m.id=member_id  
		left join knowledge_subject ks on ks.id=knowledge_subject_id 
		left join knowledge_type kt on kt.id=knowledge_type_id  
		left join member m2 on m2.id=wd.lecturer_id
		left join member m3 on m3.id=wd.lecturer2_id
		where ks.active='1' and kt.active='1' and wd.workflow_id='1' $where");
	}    
	
	function getKaryaIlmiahCommentFilter($where,$limit,$offset) 
	{  
		return $this->db->query("select wc.*,master_data_user,master_data_fullname from workflow_comment wc
		left join member m on member_id=m.id
		$where order by created_at limit $limit offset $offset");
	} 
 
	
	function getKaryaIlmiahComment($where) 
	{  
		return $this->db->query("select count(*) total from workflow_comment wc 
		$where order by created_at");
	} 
	  
	function getKaryaIlmiahDetail($id)
	{ 	    
		return $this->db->query("select wd.id,wd.title, wd.member_id, m.master_data_user, m.master_data_fullname student, m.master_data_number nim,tmp.nama_prodi, tmp2.nama_prodi unit,
		ks.name subjectname, kt.name typename, wd.lecturer_id, m2.master_data_fullname lecturer_name_1, 
		m3.master_data_fullname lecturer_name_2,
		ws.name status, wd.created_at,abstract_content,latest_state_id,wd.course_code
		FROM workflow_document wd
		left join workflow w on w.id=wd.workflow_id 
		left join knowledge_subject ks on ks.id=knowledge_subject_id 
		left join knowledge_type kt on kt.id=knowledge_type_id   
		left join member m on m.id=member_id  
		left join t_mst_prodi tmp on tmp.c_kode_prodi=m.master_data_course
		left join t_mst_prodi tmp2 on tmp2.c_kode_prodi=wd.course_code
		left join workflow_state ws on ws.id=latest_state_id 
		left join member m2 on m2.id=wd.lecturer_id
		left join member m3 on m3.id=wd.lecturer2_id
		where ks.active='1' and kt.active='1' and wd.id='$id'");
	}  

	function getWorkflowDocumentbyId($id)
	{  
		return $this->db->query("select latest_state_id,lecturer_id,lecturer.master_data_user lecturer_username
		from workflow_document wd  
		left join member m on m.id=wd.member_id 
		left join member lecturer on lecturer.id=wd.lecturer_id  
		where wd.id='$id'");
	}  
	
	function getDocumentState($id)
	{ 
		return $this->db->query("select ws.name state_name, master_data_user, master_data_fullname,mt.name, open_date,close_date from workflow_document_state wds
		left join workflow_state ws on ws.id=wds.state_id
		left join member m on m.id=wds.open_by
		left join member_type mt on mt.id=m.member_type_id
		where document_id='$id' order by wds.id asc");
	} 
	
	function delete_workflow_document_sdgs($wd_id)
	{ 
		return $this->db->query("delete from workflow_document_sdgs where document_id='$wd_id'");
	}    
	
	function getStateById($id)
	{ 
		return $this->db->query("select * from workflow_state where id='$id'");
	}  
	
	function getDocumentStateId($wd_id,$state_id)
	{ 
		return $this->db->query("select id from workflow_document_state where document_id='$wd_id' and state_id='$state_id' and close_date is null");
	}  

	function edit_workflow_document_state($wd_id,$state_id,$user_id)
	{
		return $this->db->query("update workflow_document_state set close_date='".date('Y-m-d H:i:s', strtotime('+7 hours'))."',open_by='$user_id' where document_id='$wd_id' and state_id='$state_id'");
	}
	
	function getWorkflowDocumentMember($id)
	{
		return $this->db->query("select member_id,master_data_user from workflow_document wd left join member m on m.id=wd.member_id where wd.id='$id'");
	}  
	
	function getfileUpload($member_type_id)
	{ 	  
		return $this->db->query("select id,name,extension,title,is_secure,
		(select count(*)total from member_type_upload_type where member_type_id='$member_type_id' and upload_type_id=ut.id) download, 
		(select count(*)total from member_type_upload_type_readonly where member_type_id='$member_type_id' and upload_type_id=ut.id) readonly
		from upload_type ut
		order by title");
	} 
	
	function getfileUploadDocument($id)
	{ 	  
		return $this->db->query("select name,location,created_by from workflow_document_file where document_id='$id' order by name");
	} 
	
	function getDocumentSdgs($wd_id)
	{ 
		return $this->db->query("select * from workflow_document_sdgs where document_id='$wd_id'");
	}   
	
	function getDocumentMasterSubjectByUnitId($id,$wd_id)
	{   
		return $this->db->query("SELECT name from workflow_document_subject 
		join master_subject ms on ms.id=master_subject_id
		where workflow_document_id='$wd_id' and course_code='$id' order by name");
	}
	
	function getNextState($id)
	{
		return $this->db->query("select ws.* from workflow_transition wt left join workflow_task tsk on tsk.id=task_id left join workflow_state ws on ws.id=next_state_id where wt.state_id='$id' order by name");
	}
	
	//stop==========karya ilmiah==========
	 
	//start==========news========== 

	function getNewsFilter($limit,$offset,$status,$search)
	{ 	   
		$where = "";
		if($search) $where = " AND (".implode(" OR ",$search).")";   
		return $this->db->query("select id,title,created_at,created_by from information where active='1' $status $where order by created_at desc limit $limit offset $offset ");
	}
	
	function getNews($status,$search)
	{ 	   
		$where = "";
		if($search) $where = " AND (".implode(" OR ",$search).")"; 
		return $this->db->query("select count(title) total from information where active='1' $status $where");
	}   
	
	function getNewsDetail($id)
	{ 	  
		// if($search) $where = " AND (".implode(" OR ",$search).")";   
		return $this->db->query("select id,content,title,created_at,created_by from information where id='$id'");
	}   
	
	//end==========news========== 

	//start==========notification========== 
	
	function getNotificationFilter($limit,$offset,$master_data_user)
	{ 	   
 
		return $this->db->query("select notif_id, notif_id_member, notif_content,notif_date, notif_status,notif_id_detail,
		CASE
			WHEN notif_type in ('peminjaman', 'pengembalian', 'perpanjangan','reminder H-1','reminder Hari H') THEN 'sirkulasi'
			WHEN notif_type = 'ruangan' THEN 'ruangan'
			WHEN notif_type = 'karyailmiah' THEN 'karyailmiah'
			WHEN notif_type = 'ketersediaan' THEN 'katalog'
			WHEN notif_type = 'bahanpustaka' THEN 'bahanpustaka'
			WHEN notif_type = 'bds' THEN 'bds'
		END type
		from notification_mobile where notif_id_member='$master_data_user' 
		order by notif_date desc limit $limit offset $offset");
	}
	
	function getNotification($master_data_user)
	{ 	    
		return $this->db->query("select SUM(CASE WHEN notif_status = 'unread' THEN 1 ELSE 0 END) AS unread,count(notif_id) total from notification_mobile where notif_id_member='$master_data_user'");
	} 
	
	function setNotifikasiRead($id, $item)
	{
		$this->db->where('notif_id', $id);
		$this->db->update('notification_mobile', $item);
	}  
	//end==========notification========== 

	//start==========stock==========
	
	function getStock($id)
	{ 	  
		return $this->db->query("select count(*) total from knowledge_stock ks where knowledge_item_id='$id'");
	} 
	
	function getStockFilter($limit,$offset,$id)
	{ 	  
		
		return $this->db->query("select ks.*, title, il.name building, kp.name typename from knowledge_stock ks
		left join knowledge_item ki on ki.id=knowledge_item_id 
		left join knowledge_type kp on ks.knowledge_type_id=kp.id
		left join item_location il on il.id=ks.item_location_id where knowledge_item_id='$id' order by status,ks.code limit $limit offset $offset ");
	}    
	
	function getEksemplar()
	{ 	  
		return $this->db->query("select count(kk.id) total from knowledge_item kt 
		left join knowledge_stock kk on kt.id=kk.knowledge_item_id
		left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
		left join knowledge_type kp on kt.knowledge_type_id=kp.id   where ks.active='1' and kp.active='1'   
		and kk.status not in(4,5)");
	}      

	//end==========stock==========
	
	//start==========general========== 
	
	function getall()
	{
		return $this->mn->get($this->table);
	} 
	
	function getby($user,$pass)
	{ 	 
		return $this->masterdb->query("select * from t_mst_user_login mst left join t_tem_userlogin_igracias_for_rfid igra on mst.c_username=igra.c_username where mst.c_username='$user' and password='$pass' and status_user='1'");
	}  
	
	function getbymemberAll($user,$pass)
	{ 	  
		return $this->masterdb->query("select * from batik.member where master_data_user='$user' and master_data_password='$pass' and status in(1,2)");
	} 
	
	function getbymember($user,$pass)
	{ 	 
		return $this->masterdb->query("select master_data_user, master_data_email,master_data_fullname,master_data_number,master_data_mobile_phone,master_data_lecturer_status,master_data_generation,master_data_photo,member_type_id,m.id,master_data_type,mt.name typename, nama_prodi from batik.member m left join batik.member_type mt on mt.id=member_type_id left join batik.t_mst_prodi tmp 
		on tmp.c_kode_prodi=master_data_course where master_data_user='$user' and master_data_password='$pass' and status='1'");
	} 
	
	function getLocation()
	{ 	   
		return $this->db->query("select * from item_location where show_as_footer='1' order by orderby");
	}   
	
	function getbyid($id)
	{
		$this->mn->where($this->id, $id);
		return $this->mn->get($this->table);
	} 

	function add_custom($item,$table)
	{
		$this->db->insert($table, $item);
		return $this->db->insert_id();
	} 
	
	function editCustom($colomn, $table, $id, $item)
	{
		$this->db->where($colomn, $id);
		$this->db->update($table, $item);
	}
	
	function add($item)
	{
		$this->masterdb->insert($this->table, $item);
	} 
	
	function addItem($table,$item)
	{ 
		$this->masterdb->insert($table, $item);
	}
	
	function updateItem($table,$data,$where)
	{ 
		$this->masterdb->update($table, $data, $where);
	} 
	
	function update($data,$where)
	{ 
		$this->masterdb->update('batik.member', $data, $where);
	} 
	
	function edit($id, $item)
	{
		$this->mn->where($this->id, $id);
		$this->mn->update($this->table, $item);
	}
	
	function delete($id)
	{
		$this->mn->where($this->id, $id);
		$this->mn->delete($this->table);
	}
	 
	function getGenerateMember($id)
	{ 	   return $this->db->query("select rent.*, title,author,ks.code
		from rent left join member m on m.id=member_id
		left join knowledge_stock ks on ks.id=knowledge_stock_id
		left join knowledge_item kit on kit.id=knowledge_item_id
		where return_date is null and return_date_expected < '".date('Y-m-d')."'
		and m.id in ($id)
		order by member_id");
		// return $this->db->query("select rent.*, title,author,ks.code
		// 		from rent left join member m on m.id=member_id
		// 		left join knowledge_stock ks on ks.id=knowledge_stock_id
		// 		left join knowledge_item kit on kit.id=knowledge_item_id
		// 		where return_date is null and return_date_expected < '".date('Y-m-d')."'
		// 		and m.id in ($id)
		// 		order by rent_date, return_date_expected, master_data_number, master_data_fullname, master_data_mobile_phone, master_data_email, title, ks.code");
	}   

	function getMemberFilter($limit,$offset,$data)
	{ 	      
		return $this->db->query("select  member.id,master_data_fullname,master_data_user,master_data_number from batik.member where LOWER(master_data_user) like '%$data%' or LOWER(master_data_fullname) like '%$data%' and status='1' and member_type_id in (1,2,3,4,5,6,7,9,10,25) order by master_data_fullname limit $limit offset $offset");
	}   
	
	function getMember($data)
	{ 	   

		return $this->db->query("select count(member.id) total from batik.member where LOWER(master_data_user) like '%$data%' or LOWER(master_data_fullname) like '%$data%' and status='1' and member_type_id in (1,2,3,4,5,6,7,9,10,25) order by master_data_fullname");
	}    
	
	function getKnowledgeType($type,$id)
	{ 	 
		if($type=='digital') 
			return $this->db->query("select id,name from knowledge_type where active='1' and id in ($id) order by name");
		else  
			return $this->db->query("select id,name from knowledge_type where active='1' and id not in ($id) order by name");
	} 
	
	function getbymemberUuid($uuid)
	{ 	 
		return $this->db->query("select master_data_user, master_data_email,master_data_fullname,master_data_number,master_data_mobile_phone,master_data_lecturer_status,master_data_generation,master_data_photo,member_type_id,m.id memberid,master_data_type,mt.name typename, nama_prodi		from member m 
		left join member_type mt on mt.id=member_type_id 
		left join t_mst_prodi tmp 
		on tmp.c_kode_prodi=master_data_course where master_data_uuid='$uuid' and status in (1,2)");
	} 
	
	function getbymemberUuidCivitas($uuid,$civitas,$pegawai_dosen,$mahasiswa)
	{ 	     
		return $this->db->query("select master_data_user, master_data_email,master_data_fullname,master_data_number,master_data_mobile_phone,master_data_lecturer_status,master_data_generation,master_data_photo,member_type_id,m.id memberid,master_data_type,mt.name typename, nama_prodi,
		CASE 
			WHEN member_type_id in ($pegawai_dosen) THEN 'pegawai_dosen' 
			WHEN member_type_id in ($mahasiswa) THEN 'mahasiswa' 
			ELSE 'admin' 
		END AS civitas_type
		from member m left join member_type mt on mt.id=member_type_id left join t_mst_prodi tmp 
		on tmp.c_kode_prodi=master_data_course where master_data_uuid='$uuid' and status in (1,2) and member_type_id in ($civitas)");
	} 
	
	function getbymemberUuidCivitasTemporary($uuid)
	{ 	     
		return $this->db->query("select master_data_user, master_data_email,master_data_fullname,master_data_number,master_data_mobile_phone,master_data_lecturer_status,master_data_generation,master_data_photo,member_type_id,m.id memberid,master_data_type,mt.name typename, nama_prodi
		from member m left join member_type mt on mt.id=member_type_id left join t_mst_prodi tmp 
		on tmp.c_kode_prodi=master_data_course where master_data_uuid='$uuid'  and status in (1,2)");
	} 
	
	function checkUser($user)
	{ 	 
		return $this->db->query("select master_data_user, master_data_email,master_data_fullname,master_data_number,master_data_mobile_phone,master_data_lecturer_status,master_data_generation,master_data_photo,member_type_id,m.id memberid,master_data_type,mt.name typename, nama_prodi from member m left join member_type mt on mt.id=member_type_id left join t_mst_prodi tmp 
		on tmp.c_kode_prodi=master_data_course where master_data_user='$user' and status in (1,2)");
	}   
	
	function editDb($table, $data, $id, $iddata)
	{
		$this->mn->where($id, $iddata);
		$this->mn->update($table, $data); 
	}  
	
	function getFaculty()
	{
		return $this->db->query("select c_kode_fakultas, nama_fakultas from t_mst_fakultas order by nama_fakultas");
	} 
	
	function getProdi($faculty_id)
	{
		return $this->db->query("select c_kode_prodi, nama_prodi from t_mst_prodi where c_kode_fakultas='$faculty_id' order by nama_prodi");
	}  
	
	function getTokenNotificationMobile($id){ 
        return $this->db->query("select master_data_token from member where id='$id'");
    }   
	
	//end==========general========== 
	
	/**		FOR ADDITONAL FUNCTIONj
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
	
	function item_location($id){ 
        return $this->db->query("select * from item_location where id='$id'");
    }   
	
}
?>