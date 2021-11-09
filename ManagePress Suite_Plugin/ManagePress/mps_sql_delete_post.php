<?php
global $wpdb;
global $table_prefix;




$pt_name		= get_post_type($post_id); 
$pt_id 			= tdm_get_posttype_id($pt_name);



$insert_fields 	= $wpdb->query("INSERT INTO cxvxcv (`post_id` ) VALUES ( '".$postid."-".$pt_name."-".$pt_id ."' );");	


//---- delete post_id from pt_[posttype]--------
$table_pt 		= $table_prefix."mps_pt_".$pt_name;
$delete_action 	=  $wpdb->query('DELETE FROM '.$table_pt .' WHERE post_id = '.$post_id.' ');



// get all fields from this post type
$all_fields			= $wpdb->get_results("SELECT * FROM  ".$table_prefix."mps_core_fields WHERE posttype_id = ".$pt_id." ",ARRAY_A);

// delete all entry from this post from the fields
foreach ($all_fields as $field_row) {
	$field_table_name 	= $table_prefix."mps_field_".$field_row['field_table_name'];
	$delete_action 		= $wpdb->query('DELETE FROM '.$field_table_name.' WHERE post_id = '.$post_id.' ');
$insert_fields 	= $wpdb->query("INSERT INTO cxvxcv (`post_id` ) VALUES ( '".$postid."-".$field_table_name."' );");	
};



// -------------------------------------------------------------
// Save Userconnection
// -------------------------------------------------------------
$table_name 	=  $db_name_wp.".".$table_prefix."mps_core_userconnection";

$delete_action 	=  $wpdb->query('DELETE FROM '.$table_name .' WHERE post_id = '.$post_id.' ');

// -------------------------------------------------------------
// Save Parent of Connection
// -------------------------------------------------------------


$table_name 			=  $table_prefix."mps_core_postconnection";


$delete_action 		=  $wpdb->query('DELETE FROM '.$table_name .' WHERE post_id_parent 	= '.$post_id.' ');
$delete_action 		=  $wpdb->query('DELETE FROM '.$table_name .' WHERE post_id_child 	= '.$post_id.' ');


?>