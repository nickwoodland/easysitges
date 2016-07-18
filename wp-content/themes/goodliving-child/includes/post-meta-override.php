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

global $property_details_child;
$property_details_child = array();

/*$property_details_child[] = array (  "name"  => $key."_label",
					            "std"  => "",
					            "label" => __("Hover Title","colabsthemes"),
					            "type" => "text",
					            "desc" => __("Enter the text for image hover","colabsthemes"));*/
$property_details_child[] = array (  "name"  => $key."_short_desc",
					            "std"  => "",
					            "label" => __("Short Desc","colabsthemes"),
					            "type" => "text",
					            "desc" => __("Brief Description of Property","colabsthemes"));

$property_details_child[] = array (  "name"  => $key."_short_desc_bullet_1",
					            "std"  => "",
					            "label" => __("Bullet Point","colabsthemes"),
					            "type" => "text",
					            "desc" => __("Bullet Point that will be included in short description of property","colabsthemes"));

$property_details_child[] = array (  "name"  => $key."_short_desc_bullet_2",
					            "std"  => "",
					            "label" => __("Bullet Point","colabsthemes"),
					            "type" => "text",
					            "desc" => __("Bullet Point that will be included in short description of property","colabsthemes"));

$property_details_child[] = array (  "name"  => $key."_keys",
					            "std"  => "false",
                                "label" => __("Keys","colabsthemes"),
					            "type" => "radio",
                                "options" => array(	"false" => "No","true" => "Yes"),
					            "desc" => __("Do we hold keys for this property?","colabsthemes"));

$property_details_child[] = array (  "name"  => $key."_address",
					            "std"  => "",
					            "label" => __("Address","colabsthemes"),
					            "type" => "textarea",
					            "desc" => __("Enter the address for the listing. For example, 1223 Main Street","colabsthemes"));

$property_details_child[] = array (  "name"  => $key."_citystate",
					            "std"  => "",
					            "label" => __("City and State","colabsthemes"),
					            "type" => "text",
					            "desc" => __("Enter the City and State in one line.  You can include the zip/postal code, but we think it looks better without.  This will <strong>NOT</strong> be used as search criteria. ","colabsthemes"));

$property_details_child[] = array (  "name"  => $key."_agent",
					            "label" => __("Choose an Agent","colabsthemes"),
					            "type" => "select2",
											"options" => $colabs_agents,
					            "desc" => __("If this property has a specific agent, choose one. Leave blank if there is not a specific agent for the property. If you leave it blank, then the property details contact form will not be specific to a single agent. (You create Agents by going to Agent -> Add New Agent)","colabsthemes"));

$property_details_child[] = array (  "name"  => $key."_price",
					            "std"  => "",
					            "label" => __("Price","colabsthemes"),
					            "type" => "text",
					            "desc" => __("How much is this property? Enter '0' if you want 'Price on Request' labeled instead of the price.","colabsthemes"));

$property_details_child[] = array (  "name"  => $key."_beds",
					            "std"  => "",
					            "label" => __("Bedrooms","colabsthemes"),
					            "type" => "select",
                      			"options" => $options_features_amount,
					            "desc" => __("How many bedrooms does this property have? This is used when visitors search by number of bedrooms.","colabsthemes"));

$property_details_child[] = array (  "name"  => $key."_baths",
					            "std"  => "",
					            "label" => __("Bathrooms","colabsthemes"),
					            "type" => "select",
                      			"options" => $options_features_amount,
					            "desc" => __("How many bathrooms does this property have? This is used when visitors search by number of bathrooms.","colabsthemes"));

$property_details_child[] = array (  "name"  => $key."_size",
					            "std"  => "",
					            "label" => __("Internal Area","colabsthemes"),
					            "type" => "text",
					            "desc" => __("The home/building size.  For example: 5255 sq ft.","colabsthemes"));

$property_details_child[] = array (  "name"  => $key."_size_external",
					            "std"  => "",
					            "label" => __("External Area","colabsthemes"),
					            "type" => "text",
					            "desc" => __("The home/building size.  For example: 5255 sq ft.","colabsthemes"));

$property_details_child[] = array (  "name"  => $key."_garage",
					            "std"  => "",
					            "label" => __("Garages","colabsthemes"),
					            "type" => "select",
                      			"options" => $options_features_amount,
					            "desc" => __("How many garage is this property? (Leave blank to ignore).","colabsthemes"));

$property_details_child[] = array (  "name"  => $key."_furnished",
					            "std"  => "false",
                      			"label" => __("Fully Furnished","colabsthemes"),
					            "type" => "radio",
                      			"options" => array(	"false" => "No Availabe","true" => "Availabe"),
					            "desc" => __("Are this property is fully furnished?","colabsthemes"));
$property_details_child[] = array (  "name"  => $key."_plots",
					            "std"  => "",
					            "label" => __("Plots","colabsthemes"),
					            "type" => "select",
                      			"options" => $options_features_amount,
					            "desc" => __("Number of Plots (only used for land).","colabsthemes"));
$property_details_child[] = array (  "name"  => $key."_aspect",
					            "std"  => "",
					            "label" => __("Aspect","colabsthemes"),
					            "type" => "text",
					            "desc" => __("Aspect, or facing. For example, North or North East. (only used for land)","colabsthemes"));

/*$property_details_child[] = array (  "name"  => $key."_mortgage",
					            "std"  => "false",
                      			"label" => __("Mortgage","colabsthemes"),
					            "type" => "radio",
                      			"options" => array(	"false" => "No Availabe","true" => "Availabe"),
					            "desc" => __("Are this property is mortgage?","colabsthemes"));*/

// $property_details_child[] = array (  "name"  => "expires",
// 					            "std"  => "",
// 											"label" => __("Valid Date","colabsthemes"),
// 					            "type" => "calendar",
// 					            "desc" => __("","colabsthemes"));

/*$property_details_child[] = array (  "name"  => "_colabs_property_duration",
					            "std"  => "",
                      			"label" => __("Property Duration","colabsthemes"),
					            "type" => "text",
					            "desc" => __("days","colabsthemes"));*/

function colabs_child_create_properties_meta_box() {
	if( function_exists( 'add_meta_box' ) ) add_meta_box( 'new-meta-boxes-child', __('Property Details', 'colabsthemes'), 'colabs_child_display_meta_box', COLABS_POST_TYPE, 'normal', 'high' );
  // if( function_exists( 'add_meta_box' ) ) add_meta_box( 'location-meta-boxes', __('Property Location', 'colabsthemes'), 'colabs_display_location_meta_box', COLABS_POST_TYPE, 'side', 'high' );
  //if( function_exists( 'add_meta_box' ) ) add_meta_box( 'gallery-meta-boxes', __('Property Gallery', 'colabsthemes'), 'colabs_display_gallery_meta_box', COLABS_POST_TYPE, 'side', 'high' );
}

function colabs_child_display_meta_box($post) {
	global $post, $property_details_child, $key;
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
			<th class="colabs_metabox_names"><label for="property-unique-key"><?php _e( 'Property Unique Key', 'colabsthemes' )?></label></th>
			<td><input class="colabs_input_text " value="<?php echo get_post_meta($post->ID,'property_unique_key',true); ?>" name="property_unique_key" id="colabsthemes_property_unique_key" type="text"><span class="colabs_metabox_desc description"></span></td>
		  </tr>
	  </table>
      <table class="colabs_metaboxes_table">
        <tbody>
        <tr style="border-top:1px solid #DDDDDD;">
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
            <span class="colabs_metabox_desc description"><?php _e( 'Property price periode only for rent status.', 'colabsthemes' )?></span>
		  </td>
        </tr>
		<tr class="rent" style="display:none;">
			<th class="colabs_metabox_names"><label for='price_day_low'>Daily Rent - Low</label></th>
			<td><input class="colabs_input_text " value="<?php echo get_post_meta($post->ID,'property_price_day_low',true); ?>" name="property_price_day_low" id="colabsthemes_property_price_day_low" type="text"><span class="colabs_metabox_desc description"></span></td>
		</tr>
		<tr class="rent" style="display:none;">
			<th class="colabs_metabox_names"><label for='price_day_med'>Daily Rent - Medium</label></th>
			<td><input class="colabs_input_text " value="<?php echo get_post_meta($post->ID,'property_price_day_med',true); ?>" name="property_price_day_med" id="colabsthemes_property_price_day_med" type="text"><span class="colabs_metabox_desc description"></span></td>
		</tr>
		<tr class="rent" style="display:none;">
			<th class="colabs_metabox_names"><label for='price_day_high'>Daily Rent - High</label></th>
			<td><input class="colabs_input_text " value="<?php echo get_post_meta($post->ID,'property_price_day_high',true); ?>" name="property_price_day_high" id="colabsthemes_property_price_day_high" type="text"><span class="colabs_metabox_desc description"></span></td>
		</tr>
        </tbody>
      </table>
      <?php echo colabs_custom_meta_generator($post,$property_details_child);?>

    </div>
  </div>
	<?php
}

function colabs_child_save_meta_box( $post_id ) {
	global $post, $property_details_child, $key;

	//if ( !isset($_POST[ $key . '_wpnonce' ] ) ) return $post_id;
	//if ( !wp_verify_nonce( $_POST[ $key . '_wpnonce' ], 'colabs_property_details_nounce' ) ) return $post_id;

	if ( !current_user_can( 'edit_post', $post_id )) return $post_id;

	if (( isset( $_POST['action'] ) && 'editpost' == $_POST['action'] )&&( COLABS_POST_TYPE == $post->post_type)) {
    colabs_custom_meta_save_handler($post_id,$property_details_child);
    $attachment_ids = array_filter( explode( ',', sanitize_text_field( $_POST['property_image_gallery'] ) ) );

    update_post_meta( $post_id, '_property_image_gallery', implode( ',', $attachment_ids ) );
	update_post_meta( $post_id, 'property_unique_key', $_POST['property_unique_key'] );

    $property_status = empty( $_POST['property-status'] ) ? 'sale' : sanitize_title( stripslashes( $_POST['property-status'] ) );
    wp_set_object_terms( $post_id, $property_status, COLABS_TAX_STATUS );

    if('rent'==$property_status) {
      update_post_meta( $post_id, 'property_price_periode', $_POST['property_price_periode'] );
	  update_post_meta( $post_id, 'property_price_day_low', $_POST['property_price_day_low'] );
	  update_post_meta( $post_id, 'property_price_day_med', $_POST['property_price_day_med'] );
	  update_post_meta( $post_id, 'property_price_day_high', $_POST['property_price_day_high'] );
  	}

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
?>
<?php
function remove_my_meta_boxes() {
    remove_meta_box( 'new-meta-boxes',  COLABS_POST_TYPE, 'normal');
}
add_action( 'do_meta_boxes' , 'remove_my_meta_boxes', 40 );
add_action( 'add_meta_boxes_property', 'colabs_child_create_properties_meta_box' );

remove_action( 'save_post', 'colabs_save_meta_box' );
add_action( 'save_post', 'colabs_child_save_meta_box' );
