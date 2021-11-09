<?php
global $table_prefix;
global $wpdb;

$pt_id 		= $_REQUEST['posttype_id'];
$pt_name 	= $_REQUEST['posttype_name'];



$all_fields = $wpdb->get_results( "SELECT * FROM  `".$table_prefix."mps_core_fields` WHERE posttype_id = ".$pt_id." ",ARRAY_A);

//delete table of the fields
foreach ($all_fields as $field){
	$del_action =  $wpdb->query('DROP TABLE  '. $table_prefix."mps_field_".$field['field_table_name'].' ');
}

$del_action =  $wpdb->query('DROP TABLE  '.$table_prefix.'mps_pt_'.$pt_name.' ');
$del_action =  $wpdb->query('DELETE FROM '.$table_prefix.'mps_core_posttype WHERE ID = '.$pt_id.' ');
$del_action =  $wpdb->query('DELETE FROM '.$table_prefix.'mps_core_fields WHERE posttype_id = '.$pt_id.' ');

$result_posts    =  $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = '".$pt_name."' ",ARRAY_A); // Get all posts with the posttype

foreach ($result_posts as $post_row){
	$del_action =  $wpdb->query("DELETE FROM ".$table_prefix."mps_core_userconnection WHERE post_id = '".$post_row['ID']."' ");
	$del_action =  $wpdb->query("DELETE FROM ".$table_prefix."mps_core_postconnection WHERE post_id_parent = '".$post_row['ID']."' or post_id_child = '".$post_row['ID']."' ");	
	}

$del_action        =  $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = '".$pt_name."' "); // delete all post with this posttype


die();
?>