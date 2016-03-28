<?php

class Exam_model extends CI_Model {

    CONST TABLE = "examination";
    CONST PRIMARY = "id";

    public $table_course = "course";
    public $table_faculty = "faculty";
    public $table_organization = "organization";
    public $table_students = "students";
    public $table_users = "users";
    public $table_transport = "transport";

    function getAllExaminations($orgId) {
        $sql = "select a.*,b.course_name from examination as a left join course as b on a.course_id=b.id where a.org_id=".$orgId."";
        $query = $this->db->query($sql);
        return $result = $query->result_array();
    }

    function getCourseExamination($course_id)
    {
        $sql = "select a.* from examination as a  where a.course_id=".$course_id."";
        $query = $this->db->query($sql);
        return $result = $query->result_array();
    }

    function get_student_exam($course_id,$exam_id)
    {
        $sql = "select * from students where course_id=".$course_id."";
        $query = $this->db->query($sql);
        $students = $query->result_array();
        $final_array=array();
        foreach ($students as $student) 
        {
            $id=$student["id"];
            $sql_exam = "select * from results where student_id=".$id." and exam_id=".$exam_id."";
            $query_exam = $this->db->query($sql_exam);
            $students_exam = $query_exam->result_array();
            if(count($students_exam)>0)
            {
                $student["marks"]=$students_exam[0]['marks'];
            }
            else
            {
                $student["marks"]='';
            }
            $final_array[]=$student;
        }
        return $final_array;
    }



    function get_result_data($student_id,$exam_id)
    {
        $sql = "select * from results where exam_id=".$exam_id." and student_id=".$student_id."";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        return $data;
    }

}
