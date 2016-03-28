<?php
class Organisation_model extends CI_Model{

	CONST TABLE = "organization";
	CONST PRIMARY = "id";
	public $table_course="course";
	public $table_faculty="faculty";
	public $table_organization="organization";
	public $table_students="students";
	public $table_users="users";
	public $table_parents="parents";

	public function organizationDataById($id){
		$sql= "SELECT * FROM ".self::TABLE." WHERE ".self::PRIMARY." = '$id'";
		$query=$this->db->query($sql);
		return $query->row_array();
	}
        
	public function getAllCourse($orgId){
		$sql= "SELECT * FROM ".$this->table_course." WHERE org_id=".$orgId." ";		
		$query=$this->db->query($sql);
		return $query->result_array();
	}
        
	public function getAllStudent($orgId){
		$sql= "SELECT a.*,b.course_name	 FROM ".$this->table_students." a left join ".$this->table_course." b on a.course_id=b.id WHERE a.org_id=".$orgId;
		$query=$this->db->query($sql);
		return $query->result_array();
	}

	public function getAllStudentByCourse($orgId,$courseId,$date){
		$sql= "SELECT a.*,b.course_name,u.email,u.phone,u.address,u.city,u.state,u.zipcode	 FROM ".$this->table_students." a inner join ".$this->table_users." u on a.id=u.user_code  left join ".$this->table_course." b on a.course_id=b.id WHERE a.org_id=".$orgId." and b.id=".$courseId;
		$query=$this->db->query($sql);
		$results=$query->result_array();
		$final_array=array();
		foreach($results as $result)
		{
			$student_id=$result["id"];
			$sql_attendance= "select * from attendence where course_id=".$courseId." and date='".$date."' and student_id=".$student_id."";
			$query_attendance=$this->db->query($sql_attendance);
			$result_attendance=$query_attendance->result_array();
			if(count($result_attendance)>0)
			{
				$result["exist"]=1;
			}
			else
			{
				$result["exist"]=0;	
			}
			$final_array[]=$result;
		}
		return $final_array;
	}

	public function getAllOrganization(){
		$sql= "SELECT a.*,u.email,( select count(*) from ".$this->table_faculty." b where org_id=a.id) as faculty,( select count(*) from ".$this->table_students." c where org_id = a.id) as students "
                        . "FROM ".self::TABLE." a inner join ".$this->table_users." u on a.id=u.user_code  WHERE 1";
		$query=$this->db->query($sql);
		return $query->result_array();
	}

	public function get_attendance_data($date,$course){
		$sql= "select * from attendence where course_id=".$course." and date='".$date."'";
		$query=$this->db->query($sql);
		return $query->result_array();
	}

	public function delete_attendance($date,$course){
		$sql= "delete from attendence where course_id=".$course." and date='".$date."'";
		$query=$this->db->query($sql);
		return 1;
	}

}
