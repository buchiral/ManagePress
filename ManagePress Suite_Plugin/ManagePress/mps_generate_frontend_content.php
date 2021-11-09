<?php
global $wpdb;
global $table_prefix;

$pt_id					= tdm_get_posttype_id($pt_name);
$all_fields				= $wpdb->get_results("SELECT * FROM  ".$table_prefix."mps_core_fields WHERE posttype_id = '".$pt_id."' ",ARRAY_A);

$out = '';
$out .= "<div class=\"tdm_view_customfields2\">"; 
$out .= "<div class=\"mps_view_customfields\">"; 	

foreach($all_fields as $field){
$db_value ='';
	$field_name 		= $field['field_name'];
 	$field_table_name	= $field['field_table_name'];
 	$field_type 		= $field['field_type'];
 	$field_definition	= $field['field_definition'];
 	$field_choice 		= unserialize($field['field_choices']);
	$field_description	= $field['field_description'];

	$post_field_content	= $wpdb->get_results("SELECT * FROM  ".$table_prefix."mps_field_".$field_table_name." WHERE post_id =  ".$post_id." ",ARRAY_A);

	if (in_array($field_definition,array('multiplechoice','multipleinput'))){	
		for ($i = 0; $i < count($post_field_content); $i++) {
			$value 		=    $post_field_content[$i]['value'];
			$db_value 	.= ''.$value.'<br>';  		
		}
	}

	if (in_array($field_definition,array('input','singlechoice'))){	
		$db_value 	=     $post_field_content[0]['value'];
			if ($field_type == 'coordinates'){
			$ar = explode("_;_", $db_value);
			$db_value =  $ar[0];
			}
	}
	
	if(empty($post_field_content)){
	$db_value ='-';
	}

	$content_field .= '<tr> <td class="mps_left_1">'; 	
	$content_field .= $field_name.''.$a;
	$content_field .= "</td>"; 	
	$content_field .= '<td  class="mps_right_1" >'; 	
	$content_field .= $db_value;
	$content_field .= '</td></tr>'; 
}

// user connenction
$table_name	 = $table_prefix."tdm_core_fields";
$user_search = $wpdb->get_results(
"SELECT `us`.`display_name` 
	FROM `".$table_prefix."mps_core_userconnection` tdm_us, `$wpdb->users` us 
	WHERE `us`.`ID` = `tdm_us`.`user_id`
	and `tdm_us`.`post_id` = ".$post_id." ORDER BY display_name" ,ARRAY_A);

$user_value = '';
if(count($user_search)>0){
	foreach ($user_search as $user_search_row ) {
				$user_value .= "<div class=\"tdm_userconnect\">".$user_search_row['display_name']."</div>";	
		}
} else {

$user_value = '-';	
}


// parent of connenction ---------------
if (!function_exists('mps_get_postconnection_content')) {
function mps_get_postconnection_content($post_id,$modus){
	global $table_prefix;
	global $wpdb;

	switch ($modus) {
		case 'child_of':
		   $title = "Child element of:";
		   $div_id = "tdm_child_of";
		   $button_title = "add child element";
		   $button_id = "add_child_element";
		   $select_field_name = "post_id_parent";
		   $cond_field_name = "post_id_child";
			break;
		case 'parent_of':
			$title = "Parent element of:";
			$div_id = "tdm_parent_of";
			$button_title = "add parent element";
			$button_id = "add_parent_element";
			$select_field_name = "post_id_child";
			$cond_field_name = "post_id_parent";
			break;
	}

	$get_all_connection	= $wpdb->get_results( "SELECT ".$select_field_name." as postid FROM  ".$table_prefix."mps_core_postconnection WHERE `".$cond_field_name."` =  ".$post_id." ",ARRAY_A);
				
	
if(count($get_all_connection) > 0){	
	
	foreach ($get_all_connection as $post){
		$postconnections_array[] = mps_get_post_information_line($post['postid']);
	};
			
	usort($postconnections_array, "mps_sort_postconnection");
			
	$index = 0;
	$max_index = count($postconnections_array);
	foreach($postconnections_array as $connect_post){
			
		$actual_post_pluralname = $connect_post['pt_pluralname'];
		$con_post_id 			= $connect_post['post_id'];
		$con_pt_name			= $connect_post['pt_name'];
		$con_post_line			= $connect_post['post_line'];

		// if
		if($index == 0){
			$list .= '<ul class="mps_list_ul1">
						<li>'.$actual_post_pluralname.'</li>
							<ul class="mps_list_ul2">'; 
		}
		// if
		if(($last_post_pluralname != $actual_post_pluralname) && ($index > 0)){
			$list .= "</ul> \n </ul>";
			$list .= '<ul class="mps_list_ul1">
						<li>'.$actual_post_pluralname.'</li>
							<ul class="mps_list_ul2">'; 
		}
		
		// create lines-------
			$line = '<li>';

				$permalink = get_permalink($con_post_id);
				$line .= $con_post_line; 
				$line .= '<a title="ID of the post." href="'.$permalink.'">'.$con_post_id.'</a>';
				
				
				$line .= '</li>';
		// End lines------------
		
		$list .= $line;
	
		$last_post_pluralname = $connect_post['pt_pluralname'];
		
		if($index >= $max_index){
		$list .= '</ul></ul>';	
		}
		
		$index++;	
	};

	$out .= '<div class="mps_view_postconnection">'; 
		$out .= '<div class="mps_title_001">'.$title.'</div>'; 
		$out .= '<div class="tdm_right">'; 	
		$out .= $list;
		$out .= "</div>"; 
	$out .= "</div>"; 
}

return $out;
	}// End function
};//End if function exists


 
	$out .= '<table id="mps_table_content">'; 
		$out .= $content_field;
		$out .= '<tr><td class="mps_left_1">'; 	
		$out .= "Userconnection";
		$out .= "</td>"; 	
		$out .= '<td class="mps_right_1" >'; 	
		$out .= $user_value;
		$out .= '</td></tr>'; 	
	$out .= "</table>"; 
		
$out .= "</div>";

$out .= mps_get_postconnection_content($post_id,'parent_of');
$out .= mps_get_postconnection_content($post_id,'child_of');



?>