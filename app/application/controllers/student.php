<?php

class Student extends CI_Controller {

    function Student() {
        parent::__construct();
        $this->load->database();
        $this->load->model('user_model');
        $this->load->model('organisation_model');
        $this->load->model('exam_model');
        $this->load->model('attendance_model');
        $this->load->model('notice_model');
        $this->load->model('students_model');
        error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
    }

    public function getStudentList() {
        $response = array();
        if ($this->input->post()) {
            $formdata = $this->input->post();
            $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
            if ($userdata['code'] == "admin") {
                $organizationdata = $this->organisation_model->getAllStudentByCourse($userdata['user_code'], $formdata['courseId'], $formdata['date']);
                $response_data['students'] = $organizationdata;
                $response_data['msg'] = "Success";
                $response_data['success'] = true;
                echo json_encode($response_data);
            } else {
                $response_data['msg'] = "Unauthorized Request";
                $response_data['success'] = false;
                echo json_encode($response_data);
            }
        }
    }

    function add() {
        $formdata = $this->input->post();

        $role = $this->user_model->getDataByApiKey($formdata['secret']);
        if ($role['code'] == 'admin') {
            $org_id = $role['user_code'];
            if ($formdata['studentId'] == '') {
                $data_parent = array(
                    'name' => $formdata['father_name'],
                );
                $this->db->insert('parents', $data_parent);
                $parent_id = $this->db->insert_id();
                $data_parent_user = array(
                    'username' => $formdata['father_name'],
                    'password' => md5('111111'),
                    'email' => $formdata['parent_email'],
                    'name' => $formdata['father_name'],
                    'user_group' => 3,
                    'address' => $formdata['address'],
                    'user_code' => $parent_id
                );
                $this->db->insert('users', $data_parent_user);
            }
            $data_student = array(
                'name' => $formdata['student_name'],
                'father_name' => $formdata['father_name'],
                'mother_name' => $formdata['mother_name'],
                'gender' => $formdata['gender'],
                'dob' => $formdata['student_dob'],
                'course_id' => $formdata['student_course'],
                'org_id' => $org_id
            );
            if ($formdata['studentId'] == '') {
                $data_student['parent_id'] = $parent_id;
                $this->db->insert('students', $data_student);
                $student_id = $this->db->insert_id();
            } else {
                $this->db->where('id', $formdata['studentId']);
                $this->db->update('students', $data_student);
            }

            if ($formdata['studentId'] == '') {
                $data_student_user = array(
                    'username' => $formdata['student_name'],
                    'password' => md5('111111'),
                    'email' => $formdata['student_email'],
                    'name' => $formdata['student_name'],
                    'user_group' => 4,
                    'address' => $formdata['address'],
                    'user_code' => $student_id
                );
                $this->db->insert('users', $data_student_user);
            } else {
                $data_student_user = array(
                    'name' => $formdata['student_name'],
                    'address' => $formdata['address'],
                );
                $this->db->where('user_code', $formdata['studentId']);
                $this->db->update('users', $data_student_user);
            }
            $response_data['msg'] = "Success";
            $response_data['success'] = true;
            echo json_encode($response_data);
        } else {
            $response_data['msg'] = "Unauthorized Request";
            $response_data['success'] = false;
            echo json_encode($response_data);
        }
    }

    function deleteStudent() {
        $formdata = $this->input->post();
        $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
        if ($userdata['code'] == "admin") {
            if ($formdata['student_id']) {
                $this->db->delete('users', array('user_code' => $formdata['student_id']));
                $this->db->delete('students', array('id' => $formdata['student_id']));

                $response_data['msg'] = "Deleted Successfully";
                $response_data['success'] = true;
                echo json_encode($response_data);
            } else {
                $response_data['msg'] = "Validation Failed";
                $response_data['success'] = false;
                echo json_encode($response_data);
            }
        } else {
            $response_data['msg'] = "Unauthorized Request";
            $response_data['success'] = false;
            echo json_encode($response_data);
        }
    }

    function save_attendance() {
        $formdata = $this->input->post();
        $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
        $attendance_data = $this->organisation_model->get_attendance_data($formdata['att_date'], $formdata['courseId']);
        if (count($attendance_data) > 0) {
            $this->organisation_model->delete_attendance($formdata['att_date'], $formdata['courseId']);
        }
        $org_id = $userdata["user_code"];
        foreach ($formdata['att_check'] as $attr) {
            $data = array(
                'org_id' => $org_id,
                'date' => $formdata['att_date'],
                'course_id' => $formdata['courseId'],
                'student_id' => $attr
            );
            $this->db->insert('attendence', $data);
        }
        $response_data['msg'] = "Success";
        $response_data['success'] = true;
        echo json_encode($response_data);
    }

    function getExamResult() {
        $formdata = $this->input->post();
        $data = $this->exam_model->get_student_exam($formdata['courseId'], $formdata['examinationId']);
        $response_data['students'] = $data;
        $response_data['msg'] = "Success";
        $response_data['success'] = true;
        echo json_encode($response_data);
    }

    function save_marks() {
        $formdata = $this->input->post();
        $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
        $exam_id = $formdata["examinationId"];
        for ($i = 1; $i <= $formdata["student_count"]; $i++) {
            $student_id = $formdata["student_id_" . $i . ""];
            $marks = $formdata["marks_student_" . $i . ""];
            $result_data = $this->exam_model->get_result_data($student_id, $exam_id);
            $data = array(
                'exam_id' => $exam_id,
                'student_id' => $student_id,
                'marks' => $marks
            );
            if (count($result_data) > 0) {
                $this->db->where('id', $result_data[0]["id"]);
                $this->db->update('results', $data);
            } else {
                $this->db->insert('results', $data);
            }
        }

        $response_data['msg'] = "Success";
        $response_data['success'] = true;
        echo json_encode($response_data);
    }

    public function get_attandance() {
        $response = array();
        $formdata = $this->input->post();
        $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
        $studentId=$userdata['user_code'];
        $data=$this->attendance_model->getAttendance($studentId);
        $attendance=array();
        foreach ($data as $value) {
            $attendance[] =array('title'=>'P','start'=> $value['date'],'color'=> 'green',   // a non-ajax option
                                'textColor'=> 'black');
        }
        echo json_encode($attendance);
    }
    
    public function getNotice() {
        $response = array();
        $formdata = $this->input->post();
        $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
        $studentId=$userdata['user_code'];
        $data=$this->students_model->getStudent($studentId);
        $orgId=$data[0]['org_id'];
        $courseId=$data[0]['course_id'];
        $noticeData=$this->notice_model->getNoticeStudent($studentId, $orgId, $courseId);
        $response['notice']=$noticeData;
        echo json_encode($response);
        
    }

}

//http://localhost:8001/CodeIgniter/index.php/organization/getOrganizationDetails