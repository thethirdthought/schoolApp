<?php
class category_model extends CI_Model{
	function category_model(){
		parent::__construct();
	}
	function addcategory(){
		//print_r($_POST);
		extract($_POST);
		$sql = "INSERT INTO category (name, p_id)
        VALUES (".$this->db->escape($category).", ".$this->db->escape($p_id).")";

		$this->db->query($sql);
		//echo $this->db->affected_rows(); 
		
	}



}

?>