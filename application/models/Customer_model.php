<?php

class Customer_model extends CI_Model {
    public function save($data) {
        $this->db->insert('customers', $data);
    }

    public function get_all() {
        return $this->db->get('customers')->result_array();
    }
}

?>