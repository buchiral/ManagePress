<?php
global $wpdb;
global $table_prefix;



$db_name_wp 		= DB_NAME;


$action 	= $_REQUEST['tdm_action'];
	
switch ($action) {

    case 'edit_posttype':
			$posttype_id = $_REQUEST['tdm_posttype_id'];
    
		 	$title = "Edit Posttype";
			$button_name = 'update_posttype';
		
			$table_name = $table_prefix."mps_core_posttype";
			$posttype_information	= $wpdb->get_row( " SELECT * FROM  ".$table_name." WHERE ID = ".$posttype_id." ",ARRAY_A,0);
					
			if ($posttype_information != null) {
				$pt_id			= $posttype_information['ID'];
				$pt_name 			= $posttype_information['posttype'];
				$pt_singularname 	= $posttype_information['singularname'];
				$pt_pluralname 		= $posttype_information['pluralname'];
				$pt_description 	= $posttype_information['description'];
				$pt_singularname 	= $posttype_information['singularname'];
				$pt_menu_icon 		= $posttype_information['menu_icon']; 
				$pt_supports		= unserialize($posttype_information['supports']); 
			
			} else {
			  // no Information found 
			  }

		  
			
        break;
     case 'add_posttype':
	 
$title = "Create New Posttype";
$button_name = 'save_posttype';
$checked = 'checked';
        
        break;
    default:
	
				
        break;
}
	
	
function set_checkbox_checked($input_name,$pt_supports){
	if(is_array($pt_supports)){
			if  (in_array($input_name, $pt_supports)){
			$out	= "checked";	
			return $out;
								};
	} else {
		return "";
		}
	}


function set_checkbox_checked_menuicon($var1,$var2){
	if($var1 == $var2){
	$out	= "checked";	
	return $out;
	} else {
		return "";
				}
}

?>




<div class="wrap">
<div class="mps_line">
        <div class="tdm_logo"></div>
        <h2><?php  echo $title;  ?></h2>
         </div>
<br />    <a href="admin.php?page=<?php  echo $plugname;  ?>" class="button-primary" target="_self">Go Back</a>
<br />
<br />

<form action="#" method="post" name="form_posttype_save" id="form_posttype_save">

<div class="tdm_form_posttype">

	<div class="todo-step-img">
   	 <img src=" <?php echo  plugins_url("images/step_1.PNG", __FILE__);?> " width="32" height="80" border="0" alt="">
    </div>
	
    <div class="todo-step-text">
    <h3>Posttype Information</h3> 
          
        <input alt="ID" type="hidden" readonly="readonly" name="post_type[id]" id="post_type[id]" value="<?php echo  $pt_id; ?>">

        <div class="new_line">
        <div class="left_element"><label title="The posttype name - max. 20 characters, can not contain capital letters or spaces" for="post_type[post_type]">Posttype Name:</label></div>
         <div class="right_element"><input pattern="^[a-z0-9_]*$"  title="The posttype name - max. 20 characters, can not contain capital letters or spaces" alt="Post Type Name" required="required"  type="text" name="post_type[post_type]" id="post_type[post_type]" placeholder="..." value="<?php echo  $pt_name; ?>"></div>
       </div>

       <div class="new_line">
        <div class="left_element"><label for="post_type[plural_name]">Plural Name:</label></div>
        <div class="right_element"><input alt="Post Type Name"  required="required" type="text" name="post_type[plural_name]" id="post_type[plural_name]" placeholder="..." value="<?php echo $pt_pluralname; ?>" ></div>
        </div>
    
        <div class="new_line">
       <div class="left_element"><label for="post_type[singular_name]">Singular Name:</label></div>
       <div class="right_element"><input  alt="Post Type Name" required="required"  type="text" name="post_type[singular_name]" id="post_type[singular_name]" placeholder="..." value="<?php echo $pt_singularname ; ?>"></div>
        </div>

        <div class="new_line">
        <div class="left_element"><label title="A short descriptive summary of what the post type is." for="post_type[description]">Description:</label></div>
        <div class="right_element"><textarea title="A short descriptive summary of what the post type is." name="post_type[description]" required="required" id="post_type[description]" rows="3" ><?php echo $pt_description; ?></textarea></div>
        </div>
    
		
        
        <div class="more_options">
            <div class="new_line">
               <div class="left_element">Supports</div>
               <div class="right_element">
                
                <input type="checkbox" name="supports[]" id="attr_title" <?php echo set_checkbox_checked('title',$pt_supports) ?> value="title">
                <label title="Adds the title meta box when creating content for this custom posttype"  for="attr_title">Title</label> <br />
                                               
                <input type="checkbox" name="supports[]" id="attr_editor" <?php echo set_checkbox_checked('editor',$pt_supports) ?> value="editor">
                <label title="Adds the content editor meta box when creating content for this custom post type" for="attr_editor">Editor</label><br />
                
				<input type="checkbox" name="supports[]" id="attr_thumbnail" <?php echo set_checkbox_checked('thumbnail',$pt_supports) ?> value="thumbnail">
                <label for="attr_thumbnail">Thumbnail</label> <br />

				<input type="checkbox" name="supports[]" id="attr_comments" <?php echo set_checkbox_checked('comments',$pt_supports) ?> value="comments">
                <label title="Adds the custom fields meta box when creating content for this custom post type" for="attr_comments">Comments</label> <br />


				<input type="checkbox" name="supports[]" id="attr_author" <?php echo set_checkbox_checked('author',$pt_supports) ?> value="author">
                <label title="Adds the author meta box when creating content for this custom post type" for="attr_author">Author</label> <br />
            
                

               </div>
            </div>
            
             <div class="new_line">
               <div class="left_element">Menu Icon</div>
               <div class="right_element">
                
                <?php 
				$all_menuicons	= array('themes16.png','examples16.png','analysis16.png','navigate-right16.png','layout16.png','organization16.png','tests16.png'
				,'Autocomplete16.png','content16.png','schedule16.png'				);
				
				foreach( $all_menuicons as $menu_icon2){
					
					$icon_link =  plugins_url("images/posttype_icons/".$menu_icon2, __FILE__);
					$menu_icon_checked = set_checkbox_checked_menuicon($menu_icon2,$pt_menu_icon);
					$content_menuicon = '<input type="radio" name="menu_icon" id="'.$menu_icon2.'" '.$menu_icon_checked.' '.$checked.' value="'.$menu_icon2.'">
					<label style="margin-right:20px"  for="'.$menu_icon2.'"><img title="'.$menu_icon2.'"  src="'.$icon_link.'" /> </label>';
					
					echo $content_menuicon;
				}
				?>
                

               </div>
            </div>
            
            
            
            
            </div>


		</div>
    
    
    <div style="clear:both"> </div>
	


	<div class="todo-step-img">
   	 <img src=" <?php echo  plugins_url("images/step_2.PNG", __FILE__);?> " width="32" height="80" border="0" alt="">
    </div>
	<div class="todo-step-text">
    <h3></h3> <br />
   
    <div style="float:left;"> <input style="width:100px" type="submit" class="button-primary" name="posttype_save" value="Save Posttype"></div>
    <div class="message" id="message1"></div>
    
    </div>
    <div style="clear:both"> </div>
	</div>
</form>
<script>

jQuery(document).ready(function($) {
	
	jQuery('#form_posttype_save').submit(function() {
		
		tdm_posttype_save();
					
		return false;
		});

});




</script>



</div>
		
		
		        
        