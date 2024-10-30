<?php

/**
 * The file that defines the core functions here 
 *
 * @link       https://profiles.wordpress.org/vsourz1td/
 * @since      1.0.0
 *
 * @package    Cf7_To_Mountstride
 * @subpackage Cf7_To_Mountstride/includes
 */

/*
 * Define the static fields that will be considered in the mountstride API
 */
 
if(!function_exists('cf7_to_mountstride_api_fields')){
 
 function cf7_to_mountstride_api_fields(){
	
	$mountstride_fields = array(
						"EnquiryName"			=>"EnquiryName",
						"CompanyName"			=>"CompanyName",
						"CompanyContactNumber"	=>"CompanyContactNumber",
						"CompanyEmail"			=>"CompanyEmail",
						"ContactName"			=>"ContactName",
						"ContactNumber"			=>"ContactNumber",
						"ContactEmail"			=>"ContactEmail",
						"Note"					=>"Note",
						"ReceivedDate" 			=> "ReceivedDate"
					);

	$mountstride_fields = (array) apply_filters('cf7_to_mountstride_api_fields_list',$mountstride_fields);
	return $mountstride_fields;
 }
}

/* Define function for render input fields here */

if(!function_exists('cf7_to_mountstride_option_fields')){
	
	function cf7_to_mountstride_option_fields($arr_fields,$selectedVal=''){
		if(empty($arr_fields)) return ;
		$option = '';
		foreach($arr_fields as $opt_key => $opt_val){
			$selected = '';
			if($selectedVal == $opt_key ) $selected = 'selected';
			$option .= '<option value="'.$opt_key.'" '.$selected.'>'.$opt_val.'</option>';
		}
		return $option;
	}
}
	