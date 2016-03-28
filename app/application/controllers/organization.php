<?php
class Organization extends CI_Controller{

	function Organization(){
		parent::__construct();
		$this->load->database();
		$this->load->model('user_model');
		$this->load->model('organisation_model');
		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
                header('Access-Control-Allow-Origin: *');
	}

	public function getOrganizationDetails(){
		$response= array();
		if($this->input->post()){
			$formdata=$this->input->post();
			$userdata = $this->user_model->getDataByApiKey($formdata['secret']);
			if($userdata['code']=="superadmin"){
				$organizationdata = $this->organisation_model->getAllOrganization();
				//$facultydata = $this->organisation_model->getAllFaculty();
				//$studentdata = $this->organisation_model->getAllStudent();
				$response_data['organizations']=$organizationdata;
				// $response_data['faculty']=count($facultydata);
				// $response_data['student']=count($studentdata);
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

	public function addOrganization(){
		$response_data=array();
		$this->load->library('form_validation');
                if($this->input->post('orgId')!='' && $this->input->post('orgId'))
                    $this->form_validation->set_rules('email', 'email', 'required');
		else 
                    $this->form_validation->set_rules('email', 'email', 'required|callback_check_database');
                if($this->input->post()){
			if($this->form_validation->run())
				{
					$formdata=$this->input->post();
					$userdata = $this->user_model->getDataByApiKey($formdata['secret']);
					if($userdata['code']=="superadmin"){
						$data=array(
								'name'=>$formdata['name'],
								'address'=>$formdata['address'],
								'owner'=>$formdata['owner']
								);
						$data1=array(
								'username'=>$formdata['name'],
								'password'=>md5('111111'),
								'email'=>$formdata['email'],
								'name'=>$formdata['name'],
								'user_group'=>2,
								'address'=>$formdata['address']
								);
                                                if($formdata['orgId']){
                                                        $this->db->where('id',$formdata['orgId']);
                                                        $this->db->update('organization',$data);
                                                        $this->db->where('user_code',$formdata['orgId']);
                                                        $this->db->update('users',$data1);
                                                }else{
							$this->db->insert('organization',$data);
							$lastInsertId = $this->db->insert_id();
							$data1['user_code']=$lastInsertId;
							$this->db->insert('users',$data1);
							$lastInsertId1 = $this->db->insert_id();
                                                }  
							$this->sendRegistrationMail($formdata['email'],'111111');


						if (($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg")|| ($_FILES["file"]["type"] == "image/jpg")|| ($_FILES["file"]["type"] == "image/pjpeg")|| ($_FILES["file"]["type"] == "image/x-png")|| ($_FILES["file"]["type"] == "image/png")){
							$ext=explode(".",$_FILES["file"]["name"]);			
							$filename=$id.".".$ext[count($ext)-1];
							move_uploaded_file($_FILES["file"]["tmp_name"],"uploads/".$filename);
							$data3 = array('avtar' => $lastInsertId1.".".$ext[count($ext)-1]);
							$this->db->where('id', $lastInsertId1);
							$this->db->update('users', $data3);
						} 
								
						// $response_data['data']=$data;
						$response_data['msg']="Success";
						$response_data['success']=true;	
						echo json_encode($response_data);

					}else{
						$response_data['msg']="Unauthorized Request";
						$response_data['success']=false;
						echo json_encode($response_data);
					}
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

	function check_database($email){
		$data = $this->user_model->getUserByEmail($email);
		if(count($data)>0) return false;
		else true;
	}


	function sendRegistrationMail($email,$pass){

		$html="Hello User<br>";
		$html.="Welcome to SchoolApp.You can login to your account with below details.<br>";
		$html.="Email:".$email."<br>";
		$html.="Password:".$pass."<br>";
		$html.="Regards<br>";
		$subject="Account Details";
		mail($email,$subject,$html);

	}


	function deleteOrganization(){
		$formdata=$this->input->post();
			$userdata = $this->user_model->getDataByApiKey($formdata['secret']);
			if($userdata['code']=="superadmin"){
				if($formdata['organization_id']){
					$this->db->delete('users', array('user_code' => $formdata['organization_id']));
					$this->db->delete('organization', array('id' => $formdata['organization_id']));

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
    function addTransport() {
        $formdata = $this->input->post();
        if ($formdata['secret'] && $formdata['route_no']) {
            $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
            $org_id = $userdata['user_code'];
//            echo '<pre>'; print_r($userdata);exit;
            if ($formdata['transport_id']) {
                $update_array = array(
                    'id' => $formdata['transport_id'],
                    'org_id' => $org_id,
                    'name' => $formdata['route_no'],
                    'vehical_no' => $formdata['vehical_no'],
                );
                $this->db->update('transport', $update_array);
                $response_data['msg'] = "Updated Successfully";
                $response_data['success'] = true;
                echo json_encode($response_data);
            } else {
                $insert_array = array(
                    'org_id' => $org_id,
                    'name' => $formdata['route_no'],
                    'vehical_no' => $formdata['vehical_no'],
                );
                $this->db->insert('transport', $insert_array);
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

    function deleteTransport() {
        $formdata = $this->input->post();
        $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
        if ($userdata['code'] == "admin") {
            if ($formdata['transport_id']) {
                $this->db->delete('transport', array('id' => $formdata['transport_id']));

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

    public function getAllTransport() {
        $response_data = array();
        if ($this->input->post()) {
            $formdata = $this->input->post();
            $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
            if ($userdata['code'] == "admin") {
                $this->load->model('transport_model');
                $data = $this->transport_model->getAllTransports($userdata['user_code']);
                $response_data['transport'] = $data;
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
	function addExam($data)
    {
        $formdata = $this->input->post();
        $userdata = $this->user_model->getDataByApiKey($formdata['secret']);
        $org_id=$userdata['user_code'];
        if($formdata['exam_id']=='')
        {
            $add_array = array(
                    'org_id' => $org_id,
                    'name' => $formdata['exam_name'],
                    'course_id' => $formdata['courses'],
                    'date' => $formdata['exam_date'],
                    'status' => '1',
                );
            $this->db->insert('examination', $add_array);
        }
        else
        {
            $update_array = array(
                    'id' => $formdata['transport_id'],
                    'org_id' => $org_id,
                     'name' => $formdata['exam_name'],
                    'course_id' => $formdata['courses'],
                    'date' => $formdata['exam_date'],
                    'status' => '1',
                );
            $this->db->update('examination', $update_array);
        }
        $response_data['msg'] = "Success";
        $response_data['success'] = true;
        echo json_encode($response_data);
    }

}
//http://localhost:8001/CodeIgniter/index.php/organization/getOrganizationDetails