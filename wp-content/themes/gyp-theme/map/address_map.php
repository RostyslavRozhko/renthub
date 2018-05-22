<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&language=<?php echo pll_current_language('slug'); ?>"></script>
<script type="text/javascript"> 

  function initMap() {
	var map = new google.maps.Map(document.getElementById('map_canvas'), {
      center: {lat: 50.4490244, lng: 30.5201343},
      zoom: 13
    });
	
    var input = document.getElementById('cc_city');
	
	var options = {
	  types: ['(cities)'],
	  componentRestrictions: {country: 'ua'}
	};
	
	var autocomplete = new google.maps.places.Autocomplete(input, options);
    autocomplete.bindTo('bounds', map);
	
	//var infowindow = new google.maps.InfoWindow();
    var marker = new google.maps.Marker({
      map: map
    });
/*
    marker.addListener('click', function() {
      infowindow.open(map, marker);
    });
*/	
	autocomplete.addListener('place_changed', function() {
	  //infowindow.close();	
	  var place = autocomplete.getPlace();
      if (!place.geometry) {
        return;
      }

      if (place.geometry.viewport) {
        map.fitBounds(place.geometry.viewport);
      } else {
        map.setCenter(place.geometry.location);
        map.setZoom(17);
      }

      // Set the position of the marker using the place ID and location.
      marker.setPlace({
         placeId: place.place_id,
         location: place.geometry.location
      });
      marker.setVisible(true);
/*
      infowindow.setContent('<div><strong>' + place.name + '</strong><br>' +
          place.formatted_address);
      infowindow.open(map, marker);
*/
	  jQuery('#city_id').val(place.place_id);
//	  jQuery('#city_id').val(place.address_components[2].long_name);
    });
  }
  
  google.maps.event.addDomListener(window, 'load', initMap);

</script>
<div id="map_canvas" style="height:350px; margin-top: 10px; position:relative;"  class="form_row clearfix"></div>