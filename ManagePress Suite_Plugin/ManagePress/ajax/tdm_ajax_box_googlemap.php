<?php


$address = $_REQUEST['address'];


$field_id_coordinates 	= $_REQUEST['field_id_coordinates'];
$field_id_address 		= $_REQUEST['field_id_address'];

$js_id = "'".$id."'";

?>

<script type="text/javascript">
  var geocoder;
  var map;
  var marker;
  
  function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(46.818188, 8.227511);
    var myOptions = {
      zoom: 8,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  }
  
  function codeAddress() {
    var address = document.getElementById("gm_address").value;
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
       	
		deleteOverlays2();
		
		map.setCenter(results[0].geometry.location);
        	marker = new google.maps.Marker({
            map: map, 
			animation: google.maps.Animation.DROP,
            position: results[0].geometry.location
        });
		
		var ausgabe = results[0].geometry.location.toString();								
		var code = ausgabe;								
		code = code.substr(1, code.length-2);
		
		document.getElementById('gm_geocode').value = code;
		
		
      } else {
        alert("Geocode was not successful for the following reason: " + status);
		document.getElementById('gm_geocode').value = '';
      }
    });
  }

  
   function deleteOverlays2() {
	 if (marker) {
	 marker.setMap(null)
	 }
  }


</script>
<div id="mapaOkolo" style="width:400px; height:400px">
<h2>Add a place </h2>
<br>
Adress or Coordinates: <input style="width:200px" id="gm_address" type="text" value="<?php echo $address ?>">
<input type="button" value="Search" onclick="codeAddress()">

<br><br>
<div id="map_canvas" style=" width:400px; height:250px;"></div>
generated coordinates: <input id="gm_geocode" readonly type="text">

<br><br>
<input style="width:100px;float:left"  type="button" class="button-primary" name="save"  onclick="tdm_add_coordinates(<?php echo "'".$field_id_coordinates."'"; ?>)" value="add this Place">


<input style="width:50px;float:right"  type="button" class="button-primary" name="close"  onclick="tdm_close_fancybox()" value="Close">


<script>
jQuery(document).ready(function() {
	initialize();
	
	if (document.getElementById('gm_address').value){
		codeAddress();
	}
	
});

function tdm_add_coordinates(field_id){
	
	field_id_address 		= field_id + '_address';
	field_id_coordinates 	= field_id + '_coordinates';
	
	document.getElementById(field_id_coordinates).value	=	document.getElementById('gm_geocode').value;
	document.getElementById(field_id_address).value		=	document.getElementById('gm_address').value;
	document.getElementById(field_id).value				=	document.getElementById('gm_address').value + '_;_' + document.getElementById('gm_geocode').value;
	
	if(document.getElementById('gm_address').value==''){
		document.getElementById(field_id).value = '';
		document.getElementById(field_id_address).value  = '';
		document.getElementById(field_id_coordinates).value = '';
	}

	if(document.getElementById('gm_geocode').value==''){
		document.getElementById(field_id).value = '';
		document.getElementById(field_id_address).value  = '';
		document.getElementById(field_id_coordinates).value = '';
	}
	
	
	tdm_close_fancybox();
}

</script>
</div>
<?php
die();

?>
