<?php
global $global_posttype_table;

if(is_array($global_posttype_table)) {
	$output = "";
	
	if(count($global_posttype_table) == 0) {
		$output .='<tr>
			<td>There are no posttypes created.</td>
			<td></td>
			<td></td>
			<td></td>	
			<td></td>
			</tr>';
	
	} else {
		foreach ($global_posttype_table as $posttype_row ) {
			
			$icon_link =  plugins_url("images/posttype_icons/".$posttype_row['menu_icon'], __FILE__);
			
			$field_display = $posttype_row['menu_icon'];
			$img = '<img title="'.$icon_link.'" src="'.$icon_link.'"> '.$posttype_row['posttype'];	
			
			$num = $i +1;
			$output .= '<div class="tdm_checkbox_border">'; 
			$output .= '<div><input checked="checked" type="checkbox" id="'.$posttype_row['posttype'].'" name="f[]" value="'.$posttype_row['ID'].'"> ';
			$output .= '<label for="'.$posttype_row['posttype'].'">'.$img.'</label></div>';
				$output .= '</div>';
	
			}
	
	}
	

	
}

?>


<div class="wrap">

<?php echo mps_get_page_header("Export Plugin");?>

	<div style="clear:both"> </div>


	<div>
        <div class="todo-step-img">
        <img src=" <?php echo  plugins_url("images/step_1.PNG", __FILE__);?> " width="32" height="80" border="0" alt="">
        </div>
		
        <div class="todo-step-text">
        <form action=""  method="post" id="form_export_plugin" name="form_export_plugin">
        
        
			<h3>Which posttypes do you want to export?</h3> 
		        <?php   echo $output;	?>
                
            	<br />  
            <h3>Should the exported plugin be in the safe-mode?</h3>
            
            	<input type="radio" checked="checked" name="mps_safe_mode" id="mps_safe_yes" value="true"><label for="mps_safe_yes">Yes, safe-mode.</label>
            	<br />
          		<input type="radio" name="mps_safe_mode" id="mps_safe_no" value="false"><label for="mps_safe_no">No, admin-mode.</label><br>
				<br />
                <br />
              	<div style="float:left;"><input style="width:100px" type="submit" class="button-primary" name="posttype_save" value="Export Plugin"></div>
            
	
		</form>
		</div>

	</div>















<script>
jQuery(document).ready(function($) {

	jQuery('#form_export_plugin').submit(function() {
		mps_export_plugin();
		return false;
		});
});
</script>


</div>
