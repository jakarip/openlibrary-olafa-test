<?php
class Referral_Model extends CI_Model
{

    private $table = 'referral';
    private $id    = 'ref_id';

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
    }

    // Query for new datatables purpose ;
    //--------------------------------------------------------------------------------------------------------------------------
    function dtquery($param)
    { 
        return $this->db->query("SELECT SQL_CALC_FOUND_ROWS * from (select *, (select count(*) from participant_registration where sreg_referral=ref_id) register,(select sum(rp_set_cost) from referral_payment where rp_id_ref=ref_id) total FROM ".$this->table.") a $param[where] $param[order] $param[limit]");
    }

    function dtfiltered()
    {
        $result = $this->db->query('SELECT FOUND_ROWS() as jumlah')->row();

        return $result->jumlah;
    }

    function dtcount()
    {
        return $this->db->count_all($this->table);
    }
    //--------------------------------------------------------------------------------------------------------------------------

    function dtquery_detail($param)
    { 
        return $this->db->query("SELECT SQL_CALC_FOUND_ROWS * from (select referral.*,sreg_referral,sreg_id,par_id,par_participantnumber,sreg_id_pin,par_fullname,school_name,par_phone,pin_transaction_number, (select sum(rp_set_cost) from referral_payment where rp_id_participant=a.sreg_id_participant and rp_id_pin=sreg_id_pin)total,(select GROUP_CONCAT(rp_set_status SEPARATOR ', ') AS status FROM referral_payment where rp_id_participant=a.sreg_id_participant and rp_id_pin=sreg_id_pin)status from participant_registration a
        left join pin on pin_id=sreg_id_pin
        left join participant on par_id=sreg_id_participant  
        left join referral on ref_id=sreg_referral  
        left join ms_school on school_id=par_id_school) a 
         $param[where] $param[order] $param[limit]");
    } 

    function dtcount_detail()
    {
        return $this->db->count_all('participant');
    }

    function getall()
    {
        return $this->db->get($this->table);
    }

    function getbyquery($param)
    {
        return $this->db->query("SELECT * FROM ".$this->table." $param[where] $param[order] $param[limit]");
    }

    function countbyquery($param)
    {
        $result = $this->db->query("SELECT COUNT(".$this->id.") as jumlah FROM ".$this->view." $param[where]")->row();

        if(!empty($result))
            return $result->jumlah;
        else
            return 0;
    }

    function countall()
    {
        return $this->db->count_all($this->table);
    }

    function getby($item)
    {
        $this->db->where($item);
        return $this->db->get($this->table);
    }

    function getbyid($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->get($this->table);
    }

    function add($item)
    {
        $this->db->insert($this->table, $item);
        return $this->db->insert_id();
    }

    function edit($id, $item)
    {
        $this->db->where($this->id, $id);
        return $this->db->update($this->table, $item);
    }

    function delete($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->delete($this->table);
    }

    /**		FOR ADDITONAL FUNCTION
    Untuk Menambah function baru silahkan letakkan di bawah ini.
     **/

    function getlogin($username, $password)
    {
        return $this->db->query("SELECT * FROM ".$this->table." WHERE ref_username = '$username' AND ref_password = '$password'");
    }
}
?>