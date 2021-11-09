// JavaScript Document

function mps_ini_frontend_list(){
jQuery('#mps_datatable_list').dataTable({
							"bPaginate": false,
							"bLengthChange": false,
							"bFilter": true,
							
							"bSort": true,
							"bInfo": false,
							"bAutoWidth": false ,
							"sDom": 'T<"clear">lfrtip',
							"oTableTools": {
										"sSwfPath": "wp-content/plugins/ManagePress/images/swf/copy_csv_xls_pdf.swf",
										"aButtons": [ "xls", "pdf", "copy" ]
									}
							});

jQuery('.tab_button').click(function (){
	
	jQuery('#tab1_link').attr('class','tab_button');
	jQuery('#tab2_link').attr('class','tab_button')
	jQuery(this).attr('class','tab_button active');
	
	var id = jQuery(this).attr('id');
	

	switch (id) {
  case "tab1_link":
    
	jQuery('#tab1').show();
	jQuery('#tab2').hide();
	
	break;
  case "tab2_link":
	
	jQuery('#tab2').show();
	jQuery('#tab1').hide();
	
	ini_map1();
	
	
	break;
	}
	
			} );
};


//*-------------------------------------------------------------------------
//*					Functions for Customfield Box
//*	
//*-------------------------------------------------------------------------
function mps_ini_customfield_box(){

	mps_onchange_field_type();
	mps_onchange_field_definition();
			
	jQuery('#field_type').change( function(){
		mps_onchange_field_type();
		mps_onchange_field_definition();
	});		

	jQuery('#field_definition').change( function(){
		mps_onchange_field_definition();
	});
	
	jQuery('#form_customfield_save').submit( function(){
		mps_form_customfield_submit();
		return false;
	});		
}

function mps_onchange_field_definition(){
	var value =  jQuery('#field_definition').attr('value');	
				switch (value) {
				  case "input":
						jQuery('#button_add_attribute_field').css('display','none');
						jQuery('.mps_attribute_field').detach();
					break;
				  case "multiplechoice":
						jQuery('#button_add_attribute_field').css('display','block');
					break;
				  case "singlechoice":
						jQuery('#button_add_attribute_field').css('display','block');
					break;
				  case "multipleinput":
						jQuery('#button_add_attribute_field').css('display','none');
						jQuery('.mps_attribute_field').detach();
					break;
				  default:
					break;
				}	
	jQuery.fancybox.reposition();
}

function mps_onchange_field_type(){
	var value =  jQuery('#field_type').attr('value');	

	switch (value) {
	  case "coordinates":
			jQuery('#field_definition').attr('value','input');
			jQuery('#field_definition').attr("disabled","disabled");
		break;
	  case "longtext":
			jQuery('#field_definition').removeAttr("disabled");
			jQuery('input[name=\'field_choices[]\']').attr('placeholder','...');
			jQuery('input[name=\'field_choices[]\']').attr('class','field_longtext');		
		break;
	  case "date":
			jQuery('#field_definition').removeAttr("disabled");
			jQuery('input[name=\'field_choices[]\']').attr('placeholder','YYYY-MM-DD');
			jQuery('input[name=\'field_choices[]\']').datepicker({
				changeMonth: true, changeYear: true, dateFormat: 'yy-mm-dd' });
			jQuery('input[name=\'field_choices[]\']').attr('class','field_date');		
		break;
	  case "time":
			jQuery('#field_definition').removeAttr("disabled");
			jQuery('input[name=\'field_choices[]\']').attr('placeholder','HH:MM:SS');
			jQuery('input[name=\'field_choices[]\']').attr('class','field_time');	
		break;
	  case "biginteger":
			jQuery('#field_definition').removeAttr("disabled");
			jQuery('input[name=\'field_choices[]\']').attr('placeholder','...');
			jQuery('input[name=\'field_choices[]\']').attr('class','field_biginteger');		
		break;
	  case "integer":
			jQuery('#field_definition').removeAttr("disabled");
			jQuery('input[name=\'field_choices[]\']').attr('placeholder','...');
			jQuery('input[name=\'field_choices[]\']').attr('class','field_integer');		
		break;
	  case "text":
			jQuery('#field_definition').removeAttr("disabled");
			jQuery('input[name=\'field_choices[]\']').attr('placeholder','...');
			jQuery('input[name=\'field_choices[]\']').attr('class','field_text');	
		break;
	  default:
		break;
	}

jQuery.fancybox.reposition();
};

function mps_form_customfield_submit(){
	
	if (document.form_customfield_save.field_name.value=="") {
	jQuery("#message1").text('Please fill in the name.');
	jQuery("#message1").fadeIn().delay(5000);
	jQuery("#message1").fadeOut();
	return;
	}
	
	if (document.form_customfield_save.field_description.value=="") {
	jQuery("#message1").text('Please fill in the description.');
	jQuery("#message1").fadeIn().delay(5000);
	jQuery("#message1").fadeOut();
	return;
	}
	
	var def = document.form_customfield_save.field_definition.value;
	if (def != "input" && def != "multipleinput" ) {
		if (jQuery('.mps_attribute_field').length>0) {
			//ok
		} else {
		jQuery("#message1").text('Please add attributes.');
		jQuery("#message1").fadeIn().delay(5000);
		jQuery("#message1").fadeOut();
		return;
		}
	}
	
	jQuery('#field_definition').removeAttr("disabled");
	
				var form_data = jQuery("#form_customfield_save").serialize();
				form_data = form_data+'&action=fn_tdm_save_customfield';
				 
				jQuery.post(
						ajaxurl, 
						form_data, 
						function(response){	
						
								if (response == 'ok'){
								jQuery.fancybox.close();
								location.reload();	
								} else {
									jQuery("#message1").text(response);
									jQuery("#message1").fadeIn().delay(5000);
									jQuery("#message1").fadeOut();	
								}
						
												}
				);
}


function mps_add_attribute(obj){
	var num = Math.round(Math.random()*1000);
	var code = '<div class="mps_attribute_field" id="mps_default_field2">';
	code += '<div class="input_field_label">Attribute:</div>';
	code += '<div style="float:left;">';
	code += '<input type="text" name="field_choices[]" id="field_choices'+num+'" placeholder="...">';
	code += '</div>';
	code +='<div class="mps_button_delete_attribute" onclick="mps_delete_attribute(this)"></div>'
	code += '</div>';
	jQuery('#mps_default_sector_field').append(code);
	mps_onchange_field_type();
	jQuery.fancybox.reposition();
}


function mps_delete_attribute(obj){
	var p = jQuery(obj).parents(".mps_attribute_field");
	jQuery(p).detach();
	jQuery.fancybox.reposition();
}



function tdm_close_fancybox(){
	jQuery.fancybox.close();
}



function tdm_show_field_box(posttype_id,field_id){
	jQuery.fancybox.showLoading();

	jQuery.post(
	   ajaxurl, 
	   {
		  'action':'fn_tdm_get_box_customfield',
		  'posttype_id': posttype_id,
		  'field_id': field_id
	   }, 
	   function(response){
					jQuery.fancybox.open({
						'content' 		: response
												});
		}
	);
}








function tdm_show_delete_field_box(posttype_id,field_id){
	var content = '<h2> Delete this field? </h2> <br> <span onclick="tdm_delete_field_ajax('+posttype_id+','+field_id+')" id="del_custom_field_ajax" class="button" >Yes</span> <br> <br><span onclick="tdm_close_fancybox()" id="del_custom_field_ajax_close" class="button" >No, go back</span> <br><br><br>';
	
	jQuery.fancybox.open({
						'closeBtn'		: 'true',
						'openEffect ' 	: 'fade',
						'content' 		: content
					} );
}


function tdm_delete_field_ajax(posttype_id,field_id){
	jQuery.post(
	   ajaxurl, 
	   {
		  'action':'fn_tdm_delete_customfield',
		  'posttype_id': posttype_id,
		  'field_id': field_id
	   }, 
	   function(response){
							jQuery.fancybox.close();
							location.reload();
				}
	);
};


//*-------------------------------------------------------------------------
//*					Functions for saving / deleting Posttypes
//*	
//*-------------------------------------------------------------------------

function tdm_posttype_save(){
	
	jQuery.fancybox.showLoading();

	var form_data = jQuery("#form_posttype_save").serialize();
		form_data = form_data+'&action=fn_tdm_posttype_save';
				 
				jQuery.post(
						ajaxurl, 
						form_data, 
						function(response){	
						
								if (response == 'ok'){
										window.location.href = "admin.php?page=ManagePress";
									} else {
										jQuery.fancybox.hideLoading()
										
										jQuery("#message1").hide();
										jQuery("#message1").text(response);
										jQuery("#message1").fadeIn().delay(5000);
										jQuery("#message1").fadeOut();	
								}
							});
}



function tdm_show_delete_posttype_box(posttype_id,posttype_name){
	

var content = '<h2>Delete posttype "'+posttype_name+'" </h2> <br>Do you want to delete this posttype inclusive all fields? <br><br> <span onclick="tdm_delete_posttype_ajax('+posttype_id+',\''+posttype_name+'\')" id="del_custom_field_ajax" class="button" >Yes</span> <br> <br><span onclick="tdm_close_fancybox()" id="del_custom_field_ajax_close" class="button" >No, go back</span> <br><br><br>';

jQuery.fancybox.open({
					'closeBtn'		: 'true',
					'openEffect ' 	: 'fade',
					'content' 		: content
				} );
}


function tdm_delete_posttype_ajax(posttype_id,posttype_name){
 jQuery.post(
   ajaxurl, 
   {
      'action': 'fn_tdm_posttype_delete',
      'posttype_id': posttype_id,
	  'posttype_name': posttype_name
   }, 
   function(response){
   	jQuery.fancybox.close();
	location.reload();
	 }
);
 }




//*-------------------------------------------------------------------------
//*					Functions for META BOX
//*	
//*-------------------------------------------------------------------------
// Functions Metabox for Post START ---------------
function mps_init_metabox(){
		
		jQuery( ".field_date" ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});
		
		jQuery(".field_time").focus(function() {
			//jQuery(this).after('sf');
		});

}


function tdm_show_postconnection_box(typ,posttype){
jQuery.fancybox.showLoading();
 	jQuery.post(
   ajaxurl, 
   {
      'action'	:'fn_get_posts',
      'data'	: typ,
	  'posttype': posttype
   }, 
   function(response){
	 jQuery.fancybox.open({
						'content' 		: response	 })
 
   }
);

}

function tdm_show_postconnection_box_onchange(typ,obj){
	var posttype = obj.value;
	
	 jQuery.fancybox.showLoading();
		jQuery.post(
	   ajaxurl, 
	   {
		  'action'	:'fn_get_posts',
		  'data'	: typ,
		  'posttype': posttype
	   }, 
	   function(response){
		 jQuery.fancybox.open({
							'content' 		: response	 })
	 
	   }
	);
} 


  function del_default_field(post_id){
	   jQuery("#"+post_id).detach();
  }
  
  
function del_postconnection(obj){
	
	var parent		= jQuery(obj).parent(".tdm_post_line"); 
	var parent1		= jQuery(parent).parent(".tdm_postconnection_posts"); 
	var parent2		= jQuery(parent1).parent(".tdm_postconnection_grp1");
	
	
	jQuery(parent).fadeOut('slow',function(){ jQuery(parent).detach(); 
											
												if(jQuery(parent1).children().size() == 0) {
													jQuery(parent2).fadeOut('slow',function(){  jQuery(parent2).detach(); });
												};
											});
}
  
  
  
function tdm_add_post(post_id,posttype,pt_pluralname,modus){

	var copy = jQuery('#add_'+post_id).html();
	var id_grp = 'grp_'+modus+'_'+posttype;
	
			if(jQuery('#'+id_grp).length){
				var object 	= jQuery('#'+id_grp);
				var target	= jQuery(object).children('.tdm_postconnection_posts');
				jQuery(target).append(copy);
				
			} else {			
				var code = '<div class="tdm_postconnection_grp1" id="grp_'+modus+'_'+posttype+'"> ';
					code += 	'<div class="tdm_grp2_left">'+pt_pluralname+'</div>';
					code += 	'<div class="tdm_postconnection_posts">';
					code +=			copy;
					code +=		'</div>';
				jQuery('#tdm_'+modus).append(code);
			}
				
	jQuery.fancybox.close(); 
}

function tdm_show_googlemap_box(id){
	
	var field_id_coordinates = id;
	var field_id_adress = id +'_address';
	var address = document.getElementById(field_id_adress).value;

 jQuery.fancybox.showLoading();
 	jQuery.post(
   ajaxurl, 
   {
      'action'	:'fn_tdm_get_box_google',
      'field_id_adress'			: field_id_adress,
	  'field_id_coordinates'	: field_id_coordinates,
	  'address'					: address
   }, 
   function(response){
	 jQuery.fancybox.open({
						'content' 		: response	 })
   }
	);
}


function mps_add_empty_input(obj){
	var value = jQuery(obj).prev().children(':first').val();
	jQuery(obj).prev().children(':first').val('');
	var w = jQuery(obj).prev().children(':first').clone();
	jQuery(w).children(':first').val('');
	jQuery(w).children(':first').removeClass('hasDatepicker');
	
	var now = new Date();
	var id = Date.parse(now.toGMTString());
	id = id + now.getMilliseconds();
	
	jQuery(w).children(':first').attr('id',id);
	jQuery(obj).prev().append(w);
	mps_init_metabox();
}

function mps_delete_input(obj){
	
	var p1 = jQuery(obj).parents(".mps_input_all_elements").children();
	
	if (p1.size() < 2){
		jQuery(obj).prev().val('');
	} else {
	var p = jQuery(obj).parents(".mps_input_element");
	jQuery(p).detach();
	};
}


// Functions Metabox for Post END ---------------




//*-------------------------------------------------------------------------
//*					Functions Export Plugin
//*	
//*-------------------------------------------------------------------------
function mps_export_plugin(){

	var form_data = jQuery("#form_export_plugin").serialize();
	form_data = form_data+'&action=fn_mps_export_plugin';

	 

	 
	 jQuery.fancybox.showLoading();
	 
	 
	 
		jQuery.post(
	   ajaxurl,
	   form_data, 
	   
	   function(response){
		 jQuery.fancybox.open({
							'content' 		: response	 })
	 
	   }
	);
}




//*-------------------------------------------------------------------------
//*					Functions Dashboard
//*	
//*-------------------------------------------------------------------------


function mps_ini_dashboard_list(){
	jQuery('#mps_dashboard_datatable_list').dataTable({
							"bPaginate": false,
							"bLengthChange": false,
							"bFilter": true,
							"bSort": true,
							"bInfo": false,
							"bAutoWidth": false ,
							});
	
}