<?php
global $table_prefix;
global $wpdb;

$array_posttype = $_REQUEST['post_type'];
$array_supports = $_REQUEST['supports'];
$menu_icon 		= $_REQUEST['menu_icon'];

$pt_id = $array_posttype['id'];

if(empty($pt_id)){ 

				// WP Function Output a list all registered post types
				$post_types = get_post_types('','names');
						
				// If the posttype aalready exists - die() and give an echo zo the ajax
				if(in_array($array_posttype['post_type'],$post_types)){
					echo 'This posttype exists already'; 
					die();			
					};

				
				// Create Posttype to core_posttype
								
				$sql = "INSERT INTO  ".$table_prefix."mps_core_posttype ( 	`posttype`, 
														`singularname`,
														`pluralname`,
														`description`,
														`posttype_args`,
														`supports`,
														`menu_icon`
														 ) VALUES";
								$sql .= " ( ";
								$sql .= " '".$array_posttype['post_type']."', ";
								$sql .= " '".$array_posttype['singular_name']."', ";
								$sql .= " '".$array_posttype['plural_name']."', ";
								$sql .= " '".$array_posttype['description']."', ";
								$sql .= " 'empty', ";
								$sql .= " '".serialize($array_supports)."', ";
								$sql .= " '".$menu_icon."' ";
								$sql .= " );";
				
				$insert_posttype	= $wpdb->query($sql);	
				$last_posttype_id 	= $wpdb->insert_id;
				
				// create posttype table with fields--------------- // pt = post type
				$new_table	= $table_prefix."mps_pt_".$array_posttype["post_type"];
				
				$sql = "";
								
				$make_query = $wpdb->query("CREATE TABLE  ".$new_table." ( 	
												post_id BIGINT NOT NULL,
												ID BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY
												)ENGINE = INNODB;");
} else {
// Update Posttype to core_posttype
		
				
		$posttype_information= 	$wpdb->get_row("SELECT * FROM  ".$table_prefix."mps_core_posttype WHERE ID = ".$array_posttype['id']." ",ARRAY_A,0);
		$old_posttype_name 	 = 	$posttype_information['posttype'];
		$new_posttype_name	 = 	$array_posttype['post_type'];
	
	//compare old name with the new					
	if ($old_posttype_name != $new_posttype_name){

		// update all posts to the new posttype
		$sql = $wpdb->query("UPDATE $wpdb->posts SET `wp_posts`.`post_type`='".$new_posttype_name."' 
							WHERE `wp_posts`.`post_type` = '".$old_posttype_name."'");
		
		//rename the pt_table  to the new posttype name 	
		$old_table = $table_prefix."mps_pt_".$old_posttype_name;
		$new_table = $table_prefix."mps_pt_".$new_posttype_name;
		$sql = $wpdb->query("RENAME TABLE  `".$old_table."` TO  `".$new_table."` ;");
	
	}
				
		//update the core posttype table with the new informations
		$sql = $wpdb->query("UPDATE ".$table_prefix."mps_core_posttype SET 

									`posttype`		='".$new_posttype_name."',
									`singularname` 	=  '".$array_posttype['singular_name']."',
									`pluralname` 	=  '".$array_posttype['plural_name']."',
									`description` 	=  '".$array_posttype['description']."',
									`posttype_args` =  'leeer',
									`menu_icon` 	=  '".$menu_icon."',
									`supports` 		=  '".serialize($array_supports)."'
			
							 WHERE `ID` = ".$array_posttype['id']);
							 
							 
// End IF							 
};


echo 'ok';

die();

?>