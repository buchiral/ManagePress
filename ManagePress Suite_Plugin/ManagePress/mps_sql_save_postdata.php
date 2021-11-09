<?php
global $wpdb;
global $table_prefix;

$pt_id 			= $_POST['mps_posttype_id'];
$pt_name 		= $_POST["post_type"];

$mydata 		= $_POST['f'];
$mydata_array 	= $mydata;

//---- save fields in table--------
$table_pt 		= $table_prefix."mps_pt_".$pt_name;
$delete_action 	=  $wpdb->query('DELETE FROM '.$table_pt .' WHERE post_id = '.$post_id.' ');
$insert_fields 	= $wpdb->query("INSERT INTO ".$table_pt." (`post_id` ) VALUES ( '".$post_id."' );");	


// get all fields from this post type
$all_fields			= $wpdb->get_results("SELECT * FROM  ".$table_prefix."mps_core_fields WHERE posttype_id = ".$pt_id." ",ARRAY_A);

// delete all entry from this post from the fields
foreach ($all_fields as $field_row) {
	$field_table_name 	= $table_prefix."mps_field_".$field_row['field_table_name'];
	$delete_action 		= $wpdb->query('DELETE FROM '.$field_table_name .' WHERE post_id = '.$post_id.' ');
};


// insert all values in the table of each field
foreach ($mydata_array as $field_name => $field_value) {

		$field_table_name 	= $table_prefix."mps_field_".$field_name."";

		if(is_array($field_value)){ // if multiple choice then make a foreach over every field
			foreach($field_value as $value){
				if(!empty($value)){
				$insert_fields = $wpdb->query("INSERT INTO ".$field_table_name." (`value`, `post_id` ) VALUES ( '".$value."', '".$post_id."' );");				
				}
			};
		} else {
			
			if(!empty($field_value)){
			$insert_fields = $wpdb->query("INSERT INTO ".$field_table_name." (`value`, `post_id`) VALUES ( '".$field_value."', '".$post_id."'  );");				
			}
		
		}




};


// -------------------------------------------------------------
// Save Userconnection
// -------------------------------------------------------------

$userconnection = $_POST['userconnection'];

$table_name 	=  $db_name_wp.".".$table_prefix."mps_core_userconnection";

$delete_action 	=  $wpdb->query('DELETE FROM '.$table_name .' WHERE post_id = '.$post_id.' ');

		$sql = "INSERT INTO  ".$table_name." (";
		$sql .= " `post_id`, `user_id` ) VALUES";
		
		for($i = 0, $groesse = sizeof($userconnection); $i < $groesse; ++$i){
				
				$sql .= " ( ";
				$sql .= " '".$post_id."', ";
				$sql .= " '".$userconnection[$i]."' ";
				$sql .= " ),";
				
						}
	$sql=substr($sql, 0, -1);//delete last comma
	$insert_fields = $wpdb->query($sql);			




// -------------------------------------------------------------
// Save Parent of Connection
// -------------------------------------------------------------

$parent_of_connection 	= $_POST['parent_of'];
$child_of_connection 	= $_POST['child_of'];
$table_name 			=  $table_prefix."mps_core_postconnection";


$delete_action 		=  $wpdb->query('DELETE FROM '.$table_name .' WHERE post_id_parent 	= '.$post_id.' ');
$delete_action 		=  $wpdb->query('DELETE FROM '.$table_name .' WHERE post_id_child 	= '.$post_id.' ');

		$sql = "INSERT INTO  ".$table_name." (";
		$sql .= " `post_id_parent`, `post_id_child` ) VALUES";
		
		for($i = 0, $groesse = sizeof($parent_of_connection); $i < $groesse; ++$i){
				$sql .= " ( ";
				$sql .= " '".$post_id."', ";
				$sql .= " '".$parent_of_connection[$i]."' ";
				$sql .= " ),";
						}
						
		for($i = 0, $groesse = sizeof($child_of_connection); $i < $groesse; ++$i){
				
				$sql .= "( '".$child_of_connection[$i]."', ";
				$sql .= " '".$post_id."' ),";
						}
						
$sql=substr($sql, 0, -1); //delete last comma
$insert_fields = $wpdb->query($sql); // make query


?>