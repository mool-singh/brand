<?php

class Expense_model extends CI_Model {
    public function save($data) {
        $this->db->insert('expenses', $data);
    }

    public function get_all() {
        return $this->db->get('expenses')->result_array();
    }
}

?>