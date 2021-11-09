<?php
global $wpdb;
global $table_prefix;

$wp_user_search 	= $wpdb->get_results("SELECT ID, display_name FROM $wpdb->users ORDER BY display_name");
$user_connection	= $wpdb->get_results("SELECT * FROM  ".$table_prefix."mps_core_userconnection ",ARRAY_A);

$i = 0;
$all = 0;
	foreach ( $wp_user_search as $userid ) {		 	
	$int = 0;
	$user_id       = (int) $userid->ID;		
			foreach($user_connection as $user_connection_row){
				
				$post_array = get_post($user_connection_row['post_id'], ARRAY_A);
				$post_status = $post_array['post_status'];
				
				if($post_status == 'trash'){
				}else {
					if($user_connection_row['user_id'] == $user_id){
					$int++;
					$all++;
					}
				}
				
			}
			
	$array[] = $int;			
$i++;			
}


rsort($array);// sort that the user with the most connection is at top

$max_connections = $array[0]; // the user with the most connection
$out .= '<h4>Statistic Userconnection</h4><br>';
$out .= 'Total: '.$all.' Connections<br><br>';
$i = 0;

foreach ( $wp_user_search as $userid ) { // loop over all Users
	$user_id       = (int) $userid->ID;
	$user_login    = stripslashes($userid->user_login);
	$display_name  = stripslashes($userid->display_name);
	$checked = '';
	$int = 0;		
		 	
			foreach($user_connection as $user_connection_row){ // loop over all connection
				
				$post_array = get_post($user_connection_row['post_id'], ARRAY_A);
				$post_status = $post_array['post_status'];
				
				if($post_status == 'trash'){
				}else {
					if($user_connection_row['user_id'] == $user_id){
					$int++;
					}
				}
			}
	
	if($max_connections==0){
	$px = 0;	
	$all = 0.1;
	} else {
	$px = round(170/$max_connections * $int);
	}
	
	$percent = round($int / $all *100);

	$out .= '<div style="padding-top:2px" >';
	$out .= '<div style="float:left;width:130px">'.$display_name.'</div>';
	$out .= '<div style="float:left;width:300px">
				<div style="float:left;">'.$int.'&nbsp;</div>
				<div class="mps_barometer" style="width:'.$px.'px;">&nbsp;</div>
				<div style="float:left;">&nbsp;'.$percent.'%</div></div>';
	$out .= '</div><br>';	
			
$i++;			
}
	
	$out .= '<div style="clear:both"></div>';
	echo $out;	

?>