<?php
global $wpdb;
global $table_prefix;
global $global_posttype_table;

$posttype_name 	= $_POST['posttype'];
$pt_name 		= $_POST['posttype'];
$pt_id			= tdm_get_posttype_id($pt_name);

$typ 			= $_POST['data'];

$table_prefix_wp 	= $table_prefix;




switch($typ){
	case 'parent_of':
			$title = 'This post is parent of?';
		break;
	case 'child_of':
	$title = 'This post is child of?';
	break;	
}

// generate Selectbox with post types
	$select_box = '';
	$select_box .= '<select id="select_posttype" onchange="tdm_show_postconnection_box_onchange(\''.$typ.'\',this)" name="select_posttype">';
	$select_box .= '<option '.proof_selected_posttype('page',$pt_name ).' value="page">Pages</option>';
	$select_box .= '<option '.proof_selected_posttype('post',$pt_name ).' value="post">Posts</option>';

	foreach($global_posttype_table as $pt_row){
		$select_box .= '<option '.proof_selected_posttype($pt_name,$pt_row['posttype']).'  value="'.$pt_row['posttype'].'">'.$pt_row['pluralname'].'</option>';
	
			if($pt_row['posttype'] == $pt_name){
				$posttype_information_array = $pt_row;
			};
	}
	
	$select_box .= "</select>";
// ------------------------------------

$pt_pluralname 	= get_post_type_object($pt_name)->label; // get the pluralname

$all_fields		= $wpdb->get_results(" 	SELECT * FROM  ".$table_prefix."mps_core_fields 
										WHERE posttype_id = ".$posttype_information_array['ID']." and field_display = 0 ",ARRAY_A);

$all_posts		= $wpdb->get_results(" SELECT * FROM  ".$table_prefix."mps_pt_".$posttype_name." ",ARRAY_A);
$table_name		= $table_prefix."tdm_core_posttype";

$pt_row			= $wpdb->get_row(" SELECT * FROM  ".$table_name." WHERE posttype = '".$posttype_name."'  ",ARRAY_A,0);

$show_title 	= post_type_supports($pt_name, 'title'); //suppport this posttype title --> true or false
	

// --------------List Posts    
 

$post = new WP_Query('post_type='.$pt_name .''); //get Object with all posts
			$tbody = "";

	
if ($post->have_posts()):
        while ($post->have_posts()): $post->the_post();

	//reset for next post
		$out_tbody = '';
		$out_head = '';
		$line = '';

		$post_id 	= get_the_ID();
		$post_title = get_the_title();
		$post_link	= get_permalink();


if (in_array($pt_name, array("post", "page"))){

	 $out_head = 	'<th>Post ID</th>
					<th>Post Title</th>
        			<th>Content</th>';
	 
			$line = '<div class="tdm_post_line" id="'.$typ.'_'.$post_id .'">';
			  $line .= "<span title=\"Title of the post.\">".$post_title."</span> "; 
			  $line .= "<span title=\"ID of the post.\">".$post_id."</span> ";
			  $line .= '<span class="tdm_post_connection_del_button" onclick="del_postconnection(this)" alt="delete" >&nbsp;</span>';
			  $line .= "<input type=\"text\" name=\"".$typ."[]\" id=\"input_".$typ."_".$post_id."\" class=\"tdm_post_connection_input\" value=\"".$post_id."\">";
			$line .= "</div>";		
			$line = '<div class="tdm_postconnection_box_add" id="add_'.$post_id.'">'.$line.'</div>';
			
			$out_tbody .= "<td>".$post_id."</td>";
			$out_tbody .= "<td>".$post_title."</td>";
			$out_tbody .= '<td><a href="'.$post_link.'" target="_blank">show content</a> </td>	';
		
} else { // use this for all custom post types
    
$all_fields		= $wpdb->get_results("SELECT * FROM  ".$table_prefix."mps_core_fields WHERE posttype_id = '".$pt_id."' and field_display = 0 ",ARRAY_A);

//----------------------
				
	foreach ($all_fields as $all_fields_row) {

		$field = $all_fields_row;
		$field_name 		= $field['field_name'];
		$field_table_name	= $field['field_table_name'];
		$field_type 		= $field['field_type'];
		$field_definition	= $field['field_definition'];
		$field_choice 		= unserialize($field['field_choices']);
		$field_description	= $field['field_description'];

		$post_field_content	= $wpdb->get_results("SELECT * FROM  ".$table_prefix."mps_field_".$field_table_name." WHERE post_id =  ".$post_id." ",ARRAY_A);
		
		$db_value = $post_field_content[0]['value'];
				

				if (in_array($field_definition,array('multiplechoice','multipleinput'))){	
						$db_value = '';
					for ($i = 0; $i < count($post_field_content); $i++) {
						$value 		=    $post_field_content[$i]['value'];
						$db_value 	.= ''.$value.', ';
						$line .= "<span title=\"".$field_name ."\">".$value.", </span>";
					}
					$db_value = substr($db_value, 0, -2);
				}
	
	
				if (in_array($field_definition,array('input','singlechoice'))){	
					$db_value 	=     $post_field_content[0]['value'];
			
					if ($field_type == 'coordinates'){
							if (!empty($db_value)){
							$ar = explode("_;_", $db_value);
							$db_value =  $ar[0];
							$line .= '<span title="'.$field_name.'">'.$ar[0].', </span>';
							}
					} else {

					$line .= '<span title="'.$field_name.'">'.$db_value.', </span>';
						
					};
				}

				if(empty($post_field_content)){
					$db_value =  '-';
				}
		
		
		$out_tbody .= "<td>";
		$out_tbody .= $db_value;
		$out_tbody .= "</td>";
		
		$out_head .=  '<th class="sorting">'.$field_name.'</th>';				
	} // End foreach field 

$line_tmp	= $line;
$line = '<div class="tdm_post_line" id="'.$typ.'_'.$post_id.'">';
	$line .= $line_tmp;
	$line .= "<span title=\"ID of the post.\">".$post_id."</span> ";
	$line .= '<span class="tdm_post_connection_del_button" onclick="del_postconnection(this)" alt="delete" >&nbsp;</span>';
	$line .= "<input type=\"text\" name=\"".$typ."[]\" id=\"input_".$typ."_".$post_id."\" class=\"tdm_post_connection_input\" value=\"".$post_id."\">";
$line .= "</div>";		
$line = '<div class="tdm_postconnection_box_add" id="add_'.$post_id.'">'.$line.'</div>';

	$i++;	
}



// generate thead and tbody
$tbody .= "<tr>";		    	
	$tbody .= $out_tbody;
	$tbody .= '<td><input type="button" name="'.$typ.'" id="'.$post_id.'" class="button" onclick="tdm_add_post('.$post_id.',\''.$pt_name.'\',\''.$pt_pluralname.'\',\''.$typ.'\')" value="add"> '.$line.'</td>';
$tbody .= "</tr>";


$thead = '<tr>'; //reset also thead 	
$thead .= $out_head;
$thead .=  '<th></th>';
$thead .=  '</tr>';
// ----------------

    endwhile;
  else:
    
	$thead = '<tr><th>No posts found.</th></tr>';
	
	endif;

wp_reset_query();



function proof_selected_posttype($var1,$var2){
	if($var1 == $var2){
	return 'selected';	
	} 
	return '';
};



?>
<div class="tdm_postconnection_box">

<h2><?php echo $title;  ?></h2>


Select posttype:
<?php echo $select_box;  ?>

<div id="tdm_postconnection_list">

 
<table class="tdm_table" id="dataTable_postconnection" cellspacing="0">
	<thead>
    <?php echo $thead; ?>
	</thead>

	<tbody>    
    <?php echo $tbody; ?>
    </tbody>
</table>


 </div>
 

</div>
 
 
</div>
<script>
jQuery(document).ready(function() {

var wide = jQuery('#tdm_postconnection_list').width();
var new_width = 100+wide;
	jQuery('#tdm_postconnection_list').width(new_width+'px');
				jQuery('#dataTable_postconnection').dataTable({
																"bPaginate": false,
																"bLengthChange": false,
																"bFilter": true,
																"bSort": true,
																"bInfo": false,
															 "sScrollY": "300px",
															 "bScrollCollapse": true,
															   "bScrollInfinite": true,
																"bAutoWidth": false })
				jQuery.fancybox.reposition();
	});
</script>
<?php   die(); ?>