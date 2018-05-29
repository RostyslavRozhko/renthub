<?php

  function map_script()
  {
	global $post; ?>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBpRFvYomx8_jJ2e2R6sCsGEUVkrpfohLc&libraries=places&language=<?php echo pll_current_language('slug'); ?>"></script>
  
    <script>
      function initMap() {

        var input_search = document.getElementById('s_address');

		var options = {
	      componentRestrictions: {country: 'ua'},
        types: ['(cities)']
	    };
	    
	    var autocomplete_s = new google.maps.places.Autocomplete(input_search, options);
        
        autocomplete_s.addListener('place_changed', function() {
	      var place_s = autocomplete_s.getPlace();
      if (typeof place_s.place_id != 'undefined') jQuery('#s_city_id').val(place_s.place_id);
      const city = place_s.address_components.find(res => res.types.indexOf('locality') > -1).long_name
		  jQuery('#s_address').val(city);
        });



        const cat_address_search = document.getElementById('cat_address_search')
        const autocomplete_search_cat = new google.maps.places.Autocomplete(cat_address_search, options)

        autocomplete_search_cat.addListener('place_changed', function() {
          var place_s = autocomplete_search_cat.getPlace();
          if (typeof place_s.place_id != 'undefined') 
            jQuery('#search_loc_cat').val(place_s.place_id);
            cat_address_search.value = place_s.formatted_address
        });
		
		<?php if( is_tax('cate') ) : ?>

		var input_cat = document.getElementById('cat_address');
		
		var autocomplete_cat = new google.maps.places.Autocomplete(input_cat, options);

    jQuery('#cat_address_link').click(() => {
      if(input_cat.value === '') {
        jQuery('#cat_address_link').attr("href", '<?php global $wp; echo add_query_arg( array( 'lang' => false ), site_url( $wp->request )) . '?search_loc='; ?>&address=');
      }
    })
		
		autocomplete_cat.addListener('place_changed', function() {
	      var place_cat = autocomplete_cat.getPlace();
		  if (typeof place_cat.place_id != 'undefined') jQuery('#cat_address_link').attr("href", '<?php global $wp; echo add_query_arg( array( 'lang' => false ), site_url( $wp->request )) . '?search_loc='; ?>' + place_cat.place_id + '&address=' + place_cat.address_components[0].long_name);
		  jQuery('#cat_address').val(place_cat.address_components[0].long_name);
        });
		
		<?php endif; ?>
		
		<?php if( is_single() ) : ?>
		
		const map = new google.maps.Map(document.getElementById('map_canvas'));
		var geocoder = new google.maps.Geocoder;
		const address_list = document.getElementById('address_list')
		const cc_address_list = document.getElementById('cc_address_list')
		const arr = []
    const bounds = new google.maps.LatLngBounds()
		
		function show() {
    if(cc_address_list.value) IDs = JSON.parse(cc_address_list.value)
    	IDs.map(placeId => {
    		geocoder.geocode({'placeId': placeId}, function(results, status) {
              if (status === google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                	const place = results[0]
                  map.setZoom(10);
                  map.setCenter(place.geometry.location);
                  let marker = new google.maps.Marker({
                    map: map,
                    position: place.geometry.location
                  });

                  bounds.extend(place.geometry.location)

                  let city, country, street, street_number
                  place.address_components.map(res => {
                    if (res.types.indexOf('locality') > -1) {
                      city = res.long_name
                    }
                    if (res.types.indexOf('country') > -1) {
                      country = res.long_name
                    }
                    if (res.types.indexOf('route') > -1) {
                      street = res.long_name
                    }
                    if (res.types.indexOf('street_number') > -1) {
                      street_number = res.long_name
                    }
                  })
                  
                  const placeObj = {
                    placeId: placeId,
                    location: place.geometry.location,
                    name: place.formatted_address,
                    city: city,
                    country: country,
                    street: street,
                    street_number: street_number
                  }

                  arr.push({
                  	place: placeObj,
                    marker: marker
                  })
                  map.fitBounds(bounds)
                  const listener = google.maps.event.addListener(map, "idle", function() { 
                    if (map.getZoom() > 11) map.setZoom(11); 
                    google.maps.event.removeListener(listener); 
                  })

                  showAddress()
                }
              }
            });
		})

				}
			
			function showAddress() {
        	address_list.innerHTML = ''
          
        	arr.map((obj, pos) => {
          	const id = `address_${pos}`
            let street_name = ''
            if(obj.place.street) {
              street_name += obj.place.street

              if(obj.place.street_num) {
                street_name += ", " + obj.place.street_num
              }
            }

            address_list.innerHTML += 
							`
              	<div class="address_container">
                  <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/location-pin.svg" class="location-icon" />
                  <div class="address_container__text">
                    <div class="formated-address__top">${obj.place.city}, ${obj.place.country}</div>
                    <div class="formated-address__bottom">${street_name}</div>
                  </div>
                </div>
              `          	
          })
      }
	  
	  show()

		
    <?php endif; ?>
    
    <?php if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'profile') : ?>
    var geocoder = new google.maps.Geocoder;
    var placeId = jQuery('#s_city_id').val();
        geocoder.geocode({'placeId': placeId}, function(results, status) {
          if (status === google.maps.GeocoderStatus.OK) {
            if (results[0]) {
			  jQuery('#s_address').val(results[0].formatted_address);
            }
          }
        });
		
		<?php endif; ?>
		
		<?php if( is_author() || is_single() ) : ?>
		
		var geocoder = new google.maps.Geocoder;
		var placeId = jQuery('#cc_city_id').val();
        geocoder.geocode({'placeId': placeId}, function(results, status) {
          if (status === google.maps.GeocoderStatus.OK) {
            if (results[0]) {
			  jQuery('#city').text('<?php echo __( 'c.', 'prokkat' ); ?> ' + results[0].formatted_address);
            }
          }
        });
		
    <?php endif; ?>
    
    <?php if( !is_user_logged_in() ) : ?>
    const input_search_user = document.getElementById('cc_user_address');
      
      const autocomplete_cc = new google.maps.places.Autocomplete(input_search_user, options);
        
        autocomplete_cc.addListener('place_changed', function() {
        const place_s = autocomplete_cc.getPlace();
        if (typeof place_s.place_id != 'undefined') 
          jQuery('#cc_user_city_id').val(place_s.place_id);

        jQuery('#cc_user_address').val(place_s.formatted_address);
      
      });

      <?php endif; ?>

      }
	  google.maps.event.addDomListener(window, 'load', initMap);
  </script>
  <?php
  }
  
  function edit_map()
  { ?>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBpRFvYomx8_jJ2e2R6sCsGEUVkrpfohLc&libraries=places&language=<?php echo pll_current_language('slug'); ?>"></script>
    <script>
      function initMap() {
		const defaultLocation = {lat: 50.4490244, lng: 30.5201343}
		var map = new google.maps.Map(document.getElementById('map_canvas'), {
          center: defaultLocation,
          zoom: 11
        });
		var geocoder = new google.maps.Geocoder;
    var input = document.getElementById('cc_address');
    const submit = document.getElementById('add_address')
    const address_list = document.getElementById('address_list')
	  const cc_address_list = document.getElementById('cc_address_list')
    const input_address_num = document.getElementById('address_num')
    const cc_city_id = document.getElementById('cc_city_id')
    const cc_locations = document.getElementById('cc_locations')
    const arr = []
    
    let IDs = {
      streets: [],
      cities: [],
      locations: []
    }
		
    function show() {
    if(cc_address_list.value) IDs.streets = JSON.parse(cc_address_list.value)
    if(cc_locations.value) IDs.locations = JSON.parse(cc_locations.value)
    if(cc_city_id.value) IDs.cities = JSON.parse(cc_city_id.value)
    	IDs.streets.map(placeId => {
    		geocoder.geocode({'placeId': placeId}, function(results, status) {
              if (status === google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                	const place = results[0]
                  map.setZoom(13);
                  map.setCenter(place.geometry.location);
                  let marker = new google.maps.Marker({
                    map: map,
                    position: place.geometry.location
                  });
                  
                  const city = place.address_components[3].long_name
                  
                  const placeObj = {
                    placeId: placeId,
                    location: place.geometry.location,
                    name: place.formatted_address
                  }
                  
                  arr.push({
                  	place: placeObj,
                    marker: marker
                  })
                  
                  showAddress()
                }
              }
            });      
      })

    }
  		const findMarker = new google.maps.Marker({
          map: map
        });
        
      let currPlace = {}
      
	    var autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['address'],
	      componentRestrictions: {country: 'ua'}
	    });
        autocomplete.bindTo('bounds', map);
	
        autocomplete.addListener('place_changed', function() {
          var place = autocomplete.getPlace();
            if (!place.geometry) {
              return;
            }

            if (place.geometry.viewport) {
              map.fitBounds(place.geometry.viewport);
            } else {
              map.setCenter(place.geometry.location);
              map.setZoom(13);
            }
            
            currPlace = {
               placeId: place.place_id,
               location: place.geometry.location,
               name: place.formatted_address,
               city: place.address_components[3].long_name
            }
			            
            findMarker.setPlace({
            	location: place.geometry.location,
              placeId: place.place_id
            });
            findMarker.setVisible(true);

        });
                
        submit.addEventListener('click', () => {
          jQuery('.address-input__container').hide()
        	if (currPlace.placeId && arr.length < 1) {
            let newMarker = new google.maps.Marker({
              map: map,
              position: currPlace.location
            })
            
            map.setZoom(11)
            newMarker.setVisible(true)
            findMarker.setVisible(false)
            input.value = ''
				
            arr.push({
            	place: currPlace,
              marker: newMarker
            })
            
				
            geocoder.geocode({'location': currPlace.location}, (results, status) => {
              if (status === google.maps.GeocoderStatus.OK) {
                if(results[0]){
                  IDs.streets.push(currPlace.placeId)
                  IDs.locations.push(currPlace.location)
                  
                  let cities = ''
                  results.map(res => {
                    if (res.types.indexOf('locality') > -1 || res.types.indexOf('administrative_area_level_1') > -1) {
                      cities += res.place_id
                    }
                  })
                  
                  IDs.cities.push(cities)
                  
                  cc_address_list.value = JSON.stringify(IDs.streets)
                  cc_city_id.value = JSON.stringify(IDs.cities)
                  cc_locations.value = JSON.stringify(IDs.locations)
                }
              }
            })
				

            showAddress()
          }
        })
        
        function showAddress() {
        	address_list.innerHTML = ''
          
        	arr.map((obj, pos) => {
            const text = 							
            `
                  <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/location-pin-red.svg" class="address-add__icon" />
                  <div class="formated_address">${obj.place.name}</div>
              `     
            const listElement = jQuery('<div />', {
                                    class: 'address_container add-address-container',
                                    html: text,
                                  })
            const button = jQuery('<input />', {
                            class: 'delete_btn',
                            type: 'button',
                            val: "<?php _e('Delete', 'prokkat'); ?>",
                            click: function(e){
                              jQuery('.address-input__container').show()
                              let index = pos
                                arr.splice(index, 1)
                                IDs.streets.splice(index, 1)
                                IDs.cities.splice(index, 1)
                                IDs.locations.splice(index, 1)
                                obj.marker.setMap(null)
                          
                                cc_address_list.value = JSON.stringify(IDs.streets)
                                cc_city_id.value = JSON.stringify(IDs.cities)
                                cc_locations.value = JSON.stringify(IDs.locations)
                        
                                showAddress()
                            }})

            listElement.append(button)
            jQuery(address_list).append(listElement)
          })
  
        }
        show()
      }
	  google.maps.event.addDomListener(window, 'load', initMap);
  </script>
  <?php
  }
?>