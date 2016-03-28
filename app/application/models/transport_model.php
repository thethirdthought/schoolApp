<?php
class Transport_model extends CI_Model{

	CONST TABLE = "transport";
	CONST PRIMARY = "id";
	public $table_course="course";
	public $table_faculty="faculty";
	public $table_organization="organization";
	public $table_students="students";
	public $table_users="users";
	public $table_transport="transport";

        function getAllTransports($orgId){
		$sql="select * from ".  self::TABLE." a where a.org_id=".$orgId;
		$query=$this->db->query($sql);
		return $result=$query->result_array();
	}
}