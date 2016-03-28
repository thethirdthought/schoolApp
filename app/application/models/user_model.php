<?php
class User_model extends CI_Model{

	public $table="users";
	public $table_user_group="user_role";
	public $table_user_parent="parents";
	public $table_user_student="students";
	public $table_user_organization="organization";
	public $table_navigation="navigation";
	public $table_course="course";
	public $table_faculty="faculty";
	public $table_attendence="attendence";

	function user_model(){
		parent::__construct();
	}

	function getUserByEmail($email){
		$sql="select * from ".$this->table." a where a.email='".$email."' ";
		$query=$this->db->query($sql);
		return $result=$query->row_array();
	}

	function getUserByPassword($data){
		extract($data);
		$password=md5($password);
		$sql="select a.* from ".$this->table." a left join ".$this->table_user_group." b on a.user_group=b.id where a.email='".$username."' and a.password='".$password."'  ";
		$query=$this->db->query($sql);
		 $result=$query->result_array(); 
		// return $query->num_rows();
		 return $result;
	}

	function getSummeryDetails($usertype=NULL,$user_code=NULL){
		if($usertype=='superadmin'){
			$sql="select count(a.id) as organization_count from ".$this->table_user_organization." a where 1 ";
			$query=$this->db->query($sql);
			$organization_count=$query->row_array();
			$sql1="select count(a.id) as user_count from ".$this->table." a where 1 ";
			$query1=$this->db->query($sql1);
			$user_count=$query1->row_array();
			return array(array('title'=>'No. of Users','count'=>$user_count['user_count']),array('title'=>'No. of School','count'=>$organization_count['organization_count']));
		}else if($usertype=='admin'){
			$sql="select count(a.id) as student_count from ".$this->table_user_student." a where org_id=".$user_code;
			$query=$this->db->query($sql);
			$student_count=$query->row_array();
			$sql1="select count(a.id) as course_count from ".$this->table_course." a where org_id=".$user_code;
			$query1=$this->db->query($sql1);
			$course_count=$query1->row_array();
			$sql2="select count(a.id) as faculty_count from ".$this->table_faculty." a where org_id=".$user_code;
			$query2=$this->db->query($sql2);
			$faculty_count=$query2->row_array();
			return array(array('title'=>'No. of Notice','count'=>5),array('title'=>'No. of Faculty','count'=>$faculty_count['faculty_count']),array('title'=>'No. of Students','count'=>$student_count['student_count']),array('title'=>'No. of Courses','count'=>$course_count['course_count']));
		}else if($usertype=='student'){
			
			$sql="select * from ".$this->table_user_student." a where id=".$user_code;
			$query=$this->db->query($sql);
			$student_detail=$query->row_array();
			//print_r($student_detail);
			$allAttendance = $this->getTotalAttandance($student_detail['org_id'],$student_detail['course_id']);
			$studentAttendance = $this->getStudentAttandance($student_detail['org_id'],$student_detail['course_id'],$student_detail['id']);
			if($allAttendance>0) $peratt = (count($studentAttendance)/count($allAttendance))*100;
			else $peratt=0;
			return array(array('title'=>'No. of Notice','count'=>5),array('title'=>'Attendance','count'=>$peratt."%"));
		}
	}

	function getTotalAttandance($org_id,$course_id){
		$sql1="select count(*) as total_attendance from ".$this->table_attendence." a where org_id=".$org_id." and course_id=".$course_id." group by DATE";
		$query=$this->db->query($sql1);
		$allAttendance=$query->result_array();
		return $allAttendance;
	}
	
	function getStudentAttandance($org_id,$course_id,$student_id){
		$sql1="select count(*) as total_attendance from ".$this->table_attendence." a where org_id=".$org_id." and course_id=".$course_id." and student_id=".$student_id." group by DATE";
		$query=$this->db->query($sql1);
		$allAttendance=$query->result_array();
		return $allAttendance;
	}

	function getNavigationDetails($user_group_id){
		$sql="select a.* from ".$this->table_navigation." a where a.user_group=".$user_group_id." ";
		$query=$this->db->query($sql);
		return $result=$query->result_array();
	}

	function getDataByApiKey($apikey){
		$sql="select a.email,a.password as pass,a.phone,a.address,a.city,a.state,a.avtar,a.zipcode,a.user_code,b.id as group_id,b.name,b.code from ".$this->table." a left join ".$this->table_user_group." b on a.user_group=b.id where a.api_key=".$apikey." ";
		$query=$this->db->query($sql);
		return $result=$query->row_array();
	}

	function getRoleByApiKey($apikey){
		$sql="select b.* from ".$this->table." a left join ".$this->table_user_group." b on a.user_group=b.id where a.api_key=".$apikey." ";
		$query=$this->db->query($sql);
		return $result=$query->row_array();
	}

	function getProfileDetails($user_code,$user_id){
		if($user_code=='admin'){
			return 0;
		}else if($user_code=='admin'){
			$sql="select b.name,b.image,b.owner from ".$this->table_user_organization." b where b.id=".$user_id." ";
		}else if($user_code=='parent'){
			$sql="select b.name,b.image from ".$this->table_user_parent." b  where b.id=".$user_id." ";
		}else if($user_code=='student'){
			$sql="select b.name,b.image,b.father_name,b.mother_name,b.gender,b.dob from ".$this->table_user_student." b where b.id=".$user_id." ";
		} 
		$query=$this->db->query($sql);
		return $result=$query->row_array();
	}

	function updateProfile($formdata,$table=NULL){
		// extract($data);
		if ($_FILES["file"]["type"] == "image/jpeg" || $_FILES["file"]["type"] == "image/jpg"){
			$ext=explode(".",$_FILES["file"]["name"]);			
			$filename=$code.".".$ext[count($ext)-1];
			move_uploaded_file($_FILES["file"]["tmp_name"],$table."/".$filename);
		}

		$userdata = $this->getDataByApiKey($formdata['secret']);

		$data1=array(
					'name'=>$formdata['name'],
					'phone'=>$formdata['phone'],
					'address'=>$formdata['address'],
					'city'=>$formdata['city'],
					'state'=>$formdata['state'],
					'zipcode'=>$formdata['zipcode'],
					);	
		$this->db->where('api_key', $formdata['secret']);
		$this->db->update('users', $data1);


		if($table=='organization'){
			$data=array(
						'name'=>$formdata['name'],
						'address'=>$formdata['address'],
						'owner'=>$formdata['owner']
						);	
		}else if($table=='parents'){
			$data = array(
				   'name' => $formdata['name'] 
				   );
		}else if($table=='students'){
			$data = array(
				   'name' => $formdata['name'] ,
				   'gender' => $formdata['gender'] ,
				   'father_name' => $formdata['father_name'],
				   'mother_name' => $formdata['mother_name'],
				   'dob' => $formdata['dob'],
				   );
		}
		if(isset($table) && $table!='superadmin'){
			$this->db->where('id', $userdata['user_code']);
			$this->db->update($table, $data);
		}

	}



}

?>