<?php
global $table_prefix;
global $wpdb;


if (isset($_REQUEST['posttype_id']) && isset($_REQUEST['field_id'])){

$posttype_id	= $_REQUEST['posttype_id'];
$field_id 		= $_REQUEST['field_id'];

} else {
	echo "There is a Error";
die();	}
	


if ($field_id == 'new'){
	$title ='Add new Field';
	
	$select_option 		= '	<option value="text">Text</option>
							<option value="longtext">Longtext</option>
							<option value="date">Date</option>
							<option value="time">Time</option>
							<option value="biginteger">Big-Integer</option>
							<option value="coordinates">Place on GoogleMaps</option>
							<option value="integer">Integer</option>';
	
	$select_option_def 	= '	<option value="input">Single Input Field</option>
							<option value="multipleinput">Multiple Input Field</option>
                            <option value="singlechoice">Single Choice</option>
                            <option value="multiplechoice">Multiple Choice</option>';
	
	$attribute_choices	= '';

	$checked_yes 		= 'checked="checked"';

} else {

	$title ='Edit Field';

	// get information about the customfield
	$field_row	= $wpdb->get_row("SELECT * FROM  ".$table_prefix."mps_core_fields WHERE field_id = ".$field_id." ",ARRAY_A,0);

	// create select-input for customfield Box - field type
	$select_option = '';	
	$select_option .= '<option '.mps_is_selected('text',$field_row['field_type']).' value="text">Text</option>';
	$select_option .= '<option '.mps_is_selected('longtext',$field_row['field_type']).' value="longtext">Longtext</option>';
	$select_option .= '<option '.mps_is_selected('date',$field_row['field_type']).' value="date">Date</option>';
	$select_option .= '<option '.mps_is_selected('time',$field_row['field_type']).' value="time">Time</option>';
	$select_option .= '<option '.mps_is_selected('biginteger',$field_row['field_type']).' value="biginteger">Big-Integer</option>';
	$select_option .= '<option '.mps_is_selected('integer',$field_row['field_type']).' value="integer">Integer </option>';	
	$select_option .= '<option '.mps_is_selected('coordinates',$field_row['field_type']).' value="coordinates">Place on GoogleMaps</option>';
	
	// create select-input for customfield Box - field definition
	 $select_option_def = '';
	 $select_option_def .= '<option '.mps_is_selected('input',$field_row['field_definition']).' value="input">Single Input Field </option>';
	 $select_option_def .= '<option '.mps_is_selected('multipleinput',$field_row['field_definition']).' value="multipleinput">Multiple Input Field </option>';
	 $select_option_def .= '<option '.mps_is_selected('singlechoice',$field_row['field_definition']).' value="singlechoice">Single Choice</option>';
	 $select_option_def .= '<option '.mps_is_selected('multiplechoice',$field_row['field_definition']).' value="multiplechoice">Multiple Choice</option>';
	
	
	// Look if customfield is display in list
	if($field_row['field_display'] == 0){
	$checked_yes = 'checked="checked"';	
	} else {
	$checked_no = 'checked="checked"';		
	}

	$field_choices1 = unserialize($field_row['field_choices']);
	$attribute_choices = '';
				
			if (is_array($field_choices1)){
					
					$choice_num = 0;
						
						foreach($field_choices1 as $attribute){
							$num = uniqid();
							
							$attribute_choices .= '<div class="mps_attribute_field" id="mps_default_field_'.$choice_num.'"> 
								<div class="input_field_label">Attribute:</div>
								<div style="float:left;">
									<input value="'.$attribute.'" type="text" name="field_choices[]" id="field_choices'.$num.'" placeholder="...">
								</div>
									<div class="mps_button_delete_attribute" onclick="mps_delete_attribute(this)"></div>
								</div>';
						$choice_num++;	
							
						}
				}	
}




?>
<div class="mps_field_box">


<h2><?php echo $title  ?></h2>
<form action="" id="form_customfield_save"	 method="post" name="form_customfield_save"> 

	<div class="mps_field_box_middle">
        <input type="hidden" value="<?php echo $posttype_id; ?>"  name="posttype_id" id="posttype_id" >
        <input type="hidden" value="<?php echo $field_id; ?>"  	name="field_id" id="field_id" >
        <input type="hidden" value="<?php echo $field_row['field_table_name'];?>"  	name="field_table_name_old" id="field_table_name_old" >
        <input type="hidden" value="<?php echo $field_row['field_table_name']; ?>" name="field_table_name" id="field_table_name">

     
       <div class="input_field_label">Field Name:</div>
       <div class="input_field_label_rigth"><input required="required" value="<?php echo $field_row['field_name']; ?>" alt="Field Name" type="text" name="field_name" id="field_name" placeholder="..."></div>
        
       <div class="input_field_label">Field Description:</div>
       <div class="input_field_label_rigth"><textarea  required="required" name="field_description" id="field_description" rows="2" ><?php echo $field_row['field_description']; ?></textarea></div>
       
       <div class="input_field_label">Show this field in the list of all Posts?</div>
       <div class="input_field_label_rigth">
            <input  <?php echo $checked_yes;   ?> type="radio" style="width:auto;" name="field_display" id="field_display_yes" value="0">
            <label style="margin-right:20px" for="field_display_yes">Yes</label>
            <input <?php echo $checked_no;   ?> type="radio" style="width:auto;" name="field_display" id="field_display_no" value="1">
            <label style="margin-right:20px" for="field_display_no">No</label>
       </div>
       
        <div class="input_field_label">Field Type:</div>
        <div style="float:left;">
            <select name="field_type" id="field_type" size="1">
                <?php echo $select_option;?>
            </select>
        </div>
            
           
        <div class="input_field_label">Field Definition:</div>
                    <div class="input_field_label_rigth">
                        <select name="field_definition" id="field_definition" size="1">
                        <?php echo $select_option_def;?>
                        </select>
                    </div>
    
        <div class="mps_default_sector" id="mps_default_sector_field"> 
        <?php
        echo $attribute_choices;
        ?>
        </div>       
    
         <div style="clear:both;float:left" class="button" id="button_add_attribute_field" onClick="mps_add_attribute(this)">add attribute</div>
   </div> 
   
   <div class="mps_field_box_bottom">
        <input style="width:100px;float:left"  type="submit" class="button-primary" name="save_custom_field"  value="Save Field"> <div class="message" id="message1">sdfasdf</div>
   </div>
   
</form>
</div>


<script>
jQuery(document).ready(function() {
	mps_ini_customfield_box();								
});
</script>



<?php die(); ?>