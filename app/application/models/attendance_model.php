<?php

class Attendance_model extends CI_Model {

    CONST TABLE = "attendence";
    CONST PRIMARY = "id";

    public $table_course = "course";
    public $table_faculty = "faculty";
    public $table_organization = "organization";
    public $table_students = "students";
    public $table_users = "users";
    public $table_transport = "transport";

    public function getAttendance($studentId) {
        $sql="select date from ".  self::TABLE." a where a.student_id=".$studentId;
		$query=$this->db->query($sql);
		return $result=$query->result_array();
    }
}
