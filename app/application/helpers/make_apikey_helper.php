<?php

function make_apikey($id){
			$api_key= $id.mt_rand(1000000,9999999).time();
			return $api_key;
	}
?>