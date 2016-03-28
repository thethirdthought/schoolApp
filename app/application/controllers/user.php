<?php

class User extends CI_Controller {

    function User() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('make_apikey');
        $this->load->model('user_model');
        error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
        header('Access-Control-Allow-Origin: *');
    }

    function login() {
        $response_data = array();
        if ($this->input->post()) {
            $formdata = $this->input->post();
            $data = $this->user_model->getUserByPassword($formdata);
            $apikey = $data[0]['api_key'];
            if (count($data) > 0) {
                if ($data[0]['api_key'] == '') {
                    $apikey = make_apikey($data[0]['id']);
                    $this->db->where('id', $data[0]['id']);
                    $this->db->update('users', array('api_key' => $apikey));
                }
                // $response_data['data']=$data;
                $response_data['msg'] = "Success";
                $response_data['secret'] = $apikey;
                $response_data['success'] = true;
                echo json_encode($response_data);
            } else {
                $response_data['msg'] = "Invalid Username And Password";
                $response_data['success'] = false;
                echo json_encode($response_data);
            }
        } else {
            $response_data['msg'] = "Unauthorized Request";
            $response_data['success'] = false;
            echo json_encode($response_data);
        }
    }

    function edit() {
        $response_data = array();
        if ($this->input->post()) {
            $formdata = $this->input->post();
            if (isset($formdata['secret']) && $formdata['secret']) {
                $role = $this->user_model->getRoleByApiKey($formdata['secret']);
                if ($role['code'] == 'admin')
                    $data = $this->user_model->updateProfile($formdata, 'organization');
                else if ($role['code'] == 'parent')
                    $data = $this->user_model->updateProfile($formdata, 'parents');
                else if ($role['code'] == 'student')
                    $data = $this->user_model->updateProfile($formdata, 'students');
                else if ($role['code'] == 'superadmin')
                    $data = $this->user_model->updateProfile($formdata, 'superadmin');

                $response_data['data'] = $data;
                $response_data['msg'] = "Success";
                $response_data['success'] = true;
                echo json_encode($response_data);
            }else {
                $response_data['msg'] = "Invalid Id";
                $response_data['success'] = false;
                echo json_encode($response_data);
            }
        } else {
            $response_data['msg'] = "Unauthorized Request";
            $response_data['success'] = false;
            echo json_encode($response_data);
        }
    }

    public function user_nav_check($formdata) {
        $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
        if (isset($userdata['user_code']) && $userdata['user_code']) {
            $ProfileDetails = $this->user_model->getProfileDetails($userdata['code'], $userdata['user_code']);
        }
        $NavigationDetails = $this->user_model->getNavigationDetails($userdata['group_id']);
        $SummeryDetails = $this->user_model->getSummeryDetails($userdata['code'], $userdata['user_code']);

        $nav_head = array();
        $nav_child = array();
        foreach ($NavigationDetails as $value) {
            if ($value['parent_id'] == 0) {
                $nav_head[] = $value;
            }
        }
        for ($i = 0; $i < count($nav_head); $i++) {
            foreach ($NavigationDetails as $value) {
                if ($value['parent_id'] == $nav_head[$i]['id']) {
                    $nav_head[$i]['child'][] = $value;
                }
            }
        }
        $NavigationDetails = array(
            'parent' => $nav_head
        );
//        print_r($NavigationDetails);
        return array(
            'userdata' => $userdata,
            'profileDetails' => $ProfileDetails,
            'navigationDetails' => $NavigationDetails,
            'SummaryDetails' => $SummeryDetails
        );
    }

    function dashboardetails() {
        $response_data = array();
        if ($this->input->post()) {
            $formdata = $this->input->post();
            $userDetails = $this->user_nav_check($formdata);
            $userdata = $userDetails['userdata'];
            $ProfileDetails = $userDetails['profileDetails'];
            $NavigationDetails = $userDetails['navigationDetails'];
            $SummaryDetails = $userDetails['SummaryDetails'];
            // $SummaryDetails=array(
            // 			array('title'=>"Attendance",'count'=>50),
            // 			array('title'=>"Assignment",'count'=>10),
            // 			array('title'=>"Notice",'count'=>5),
            // 			array('title'=>"Upcoming Exam",'count'=>2));	
            $notices = array(array(
                    'title' => "Farewell",
                    'description' => "Farewell for senior year",
                    'date' => "27-02-2016",
                ),);
            $response_data['profile_detail'] = $userdata;
            $response_data['navigation'] = $NavigationDetails;
            $response_data['summary'] = $SummaryDetails;
            $response_data['notices'] = $notices;
            $response_data['msg'] = "Success";
            $response_data['success'] = true;
            echo json_encode($response_data);
        } else {
            $response_data['msg'] = "Unauthorized Request";
            $response_data['success'] = false;
            echo json_encode($response_data);
        }
    }

    function changePassword() {
        $response_data = array();
        if ($this->input->post()) {
            $formdata = $this->input->post();
            $newPassword = $formdata['new_password'];
            $oldPassword = $formdata['old_password'];
//            unset($formdata['newPassword']);
//            unset($formdata['confirmPassword']);
            $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
//            print_r($userdata);die();
            if (count($userdata) > 0) {
                if(md5($oldPassword)==$userdata['pass']){
                    $newPassword = md5($newPassword);
                    $this->db->where('api_key', $formdata['secret']);
                    $this->db->update('users', array('password' => $newPassword));
                    $response_data['msg'] = "Password Successfuly Changed";
                    $response_data['success'] = true;
                    echo json_encode($response_data);
                }else{
                    $response_data['msg'] = "Password doesn't match!";
                    $response_data['success'] = false;
                    echo json_encode($response_data);
                }
            } else {
                $response_data['msg'] = "Email Does not exist please contact Admin";
                $response_data['success'] = false;
                echo json_encode($response_data);
            }
        } else {
            $response_data['msg'] = "Unauthorized Request";
            $response_data['success'] = false;
            echo json_encode($response_data);
        }
    }

    public function forgetPassword() {
        $response_data = array();
        if ($this->input->post()) {
            $formdata = $this->input->post();
            $data = $this->user_model->getUserByEmail($formdata['email']);
            if (count($data) > 0) {
                $newPassword = md5('111111');
                //$apikey = make_apikey($data[0]['id']);
                $this->db->where('id', $data[0]['id']);
                $this->db->update('users', array('password' => $newPassword));
                // $response_data['data']=$data;
                $response_data['msg'] = "Success";
                // $response_data['']=$apikey;
                $response_data['success'] = true;
                echo json_encode($response_data);
            } else {
                $response_data['msg'] = "Email Does not exist please contact Admin";
                $response_data['success'] = false;
                echo json_encode($response_data);
            }
        } else {
            $response_data['msg'] = "Unauthorized Request";
            $response_data['success'] = false;
            echo json_encode($response_data);
        }
    }

}

?>