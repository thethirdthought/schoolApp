<?php
class Notice_model extends CI_Model{

	public $table="notice_board";
	public $table_user_group="user_role";
	public $table_user_parent="parents";
	public $table_user_student="students";
	public $table_user_organization="organization";
	public $table_user="users";
	public $table_course="course";
	function notice_model(){
		parent::__construct();
	}

	function getAllNotice($orgId){
		$sql="select * from ".$this->table." a where a.org_id=".$orgId;
		$query=$this->db->query($sql);
		return $result=$query->result_array();
	}




}

?>