<?php

if(is_array($array_coordinates)){
	for ($i = 0; $i < count($array_coordinates); $i++) {
		$js_array .= "allPoints[".$i."] = []; \n";
		$js_array .= "allPoints[".$i."]['coordinates'] 	= '".$array_coordinates[$i]."';\n";
		$js_array .= "allPoints[".$i."]['title']		= '".$array_titles[$i]."';\n";
		$js_array .= "allPoints[".$i."]['html']			= '".$array_html[$i]."';\n";
	}
}

?>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
<script>
	
jQuery(document).ready(function() {
//loadScript();
});

var map;
var allMarkerArray = [];
var allPoints = [];
var allInfoArray = [];
var infowindow;
 
<?php echo $js_array; ?>

  
 function ini_map1() {

    var myLatlng = new google.maps.LatLng(-34.397, 150.644);
    var myOptions = {
      zoom: 8,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
												   }
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

	infowindow = new google.maps.InfoWindow( { content: 'plachoder..'  });
	
	if(allPoints){
		if(allPoints.length){
		greate_markers();
		}
	}
 };



function greate_markers(){
var bounds = new google.maps.LatLngBounds();

	for (var i = 0; i < allPoints.length; i++) {

		var teil = allPoints[i]['coordinates'].split(",");
		var position = new google.maps.LatLng(teil[0],teil[1]);
		
		var marker 	= new google.maps.Marker({
														position:  position, 
														map: map,
														title:allPoints[i]['title'],
														animation: google.maps.Animation.DROP,
														html: allPoints[i]['html']
														}); 

		allMarkerArray.push(marker);	
		google.maps.event.addListener(marker, 'click', function() {
																	  infowindow.setContent(this.html);
																	  infowindow.open(map,this);   });
		bounds.extend(position);
	};
	
	map.fitBounds(bounds);
}

</script>




