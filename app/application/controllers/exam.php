<?php

class Exam extends CI_Controller {

    function Exam() {
        parent::__construct();
        $this->load->database();
        $this->load->model('user_model');
        $this->load->model('organisation_model');
        error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
        header('Access-Control-Allow-Origin: *');
    }

    function addExam() {
        $formdata = $this->input->post();
        if ($formdata['secret'] && $formdata['route_no']) {
            $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
            $org_id = $userdata['user_code'];
//            echo '<pre>'; print_r($userdata);exit;
            if ($formdata['exam_id']) {
                $update_array = array(
                    'id' => $formdata['exam_id'],
                    'name' => $formdata['exam_name'],
                    'org_id' => $org_id,
                    'course_id' => $formdata['course_id'],
                    'date' => $formdata['exam_date'],
                    'status' => $formdata['status'],
                );
                $this->db->update('examination', $update_array);
                $response_data['msg'] = "Updated Successfully";
                $response_data['success'] = true;
                echo json_encode($response_data);
            } else {
                $insert_array = array(
                    'name' => $formdata['exam_name'],
                    'org_id' => $org_id,
                    'course_id' => $formdata['course_id'],
                    'date' => $formdata['exam_date'],
                    'status' => $formdata['status'],
                );
                $this->db->insert('examination', $insert_array);
                $response_data['msg'] = "Inserted Successfully";
                $response_data['success'] = true;
                echo json_encode($response_data);
            }
        } else {
            $response_data['msg'] = "Unauthorized Request";
            $response_data['success'] = false;
            echo json_encode($response_data);
        }
    }

    function deleteExam() {
        $formdata = $this->input->post();
        $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
        if ($userdata['code'] == "admin") {
            if ($formdata['exam_id']) {
                $this->db->delete('examination', array('id' => $formdata['exam_id']));

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

    public function getAllExam() {
        $response_data = array();
        if ($this->input->post()) {
            $formdata = $this->input->post();
            $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
            if ($userdata['code'] == "admin") {
                $this->load->model('exam_model');
                $data = $this->exam_model->getAllExaminations($userdata['user_code']);
                $response_data['exam'] = $data;
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

    public function getExamination()
    {
        $formdata = $this->input->post();
        $this->load->model('exam_model');
        $data = $this->exam_model->getCourseExamination($formdata['course']);
        $response_data['exam_option'] = $data;
        $response_data['msg'] = "Success";
        $response_data['success'] = true;
        echo json_encode($response_data);
    }

}
