<?php
global $wpdb;
global $table_prefix;

$custom_posttypes = $wpdb->get_row( " SELECT * FROM  ".$table_prefix."mps_core_posttype WHERE posttype = '".$atts['posttype']."' ",ARRAY_A);
$pt_id = $custom_posttypes['ID'];

$all_fields	= $wpdb->get_results( "SELECT * FROM  ".$table_prefix."mps_core_fields WHERE posttype_id = ".$pt_id." and field_display = 0 ",ARRAY_A);	

$wp_user_search 	= $wpdb->get_results("SELECT ID, display_name FROM $wpdb->users ORDER BY display_name");
$user_id =  get_current_user_id();

$user_connection	= $wpdb->get_results("SELECT * FROM  ".$table_prefix."mps_core_userconnection where user_id = '".$user_id."' ",ARRAY_A);


foreach($user_connection as $user_con_row){

			$post_id 		= 	$user_con_row['post_id'];
			$pt_name		= get_post_type($post_id); 
			$posttype_name  = 	$pt_name;
			$post_array = get_post($post_id, ARRAY_A);

$array_coordinates 	= '';
$array_html			= '';
$array_titles 		= '';
$post_status		= $post_array['post_status'];


$permalink = get_edit_post_link($post_id);

$info = mps_get_post_information_line($post_id);

$pt_pluralname = get_post_type_object($pt_name)->labels->singular_name;

if(in_array($post_status,array('trash','')) ) {

}else{
$tbody .= "<tr>";

	$tbody .= "<td>"; 	
	$tbody .= $pt_pluralname;
	$tbody .= "</td>";

	$tbody .= "<td>"; 	
	$tbody .= $db_value = substr($info['post_line'], 0, -2);
	$tbody .= "</td>";	

	$tbody .= '<td class="td_nowrap">'; 	
	$tbody .= '<a title="edit this post" href="'.$permalink.'">'.$post_id.'</a>';
	$tbody .= "</td>";
	
$tbody .= "</tr>";

}

}


$thead='';// always reset 
$thead .= '<tr>'; 	
$thead .= '<th>'; 	
$thead .= 'Posttype';
$thead .= "</th>";
$thead .= '<th>'; 	
$thead .= 'Content';
$thead .= "</th>";
$thead .= '<th>'; 	
$thead .= 'Post ID';
$thead .= "</th>";
$thead .= '</tr>'; 	
echo $line;
// ------------------------------------------------------------------------------------------------
// Create Content
// ------------------------------------------------------------------------------------------------


	
	$content .= '
					<table class="tdm_table" id="mps_dashboard_datatable_list">
						<thead>'.$thead."</thead>
						<tbody>".$tbody."</tbody>
					</table>
				";



$content .= "<script>		
					
			jQuery(document).ready(function(){ mps_ini_dashboard_list(); });    
			
			</script>";


echo $content;
 


?>


