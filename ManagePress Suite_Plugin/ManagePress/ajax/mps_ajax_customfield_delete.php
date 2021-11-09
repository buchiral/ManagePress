<?php
global $table_prefix;
global $wpdb;

$field_id	=   $_REQUEST['field_id'];
$pt_id 		=	$_REQUEST['posttype_id'];

// get field_table_name
$pt_fields_row		= $wpdb->get_row(" SELECT * FROM  ".$table_prefix."mps_core_fields WHERE field_id = ".$field_id." ",ARRAY_A,0);
$field_table_name 	= $pt_fields_row['field_table_name'];

// get posttype_name
$pt_name	= $wpdb->get_var("SELECT posttype FROM ".$table_prefix."mps_core_posttype WHERE id = ".$pt_id." ");


// Delete field from pt_table 
$del_query	= $wpdb->query('ALTER TABLE `'.$table_prefix."mps_pt_".$pt_name.'` DROP  `'.$field_table_name.'`  ');

// Delete field from core_fields
$del_query	= $wpdb->query("DELETE FROM  ".$table_prefix."mps_core_fields WHERE  `field_id` =".$field_id."; ");

// Delete field TABLE 
$del_query	= $wpdb->query("DROP TABLE `".$table_prefix."mps_field_".$field_table_name."` ");


echo $field_id;
?>

<?php
die();
?>