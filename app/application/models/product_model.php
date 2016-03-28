<?php
class Product_model extends CI_Model{
	function Product_model(){
		parent::__construct();
	}	
	function product_list(){
		$query = $this->db->get('product');
		return $query->result_array();
	}
	

}

?>