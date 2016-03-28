<?php

class Notice_model extends CI_Model {

    public $table = "notice_board";
    public $table_user_group = "user_role";
    public $table_user_parent = "parents";
    public $table_user_student = "students";
    public $table_user_organization = "organization";
    public $table_user = "users";
    public $table_course = "course";

    function notice_model() {
        parent::__construct();
    }

    function getAllNotice($orgId) {
        $sql = "select * from " . $this->table . " a where a.org_id=" . $orgId;
        $query = $this->db->query($sql);
        return $result = $query->result_array();
    }

    function getNoticeStudent($studentId, $orgId, $courseId) {
        $sql1 = "select * from " . $this->table . " a where a.org_id=" . $orgId . " AND a.notice_type = 1";
        $query1 = $this->db->query($sql1);
        $result1 = $query1->result_array();
        $sql2 = "select * from " . $this->table . " a where a.org_id=" . $orgId . " AND a.notice_type = 3 AND a.type_id = $courseId";
        $query2 = $this->db->query($sql2);
        $result2 = $query2->result_array();
        $sql3 = "select * from " . $this->table . " a where a.org_id=" . $orgId . " AND a.notice_type = 2 AND a.type_id = $studentId";
        $query3 = $this->db->query($sql3);
        $result3 = $query3->result_array();
        return array_merge($result1, $result2, $result3);
    }

}

?>