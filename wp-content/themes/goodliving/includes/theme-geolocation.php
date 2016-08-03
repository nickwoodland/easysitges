<?php
/**
 * Colabs Geoloaction functions
 * This file controls code for the Geolocation features.
 */
 
define('COLABS_DEFAULT_ZOOM', 1);

function colabs_clean_coordinate($coordinate) {
	$pattern = '/^(\-)?(\d{1,3})\.(\d{1,15})/';
	preg_match($pattern, $coordinate, $matches);
	if (isset($matches[0])) return $matches[0];
}

function colabs_reverse_geocode($latitude, $longitude) {

  $colabs_gmaps_lang = get_option('colabs_gmaps_lang');
	$colabs_gmaps_region = get_option('colabs_gmaps_region');
	$http = (is_ssl()) ? 'https' : 'http';
	
	$url = "http://maps.google.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&language=".$colabs_gmaps_lang."&region=".$colabs_gmaps_region."&sensor=false";

	$result = wp_remote_get($url);
	
	if( is_wp_error( $result ) ) :
		global $colabs_log;
		// $colabs_log->write_log( __('Could not access Google Maps API. Your server may be blocking the request.', 'colabsthemes') ); 
		return false;
	endif;
	$json = json_decode($result['body']);
	$city = '';
	$country = '';
	$short_country = '';
	$state = '';
	$long_address = '';
	
	foreach ($json->results as $result)
	{
		foreach($result->address_components as $addressPart) {
			if((in_array('locality', $addressPart->types)) && (in_array('political', $addressPart->types)))
	    		$city = $addressPart->long_name;
	    	else if((in_array('administrative_area_level_1', $addressPart->types)) && (in_array('political', $addressPart->types)))
	    		$state = $addressPart->long_name;
	    	else if((in_array('country', $addressPart->types)) && (in_array('political', $addressPart->types))) {
	    		$country = $addressPart->long_name;
	    		$short_country = $addressPart->short_name;
	    	}
		}
	}
	
	if(isset($json->results[0]->formatted_address)) 
		$long_address = $json->results[0]->formatted_address;
	else
		$long_address = '';
		
	if(($city != '') && ($state != '') && ($country != ''))
		$address = $city.', '.$state.', '.$country;
	else if(($city != '') && ($state != ''))
		$address = $city.', '.$state;
	else if(($state != '') && ($country != ''))
		$address = $state.', '.$country;
	else if($country != '')
		$address = $country;
		
	if ($country=='United Kingdom') $short_country = 'UK';
		
	if(($city != '') && ($state != '') && ($country != '')) {
		$short_address = $city;
		$short_address_country = $state.', '.$country;
	} else if(($city != '') && ($state != '')) {
		$short_address = $city;
		$short_address_country = $state;
	} else if(($state != '') && ($country != '')) {
		$short_address = $state;
		$short_address_country = $country;
	} else if($country != '') {
		$short_address = $country;
		$short_address_country = '';
	}
	
	return array(
		'address' => $address,
		'country' => $country,
		'short_address' => $short_address,
		'short_address_country' => $short_address_country,
		'long_address' => $long_address,
	);
}


/**
 * Print Map Geolocation Script on Footer
 */
function colabs_geolocation_scripts($lat = '', $long = '') {
	global $colabs_map_lat, $colabs_map_long;
	$colabs_map_lat = $lat;
	$colabs_map_long = $long;
	add_action( 'wp_footer', 'colabs_geolocation_print_scripts', 100 );
}

function colabs_geolocation_print_scripts() {
	global $colabs_map_lat, $colabs_map_long;
	$lat = $colabs_map_lat;
	$long = $colabs_map_long;
	$zoom = COLABS_DEFAULT_ZOOM;
	$http = (is_ssl()) ? 'https' : 'http';
	$google_maps_api = (is_ssl()) ? 'https://maps-api-ssl.google.com/maps/api/js' : 'http://maps.google.com/maps/api/js';
	?>

	<script type="text/javascript">
		
		function initialize_map() {
			var hasLocation = false;
			var center = new google.maps.LatLng(0.0, 0.0);

			var postLatitude =  '<?php if( $lat == '' ){ global $posted, $property_details, $post; if (isset($posted['colabs_geo_latitude'])) echo $posted['colabs_geo_latitude']; elseif (isset($property_details->ID)) echo get_post_meta($property_details->ID, '_colabs_geo_latitude', true); elseif (isset($post->ID)) echo get_post_meta($post->ID, '_colabs_geo_latitude', true); }else{ echo $lat; } ?>';
			var postLongitude =  '<?php if( $long == ''){ global $posted, $property_details, $post;; if (isset($posted['colabs_geo_longitude'])) echo $posted['colabs_geo_longitude']; elseif (isset($property_details->ID)) echo get_post_meta($property_details->ID, '_colabs_geo_longitude', true); elseif (isset($post->ID)) echo get_post_meta($post->ID, '_colabs_geo_longitude', true); }else{ echo $long; } ?>';

			if( (postLatitude != '') && (postLongitude != '') ) {
			  center = new google.maps.LatLng(postLatitude, postLongitude);
			  hasLocation = true;
			  jQuery("#geolocation-latitude").val(center.lat());
			  jQuery("#geolocation-longitude").val(center.lng());
			  reverseGeocode(center);
			}

			var myOptions = {
				zoom: <?php echo $zoom; ?>,
				center: center,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};

			var geocoder = new google.maps.Geocoder();
			var map = new google.maps.Map( document.getElementById('geolocation-map'), myOptions );
			var marker = '';

			if( !hasLocation ) {
				map.setZoom(<?php echo (int) $zoom; ?>);
			} else {
				map.setZoom( 9 );
			}

			<?php if( !is_singular( COLABS_POST_TYPE ) ) : ?>
				google.maps.event.addListener( map, 'click', function(event) {
					reverseGeocode( event.latLng );
				});
			<?php endif; ?>

			var currentAddress;
			var customAddress = false;

			jQuery('#geolocation-load').on('click', function(e){
				if( jQuery('#geolocation-address').val() != '' ) {
					customAddress = true;
					currentAddress = jQuery("#geolocation-address").val();
          geocode(currentAddress);
          return false;
				}

				else {
					marker.setMap(null);
					marker = '';
					jQuery("#geolocation-latitude").val('');
					jQuery("#geolocation-longitude").val('');
					return false;
				}
			});

			jQuery("#geolocation-address").keyup(function(e) {
			  if(e.keyCode == 13)
			    jQuery("#geolocation-load").click();
			});

			// Prevent submitting the form when user press enter key on
			// geolocation address field
			jQuery('#geolocation-address').on('keyup', function(e){
				if( e.which == 13 ) {
					jQuery('#geolocation-load').trigger('click');
				}
			});

			/**
			 * Place Marker
			 */
			function placeMarker( location ) {
				if ( marker=='' ) {
				  marker = new google.maps.Marker({
				    position: center,
				    map: map, 
				    title:'Property Location'
				  });
				}
				marker.setPosition(location);
				map.setCenter(location);
				if((location.lat() != '') && (location.lng() != '')) {
				  jQuery("#geolocation-latitude").val(location.lat());
				  jQuery("#geolocation-longitude").val(location.lng());
				}
			}

			/**
			 * GeoCode
			 */
			function geocode(address) {
			  var geocoder = new google.maps.Geocoder();
	    	if (geocoder) {
		    	geocoder.geocode({"address": address}, function(results, status) {
		      	if (status == google.maps.GeocoderStatus.OK) {
		        	placeMarker(results[0].geometry.location);
		        	reverseGeocode(results[0].geometry.location);
		        	if(!hasLocation) {
		            map.setZoom(9);
		            hasLocation = true;
		        	}
		      	}
		    	});
		  	}
			  // jQuery("#geodata").html(latitude + ', ' + longitude);
			}
			
			/**
			 * Reverse Geocoding
			 */
			function reverseGeocode( location ) {
				var geocoder = new google.maps.Geocoder();
			  if (geocoder) {
				  geocoder.geocode({"latLng": location}, function(results, status) {
					  if (status == google.maps.GeocoderStatus.OK) {

					    var address, country, state;
					    var city = [];

					    for ( var i in results ) {
				        var address_components = results[i]['address_components'];
					        
			        	for ( var j in address_components ) {
				        
				          var types = address_components[j]['types'];
				          var long_name = address_components[j]['long_name'];
				          var short_name = address_components[j]['short_name']; 
				          
				          if ( jQuery.inArray('locality', types)>=0 && jQuery.inArray('political', types)>=0 ) {
				            city.push(long_name);
				          }

				          else if ( jQuery.inArray('administrative_area_level_1', types)>=0 && jQuery.inArray('political', types)>=0 ) {
				            state = long_name;
				          }

				          else if ( jQuery.inArray('country', types)>=0 && jQuery.inArray('political', types)>=0 ) {
				            country = long_name;
				          }

				        } 
				        if((city) && (state) && (country)) break;
					    }
					    
					    city = city.join(", ");

					    if((city) && (state) && (country))
					      address = city + ', ' + state + ', ' + country;
					    else if((city) && (state))
					      address = city + ', ' + state;
					    else if((state) && (country))
					      address = state + ', ' + country;
					    else if(country)
					      address = country;
					    
					    jQuery("#geolocation-address").val(results[i]['formatted_address']);
					    placeMarker(location);
					    
					    return true;
					  } 
				  });
				}
				return false;
			}
		}

		function loadScript() {
		  var script = document.createElement("script");
		  script.type = "text/javascript";
		  script.src = "<?php echo $google_maps_api; ?>?v=3&sensor=false&language=<?php echo get_option('colabs_gmaps_lang') ?>&region=<?php echo get_option('colabs_gmaps_region') ?>&callback=initialize_map";
		  document.body.appendChild(script);
		}
		  
		jQuery(function(){
			var google = google || {};
			// Prevent form submission on enter key
			jQuery("#submit_form").submit(function(e) {
				if (jQuery("input:focus").attr("id")=='geolocation-address') return false;
			});

			if( typeof google.maps != "object" ) {
				loadScript();
			} else {
				initialize_map();
			}

		});  
		

	</script>
	<?php
}

add_action( 'admin_footer-post-new.php', 'colabs_admin_location_print_scripts', 11 );
add_action( 'admin_footer-post.php', 'colabs_admin_location_print_scripts', 11 );
 
function colabs_admin_location_print_scripts() {
  global $post_type;
  if(( COLABS_POST_TYPE == $post_type )){
    //colabs_geolocation_print_scripts();
  }
}


/**
 * Fetch result of facility found near location
 *
 * @param  String $latitude  Google maps latitude
 * @param  String $longitude Google maps longitude
 * @return Array
 */
function colabs_get_nearby_places( $latitude, $longitude ) {

	$cache_key = colabs_generate_nearbysearch_transient( $latitude, $longitude );
	$results = get_transient( $cache_key );

	// Try to get results from transient
	if( $results !== false ) {
		return $results;
	}

	// Transient not found, lets fetch the api
	else {
		$data = colabs_recursive_nearby_search($latitude, $longitude);
		$colabs_gmaps_types = get_option('colabs_gmaps_types');

		// Make sure data is available
		if( !$data )
			return false;

		$results = array(
			'facility_found' => array(),
			'facility_data' => array()
		);

		$types = array();
		$neighborhood_info = array();

		// Turn string from google map type on theme options into an array
		$colabs_gmaps_types_array = explode('|', $colabs_gmaps_types);

		// Iterate each result, and add the result type into an array
		// for easier counting the number of result found
		foreach ($data as $result){
		  $get_types = $result->types;

		  foreach($get_types as $type){
		  	// Only add type that match with types on theme options
		  	if( in_array( $type, $colabs_gmaps_types_array) ) {
		    	$types[] = $type; 
		  	}
		  }
		}

		// Count number of result found
		$neighborhoods = array_count_values($types);
		if( !function_exists('colabs_get_google_map_types') && !function_exists('colabs_get_icon_list') ) {
			require( get_template_directory() . '/includes/default-variables.php');
		}
		$default_types_name = colabs_get_google_map_types();

		foreach( $neighborhoods as $place_key => $place_count ) {
			$results['facility_found'][ $place_key ] = array(
				'label' => isset( $default_types_name[$place_key] ) ? $default_types_name[$place_key] : __('Unknown', 'colabsthemes'),
				'count' => $place_count
			);
		}

		// Push the facility data into results array
		$results['facility_data'] = $data;

		// Save results into transient
		set_transient( $cache_key, $results, WEEK_IN_SECONDS );
		return $results;
	}
}

/**
 * Resursive function for fetching nearby place
 *
 * We use recursive functions because results from google places only 
 * return 20 items. For fetching next 20 items, pagetoken parameter 
 * is needed. Page token included inside the results.
 */
function colabs_recursive_nearby_search( $latitude = '', $longitude = '', $page_token = '', $prev_data = array() ) {
	$colabs_gmaps_lang = get_option('colabs_gmaps_lang');
	$colabs_gmaps_key = get_option('colabs_gmaps_key');
	$colabs_gmaps_radius = get_option('colabs_gmaps_radius');
	$colabs_gmaps_types = get_option('colabs_gmaps_types');
	$results = array(
		'facility_found' => array(),
		'facility_data' => array()
	);

	if( $page_token == '' ) {
		$url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".$latitude.",".$longitude."&radius=".$colabs_gmaps_radius."&types=".$colabs_gmaps_types."&key=".$colabs_gmaps_key."&sensor=false";
	} else {
		$url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?pagetoken=". $page_token ."&key=".$colabs_gmaps_key."&sensor=false";
	}

	$result = wp_remote_get($url);

	if( is_wp_error( $result ) ) {
		$all_results = array_merge( $prev_data, array() );
	} else {
		$json = json_decode($result['body']);
		$all_results = array_merge( $prev_data, $json->results );
	}


	if( !isset($json->next_page_token) ) {
		return $all_results;
	} else {
		sleep(2);
		return colabs_recursive_nearby_search( $latitude, $longitude, $json->next_page_token, $all_results );
	}
}

/**
 * Generate transient key for nearby place search
 *
 * @param  String $latitude  Google maps latitude
 * @param  String $longitude Google maps longitude
 *
 * @return String Generated Transient Key
 */
function colabs_generate_nearbysearch_transient( $latitude = '', $longitude = '' ) {
	$colabs_gmaps_types = get_option('colabs_gmaps_types');
	$colabs_gmaps_lang = get_option('colabs_gmaps_lang');
	$colabs_gmaps_key = get_option('colabs_gmaps_key');
	$colabs_gmaps_radius = get_option('colabs_gmaps_radius');

	$transient_key = 'colabs_' . md5( "nearby_search_{$colabs_gmaps_types}_{$colabs_gmaps_lang}_{$colabs_gmaps_key}_{$colabs_gmaps_radius}_{$latitude}_{$longitude}" );

	return $transient_key;
}

/**
 * Output the facility found near the given location
 * 
 * @param  String $latitude  Google maps latitude
 * @param  String $longitude Google maps longitude
 * @return void
 */
function colabs_output_nearbysearch($latitude, $longitude) {

  $neighborhoods = colabs_get_nearby_places( $latitude, $longitude );

  if(!empty($neighborhoods)):
    echo '<div class="column col5">';
    echo '<h4 class="property-row-title">'.__('Neighborhood Info','colabsthemes').'</h4>';
    echo '<ul class="neighborhood-info">';
    foreach ( $neighborhoods['facility_found'] as $facility_found ){
      echo '<li>';
      	echo $facility_found['label'] . ' ';
      	echo '<a href="#map-neighborhood-modal" data-toggle="modal">';
      		echo '( '.__('Found','colabsthemes').' '.$facility_found['count'].' )';
      	echo '</a>';
      echo '</li>';
    }
    echo '</ul>';
    echo '</div>';
  endif;
}

function _colabs_get_geolocation_url( $address = '' ) {

	$google_maps_json_url = ( is_ssl() ? 'https' : 'http' ) . '://maps.googleapis.com/maps/api/geocode/json';

	$lang = get_option('colabs_gmaps_lang');
	$region = get_option('colabs_gmaps_region');

	$args = array(
		'sensor' 	=> 'false',
		'language' 	=> $lang,
	);

	if ( is_array( $address ) ) {
		$args['latlng'] = implode( ',', $address );
	} elseif( $address ) {
		$args['address'] = urlencode( $address );
	}
	$args['region'] = $region;

	$args = apply_filters( 'colabs_geolocation_params', $args, 'json' );

	return add_query_arg( $args, $google_maps_json_url );
}

// Radial location search
function colabs_radial_search($radius, $address_array = '') {
	global $wpdb;


	$unit = get_option('colabs_distance_unit');

	// Final fallback just in case radius is not set and smart_radius fails due to API not returning a bounds/viewport.
	if ( ! $radius )
		$radius = 50;
  
	if ( is_array( $address_array ) ) {

		if (isset($address_array['longitude']) && isset($address_array['latitude'])) :

			$lat = $address_array['latitude']; 
			$lng = $address_array['longitude']; 
			$radius = (int) $radius; 

			$R = 'mi' == $unit ? 3959 : 6371;

			$geo_data =  "
					SELECT latitude.post_id as post_id, lat, lng, latitude.post_date as post_date FROM
						( SELECT post_id, meta_value lat, post_date
							FROM $wpdb->posts 
							LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
							WHERE meta_key = '_colabs_geo_latitude' AND $wpdb->posts.post_status = 'publish'
						) latitude, 
						( SELECT post_id, meta_value lng, post_date
							FROM $wpdb->posts 
							LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
							WHERE meta_key = '_colabs_geo_longitude' AND $wpdb->posts.post_status = 'publish'
						) longitude
					WHERE  latitude.post_id = longitude.post_id
					";

			$radial_query = $wpdb->prepare( "
						SELECT geo_data.post_id, ( %d * acos( cos( radians(%f) ) * cos( radians(geo_data.lat) ) * cos( radians(geo_data.lng) - radians(%f) ) + sin( radians(%f) ) * sin( radians(geo_data.lat) ) ) ) AS distance, geo_data.post_date 
						FROM ( $geo_data ) as geo_data 
            HAVING distance < %d
						ORDER BY COALESCE(distance, 999999999) ASC, geo_data.post_date DESC", $R, $lat, $lng, $lat, $radius );

			$result = $wpdb->get_results( $radial_query );

			return $result;
			
		endif;
	}

	return false;
}