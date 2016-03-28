<?php

class Students_model extends CI_Model {

    CONST TABLE = "students";
    CONST PRIMARY = "id";

    public $table_course = "course";
    public $table_faculty = "faculty";
    public $table_organization = "organization";
    public $table_students = "students";
    public $table_users = "users";
    public $table_transport = "transport";

    public function getStudent($studentId) {
        $sql="select * from ".  self::TABLE." a where a.id=".$studentId;
		$query=$this->db->query($sql);
		return $result=$query->result_array();
    }
}
