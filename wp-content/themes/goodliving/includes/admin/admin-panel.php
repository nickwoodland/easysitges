<?php
global $property_details, $key;

//Get Agent via an Array
global $wpdb;
$colabs_agents = array();
$agentarray = $wpdb->get_results( "
SELECT id,post_title
FROM $wpdb->posts AS p
WHERE p.post_type = 'agent' AND p.post_status = 'publish'
" );
$i = 0;
foreach ($agentarray as $item) {
	$colabs_agents[$item->id] = $item->post_title;
}

$key='property';						

$options_features_amount = array("0","1","2","3","4","5","6","7","8","9","10+");

$property_details[] = array (  "name"  => $key."_label",
					            "std"  => "",
					            "label" => __("Hover Title","colabsthemes"),
					            "type" => "text",
					            "desc" => __("Enter the text for image hover","colabsthemes"));		

$property_details[] = array (  "name"  => $key."_address",
					            "std"  => "",
					            "label" => __("Address","colabsthemes"),
					            "type" => "textarea",
					            "desc" => __("Enter the address for the listing. For example, 1223 Main Street","colabsthemes"));		

$property_details[] = array (  "name"  => $key."_citystate",
					            "std"  => "",
					            "label" => __("City and State","colabsthemes"),
					            "type" => "text",
					            "desc" => __("Enter the City and State in one line.  You can include the zip/postal code, but we think it looks better without.  This will <strong>NOT</strong> be used as search criteria. ","colabsthemes"));								

$property_details[] = array (  "name"  => $key."_agent",
					            "label" => __("Choose an Agent","colabsthemes"),
					            "type" => "select2",
											"options" => $colabs_agents,
					            "desc" => __("If this property has a specific agent, choose one. Leave blank if there is not a specific agent for the property. If you leave it blank, then the property details contact form will not be specific to a single agent. (You create Agents by going to Agent -> Add New Agent)","colabsthemes"));								

$property_details[] = array (  "name"  => $key."_price",
					            "std"  => "",
					            "label" => __("Price","colabsthemes"),
					            "type" => "text",
					            "desc" => __("How much is this property? Enter '0' if you want 'Price on Request' labeled instead of the price.","colabsthemes"));

$property_details[] = array (  "name"  => $key."_beds",
					            "std"  => "",
					            "label" => __("Bedrooms","colabsthemes"),
					            "type" => "select",
                      "options" => $options_features_amount,
					            "desc" => __("How many bedrooms does this property have? This is used when visitors search by number of bedrooms.","colabsthemes"));
								
$property_details[] = array (  "name"  => $key."_baths",
					            "std"  => "",
					            "label" => __("Bathrooms","colabsthemes"),
					            "type" => "select",
                      "options" => $options_features_amount,
					            "desc" => __("How many bathrooms does this property have? This is used when visitors search by number of bathrooms.","colabsthemes"));

$property_details[] = array (  "name"  => $key."_size",
					            "std"  => "",
					            "label" => __("Size","colabsthemes"),
					            "type" => "text",
					            "desc" => __("The home/building size.  For example: 5255 sq ft.","colabsthemes"));	

$property_details[] = array (  "name"  => $key."_garage",
					            "std"  => "",
					            "label" => __("Garages","colabsthemes"),
					            "type" => "select",
                      "options" => $options_features_amount,
					            "desc" => __("How many garage is this property? (Leave blank to ignore).","colabsthemes"));	

$property_details[] = array (  "name"  => $key."_furnished",
					            "std"  => "false",
                      "label" => __("Fully Furnished","colabsthemes"),
					            "type" => "radio",
                      "options" => array(	"false" => "No Availabe","true" => "Availabe"),
					            "desc" => __("Are this property is fully furnished?","colabsthemes"));

$property_details[] = array (  "name"  => $key."_mortgage",
					            "std"  => "false",
                      "label" => __("Mortgage","colabsthemes"),
					            "type" => "radio",
                      "options" => array(	"false" => "No Availabe","true" => "Availabe"),							
					            "desc" => __("Are this property is mortgage?","colabsthemes"));								

// $property_details[] = array (  "name"  => "expires",
// 					            "std"  => "",
// 											"label" => __("Valid Date","colabsthemes"),
// 					            "type" => "calendar",							
// 					            "desc" => __("","colabsthemes"));		

$property_details[] = array (  "name"  => "_colabs_property_duration",
					            "std"  => "",
                      "label" => __("Property Duration","colabsthemes"),
					            "type" => "text",							
					            "desc" => __("days","colabsthemes"));	

function colabs_create_properties_meta_box() {
	if( function_exists( 'add_meta_box' ) ) add_meta_box( 'new-meta-boxes', __('Property Details', 'colabsthemes'), 'colabs_display_meta_box', COLABS_POST_TYPE, 'normal', 'high' );
  // if( function_exists( 'add_meta_box' ) ) add_meta_box( 'location-meta-boxes', __('Property Location', 'colabsthemes'), 'colabs_display_location_meta_box', COLABS_POST_TYPE, 'side', 'high' );
  if( function_exists( 'add_meta_box' ) ) add_meta_box( 'gallery-meta-boxes', __('Property Gallery', 'colabsthemes'), 'colabs_display_gallery_meta_box', COLABS_POST_TYPE, 'side', 'high' );
}

function colabs_display_meta_box($post) {
	global $post, $property_details, $key;
	?>
	
  <div class="panel-wrap">	
    <div class="form-wrap">
      <?php
      wp_nonce_field( 'colabs_property_details_nounce', $key . '_wpnonce', false, true );
      if ( $terms = wp_get_object_terms( $post->ID, 'property_status' ) )
        $property_status = sanitize_title( current( $terms )->slug );
      else
        $property_status = apply_filters( 'default_property_status', 'sale' );
 
      $property_status_selector = apply_filters( 'property_status_selector', array(
        'sale' 	=> __( 'For Sale', 'colabsthemes' ),
        'rent' 	=> __( 'For Rent', 'colabsthemes' ),
        'sold' 	=> __( 'Recently Sold', 'colabsthemes' ),
        'not-sale'  => __( 'Not for Sale', 'colabsthemes' )
      ), $property_status );

      $type_box  = '';
      foreach ( $property_status_selector as $value => $label ){
        $type_box .= '<input type="radio" name="property-status" class="colabs_input_radio" value="' . esc_attr( $value ) . '" ' . checked( $property_status, $value, false ) .'>';
        $type_box .= '<span style="display:inline" class="colabs_input_radio_desc">' . esc_html( $label ) . '</span>';
        $type_box .= '<div class="colabs_spacer"></div>';
      }
      
      ?>
      <table class="colabs_metaboxes_table">
        <tbody>
        <tr>		
          <th class="colabs_metabox_names"><label for="property-status"><?php _e( 'Status', 'colabsthemes' )?></label></th>
          <td><?php echo $type_box;?></td>
        </tr>
        <tr class="rent" style="display:none;">		
          <th class="colabs_metabox_names"><label for="price_period"><?php _e( 'Price Period', 'colabsthemes' )?></label></th>
          <td>
            <select name="property_price_periode" id="colabsthemes_property_price_periode" class="colabs_input_select">
              <option value="day" <?php selected( get_post_meta($post->ID,'property_price_periode',true), 'day', true );?>><?php _e( 'Day', 'colabsthemes' )?></option>
              <option value="month" <?php selected( get_post_meta($post->ID,'property_price_periode',true), 'month', true );?>><?php _e( 'Month', 'colabsthemes' )?></option>
              <option value="year" <?php selected( get_post_meta($post->ID,'property_price_periode',true), 'year', true );?>><?php _e( 'Year', 'colabsthemes' )?></option>
            </select>
            <span class="colabs_metabox_desc description"><?php _e( 'Property price periode only for rent status.', 'colabsthemes' )?></span></td>
        </tr>
        </tbody>
      </table>  
      <?php echo colabs_custom_meta_generator($post,$property_details);?>
    
    </div>
  </div>	
	<?php
}

function colabs_display_gallery_meta_box($post) {
	?>
		<div id="property_images_container">
			<ul class="property_images">
				<?php
					if ( metadata_exists( 'post', $post->ID, '_property_image_gallery' ) ) {
						$property_image_gallery = get_post_meta( $post->ID, '_property_image_gallery', true );
					} else {
						// Backwards compat
						$attachment_ids = get_posts( 'post_parent=' . $post->ID . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids&meta_key=_foxestate_exclude_image&meta_value=0' );
						$attachment_ids = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
						$property_image_gallery = implode( ',', $attachment_ids );
					}

					$attachments = array_filter( explode( ',', $property_image_gallery ) );

					if ( $attachments )
						foreach ( $attachments as $attachment_id ) {
							echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
								' . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . '
								<ul class="actions">
									<li><a href="#" class="delete tips" data-tip="' . __( 'Delete image', 'colabsthemes' ) . '">&times;</a></li>
								</ul>
							</li> ';
						}
				?>
			</ul>

			<input type="hidden" id="property_image_gallery" name="property_image_gallery" value="<?php echo esc_attr( $property_image_gallery ); ?>" />

		</div>
		<p class="add_property_images hide-if-no-js">
			<a href="#" data-choose="<?php _e( 'Add Images to Property Gallery', 'colabsthemes' ); ?>" data-update="<?php _e( 'Add to gallery', 'colabsthemes' ); ?>" data-delete="<?php _e( 'Delete image', 'colabsthemes' ); ?>" data-text="<?php _e( 'Delete', 'colabsthemes' ); ?>"><?php _e( 'Add property gallery images', 'colabsthemes' ); ?></a>
		</p>
		
		<style>
			.property_images {
				
			}
			.property_images .image {
				position: relative;
				display: inline-block;
				*display: inline;
				*zoom: 1;
				background: #eee;
				padding: 5px;
				width: 65px;
				margin: 4px 2px;
				vertical-align: top;
				border: 1px solid #ccc;
			}
			.property_images .actions {
				position: absolute;
				right: 5px;
				top: 5px;
			}
			.property_images .delete {
				background: #333;
				color: #fff;
				padding: 1px 5px;
				text-decoration: none;
			}
			.property_images img {
				max-width: 100%;
				height: auto;
				width: auto;
				display: block;
			}
		</style>

		<script>
			(function($){

				var prop_gallery = {
					el: {},
					post_gallery_frame: undefined,

					/**
					 * Event Binding
					 */
					eventBinding: function() {
						this.el.$add_gallery_button.on('click', $.proxy( this.openMediaGallery, this ));
						this.el.$post_image.on('click', '.delete', $.proxy( this.removeImage, this ));
					},

					/**
					 * Open WordPress Media Gallery
					 */
					openMediaGallery: function(e) {
						e.preventDefault();

						var _self = this,
								$el = $(e.currentTarget),
								attachment_ids = this.el.$image_gallery_ids.val();

						// If the media frame already exists, reopen it.
						if( this.post_gallery_frame ) {
							this.post_gallery_frame.open();
							return;
						}

						// Create media frame
						this.post_gallery_frame = wp.media.frames.colabs_property_gallery = wp.media({
							title: "<?php _e(' Add Images to Property Gallery ', 'colabsthemes'); ?>",
							library: {
								type: 'image'
							},
							button: {
								text: "<?php _e('Add to gallery', 'colabsthemes'); ?>"
							},
							multiple: true
						});

						// When image is selected, run a callback
						this.post_gallery_frame.on('select', function() {
							var selection = _self.post_gallery_frame.state().get('selection');
							selection.map( function( attachment ) {
								attachment = attachment.toJSON();

								if( attachment.id ) {
									attachment_ids = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;

									_self.el.$post_image.append('\
										<li class="image" data-attachment_id="' + attachment.id + '">\
											<img src="' + attachment.url + '" />\
											<ul class="actions">\
												<li><a href="#" class="delete" title="<?php _e( 'Delete image', 'colabsthemes' ); ?>">&times;</a></li>\
											</ul>\
										</li>');
								}
							});

							_self.el.$image_gallery_ids.val( attachment_ids );
						});

						// Finally, open the modal
						this.post_gallery_frame.open();
					},

					/**
					 * Remove Image from Gallery
					 */
					removeImage: function(e) {
						e.preventDefault();

						var $btn = $(e.currentTarget),
								attachment_ids = '';

						$btn.closest('li.image').remove();

						this.el.$post_image.find('li.image').css('cursor','default').each(function() {
							var attachment_id = $(this).attr( 'data-attachment_id' );
							attachment_ids = attachment_ids + attachment_id + ',';
						});

						// Remove trailing comma
						attachment_ids = attachment_ids.replace(/\,$/g, '');

						this.el.$image_gallery_ids.val( attachment_ids );
					},

					/**
					 * Initialization
					 */
					init: function() {
						this.el.$image_gallery_ids = $('#property_image_gallery');
						this.el.$post_image = $('#property_images_container .property_images');
						this.el.$add_gallery_button = $('.add_property_images a');

						this.eventBinding();
					}
				};

				prop_gallery.init();

			})(jQuery);
		</script>

	<?php
}

function colabs_save_meta_box( $post_id ) {
	global $post, $property_details, $key;
	
	if ( !isset($_POST[ $key . '_wpnonce' ] ) ) return $post_id;
	if ( !wp_verify_nonce( $_POST[ $key . '_wpnonce' ], 'colabs_property_details_nounce' ) ) return $post_id;
	
	if ( !current_user_can( 'edit_post', $post_id )) return $post_id;

	if (( isset( $_POST['action'] ) && 'editpost' == $_POST['action'] )&&( COLABS_POST_TYPE == $post->post_type)) {
    colabs_custom_meta_save_handler($post_id,$property_details);
    $attachment_ids = array_filter( explode( ',', sanitize_text_field( $_POST['property_image_gallery'] ) ) );

    update_post_meta( $post_id, '_property_image_gallery', implode( ',', $attachment_ids ) );
    
    $property_status = empty( $_POST['property-status'] ) ? 'sale' : sanitize_title( stripslashes( $_POST['property-status'] ) );
    wp_set_object_terms( $post_id, $property_status, COLABS_TAX_STATUS );
    
    if('rent'==$property_status)
      update_post_meta( $post_id, 'property_price_periode', $_POST['property_price_periode'] );
    
  }
    
	// Update location
	
	if (!empty($_POST['colabs_address'])) :
		
		$latitude = colabs_clean_coordinate($_POST['colabs_geo_latitude']);
		$longitude = colabs_clean_coordinate($_POST['colabs_geo_longitude']);
		
		update_post_meta($post_id, '_colabs_geo_latitude', $latitude);
		update_post_meta($post_id, '_colabs_geo_longitude', $longitude);
		
		if ($latitude && $longitude) :
			$address = colabs_reverse_geocode($latitude, $longitude);
			
			update_post_meta($post_id, 'geo_address', $address['address']);
			update_post_meta($post_id, 'geo_country', $address['country']);
			update_post_meta($post_id, 'geo_short_address', $address['short_address']);
			update_post_meta($post_id, 'geo_short_address_country', $address['short_address_country']);
			update_post_meta($post_id, 'geo_long_address', $address['long_address']);
			
		endif;
	
	else :
	
		// They left the field blank so we assume the property is for 'anywhere'
		delete_post_meta($post_id, '_colabs_geo_latitude');
		delete_post_meta($post_id, '_colabs_geo_longitude');
		delete_post_meta($post_id, 'geo_address');
		delete_post_meta($post_id, 'geo_country');
		delete_post_meta($post_id, 'geo_short_address');
		delete_post_meta($post_id, 'geo_short_address_country');
		delete_post_meta($post_id, 'geo_long_address');
	
	endif;

}

function colabs_display_location_meta_box() {
	global $post, $key;
	
	?>
<div>	
	<?php wp_nonce_field( plugin_basename( __FILE__ ), $key . '_wpnonce', false, true ); ?>
	
	<div id="geolocation_box">
	
		<?php 
			
			$geo_address = get_post_meta($post->ID, 'geo_address', true);
			$colabs_geo_latitude = get_post_meta($post->ID, '_colabs_geo_latitude', true);
			$colabs_geo_longitude = get_post_meta($post->ID, '_colabs_geo_longitude', true);
			
			if(isset($geo_address) && $geo_address!='') {}else $geo_address = '';
			
			if ($colabs_geo_latitude && $colabs_geo_longitude) :
				$colabs_address = colabs_reverse_geocode($colabs_geo_latitude, $colabs_geo_longitude);
				$colabs_address = $colabs_address['address'];
			else :
				$colabs_address = 'Anywhere';
			endif;
			
		?>
	
		<div>
		<input type="text" class="text" name="colabs_address" id="geolocation-address" autocomplete="off" value="<?php echo $geo_address; ?>" /><label><input id="geolocation-load" type="button" class="button geolocationadd" value="<?php _e('Find', 'colabsthemes'); ?>" /></label>
		<input type="hidden" class="text" name="colabs_geo_latitude" id="geolocation-latitude" value="<?php echo $colabs_geo_latitude; ?>" />
		<input type="hidden" class="text" name="colabs_geo_longitude" id="geolocation-longitude" value="<?php echo $colabs_geo_longitude; ?>" />
		</div>

		<div id="map_wrap" style="margin-top:5px; border:solid 2px #ddd;"><div id="geolocation-map" style="width:100%;min-height:200px;" ></div></div>
	
	</div>
	
	<p><strong><?php _e('Current location:', 'colabsthemes'); ?></strong><br/><?php echo $colabs_address; ?><?php
		if ($colabs_geo_latitude && $colabs_geo_longitude) :
			echo '<br/><em>Latitude:</em> '.$colabs_geo_latitude;
			echo '<br/><em>Longitude:</em> '.$colabs_geo_longitude;
		endif;
	?></p>
</div>	
	<?php
}

add_action( 'add_meta_boxes_property', 'colabs_create_properties_meta_box' );
add_action( 'save_post', 'colabs_save_meta_box' );