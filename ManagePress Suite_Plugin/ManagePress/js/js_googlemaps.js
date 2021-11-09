// JavaScript Document

 function loadScript() {
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=initialize";
    document.body.appendChild(script);
  }
  
 function initialize() {

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
