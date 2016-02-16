<form id="submit_form" action="<?php echo esc_url( $form_action ) ?>" class="submit_form main_form property-submission" method="post" enctype="multipart/form-data">
  <?php
    $property_id = ( isset( $_GET['property_id'] ) ) ? $_GET['property_id'] : '';
    colabsthemes_metabox_maps_header();
    $currency_symbol = get_option( 'colabs_currency_symbol' );
    $number = array('0' => '0','1' => '1','2' => '2','3' => '3','4' => '4','5' => '5','6' => '6','7' => '7','8' => '8','9' => '9','10+' => '10+',);

    $property_type_type = 'select-tax';
    if( get_option( 'colabs_allow_multiple_property_type' ) == 'true' ) {
      $property_type_type = 'multicheck-tax';
    }

    $agents = array();
    $agentarray = get_posts( array('post_type' => 'agent', 'post_status' => 'publish'));
    foreach ($agentarray as $dataagent){
      $agents[$dataagent->ID] = $dataagent->post_title;
    }

    $options_submit_field = array (
      array(
        'field_name' => 'post_title',
        'field_label' => __('Title', 'colabsthemes'),
        'field_values' => '',
        'field_type' => 'text',
        'field_class' => 'form-row',
        'field_req' => true,
      ),
      array(
        'field_name' => 'property_type',
        'field_label' => __('Type', 'colabsthemes'),
        'field_values' => 'property_type',
        'field_type' => $property_type_type,
        'field_class' => 'form-row',
        'field_req' => true,
      ),
      array(
        'field_name' => 'property_status',
        'field_label' => __('Status', 'colabsthemes'),
        'field_values' => 'property_status',
        'field_type' => 'select-tax',
        'field_class' => 'form-row',
        'field_req' => true,
      ),
      array(
        'field_name' => 'property_price',
        'field_label' => __('Price', 'colabsthemes').' ('.$currency_symbol.')',
        'field_values' => '',
        'field_type' => 'text',
        'field_req' => true,
      ),
      /*array(
        'field_name' => 'property_price_periode',
        'field_label' => __('Price Periode', 'colabsthemes'),
        'field_values' => array(
                          'day' => __('Day','colabsthemes'),
                          'month' => __('Month','colabsthemes'),
                          'year' => __('Year','colabsthemes')
                          ),
        'field_type' => 'select',
        'field_req' => false,
      ),*/
      array(
        'field_name' => 'property_size',
        'field_label' => __('Size', 'colabsthemes').' ('.get_option('colabs_unit_measure').')',
        'field_values' => '',
        'field_type' => 'text',
        'field_req' => true,
      ),
      array(
        'field_name' => 'property_address',
        'field_label' => __('Address', 'colabsthemes'),
        'field_values' => '',
        'field_type' => 'text',
        'field_req' => false,
      ),
      array(
        'field_name' => 'property_citystate',
        'field_label' => __('City and State', 'colabsthemes'),
        'field_values' => '',
        'field_type' => 'text',
        'field_req' => false,
      ),
      array(
        'field_name' => 'post_content',
        'field_label' => __('Description', 'colabsthemes'),
        'field_values' => '',
        'field_type' => 'textarea',
        'field_class' => 'form-row',
        'field_req' => true,
      ),
    );
    colabs_form_builder($options_submit_field, $property);

    echo '<div class="row">';
      echo '<div class="column col6">';
        $options_submit_field = array(
          array(
            'field_name' => 'property_features',
            'field_label' => __('Features', 'colabsthemes'),
            'field_values' => 'property_features',
            'field_type' => 'multicheck-tax',
            'field_req' => '',
          ),
          array(
            'field_name' => 'property_beds',
            'field_label' => __('Bedrooms', 'colabsthemes'),
            'field_values' => $number,
            'field_type' => 'select',
            'field_req' => '',
          ),
          array(
            'field_name' => 'property_baths',
            'field_label' => __('Bathrooms', 'colabsthemes'),
            'field_values' => $number,
            'field_type' => 'select',
            'field_req' => '',
          ),
          array(
            'field_name' => 'property_garage',
            'field_label' => __('Garages', 'colabsthemes'),
            'field_values' => $number,
            'field_type' => 'select',
            'field_req' => '',
          ),
        );
        colabs_form_builder($options_submit_field, $property);
      echo '</div>';


      echo '<div class="column col6">';
        $options_submit_field = array(
          array(
            'field_name' => 'property_location',
            'field_label' => __('Location', 'colabsthemes'),
            'field_values' => 'property_location',
            'field_type' => 'select-tax',
            'field_req' => '',
          ),
          array(
            'field_name' => 'property_agent',
            'field_label' => __('Agent', 'colabsthemes'),
            'field_values' => $agents,
            'field_type' => 'select',
            'field_req' => '',
          ),
          array(
            'field_name' => 'property_furnished',
            'field_label' => __('Fully Furnished', 'colabsthemes'),
            'field_values' => array( 'true' => __("Available","colabsthemes"),'false' => __("Not Available","colabsthemes")),
            'field_type' => 'radio',
            'field_req' => 'false',
          ),
          array(
            'field_name' => 'property_mortgage',
            'field_label' => __('Mortgage', 'colabsthemes'),
            'field_values' => array( 'true' => __("Available","colabsthemes"),'false' => __("Not Available","colabsthemes")),
            'field_type' => 'radio',
            'field_req' => 'false',
          ),
        );
        colabs_form_builder($options_submit_field, $property);
      echo '</div>';  
    echo '</div>';

    $options_submit_field = array(
      array(
        'field_name' => '_thumbnail_id',
        'field_label' => __('Featured Image', 'colabsthemes'),
        'field_values' => '',
        'field_type' => 'upload',
        'field_req' => '',
      ),
      array(
        'field_name' => '_property_image_gallery',
        'field_label' => __('Gallery', 'colabsthemes'),
        'field_values' => '',
        'field_type' => 'gallery',
        'field_req' => '',
      ),
    );
    colabs_form_builder($options_submit_field, $property);
  ?>

  <?php 
  colabsthemes_metabox_maps_create( $property_id );
  ?>  

  <?php
	$featured_cost = get_option('colabs_cost_to_feature');
	if ($featured_cost && is_numeric($featured_cost) && $featured_cost > 0) :
    $checked = '';
    if( $property ) {
      if(is_sticky($property->ID))$checked = 'checked="checked"';
    }
		
		// Featuring is an option
		echo '<div class="featured"><h3>'.__('Feature your listing for ', 'colabsthemes').colabs_get_price($featured_cost).__('?', 'colabsthemes').'</h3>';
		
		echo '<p>'.__('Featured listings are displayed on the homepage and are also listed in all other listing pages.', 'colabsthemes').'</p>';
		
		echo '<label class="custom-checkbox"><input type="checkbox" '.$checked.' name="featureit" id="featureit" value="true"/> '.__('Yes please, feature my listing.', 'colabsthemes').'</label></div>';
		
	endif;
	?>
  
  <div class="form-builder-input input-submit">
    <input type="hidden" name="action" value="<?php echo esc_attr($post_action); ?>" />
    <input type="hidden" name="step" value="<?php echo esc_attr($step); ?>"/>
    <?php if( $property ) : ?>
      <input type="hidden" name="ID" value="<?php echo esc_attr($property->ID); ?>">
    <?php endif; ?>
    <input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">
  
    <?php wp_nonce_field('submit_property', 'nonce') ?>

    <input type="hidden" name="preview_form" />
    <?php if ( get_query_var('property_relist') ): ?>
      <input type="hidden" name="relist" value="1"/>
    <?php endif; ?>
    
    <input type="hidden" name="property_submit" value="true">
    <input type="submit" id="property_submit" class="button button-bold" value="<?php echo esc_attr( $submit_text ); ?>">
  </div>
</form>