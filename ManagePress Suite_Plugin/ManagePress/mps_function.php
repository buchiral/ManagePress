<?php

//************************************************************************
function tdm_get_posttype_id($custom_posttype){
	global $wpdb;
	global $table_prefix;

	$posttype_id	= $wpdb->get_var(" SELECT id FROM   `".$table_prefix."mps_core_posttype` WHERE posttype = '".$custom_posttype."' ");

	return $posttype_id;	
}

//************************************************************************
if (!function_exists('mps_is_selected')) {
	function mps_is_selected($var1,$var2){
		// proof if var1 = var2 
		// if true then return selected!!
		 if( $var1 == $var2){
			return "selected";	
		 }
		return "";
	};
};


//************************************************************************


if (!function_exists('mps_get_post_information_line')) {
function mps_get_post_information_line($post_id){
	global $wpdb;
	global $table_prefix;
	global $global_posttype_table;

	$pt_name		= get_post_type($post_id); 
	$standard_types = array("post", "page"); 

	$pt_pluralname = get_post_type_object($pt_name)->label;
	
	$pt_row = $wpdb->get_row( "SELECT * FROM  `".$table_prefix."mps_core_posttype` WHERE posttype = '".$pt_name."' ",ARRAY_A,0);
	$pt_id	= $pt_row['ID'];
	
	$table_name_of_posttype = $table_prefix."mps_pt".$pt_name;	
	$post_field_content	= $wpdb->get_row( "SELECT * FROM  ".$table_name_of_posttype." WHERE post_id =  ".$post_id." ",ARRAY_A,0);
			
	$all_fields = $wpdb->get_results( "SELECT * FROM  `".$table_prefix."mps_core_fields` WHERE posttype_id = ".$pt_id." and field_display = 0 ",ARRAY_A);



	if (post_type_supports($pt_name, 'title' )){
		$post_title = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID = ".$post_id." ");
			
		$line .= '<span title="title of the post">'.$post_title.'</span>, ';
	} else {
		$post_title = '';
	}
	
	//----------------------
				
	foreach ($all_fields as $field) {
	
		$field_name 		= $field['field_name'];
		$field_table_name	= $field['field_table_name'];
		$field_type 		= $field['field_type'];
		$field_definition	= $field['field_definition'];
		$field_choice 		= unserialize($field['field_choices']);
		$field_description	= $field['field_description'];
		
		$post_field_content	= $wpdb->get_results("SELECT * FROM  ".$table_prefix."mps_field_".$field_table_name." WHERE post_id =  ".$post_id." ",ARRAY_A);
		
		$db_value = $post_field_content[0]['value'];
	
				if (in_array($field_definition,array('multiplechoice','multipleinput'))){	
					for ($i = 0; $i < count($post_field_content); $i++) {
						$value 		=    $post_field_content[$i]['value'];
						$line .= "<span title=\"".$field_name ."\">".$value."</span>, ";
					}
				}
	
	
				if (in_array($field_definition,array('input','singlechoice'))){	
					$db_value 	=     $post_field_content[0]['value'];
					
					if ($field_type == 'coordinates'){
							if (!empty($db_value)){
							$ar = explode("_;_", $db_value);
							$line .= '<span title="'.$field_name.'">'.$ar[0].'</span>, ';
							}
					} else {

					if(!empty($db_value)){
					$line .= '<span title="'.$field_name.'">'.$db_value.'</span>, ';	
					}
					};
				}
				
				
				
	}// End foreach field

						$array['post_id'] 		= $post_id;
						$array['post_title']	= $post_title;
						$array['pt_row'] 		= $pt_row;
						$array['pt_name']		= $pt_name;
						$array['pt_pluralname']	= $pt_pluralname;
						$array['post_line'] 	= $line;

	 return $array;
	}// End function
	
};//End if function exist




//************************************************************************
if (!function_exists('mps_sort_postconnection')) {
	function mps_sort_postconnection($a, $b)
	{
		return strcmp($a["pt_pluralname"], $b["pt_pluralname"]);
	}
};


//************************************************************************
if (!function_exists('mps_is_selected')) {
	function mps_is_selected($var1,$var2){
		// proof if var1 = var2 
		// if true then return selected!!
		 if( $var1 == $var2){
			return "selected";	
		 }
		return "";
	};
};



//************************************************************************
if (!function_exists('mps_get_page_header')) {
	function mps_get_page_header($title){
		
		$output ='
		<div class="mps_line">
		<div class="tdm_logo"></div>
  		<h2>'.$title.'</h2>
    	</div>
	    <br />';
		
		return $output;
	};
};


?>