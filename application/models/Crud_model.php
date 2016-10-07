<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Crud_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function getdata($id) {
        $sql = $this->db->query("SELECT val FROM value_excel WHERE id_upload=$id");
        return $sql->result();
    }

    function get_data_excel($id) {
        $sql = $this->db->query("SELECT name_file FROM upload_excel WHERE id=$id");
        return $sql->result();
    }

    function insert_upload($file) {
        $this->db->query("INSERT INTO upload_excel (name_file) VALUES ('$file')");
        return $this->db->insert_id();
    }

    function save_val_upload($id, $val) {
        $this->db->query("INSERT INTO value_excel (id_upload, val) VALUES ($id, '$val')");
    }

}
