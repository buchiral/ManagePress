<?php
global $table_prefix;
global $wpdb;

$field_description 		=  	$_REQUEST['field_description'];
$field_name 			=  	$_REQUEST['field_name'];
$field_table_name 		=  	$_REQUEST['field_table_name'];
$field_table_name_old 	=  	$_REQUEST['field_table_name_old'];
$field_definition 		=  	$_REQUEST['field_definition'];
$field_choices 			=	$_REQUEST['field_choices'];
$field_type 			=	$_REQUEST['field_type'];
$field_display 			=	$_REQUEST['field_display'];

$field_id				=   $_REQUEST['field_id'];
$pt_id 					= 	$_REQUEST['posttype_id'];

// get posttype_name
$pt_name	= $wpdb->get_var(' SELECT posttype FROM   `'.$table_prefix.'mps_core_posttype` WHERE id = '.$pt_id.' ');
//-------

		
if ($field_id == 'new'){
// ---------------------------------------------------------------------------
// CREATE a new field
// ---------------------------------------------------------------------------

	$sql = "INSERT INTO  ".$table_prefix."mps_core_fields ( 	
											`posttype_id` ,
											`field_name` ,
											`field_table_name` ,
											`field_type` ,
											`field_definition` ,
											`field_description` ,
											`field_choices`,
											`field_display` ) VALUES";
		
					$sql .= " ( ";
						$sql .= " '".$pt_id."', ";
						$sql .= " '".$field_name."', ";
						$sql .= " 'empty', ";
						$sql .= " '".$field_type."', ";
						$sql .= " '".$field_definition."', ";
						$sql .= " '".$field_description."', ";
						$sql .= " '".serialize($field_choices)."', ";
						$sql .= " '".$field_display."' ";
					$sql .= " )";
	
	$insert_row	= $wpdb->query($sql); // Insert field in the table core_fields
	$lastid 	= $wpdb->insert_id; // get the field-ID from the Insert
	
	
	$new_field_table_name 	= preg_replace('/[^a-zA-Z0-9_]/u','', $field_name);
	$new_field_table_name 	= strtolower($new_field_table_name).'_'.$lastid; // create a new table with the field_name and the field_id	
	
	
	$update_field_table_name = $wpdb->query("UPDATE  `".$table_prefix."mps_core_fields` 
											SET `field_table_name` = '".$new_field_table_name."' 
											WHERE 	`field_id` = ".$lastid."; "); // update the	field_table_name
	
	// create field table 
	$make_query = $wpdb->query("CREATE TABLE ".$table_prefix."mps_field_".$new_field_table_name."(
													ID BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
													post_id BIGINT NOT NULL,
													".mps_get_sql_field_input('value',$field_type)."
													)ENGINE = INNODB;"		);
																			
																			
																			
} else {
// ---------------------------------------------------------------------------
// UPDATE a field
// ---------------------------------------------------------------------------

	$new_field_table_name 	= preg_replace('/[^a-zA-Z0-9_]/u','', $field_name);
	$new_field_table_name 	= strtolower($new_field_table_name).'_'.$field_id; // create a new table with the field_name and the field_id	
		
	// update all field elements		
	$sql = "UPDATE  ".$table_prefix."mps_core_fields SET  
														`field_name` 		= '".$field_name."',
														`field_table_name` 	= '".$new_field_table_name."',
														`field_type` 		= '".$field_type."',
														`field_definition` 	= '".$field_definition."',
														`field_description`	= '".$field_description."',
														`field_choices`		= '".serialize($field_choices)."',
														`field_display`		= '".$field_display."'
												
														WHERE 	`field_id` = ".$field_id."; ";
	$update_field	= $wpdb->query($sql); // update on core_fields

	// rename the field table  to the new field name 
	if ($field_table_name_old != $new_field_table_name){
		$old_table = $table_prefix."mps_field_".$field_table_name_old;
		$new_table = $table_prefix."mps_field_".$new_field_table_name;
		$sql = $wpdb->query("RENAME TABLE  `".$old_table."` TO  `".$new_table."` ;");
	};
	
	// Change the field table
	$table_name 	= $table_prefix."mps_field_".$new_field_table_name;		
	$change_field	= $wpdb->query('ALTER TABLE  `'.$table_name.'` CHANGE `value`  '.mps_get_sql_field_input('value',$field_type).' '); // change field
	
// END IF	
};


echo 'ok';



// FUNCTIONS


function mps_get_sql_field_input($field_table_name,$field_type){
// make the sql code for the fields
// using in alert and create events

	switch ($field_type) {
			case 'time':// Datetime
				$out = "  `".$field_table_name."` TIME NOT NULL DEFAULT  '00:00:00'";
				break;
			case 'integer': // Integer
				$out = "  `".$field_table_name."` INT NOT NULL DEFAULT  '0'"; 
				break;
			case 'biginteger': // BIGINT
				$out = "  `".$field_table_name."` BIGINT NOT NULL DEFAULT  '0'"; 
				break;
			case 'date':// Date
				$out = "  `".$field_table_name."` DATE NOT NULL DEFAULT  '0000-00-00' ";
				break;
			case 'longtext':// Long-Text
				$out = "  `".$field_table_name."` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
				break;
			case 'text':// text
				$out = "  `".$field_table_name."` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL"; 
				break;
			case 'coordinates':// longtext
				$out = "  `".$field_table_name."` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL"; 
				break;
			default:
				$out ="  `".$field_table_name."` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL"; //- Text
		}
	return $out;
}

die();
?>