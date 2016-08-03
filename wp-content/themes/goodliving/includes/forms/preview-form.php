<form action="<?php echo esc_url( $form_action ) ?>" method="post" enctype="multipart/form-data" id="submit_form" class="submit_form main_form">
  <?php wp_nonce_field('submit_property', 'nonce') ?>
  <h5><?php _e('Below is a preview of what your listing will look like when published:', 'colabsthemes'); ?></h5>

  <p><?php _e('The listings page will contain this following information:','colabsthemes');?></p>
  
  <?php
  $id = $property->ID;
  $address = get_post_meta($id,'colabs_maps_address',true);
  $beds = get_post_meta($id, 'property_beds',true);
  $baths = get_post_meta($id, 'property_baths',true);
  $size = get_post_meta($id, 'property_size',true);
  $garage = get_post_meta($id, 'property_garage',true);
  $furnished= get_post_meta($id, 'property_furnished',true);
  $mortgage= get_post_meta($id, 'property_mortgage',true);
  $maps_active = get_post_meta($id,'colabs_maps_enable',true);
  ?>

  <article <?php post_class('single-entry-post');?>>
    <header class="entry-header">
      <h1 class="entry-title"><?php echo get_the_title($property->ID);?></h1>
    </header>

    <div class="property-info">
      <ul class="property-info-tabs clearfix">
        <li><a href="#property-gallery"><i class="icon-camera"></i> <?php _e('Gallery','colabsthemes');?></a></li>
        
        <?php if($maps_active == 'on') : ?>
          <li><a href="#property-maps"><i class="icon-map-marker"></i> <?php _e('Google Maps','colabsthemes');?></a></li>
        <?php endif; ?>

        <li><a href="#property-facilities"><i class="icon-tasks"></i> <?php _e('Facilities','colabsthemes');?></a></li>
      </ul>

      <div class="property-info-panel" id="property-gallery">
        <div class="property-gallery-large">
          <?php colabs_image('width=600&link=img&id='.$property->ID);?>
        </div>
      </div>
  
      <?php if($maps_active == 'on') : ?>
        <div class="property-info-panel" id="property-maps">
          <?php
            $mode = get_post_meta($id,'colabs_maps_mode',true);
            $streetview = get_post_meta($id,'colabs_maps_streetview',true);
            $address = get_post_meta($id,'colabs_maps_address',true);
            $long = get_post_meta($id,'colabs_maps_long',true);
            $lat = get_post_meta($id,'colabs_maps_lat',true);
            $pov = get_post_meta($id,'colabs_maps_pov',true);
            $from = get_post_meta($id,'colabs_maps_from',true);
            $to = get_post_meta($id,'colabs_maps_to',true);
            $zoom = get_post_meta($id,'colabs_maps_zoom',true);
            $type = get_post_meta($id,'colabs_maps_type',true);
            $yaw = get_post_meta($id,'colabs_maps_pov_yaw',true);
            $pitch = get_post_meta($id,'colabs_maps_pov_pitch',true);
                        
            if(!empty($lat) OR !empty($from)){
              colabs_maps_single_output("mode=$mode&streetview=$streetview&address=$address&long=$long&lat=$lat&pov=$pov&from=$from&to=$to&zoom=$zoom&type=$type&yaw=$yaw&pitch=$pitch"); 
            }
          ?>
        </div>
      <?php endif; ?>

      <div class="property-info-panel" id="property-facilities">
        <?php 
           echo '<ul class="property-facility">'; 
            if($size!='') echo '<li class="prop-size"><span>'.$size.' '.__("sq ft","colabsthemes").'</span></li>';
            if($beds!='') echo '<li class="prop-beds"><span>'.$beds.' '.__("beds","colabsthemes").'</span></li>';
            if($baths!='') echo '<li class="prop-baths"><span>'.$baths.' '.__("baths","colabsthemes").'</span></li>';
            if($garage!='') echo '<li class="prop-garage"><span>'.$garage.' '.__("garage","colabsthemes").'</span></li>';
            if($mortgage=='true') echo '<li class="prop-mortgage"><span>'.__("Mortgage","colabsthemes").'</span></li>';
            if($furnished=='true') echo '<li class="prop-furnished"><span>'.__("Furnished","colabsthemes").'</span></li>';
           echo '</ul>';
        ?>
        <div class="property-facilities-info">
          <?php echo get_the_term_list($id, 'property_features', '<div class="entry-features">'.__("Features","colabsthemes").' : ', ', ','</div>');   ?>
          <?php echo get_the_term_list($id, 'property_type', '<div class="entry-type">'.__("Type","colabsthemes").' : ', ', ','</div>');   ?>
          <?php echo get_the_term_list($id, 'property_location', '<div class="entry-location">'.__("Location","colabsthemes").' : ', ', ','</div>');   ?>
          <?php echo get_the_term_list($id, 'property_status', '<div class="entry-status">'.__("Status","colabsthemes").' : ', ', ','</div>');   ?>
        </div>
      </div>
    </div>
  <!-- </article> -->
  
  <div class="form-builder-input input-submit">
    <input type="hidden" name="action" value="<?php echo esc_attr($post_action); ?>" />
		<input type="hidden" name="ID" value="<?php echo esc_attr($property->ID); ?>">
		<input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">
		<input type="hidden" name="step" value="<?php echo esc_attr($step); ?>"/>
    <input type="submit" class="button button-bold button-uppercase button-grey button-wide" name="goback" value="<?php esc_attr_e( 'Go Back','colabsthemes' ); ?>"  />
    <input type="submit" name="preview_submit" class="button button-bold button-uppercase button-primary button-wide" value="<?php _e('Next','colabsthemes');?>">
  </div>
</form>