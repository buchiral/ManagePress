<?php
if(is_array($global_posttype_table)) {
$output = "";

if(count($global_posttype_table) == 0) {
	$output .='<tr>
        <td>There are no posttypes.</td>
		<td></td>
        <td></td>
        <td></td>	
        <td></td>
		<td></td>
        </tr>';

} else {
	foreach ($global_posttype_table as $posttype_row ) {
		
		$icon_link =  plugins_url("images/posttype_icons/".$posttype_row['menu_icon'], __FILE__);
		
		$field_display = $posttype_row['menu_icon'];
		
		$num = $i +1;
		$output .= "<tr>"; 
		$output .= "<td>".$posttype_row['ID']."</td>"; 
		$output .= "<td><img title=\"themes16.png\" src=\"".$icon_link."\"> ".$posttype_row['posttype']."</td>"; 
		$output .= "<td>".$posttype_row['pluralname']."</td>"; 
		$output .= "<td>".$posttype_row['description']."</td>";
		$output .= '<td nowrap="nowrap" ><div class="button tdm_button_posttype" onclick="tdm_show_delete_posttype_box(\''.$posttype_row['ID'].'\',\''.$posttype_row['posttype'].'\')">Delete</div>'; 
		$output .= '<a href="admin.php?page='.$plugname.'&tdm_posttype_id='.$posttype_row['ID'].'&tdm_action=edit_posttype" class="button tdm_button_posttype" target="_self">Edit</a></td>';
		$output .= '<td><a href="admin.php?page='.$plugname.'&tdm_posttype_id='.$posttype_row['ID'].'&tdm_action=edit_fields" class="button tdm_button_posttype" target="_self">Add / Edit fields</a></td>';
		$output .= "</tr>";  		
		}
	}
}




?>




<div class="wrap">

<?php echo mps_get_page_header("Manage Posttypes");?>
  
 <div style="clear:both"> </div>
 <div>
	<div class="todo-step-img">
    <img src=" <?php echo  plugins_url("images/step_1.PNG", __FILE__);?> " width="32" height="80" border="0" alt="">
    </div>
	<div class="todo-step-text">
    <h2>Create New Posttype</h2> 


    <br />
    <span id="add_post_type_todo"><a href="admin.php?page=<?php  echo $plugname;  ?>&tdm_action=add_posttype" class="button" target="_self">Create new Posttype</a></span>
</div>
</div>

 <div style="clear:both"> </div>

 <div>
	<div class="todo-step-img">
    <img src=" <?php echo  plugins_url("images/step_2.PNG", __FILE__);?> " width="32" height="80" border="0" alt="">
    </div>
	<div class="todo-step-text"> 
    <h2>Modify Posttype</h2>     

<br />
		<table class="tdm_table" id="datatable_showposttypes"  cellspacing="0">


                <thead>
                    <tr>
                    <th>ID</th>
                    <th>Post-Type</th>
                    <th>Pluralname</th>
                    <th>Description</th>	
                    <th>Action</th>
                    <th>Customfields</th>
                    </tr>
                </thead>
            
            <tbody id="the-list">
                <?php echo $output; ?>
                </tbody>
            
            
            <tfoot>
                <tr>
                    <th>ID</th>
                    <th >Post-Type</th>
                    <th >Pluralname</th>
                    <th >Description</th>
                    <th >Action</th>
                    <th>Customfields</th>
                </tfoot>
                
                
            
                </table>

		</div>
	</div>
	<div style="clear:both"> </div>
<br />
<br />
	<div>
        <div class="todo-step-img">
	        <img src=" <?php echo  plugins_url("images/step_3.PNG", __FILE__);?> " width="32" height="80" border="0" alt="">
        </div>
    
        <div class="todo-step-text"> 
        <h3>Shortcodes & Links</h3>
        <br />
 		
        <table class="tdm_table" id="mps_shortcode"  cellspacing="0">
			<tr style="background-color:#ECECEC;">       		
                <td width="200px">Shortcode</td>
                <td>Description</td>
        	</tr>
			<tr>       		
                <td>[mps_list posttype="..."]</td>
                <td>This shortcode shows you a list with all post from your selected custom posttype.
                <br />- This works only for post types which are created by ManagePress Suite
                <br />- Hint: If the list is to wide for your template - change the field-attribute "Display in list" from yes to no.  
                <br />- Example: [mps_list posttype="client"] 
                </td>
        	</tr>
			<tr style="background-color:#ECECEC;">       		
                <td width="200px">Links</td>
                <td>Description</td>
        	</tr>
   			<tr>       		
                <td>/?post_type=...</td>
				<td>This link shows the archive of all posts from the selected post type.
                <br />- Example: www.mysite.com/wordpress/?post_type=client
                </td>
        	</tr>


        </table>
        
        
		</div>
	</div>
    
</div><!--END wrap mps -->
