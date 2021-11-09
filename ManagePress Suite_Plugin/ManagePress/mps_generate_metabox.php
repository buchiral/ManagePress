<?php
global $wpdb;
global $table_prefix;

$post_id 	= $post->ID;
$pt_name 	= $post->post_type;

$pt_id 		= $metabox['args']['posttype_id']; // of tdm intern

// get all fields from this post type
$all_fields			= $wpdb->get_results("SELECT * FROM  ".$table_prefix."mps_core_fields WHERE posttype_id = ".$pt_id." ",ARRAY_A);

$user_connection	= $wpdb->get_results("SELECT * FROM  ".$table_prefix."mps_core_userconnection WHERE post_id =  ".$post_id." ",ARRAY_A);


foreach($all_fields as $field_row){
	
	$field_name 		= $field_row['field_name'];
 	$field_table_name	= $field_row['field_table_name'];
 	$field_type 		= $field_row['field_type'];
 	$field_definition	= $field_row['field_definition'];
 	$field_choice 		= unserialize($field_row['field_choices']);
	$field_description	= $field_row['field_description'];
	
	$post_field_content	= $wpdb->get_results("SELECT * FROM  ".$table_prefix."mps_field_".$field_table_name." WHERE post_id =  ".$post_id." ",ARRAY_A);

 	$content_field .= '<div class="mps_input_sector_1">';		
		switch ($field_definition) {
		case 'multiplechoice':
			$content_field .= 	multiplechoice_form($field_row,$post_field_content);	
			break;
		case 'singlechoice':
			$content_field .= 	singlechoice_form($field_row,$post_field_content);
			break;
		case 'multipleinput':
			$content_field .= 	multipleinput_form($field_row,$post_field_content);
			break;
		case 'input':
			$content_field .= 	input_form($field_row,$post_field_content );
			break;
			}			
		$content_field .= "</div>";	
		$content_field .= '<div style="clear:both"> </div>';		
}// End foreach fields
//---------------


		$output .= $content_field;
			$output .= "<div class=\"mps_input_sector_1\">";	
			$output .= get_user_connection_form($user_connection);
			$output .= "</div>";
			$output .= '<div style="clear:both"> </div>';	
			$output .= "<div class=\"mps_input_sector_1\">";	
			$output .= get_postconnection_of_form('parent_of',$post_id);
			$output .= "</div>";
			$output .= '<div style="clear:both"> </div>';		
			$output .= "<div class=\"mps_input_sector_1\">";	
			$output .= get_postconnection_of_form('child_of',$post_id);
			$output .= "</div>";	
			
  		$output .= '<input type="hidden"  id="mps_action" name="mps_action" value="ok" >';
		$output .= '<input type="hidden"  id="mps_posttype_id" name="mps_posttype_id" value="'.$pt_id.'" >'; 	
 	
echo $output;



// ----------------------------------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------------------------------
//					FUNCTIONS
// ----------------------------------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------------------------------



function multiplechoice_form($field_row,$post_field_content){
// generate a multiplechoice checkbox formular

	$array_fieldchoice 	= unserialize($field_row['field_choices']);
	$field_table_name 	= $field_row['field_table_name'];
	
	$out = 		'<div title="'.$field_row['field_description'].'" class="mps_input_label">'.$field_row['field_name'].'</div>'; 	
	$out .= 	'<div class="mps_input_border">'; 	
	
		foreach ($array_fieldchoice as $choice) {
			$checked	= '';	
				foreach ($post_field_content as $row) {	 //look if a entry is like a choice
					if($choice == $row['value']){
					$checked = 'checked';
					};
				}
				
			$out .= '<div>';
			$out .= '<input '.$checked.' type="checkbox" id="'.$choice.'" name="f['.$field_table_name.'][]" value="'.$choice.'">';
			$out .= '<label for="'.$choice.'">'.$choice.'</label></div>';
		}; // End foreach
	
	$out .= '</div>'; 
	$out .= '<div style="clear:both"> </div>';	
	
	return $out;
}


function singlechoice_form($field_row,$post_field_content){
	
	$field_table_name 	= $field_row['field_table_name'];
	$field_name 		= $field_row['field_name'];
	$field_description 	= $field_row['field_description'];
	$array_fieldchoice 	= unserialize($field_row['field_choices']);
	$db_value 			= $post_field_content[0]['value'];
	
	$out =  '<label title="'.$field_description.'" class="mps_input_label" for="'.$field_table_name.'">'.$field_name.'</label>'; 	
	$out .= '<select title="'.$field_description.'" name="f['.$field_table_name.']" id="'.$field_table_name.'" size="1">'; 	
	$out .= '<option value="">-----</option>';
		
		foreach ($array_fieldchoice as $choice) {
			$selected = '';
			
			if($db_value == $choice){
			$selected = 'selected';			
			}
			
		 $out .= "<option ".$selected." value=\"".$choice."\">".$choice."</option>";
		}; // End foreach
	$out .= "</select>"; 	
	
	return $out;
}



function multipleinput_form($field_row,$post_field_content){
	
	$field_type 		= $field_row['field_type'];
	$field_table_name 	= $field_row['field_table_name'];
	$field_name 		= $field_row['field_name'];
	$field_description 	= $field_row['field_description'];

	$value = $post_field_content[0]['value'];
 	$out .= '<div class="mps_input_label">'.$field_name.'</div>';
	
	$out .= '<div class="mps_input_sector_2" >';
	$out .= '<div class="mps_input_all_elements" >';
	
	
	//$out .= '<label title="'.$field_description.'" class="mps_input_label" for="'.$field_table_name.'">'.$field_name.'</label>';
	
	if(count($post_field_content) < 1 ){
		$post_field_content[0]['value'] = '';
	}

	foreach ($post_field_content  as $content_row){

	$value = $content_row['value'];
	$out .= '<div class="mps_input_element" >';
	
	switch ($field_type) {
		case 'time':// Time
			$out .= '<input  title="'.$field_description.'" type="time" pattern="^((0?[1-9]|1[012])(:[0-5]\d){0,2}(\ [AP]M))$|^([01]\d|2[0-3])(:[0-5]\d){0,2}$" placeholder="HH:MM:SS" class="field_'.$field_type.'" id="'.$field_table_name.'" name="f['.$field_table_name.'][]" value="'.$value.'" size="25"/>';
			break;
		case 'integer': // Integer
			$out .= '<input  title="'.$field_description.'" class="field_'.$field_type.'" type="number" id="'.$field_table_name.'" name="f['.$field_table_name.'][]" value="'.$value.'" size="25"/>';
			break;
		case 'biginteger': // BIGINT
			$out .= '<input  title="'.$field_description.'"  class="field_'.$field_type.'" type="number" id="'.$field_table_name.'" name="f['.$field_table_name.'][]" value="'.$value.'" size="25"/>';
			break;
		case 'date': // Date
			$out .= '<input  title="'.$field_description.'"  placeholder="YYYY-MM-DD" class="field_'.$field_type.'" type="date" id="'.$field_table_name.'" name="f['.$field_table_name.'][]" value="'.$value.'" pattern="^([0-9][0-9])\d\d[- /.](0[0-9]|1[012])[- /.](0[0-9]|[12][0-9]|3[01])$" size="25"/>';
			break;
		case 'longtext':// Long-Text
			$out .= '<textarea  title="'.$field_description.'" class="field_'.$field_type.'" id="'.$field_table_name.'" name="f['.$field_table_name.'][]" >'.$value.'</textarea>';
			break;
		case 'text':// text
			$out .= '<input  title="'.$field_description.'" class="field_'.$field_type.'" type="text" id="'.$field_table_name.'" name="f['.$field_table_name.'][]" value="'.$value.'" size="25"/>';
			
			break;
		case 'coordinates':// Coordinates
		$ar = unserialize($value);
		
		$out .= '<input title="'.$field_description.'" class="field_'.$field_type.'_address" 
				type="text" readonly="readonly" id="'.$field_table_name.'_address" 
				name= "f['.$field_table_name.'][address]" 
				value="'.$ar['address'].'" >';
		
		$out .= '<input title="'.$field_description.'" class="field_'.$field_type.'" 
				type="hidden" readonly="readonly" id="'.$field_table_name.'" 
				name= "f['.$field_table_name.'][coordinates]" 
				value="'.$ar['coordinates'].'" >';
				
		$out .= '<input type="button" name="" id="add_coordinates" class="button" onclick="tdm_show_googlemap_box(\''.$field_table_name.'\')" value="add a place from GoogleMaps">';
			break;			
		default:
			$out .= $field_type;
		}
		
	$out .=	'<div title="delete this element!	" class="mps_button_delete_attribute" onclick="mps_delete_input(this)"></div>';	
	$out .= '</div>';
	}
	
	$out .= '</div>';
	$out .= '<div title="add element" class="mps_button_add_input_attribute" id="button_add_attribute_field" onclick="mps_add_empty_input(this)"></div>';
	
	$out .= '</div>';
	
	$out .= '<div style="clear:both"> </div>';
	return $out;
}











function input_form($field_row,$post_field_content){
	
	$field_type 		= $field_row['field_type'];
	$field_table_name 	= $field_row['field_table_name'];
	$field_name 		= $field_row['field_name'];
	$field_description 	= $field_row['field_description'];

	$value = $post_field_content[0]['value'];
	
	$out = '<label title="'.$field_description.'" title="" class="mps_input_label" for="'.$field_table_name.'">'.$field_name.'</label>'; 	
	switch ($field_type) {
		case 'time':// Time
			$out .= '<input  title="'.$field_description.'" type="time" pattern="^((0?[1-9]|1[012])(:[0-5]\d){0,2}(\ [AP]M))$|^([01]\d|2[0-3])(:[0-5]\d){0,2}$" placeholder="HH:MM:SS" class="field_'.$field_type.'" id="'.$field_table_name.'" name="f['.$field_table_name.']" value="'.$value.'" size="25"/>';
			break;
		case 'integer': // Integer
			$out .= '<input  title="'.$field_description.'" class="field_'.$field_type.'" type="number" id="'.$field_table_name.'" name="f['.$field_table_name.']" value="'.$value.'" size="25"/>';
			break;
		case 'biginteger': // BIGINT
			$out .= '<input  title="'.$field_description.'"  class="field_'.$field_type.'" type="number" id="'.$field_table_name.'" name="f['.$field_table_name.']" value="'.$value.'" size="25"/>';
			break;
		case 'date': // Date
			$out .= '<input  title="'.$field_description.'"  placeholder="YYYY-MM-DD" class="field_'.$field_type.'" type="date" id="'.$field_table_name.'" name="f['.$field_table_name.']" value="'.$value.'" pattern="^([0-9][0-9])\d\d[- /.](0[0-9]|1[012])[- /.](0[0-9]|[12][0-9]|3[01])$" size="25"/>';
			break;
		case 'longtext':// Long-Text
			$out .= '<textarea  title="'.$field_description.'" class="field_'.$field_type.'" id="'.$field_table_name.'" name="f['.$field_table_name.'][]" >'.$value.'</textarea>';
			break;
		case 'text':// text
			$out .= '<input  title="'.$field_description.'" class="field_'.$field_type.'" type="text" id="'.$field_table_name.'" name="f['.$field_table_name.']" value="'.$value.'" size="25"/>';
			break;
		case 'coordinates':// Coordinates
		$ar = $teile = explode("_;_", $value);
		
		
		$out .= '<input title="'.$field_description.'" class="field_'.$field_type.'" 
				type="hidden" readonly="readonly" id="'.$field_table_name.'" 
				name= "f['.$field_table_name.']" 
				value="'.$value.'" >';

		$out .= '<input title="'.$field_description.'" 
				type="text" readonly="readonly" id="'.$field_table_name.'_address" 
				name= "empty['.$field_table_name.'][address]" 
				value="'.$ar[0].'" >';
		
		$out .= '<input title="'.$field_description.'" 
				type="hidden" id="'.$field_table_name.'_coordinates" 
				name= "empty['.$field_table_name.'][coordinates]" 
				value="'.$ar[1].'" >';
				
		$out .= '<input type="button" name="" id="add_coordinates" class="button" onclick="tdm_show_googlemap_box(\''.$field_table_name.'\')" value="add a place from GoogleMaps">';
			break;			
		default:
			$out .= $field_type;
		}


$out .= '<div style="clear:both"> </div>';
	return $out;
}




















function get_user_connection_form($user_connection){
global $wpdb;
$wp_user_search = $wpdb->get_results("SELECT ID, display_name FROM $wpdb->users ORDER BY display_name");
	
	$out .= "<div class=\"mps_input_label\">"; 	
	$out .= "User Connection:";
	$out .= "</div>"; 	
	$out .= '<div class="mps_input_border">'; 	

	
		foreach ( $wp_user_search as $userid ) {
		$user_id       = (int) $userid->ID;
		$user_login    = stripslashes($userid->user_login);
		$display_name  = stripslashes($userid->display_name);
		$checked = '';
				
			foreach($user_connection as $user_connection_row){
				if($user_connection_row['user_id'] == $user_id){
				$checked = 'checked';	
				}
			}
		
		$out .= '<div><input '.$checked.' type="checkbox" id="'.$user_id.'" name="userconnection[]" value="'.$user_id.'">  <label for="'.$user_id.'">'.$display_name.'</label></div>';
			
		}
				
$out .= "</div>"; 
$out .= "<div style=\"clear:both\"></div>";

return $out;
}

//------------------------------------------------------------------------------
// generate post connecntion --------------------------------------------------- 
function get_postconnection_of_form($input,$post_id){
global $wpdb;
global $table_prefix;

		switch ($input) {
			case 'child_of':
			   $title = "Child element of:";
			   $div_id = "tdm_child_of";
			   $button_title = "add parent element";
			   $button_id = "add_parent_element";
			   $select_field_name = "post_id_parent";
			   $cond_field_name = "post_id_child";
				break;
			case 'parent_of':
				$title = "Parent element of:";
				$div_id = "tdm_parent_of";
				$button_title = "add child element";
				$button_id = "add_child_element";
				$select_field_name = "post_id_child";
				$cond_field_name = "post_id_parent";
				break;
		}

$all_postconnections = $wpdb->get_results( "SELECT ".$select_field_name." as postid FROM  ".$table_prefix."mps_core_postconnection WHERE ".$cond_field_name." = '".$post_id."' ",ARRAY_A);

if(count($all_postconnections) > 0){

	$postconnections_array = '';
		foreach ($all_postconnections as $row){
			$postconnections_array[] = mps_get_post_information_line($row['postid']);
		};
	
	usort($postconnections_array, "mps_sort_postconnection");
		
	$index = 1;
	$max_index = count($postconnections_array);

	foreach($postconnections_array as $connect_post){
		$actual_post_pluralname = $connect_post['pt_pluralname'];
		
		$con_post_id 	= $connect_post['post_id'];
		$con_pt_name	= $connect_post['pt_name'];
		$con_post_line	= $connect_post['post_line'];
		
		// if first index
		if($index == 1){
			$list .= '<div class="tdm_postconnection_grp1" id="grp_'.$input.'_'.$con_pt_name.'">
						<div class="tdm_grp2_left">'.$actual_post_pluralname.'</div>
						<div class="tdm_postconnection_posts">'; 
		}
	
		// if
		if(($last_post_pluralname != $actual_post_pluralname) && ($index > 1)){
			$list .= "</div></div>";
			$list .= '<div class="tdm_postconnection_grp1" id="grp_'.$input.'_'.$con_pt_name.'">
						<div class="tdm_grp2_left">'.$actual_post_pluralname.'</div>
						<div class="tdm_postconnection_posts">'; 
		}
		
		// create lines-------
		$line = '<div class="tdm_post_line" id="'.$input.'_'.$con_post_id.'">';
		$line .= $con_post_line;
		$line .= '<span title="ID of the post.">'.$con_post_id.'</span>';
		$line .= '<span class="tdm_post_connection_del_button" onclick="del_postconnection(this)" alt="delete" >&nbsp;</span>';
		$line .= '<input type="text" name="'.$input.'[]" id="input_'.$input.'_'.$con_post_id.'" class="tdm_post_connection_input" value="'.$con_post_id.'">';

		$line .= '</div>';
		// End lines-----------
		
		$list .= $line;
	
		if($index == $max_index){
		$list .= '</div></div>';		
		}
		
		$last_post_pluralname = $connect_post['pt_pluralname'];
		$index++;	
	};
	
};


$out ='';

	$out .= '<div class="mps_input_label">'; 	
	$out .= $title;
	
	$out .= "</div>";

$out .= '<div class="tdm_postconnection" id="'.$div_id.'">';

$out .= '<input type="button" name="" id="'.$button_id.'" class="button" onclick="tdm_show_postconnection_box(\''.$input.'\',\'post\')" value="'.$button_title.'">';

$out .= $list;
$out .= '</div>';

$out .= "<div style=\"clear:both\"></div>";

return $out;
};
?>

<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
<script>
jQuery(document).ready(function($) {
	mps_init_metabox();
});



</script>