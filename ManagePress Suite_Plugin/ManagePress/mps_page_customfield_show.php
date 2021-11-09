<?php
global $wpdb;
global $table_prefix;
global $global_posttype_table;

// get post type informations
foreach($global_posttype_table as $pt_row){
	if($pt_row['ID'] == $pt_id){	
	// now we have the $pt_row with all informations
	$pt_row2=$pt_row;
	break;	
	}	
}

if ($pt_row2 != null) {
	$pt_id				= $pt_row2['ID'];
	$pt_name 			= $pt_row2['posttype'];
	$pt_singularname 	= $pt_row2['singularname'];
	$pt_pluralname 		= $pt_row2['pluralname'];
	$pt_description 	= $pt_row2['description'];
	$pt_singularname 	= $pt_row2['singularname'];  

	$title 				= "Customfields for ".$pt_pluralname." ";

} else {
// no data found	
}
  
  $button_name = 'update_fields';



?>





<div class="wrap">
	<div class="tdm_cf">
    <?php echo mps_get_page_header($title);?>
    
<a href="admin.php?page=<?php  echo $plugname;  ?>" class="button-primary" target="_self">Go Back</a>
<br />
<br />




<div>
	<div class="todo-step-img">
   	 <img src=" <?php echo  plugins_url("images/step_1.PNG", __FILE__);?> " width="32" height="80" border="0" alt="">
    </div>
	<div class="todo-step-text">
    <h3>Posttype Information</h3> 
   <div>
   	<div  style="width:100px;float:left;">ID:</div>
    <div><?php echo  $pt_id; ?></div>
   </div>
   <div>
   	<div  style="width:100px;float:left;">Posttype Name:</div>
    <div><?php echo  $pt_name; ?></div>
   </div>
   <div>
   <div  style="width:100px;float:left;">Plural Name:</div>
   <div ><?php echo $pt_pluralname; ?></div>
    </div>
    <div>
    <div  style="width:100px;float:left;">Singular Name:</div>
   <div> <?php echo $pt_singularname ; ?></div>
    </div>
    <div>
    <div  style="width:100px;float:left;">Description:</div>
    <div ><?php echo $pt_description; ?></div>
    </div>
    </div>
    <div style="clear:both"> </div>
</div>



<div>
    <div class="todo-step-img">
     	<img src=" <?php echo  plugins_url("images/step_2.PNG", __FILE__);?> " width="32" height="80" border="0" alt="">
    </div>
	
    <div class="todo-step-text">
        <div><h3>Custom Fields</h3>
           
  		</div>
        
        <div class="container">
		<?php 
		 

	$ct_fields	= $wpdb->get_results(" SELECT * FROM  ".$table_prefix."mps_core_fields WHERE posttype_id = ".$pt_id." ",ARRAY_A);
		
		
	$content =	'<table id="tdm_table_customfields" class="tdm_table">
				<thead>	<tr class="">
						<th style="width:40px">ID</th>
						<th class="">Fieldname</th>
						<th class="">Description</th>
						<th class="">Type</th>
						<th class="">Definition</th>
						<th class="">Attributes</th>
						<th>Display<br> in list</th>
						<th style="width:120px">Actions</th>
					</tr>
					</thead>';
	

$tbody =	'<tbody>';

		$fields = '';
		
		if(count($ct_fields)<=0){
		$tbody .=	'<tr class="">';
					$tbody .=	'<td colspan="3">There are no customfields.</td>';
					$tbody .=	'<td></td>';
					$tbody .=	'<td></td>';
					$tbody .=	'<td></td>';
					$tbody .=	'<td></td>';
					$tbody .=	'<td></td>';
		$tbody .=	'</tr class="">';
		};
		
		
		foreach($ct_fields as $pt_fields_row){
		
			$choices_array = unserialize($pt_fields_row['field_choices']);
			$field_choices = '';
			if (is_array($choices_array)){
			foreach($choices_array as $choice){
				$field_choices .= '- '.$choice.'<br>';
			}
			substr($field_choices, 0, -4);
			};
			
			$field_display = $pt_fields_row['field_display'];
			if($field_display == 0){
			$field_display = 'yes';	
			} else {
			$field_display = 'no';	
			}
		
		$tbody .=	'<tr class="">';
			$tbody .=	'<td class="">'.$pt_fields_row['field_id'].'</td>';
			$tbody .=	'<td class="">'.$pt_fields_row['field_name'].'</td>';
			$tbody .=	'<td class="">'.$pt_fields_row['field_description'].'</td>';
			$tbody .=	'<td class="">'.$pt_fields_row['field_type'].'</td>';
			$tbody .=	'<td class="">'.$pt_fields_row['field_definition'].'</td>';
			$tbody .=	'<td class="">'.$field_choices.'</td>';
			$tbody .=	'<td class="">'.$field_display.'</td>';
			
			$tbody .=	'<td nowrap="nowrap">
			<span onclick="tdm_show_field_box(\''.$pt_id.'\',\''.$pt_fields_row['field_id'].'\')" id="add_custom_field" class="button" >Edit</span>
			<span onclick="tdm_show_delete_field_box(\''.$pt_id.'\',\''.$pt_fields_row['field_id'].'\')" id="del_custom_field" class="button" >Delete</span>
			</td>';
		$tbody .=	'</tr>';
				} 
				
$tbody .=	'</tbody>';

$content .=	$tbody;
$content .=	'</table>';
$content .=	'<div id="tdm_add_field_div"><input type="button" onclick="tdm_show_field_box(\''.$pt_id.'\',\'new\')" id="add_custom_field" class="button-primary" value="+ Add Field"></div>';

echo $content;
		
		 
		 ?>
         </div> 
    </div>
	<div style="clear:both"> </div>
                  
</div>
<br />
<br />
<br />



<div id="ha"></div>

</div>
</div><!-- end wrap mps-->




