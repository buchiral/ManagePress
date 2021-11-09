<?php
global $wpdb;
global $table_prefix;

$custom_posttypes = $wpdb->get_row( " SELECT * FROM  ".$table_prefix."mps_core_posttype WHERE posttype = '".$atts['posttype']."' ",ARRAY_A);
$pt_id = $custom_posttypes['ID'];

$all_fields	= $wpdb->get_results( "SELECT * FROM  ".$table_prefix."mps_core_fields WHERE posttype_id = ".$pt_id." and field_display = 0 ",ARRAY_A);	


$post = new WP_Query('post_type='.$atts['posttype'].''); //get Object with all posts
  

// variables for google map
$array_coordinates 	= '';
$array_html			= '';
$array_titles 		= '';
$show_map			= false;



if ($post->have_posts())
        while ($post->have_posts()): $post->the_post();
           
		   
			$posttype_name  = $atts['posttype'];
			$post_id 		= get_the_ID();


$out = '';
$thead='';// always reset 
$thead .= '<th>'; 	
$thead .= 'Title';
$thead .= "</th>";

foreach($all_fields as $field){

	$field_name 		= $field['field_name'];
 	$field_table_name	= $field['field_table_name'];
 	$field_type 		= $field['field_type'];
 	$field_definition	= $field['field_definition'];
 	$field_choice 		= unserialize($field['field_choices']);
	$field_description	= $field['field_description'];

	$post_field_content	= $wpdb->get_results("SELECT * FROM  ".$table_prefix."mps_field_".$field_table_name." WHERE post_id =  ".$post_id." ",ARRAY_A);
	
	$db_value = '';
	
	if (in_array($field_definition,array('multiplechoice','multipleinput'))){	
		for ($i = 0; $i < count($post_field_content); $i++) {
			$value 		=    $post_field_content[$i]['value'];
			$db_value 	.= ''.$value.', ';  		
		}
		$db_value = substr($db_value, 0, -2);
	}
	

	if (in_array($field_definition,array('input','singlechoice'))){	
		$db_value 	=     $post_field_content[0]['value'];

		if ($field_type == 'coordinates'){
				if (!empty($db_value)){
				$ar = explode("_;_", $db_value);
				$db_value =  $ar[0];
				
				$array_coordinates[] 	=  $ar[1];
				//$array_titles[] 		= '<a href="'.get_permalink().'"> '.get_the_title().'</a>';
				$array_html[] 		= '<a href="'.get_permalink().'"> '.get_the_title().'</a><br>Address: '. $ar[0].'<br>';
				$array_titles[] 	= get_the_title();
				$show_map = true;
				}
		};
	}
	

	
	if (in_array($field_type,array('time','date'))){
	$special_class = 'class="td_nowrap"';
	} else {
	$special_class = '';	
	}

	if(empty($post_field_content)){
	$db_value='-';
	}
	
	
	$out .= '<td '.$special_class.'>'; 	
	$out .= $db_value;
	$out .= "</td>";	
	

	$thead .= '<th>'; 	
	$thead .= $field_name;
	$thead .= "</th>";
}

// user connenction

$table_name			= $table_prefix."mps_core_fields";
$user_search = $wpdb->get_results(
	"SELECT `us`.`display_name` 
	FROM `".$table_prefix."mps_core_userconnection` tdm_us, `$wpdb->users` us 
	WHERE `us`.`ID` = `tdm_us`.`user_id`
	and `tdm_us`.`post_id` = ".$post_id." ORDER BY display_name" ,ARRAY_A);

$user_value = '';
if(count($user_search)>0){
	foreach ($user_search as $user_search_row ) {
				$user_value .= '<div class="tdm_userconnect">'.$user_search_row['display_name'].' </div>';	
		}

} else {

$user_value = '-';	
}

	$out .= "<td>"; 	
	$out .= $user_value;
	$out .= "</td>";	

$thead .= '<th>'; 	
$thead .= 'Userconnection';
$thead .= "</th>";	


$tbody .= "<tr>";
	$tbody .= '<td class="td_nowrap">'; 	
	$tbody .= '<a href="'.get_permalink().'"> '.get_the_title().'</a>';
	$tbody .= "</td>"; 

	
	$tbody .= $out;
$tbody .= "</tr>";	



			
    endwhile;
  else
    return; // no posts found
wp_reset_query();

// ------------------------------------------------------------------------------------------------
// Create Content
// ------------------------------------------------------------------------------------------------

	if($show_map){
		$map_link 		= 	'	<div id="tab2_link" class="tab_button">Map</div>';
		$map_content 	= 	'	<div id="tab2" style="display:none"class="tdm_list_content_1">
									<div id="map_canvas" style=" width:100%; height:350px;"></div>
								</div>';
	} else {
		$map_link = '';
		$map_content= '';	}



$content = '<div class="tdm_list_template" >';
	$content .= '<div class="navi" > 
						<div id="tab1_link" class="tab_button active"> 	Table </div>
						'.$map_link.'
				</div>';
				
	$content .= '<div style="clear:both"> </div> ';
	
	$content .= '<div id="tab1" class="tdm_list_content_2">
					<table class="tdm_table" id="mps_datatable_list">
						<thead>'.$thead."</thead>
						<tbody>".$tbody."</tbody>
					</table>
				</div>";


	$content .= $map_content;

				
$content .= "</div>";



$content .= "<script>		
					
			jQuery(document).ready(function(){ mps_ini_frontend_list(); });    
			
			</script>";


include ("mps_generate_frontend_map.php");


 


?>


