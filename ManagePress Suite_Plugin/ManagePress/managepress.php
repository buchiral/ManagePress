<?php
/*
Plugin Name: ManagePress Suite
Plugin URI: 
Description: ManagePress Suite, Individual Data Managment for Everything (Posttypes and Custom fields)
Author: Ralph Buchi
Version: 1.0
Author URI: 
License: Copyright 2012 ZHAW Winterthur. All Rights Reserved.
*/

global $plugname;
global $table_prefix;
global $import_sql;
global $global_posttype_table;

include('mps_config.php');
include('mps_function.php');

$plugname 	= 'ManagePress';  // Filename 


register_activation_hook( __FILE__, 'mps_install' );
register_deactivation_hook( __FILE__,'mps_deactivate' );
register_uninstall_hook( __FILE__,'mps_uninstall') ;

add_action('init', 'mps_load_global_variable');// load global variables for preocessing 
add_action('init', 'mps_register_posttype' ); // register post type from DB
add_action('init', 'mps_load_scripts'); // load css-style and javascript-files
add_action('init', 'mps_init_custom_columns'); // modify WP-Tables with ManagePress Suite Fields
add_action('admin_menu'		, 'mps_create_admin_menu'); // register admin menu
add_action('save_post'		, 'mps_save_custom_postdata' ); // function for saving postdata
add_action('delete_post'	, 'mps_delete_post'); // delete rows from DB 
add_action('add_meta_boxes'	, 'mps_add_metaboxes' ); // make metaboxes for posts
add_action('wp_dashboard_setup', 'mps_load_dashboard_widgets' ); // create wirdgets

add_filter( 'the_content'	, 'mps_generate_frontend_content' ); // add fields-content to the single-view
add_filter( 'the_title'		, 'mps_generate_title' ); // change title of all posts without tile
add_shortcode('mps_list'	, 'mps_shortcode_list'); // load shortcodes




// ----------------------------------------------------------------------------------
// 				 Load Scripts and Styles						don't change order!!
// ----------------------------------------------------------------------------------
wp_enqueue_style('mps_css_1', plugins_url( 'css/stylesheet.css', __FILE__ ) );
wp_enqueue_style('mps_css_2', plugins_url( 'css/frontend_style.css', __FILE__ ) );
wp_enqueue_style('mps_fancybox_css', plugins_url( 'js/jquery/fancybox_2/jquery.fancybox.css', __FILE__ ));
wp_enqueue_style('mps_datepicker_css', plugins_url( 'js/jquery/datepicker/jquery-ui-1.8.18.custom.css', __FILE__ ));
wp_enqueue_style('mps_dataTables_1_css', plugins_url( 'js/jquery/dataTables/dataTables_1.9.0/css/custom_dataTable.css',__FILE__ ));
wp_enqueue_style('mps_dataTables_2_css', plugins_url( 'js/jquery/dataTables/TableTools_2.0.3/css/TableTools.css',__FILE__ ));

wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script('jquery-ui-slider');
wp_enqueue_script('jquery-ui-tabs');

function mps_load_scripts(){
	//don't change order!!
	wp_enqueue_script('mps_js_core',plugins_url( 'js/js_core.js', __FILE__ ), array( 'jquery','jquery-ui-core','jquery-ui-datepicker','jquery-ui-tabs','jquery-ui-slider' ));									
	wp_enqueue_script('mps_js_fancybox_1',plugins_url( 'js/jquery/fancybox_2/jquery.fancybox.pack.js', __FILE__ ),array( 'jquery' ));
	wp_enqueue_script('mps_js_fancybox_2',plugins_url( 'js/jquery/fancybox_2/jquery.mousewheel-3.0.6.pack.js', __FILE__ ),array( 'jquery' ));
	wp_enqueue_script('mps_js_dataTables_1', plugins_url( 'js/jquery/dataTables/dataTables_1.9.0/js/jquery.dataTables.min.js', __FILE__ ),array( 'jquery' ));
	wp_enqueue_script('mps_js_dataTables_2', plugins_url( 'js/jquery/dataTables/TableTools_2.0.3/js/ZeroClipboard.js', __FILE__ ),array( 'jquery' ));
	wp_enqueue_script('mps_js_dataTables_3', plugins_url( 'js/jquery/dataTables/TableTools_2.0.3/js/TableTools.min.js', __FILE__ ),array( 'jquery' ));

}




// ----------------------------------------------------------------------------------
// 				 START  Install / Uninstall
// ----------------------------------------------------------------------------------
function mps_install(){ //register_activation_hook
	global $table_prefix;
	global $wpdb;
	global $import_sql;
	
	$sql = "CREATE TABLE  `".$table_prefix."mps_core_userconnection` ( 	
			`ID` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`post_id` BIGINT NOT NULL ,
			`user_id` BIGINT NOT NULL) ENGINE = INNODB;";
	
	$make_query = $wpdb->query($sql);
	
	
	$sql = "CREATE TABLE `".$table_prefix."mps_core_postconnection` ( 	
			`ID` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`post_id_parent` BIGINT NOT NULL ,
			`post_id_child` BIGINT NOT NULL ) ENGINE = INNODB;";
	
	$make_query = $wpdb->query($sql);
	
	
	$sql = "CREATE TABLE  `".$table_prefix."mps_core_posttype`(
			`ID` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`posttype` 		TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`singularname` 	TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`pluralname` 	TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`description` 	LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			`posttype_args` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			`supports` 		LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			`menu_icon` 	TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ) ENGINE = INNODB;";
	
	$make_query = $wpdb->query($sql);
	

	$sql = "CREATE TABLE  `".$table_prefix."mps_core_fields` ( 	
			`field_id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`posttype_id` BIGINT NOT NULL ,
			`field_name` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`field_table_name` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`field_type` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`field_definition` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`field_description` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`field_choices` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`field_display` INT NOT NULL	) ENGINE = INNODB;";
	$make_query = $wpdb->query($sql);


	if(is_array($import_sql)){
		foreach($import_sql as $sql){
				$make_query = $wpdb->query($sql);
		}
	};
}; 


function mps_uninstall(){ //register_uninstall_hook
	global $table_prefix;
	global $wpdb;
	global $global_posttype_table;
	
	// load $global_posttype_table because unistall hook doesn't support all "ini" functions
	$global_posttype_table		= $wpdb->get_results( " SELECT * FROM  `".$table_prefix."mps_core_posttype` ",ARRAY_A);
	$all_fields 				= $wpdb->get_results( "SELECT * FROM  `".$table_prefix."mps_core_fields` ",ARRAY_A);


	$delete_action 		=  $wpdb->query('DROP TABLE `'.$table_prefix.'mps_core_fields` ');
	$delete_action 		=  $wpdb->query('DROP TABLE `'.$table_prefix.'mps_core_posttype` ');
	$delete_action 		=  $wpdb->query('DROP TABLE	`'.$table_prefix.'mps_core_userconnection` ');
	$delete_action 		=  $wpdb->query('DROP TABLE `'.$table_prefix.'mps_core_postconnection` ');
	
	foreach($global_posttype_table as $pt_row){
		$delete_action 		=  $wpdb->query('DROP TABLE `'.$table_prefix.'mps_pt_'.$pt_row['posttype'].'` ');
	}
	
	
	foreach ($all_fields as $field) {
		$field_table_name	= $field['field_table_name'];
		$delete_action 		=  $wpdb->query('DROP TABLE `'.$table_prefix.'mps_field_'.$field_table_name.'` ');
		}
	
};
	



// Load Globalvariable -----------------------
function mps_load_global_variable(){
	global $wpdb;
	global $table_prefix;
	global $global_posttype_table;
	
	$global_posttype_table		= $wpdb->get_results( " SELECT * FROM  `".$table_prefix."mps_core_posttype` ",ARRAY_A);
}




// Create adminmenu und submenu -----------------------
function mps_create_admin_menu() {
	global $plugname;
	global $mps_safe_mode;
	
	// If this Plugin is in Safe-Mode --> don't create Adminmenus
	if(!$mps_safe_mode){
		$plugin_icon =  plugins_url("images/posttype_icons/organization16.png", __FILE__);
		add_menu_page('ManagePress Suite', 'ManagePress', 'manage_options' ,$plugname , 'mps_content',$plugin_icon);
		add_submenu_page($plugname, 'ManagePress Suite: About', 'About', 'manage_options', $plugname.'_About', 'mps_content_about');
		add_submenu_page($plugname, 'ManagePress Suite: Export', 'Export', 'manage_options', $plugname.'_Export', 'mps_content_export');
	} else {
		$plugin_icon =  plugins_url("images/posttype_icons/organization16.png", __FILE__);
		add_menu_page('ManagePress Suite', 'ManagePress', 'manage_options' ,$plugname , 'mps_content_about',$plugin_icon);
		
		
	};
}
	
function mps_content(){
include 'mps_page_index.php';	}

function mps_content_export(){
include('mps_page_export.php');}

function mps_content_about(){
include('mps_page_about.php');}


function mps_save_custom_postdata($post_id) {
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return; };

	if ( 'ok' == $_POST['mps_action']) {
		include('mps_sql_save_postdata.php'); };

}


function mps_delete_post($post_id){
include('mps_sql_delete_post.php');
}


// POSTTYPES------------------------------------------------------------------------
// 				Register Posttypes
// ----------------------------------------------------------------------------------

function mps_register_posttype(){
// register post type from DB
global $wpdb;
global $global_posttype_table; // variable with all posttypes

	If(is_array($global_posttype_table)) {
		foreach ($global_posttype_table as $posttype_row )  // Loop over all posttypes in $global_posttype_table
		{
			if(is_array(unserialize($posttype_row['supports'])) ) // Proof if posttype has supports, not--> make an empty-array
				{$support_array = unserialize($posttype_row['supports']);
			} else {
				$support_array = array('');
			}
			
		$labels = array(
			'name' => 			$posttype_row['pluralname'],
			'singular_name' => 	$posttype_row['singularname'],
			'menu_name' => 		$posttype_row['pluralname'],
			'add_new_item' =>  'Add new '.$posttype_row['singularname']);
		
		$args = array(
			'labels' => $labels,
			'description' => $posttype_row['description'], 
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => false, 
			'hierarchical' => true,
			'menu_position' => null,
			'menu_icon' => plugins_url("images/posttype_icons/".$posttype_row['menu_icon']."", __FILE__),
			'supports' => $support_array	);
		 			  
		register_post_type($posttype_row['posttype'],$args); // register posttype
		}
	}
};


// METABOXES-------------------------------------------------------------------------
// 				Load Metaboxes
// ----------------------------------------------------------------------------------
function mps_add_metaboxes(){
global $global_posttype_table;

	If(is_array($global_posttype_table)) {
		foreach ( $global_posttype_table as $pt_row ){
		$pt_name 		= $pt_row['posttype']; 
		$pt_id 			= $pt_row['ID'];
		$metabox_title 	= 'Meta Box for '.$pt_row['pluralname'];
		add_meta_box('tdm_meta_box',$metabox_title, 'mps_generate_metabox_content',$pt_name,'normal','low', array( 'posttype_id' => $pt_id  ));
		}
	}
}
	
function mps_generate_metabox_content($post,$metabox) {
	include('mps_generate_metabox.php');
}



// CUSTOM COLUMNS--------------------------------------------------------------------
// 				Load custom Columns of posttypes - backend
// ----------------------------------------------------------------------------------
function mps_init_custom_columns(){
	global $global_posttype_table;

	foreach ($global_posttype_table as $row){
		$pt_name = $row['posttype'];
		
		$hook1 = 'manage_'.$pt_name.'_posts_custom_column';
		$hook2 = 'manage_'.$pt_name.'_posts_columns';
			
		add_action($hook1, 'mps_set_rows', 10, 2 ); // 10 = priority of the action (default 10) / 2 = accepted_args
		add_filter($hook2, 'mps_set_columns_header');
	}
}

function mps_set_columns_header($columns) {
	global $wpdb;
	global $table_prefix;
	
	$pt_name 		= $_REQUEST['post_type'];
	$pt_id 			= tdm_get_posttype_id($pt_name);
		
	$all_fields 	= $wpdb->get_results( " SELECT * FROM `".$table_prefix."mps_core_fields` WHERE posttype_id = ".$pt_id." and field_display = 0 ",ARRAY_A);
	
	if(is_array($all_fields)){
		if(count($all_fields)>0){
			$columns_new = '';
				
				foreach ($all_fields  as $field){
					$columns_new[$field['field_table_name']] = $field['field_name'];
				}
			
			$columns_new = $columns + $columns_new;
			return $columns_new;
		};
	};
	return $columns;
}


function mps_set_rows($column_name, $post_id) {
 	global $wpdb;
	global $table_prefix;
	
	$pt_name 			=  $_REQUEST['post_type'];
	$field_table_name 	= $column_name;

	$pt_id 		= tdm_get_posttype_id($pt_name);

	$post_row	= $wpdb->get_row(" SELECT * FROM `".$table_prefix."tdm_".$pt_name."` WHERE post_id = '".$post_id."' ",ARRAY_A,0);
		
	$field 		= $wpdb->get_row("	SELECT * FROM `".$table_prefix."mps_core_fields"."` 
									WHERE posttype_id = ".$pt_id." and field_table_name = '".$field_table_name."' ",ARRAY_A,0);
	
	$post_field_content	= $wpdb->get_results("SELECT * FROM  ".$table_prefix."mps_field_".$field_table_name." WHERE post_id =  ".$post_id." ",ARRAY_A);
	
	$field_name 		= $field['field_name'];
 	$field_table_name	= $field['field_table_name'];
 	$field_type 		= $field['field_type'];
 	$field_definition	= $field['field_definition'];

						//--------------------------------------------------
						if (in_array($field_definition,array('multiplechoice','multipleinput'))){
								foreach($post_field_content as $value ){
									$db_value .=  "- ".$value['value']."<br>";		
								}	
						}


						if (in_array($field_definition,array('input','singlechoice'))){

							$db_value =  $post_field_content[0]['value'];	

							if ($field_type == 'coordinates'){
							$ar = $teile = explode("_;_", $db_value);
							$db_value =  $ar[0];
							}

						}
						
						if(empty($post_field_content)){
						$db_value ='-';
						}
						//-------------------------------------------------

				$cell = $db_value;

	echo $cell;
}


// AJAX Functions--------------------------------------------------------------------
// 				Define AJAX Functions 
// ----------------------------------------------------------------------------------
add_action('wp_ajax_fn_get_posts', 'ajax_fn_get_posts');
add_action('wp_ajax_fn_tdm_get_box_google', 'ajax_fn_tdm_get_box_google');

add_action('wp_ajax_fn_tdm_posttype_delete', 'ajax_fn_tdm_posttype_delete');
add_action('wp_ajax_fn_tdm_posttype_save', 'ajax_fn_tdm_posttype_save');

add_action('wp_ajax_fn_tdm_get_box_customfield', 'ajax_fn_tdm_get_box_customfield');
add_action('wp_ajax_fn_tdm_save_customfield', 'ajax_fn_tdm_save_customfield');

add_action('wp_ajax_fn_tdm_delete_customfield', 'ajax_fn_tdm_delete_customfield');

add_action('wp_ajax_fn_mps_export_plugin', 'ajax_fn_mps_export_plugin');

function ajax_fn_tdm_save_customfield(){
include('ajax/mps_ajax_customfield_save.php');	}

function ajax_fn_tdm_delete_customfield(){
include('ajax/mps_ajax_customfield_delete.php');		}

function ajax_fn_tdm_get_box_customfield(){
include('ajax/mps_ajax_customfield_box.php');	}

function ajax_fn_get_posts() {
include("ajax/tdm_ajax_post_connection.php");	}

function ajax_fn_tdm_posttype_delete() {
include("ajax/mps_ajax_posttype_delete.php");	}

function ajax_fn_tdm_posttype_save() {
include("ajax/mps_ajax_posttype_save.php");		}

function ajax_fn_tdm_get_box_google(){
include("ajax/tdm_ajax_box_googlemap.php");		}

function ajax_fn_mps_export_plugin(){
include("ajax/mps_ajax_export_plugin.php");		}
// --- End Function AJAX

 
// FILTER----------------------------------------------------------------------------
// 				Generate Content for Single-View / Archive-View 
// ----------------------------------------------------------------------------------
function mps_generate_frontend_content($content) {
	global $post;
	global $global_posttype_table;
	
	// create a simple array with all custom post types
	$custom_posttypes_array='';
	foreach($global_posttype_table as $pt_row){
	$custom_posttypes_array[]=$pt_row['posttype'];
	}
	
	//if the actuel posttype in array of all custom posttypes, then insert the individual content
	if (in_array($post->post_type,$custom_posttypes_array)){
		$pt_name  	= $post->post_type;
		$post_id	= $post->ID;
		
		include("mps_generate_frontend_content.php");
		
		$content .= $out;
		$content .= '<br/>';	
	};
 return $content;
}

// FILTER----------------------------------------------------------------------------
// 				Generate Titel for all post from ManagePress Suite      
// ----------------------------------------------------------------------------------
function mps_generate_title($title){
	global $post;
	global $wpdb;
	global $table_prefix;
	global $global_posttype_table;
	global $id;
			
	foreach($global_posttype_table as $pt_row2){
	
    if ( $id && $post && $post->post_type == $pt_row2['posttype'] ) {
			
			$supports = unserialize($pt_row2['supports']);
				if(is_array($supports) && in_array('title',$supports) ){		
					
					if($title == ''	){
					$title = $pt_row2['singularname']." ".$post->ID;
					}
					
				} else {
					$title = $pt_row2['singularname']." ".$post->ID;	
				} 
					
		}	
	}	
return $title;
}


// SHORTCODE-------------------------------------------------------------------------
// 				Generate Shortcodes for ManagePress Suite      
// ----------------------------------------------------------------------------------
function mps_shortcode_list($atts, $content){
	//parameter posttype="..."
		
	if(array_key_exists('posttype',$atts)){
		include("mps_generate_frontend_list.php");
	} else {		
		$content = 'The shortcode is not corret! Please change it like this: [mps_list posttype="..."]';	
	}
return $content;
}



// DASHBOARD WIDGET------------------------------------------------------------------
// 				Generate Widgets for ManagePress Suite      
// ----------------------------------------------------------------------------------
function mps_load_dashboard_widgets() {
	wp_add_dashboard_widget('mps_widget_1', 'ManagePress Suite 1.0 / Statistic', 'mps_widget_1');	
	wp_add_dashboard_widget('mps_widget_2', 'ManagePress Suite 1.0 / My Posts', 'mps_widget_2');
} 

function mps_widget_1() {
	include('mps_generate_widget_userstatistic.php');	
} 
function mps_widget_2(){
	include('mps_generate_widget_myposts.php');	
}

?>