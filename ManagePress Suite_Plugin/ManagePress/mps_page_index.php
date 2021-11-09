<?php
global $wpdb;
global $plugname;
global $global_posttype_table;


if ( isset($_REQUEST['tdm_action']) ) {
	
	$action 	= $_REQUEST['tdm_action'];
	
	
	switch ($action) {
    case 'edit_posttype':
          
		  if ( isset($_REQUEST['tdm_posttype_id']) ) {
				$posttype_id = $_REQUEST['tdm_posttype_id'];
				include('mps_page_posttype_save.php');
				} else {
				echo "fehler";	
				}
		break;  		  
    case 'edit_fields':
        
				if (isset($_REQUEST['tdm_posttype_id']) ){
				$pt_id = $_REQUEST['tdm_posttype_id'];
				include('mps_page_customfield_show.php');
				} else {
				echo "fehler";	
				}
		
		
        break;
    case 'add_posttype':
        
				include('mps_page_posttype_save.php');
				
		break;		
    case 2:
        echo "i equals 2";
        break;
		}
	
} else {
	
	
include('mps_page_posttype_show.php');
	
}



?>

		
		        
        