<?php
class Notice extends CI_Controller{

	function Notice(){
		parent::__construct();
		$this->load->database();
		$this->load->model('user_model');
		$this->load->model('organisation_model');
		$this->load->model('notice_model');
		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
                header('Access-Control-Allow-Origin: *');
	}

	public function getAllNotice(){
		$response= array();
		if($this->input->post()){
			$formdata=$this->input->post();
			$userdata = $this->user_model->getDataByApiKey($formdata['secret']);
			if($userdata['code']=="admin"){
				$data = $this->notice_model->getAllNotice($userdata['user_code']);
				$response_data['notice']=$data;
				$response_data['msg']="Success";
				$response_data['success']=true;	
				echo json_encode($response_data);
			}else{
				$response_data['msg']="Unauthorized Request";
				$response_data['success']=false;
				echo json_encode($response_data);
			}

		}
	} 

	public function addNotice(){
		$response_data=array();
		$this->load->library('form_validation');
               
                $this->form_validation->set_rules('subject', 'subject', 'required');
                $this->form_validation->set_rules('description', 'description', 'required');
                if($this->input->post()){
                    $formdata=$this->input->post();
			$userdata = $this->user_model->getDataByApiKey($formdata['secret']);
			if($userdata['code']=="admin"){
                            if($this->form_validation->run())
                                    {                                            
                                        $data=array(
                                                    'subject'=>$formdata['subject'],
                                                    'description'=>$formdata['description'],
                                                    'expiration_date'=>$formdata['expiration_date'],
                                                    'created_on'=>date('Y-m-d'),
                                                    'notice_type'=>$formdata['notice_type'],
                                                    'type_id'=>$formdata['notice_to'],
                                                    'org_id'=>$userdata['user_code'],
                                                    'status' =>$formdata['status']
                                                );
                                        
                                        if($formdata['noticeId']){
                                                $this->db->where('id',$formdata['noticeId']);
                                                $this->db->update('notice_board',$data);
                                        }else{
                                                $this->db->insert('notice_board',$data);
                                        }  
                                       
                                        // $response_data['data']=$data;
                                        $response_data['msg']="Success";
                                        $response_data['success']=true;	
                                        echo json_encode($response_data);

                                            
                                    }else{
                                            $response_data['msg']="Validation Failed";
                                            $response_data['success']=false;
                                            echo json_encode($response_data);
                                    }
                            }else{
                                    $response_data['msg']="Unauthorized Request";
                                    $response_data['success']=false;
                                    echo json_encode($response_data);
                            }
		}else{
			$response_data['msg']="Unauthorized Request";
			$response_data['success']=false;
			echo json_encode($response_data);
		}
	}

	
	function deleteNotice(){
		$formdata=$this->input->post();
			$userdata = $this->user_model->getDataByApiKey($formdata['secret']);
			if($userdata['code']=="admin"){
				if($formdata['notice_id']){
					$this->db->delete('notice_board', array('id' => $formdata['notice_id']));

					$response_data['msg']="Deleted Successfully";
					$response_data['success']=true;	
					echo json_encode($response_data);
				}else{
					$response_data['msg']="Validation Failed";
					$response_data['success']=false;
					echo json_encode($response_data);
				}				
			}else{
				$response_data['msg']="Unauthorized Request";
				$response_data['success']=false;
				echo json_encode($response_data);
			}
	}
        
        function getAllCourseStudent(){
            $secret=$this->input->get('secret');
            $userdata = $this->user_model->getDataByApiKey($secret);
            $response_data=array();
            if($userdata['code']=="admin"){
                $coursedata = $this->organisation_model->getAllCourse($userdata['user_code']);
		$studentdata = $this->organisation_model->getAllStudent($userdata['user_code']); 
                $response_data=array('coursedata'=>$coursedata,'studentdata'=>$studentdata);
                $response_data['msg']="Deleted Successfully";
                $response_data['success']=true;	
                echo json_encode($response_data);
            }else{
                $response_data['msg']="Unauthorized Request";
                $response_data['success']=false;
                echo json_encode($response_data);
            }
                        
        }
        
        public function getAllCourse(){
		$response= array();
		if($this->input->post()){
			$formdata=$this->input->post();
			$userdata = $this->user_model->getDataByApiKey($formdata['secret']);
			if($userdata['code']=="admin"){
				$data = $this->organisation_model->getAllCourse($userdata['user_code']);
				$response_data['course']=$data;
				$response_data['msg']="Success";
				$response_data['success']=true;	
				echo json_encode($response_data);
			}else{
				$response_data['msg']="Unauthorized Request";
				$response_data['success']=false;
				echo json_encode($response_data);
			}

		}
	}
        
        function addCourse(){
            $response_data=array();
		$this->load->library('form_validation');               
                $this->form_validation->set_rules('course_name', 'course_name', 'required');
                if($this->input->post()){
                    $formdata=$this->input->post();
			$userdata = $this->user_model->getDataByApiKey($formdata['secret']);
			if($userdata['code']=="admin"){
                            if($this->form_validation->run())
                                    {                                            
                                        $data=array(
                                                    'course_name'=>$formdata['course_name'],
                                                    'org_id'=>$userdata['user_code'],
                                                    'status' =>1
                                                );
                                        
                                        if($formdata['CourseId']){
                                                $this->db->where('id',$formdata['CourseId']);
                                                $this->db->update('course',$data);
                                        }else{
                                                $this->db->insert('course',$data);
                                        }  
                                       
                                        $response_data['msg']="Success";
                                        $response_data['success']=true;	
                                        echo json_encode($response_data);

                                            
                                    }else{
                                            $response_data['msg']="Validation Failed";
                                            $response_data['success']=false;
                                            echo json_encode($response_data);
                                    }
                            }else{
                                    $response_data['msg']="Unauthorized Request";
                                    $response_data['success']=false;
                                    echo json_encode($response_data);
                            }
		}else{
			$response_data['msg']="Unauthorized Request";
			$response_data['success']=false;
			echo json_encode($response_data);
		}
        }
        
        function deleteCourse(){
            $formdata=$this->input->post();
            $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
            if($userdata['code']=="admin"){
                    if($formdata['course_id']){
                            $this->db->delete('course', array('id' => $formdata['course_id']));

                            $response_data['msg']="Deleted Successfully";
                            $response_data['success']=true;	
                            echo json_encode($response_data);
                    }else{
                            $response_data['msg']="Validation Failed";
                            $response_data['success']=false;
                            echo json_encode($response_data);
                    }				
            }else{
                    $response_data['msg']="Unauthorized Request";
                    $response_data['success']=false;
                    echo json_encode($response_data);
            }
        }

}
//http://localhost:8001/CodeIgniter/index.php/organization/getOrganizationDetails