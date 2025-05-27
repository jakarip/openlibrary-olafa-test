<?php
class Referral_Payment_Model extends CI_Model
{

    private $table = 'referral_payment';
    private $id    = 'rp_id';

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
        return $this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM ".$this->table." 
        left join referral on ref_id=rp_id_ref
        left join participant_registration on (sreg_id_participant=rp_id_participant and sreg_id_pin=rp_id_pin)
        left join participant on par_id=sreg_id_participant
        left join pin on pin_id=rp_id_pin 
        left join ms_school on school_id=par_id_school
        $param[where] $param[order] $param[limit]");
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