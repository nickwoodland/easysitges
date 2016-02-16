<div id="default-search">

<?php
$is_price_panel_shown = isset( $_GET['is_price_size_shown'] ) && $_GET['is_price_size_shown'] == 'true' ? true : false;
$price_panel_form_class = $is_price_panel_shown ? 'active' : '';
?>

<form class="advance-search clearfix <?php echo $price_panel_form_class; ?>" name="property-search" id="property-search" method="get" action="<?php echo home_url('/'); ?>/">
  <div class="column col10">
		<?php
	  if (isset($_GET['s'])) { $keyword = strip_tags($_GET['s']);  } else { $keyword = '';  }
		if ( $keyword == 'Your Keywords' ) { $keyword = ''; }
	  ?>
    <div class="input-text column col5 input-search">
			<input type="text" name="s" value="<?php if ( $keyword != '' ) { echo $keyword; } ?>" placeholder="<?php _e(get_option('colabs_search_keyword_text'), 'colabsthemes'); ?>">
		</div>
    <div class="input-select column col4">
      <?php
    	//property locations drop down
    	if (isset($_GET['location_names'])) { $category_ID = $_GET['location_names']; } else { $category_ID = 0; }
      if ($category_ID <= 0) {
				$category_ID = 0;
      }
      $dropdown_options = array	(	
            								'show_option_all'	=> __(get_option('colabs_label_locations_dropdown_view_all')), 
            								'hide_empty' 			=> 0, 
            								'hierarchical' 		=> 1,
														'show_count' 			=> 0, 
														'orderby' 				=> 'name',
														'name' 						=> 'location_names',
														'id' 							=> 'location_names',
														'taxonomy' 				=> 'property_location', 
														'hide_if_empty'		=> 1,
														'selected' 				=> $category_ID
														);
			wp_dropdown_categories($dropdown_options);
    	?>
    </div>
    <div class="input-select column col3">
      <?php
    	//property types drop down
    	if (isset($_GET['property_types'])) { $category_ID = $_GET['property_types']; } else { $category_ID = 0; }
      if ($category_ID <= 0) {
        $category_ID = 0;
      }
      $dropdown_options = array	(	
            								'show_option_all'	=> __(get_option('colabs_label_property_type_dropdown_view_all')), 
            								'hide_empty' 			=> 0, 
            								'hierarchical' 		=> 1,
														'show_count' 			=> 0, 
														'orderby' 				=> 'name',
														'name' 						=> 'property_types',
														'id' 							=> 'property_types',
														'taxonomy' 				=> 'property_type', 
														'hide_if_empty'		=> 1,
														'selected' 				=> $category_ID,
														'class'						=> 'last'
														);
			wp_dropdown_categories($dropdown_options);?>
    </div>
    <div class="column col5 alpha">
      <?php
    	//property status drop down
    	if (isset($_GET['property_status_id'])) { $category_ID = $_GET['property_status_id']; } else { $category_ID = 0; }
      if ($category_ID <= 0) {
        $category_ID = 0;
      }
      $dropdown_options = array	(	
            								'show_option_all'	=> __(get_option('colabs_label_property_status_dropdown_view_all')), 
            								'hide_empty' 			=> 0, 
            								'hierarchical' 		=> 1,
														'show_count' 			=> 0, 
														'orderby' 				=> 'name',
														'name' 						=> 'property_status_id',
														'id' 							=> 'property_status_id',
														'taxonomy' 				=> 'property_status', 
														'hide_if_empty'		=> 1,
														'selected' 				=> $category_ID,
														'class'						=> 'last'
														);
			wp_dropdown_categories($dropdown_options);?>
    </div>
    <div class="input-select additional-field column col7">
			<?php if (isset($_GET['no_garages'])) { $no_garages = $_GET['no_garages'];  } else { $no_garages = 'all';  } ?>
			<?php if (isset($_GET['no_beds'])) { $no_beds = $_GET['no_beds'];  } else { $no_beds = 'all';  }  ?>
			<?php if (isset($_GET['no_baths'])) { $no_baths = $_GET['no_baths'];  } else { $no_baths = 'all';  }  ?>
			<?php $options_features_amount = array("0","1","2","3","4","5","6","7","8","9","10+"); ?>
			<select class="postform" id="no_garages" name="no_garages">
				<option <?php if ($no_garages == 'all') { ?>selected="selected"<?php }?> value="all"><?php _e(get_option('colabs_label_garages'), 'colabsthemes'); ?></option>
				<?php foreach ($options_features_amount as $option) {?>
					<option <?php if ($no_garages == $option) { ?>selected="selected"<?php }?> value="<?php echo $option; ?>"><?php echo $option; ?></option>
				<?php }?>
			</select>
						
			<select class="postform" id="no_beds" name="no_beds">
				<option <?php if ($no_beds == 'all') { ?>selected="selected"<?php }?> value="all"><?php _e(get_option('colabs_label_beds'), 'colabsthemes'); ?></option>
				<?php foreach ($options_features_amount as $option) {?>
					<option <?php if ($no_beds == $option) { ?>selected="selected"<?php }?> value="<?php echo $option; ?>"><?php echo $option; ?></option>
				<?php }?>
			</select>
						
			<select class="postform last" id="no_baths" name="no_baths">
				<option <?php if ($no_baths == 'all') { ?>selected="selected"<?php }?> value="all"><?php _e(get_option('colabs_label_baths_long'), 'colabsthemes'); ?></option>
				<?php foreach ($options_features_amount as $option) {?>
					<option <?php if ($no_baths == $option) { ?>selected="selected"<?php }?> value="<?php echo $option; ?>"><?php echo $option; ?></option>
				<?php }?>
			</select>	
    </div>
		<div class="clear"></div>

    <div class="advance-search-button">
      <a href="#show" class="show button button-bold button-orange"><?php echo get_option('colabs_label_advanced_search');?></a>
      <a href="#hide" class="hide button button-bold button-grey"><?php echo get_option('colabs_label_hide_advanced_search');?></a>
    </div>
		<div class="advance-search-extra clearfix">
			<?php
			if (isset($_GET['price_min'])) { $price_min = $_GET['price_min'];  } else { $price_min = '';  }
			if (isset($_GET['price_max'])) { $price_max = $_GET['price_max'];  } else { $price_max = '';  }
			$max_price_properties = get_option('colabs_property_max');
			if($max_price_properties == '')$max_price_properties = 700000;
			?>
      <div class="input-slider column col6">
        <div class="price-slider-wrapper">
          <div class="price-slider-label">
            <span class="label-text"><?php _e(get_option('colabs_label_price'), 'colabsthemes'); ?> <?php echo '('.get_option('colabs_currency_symbol').')'; ?>:</span> <span class="from"></span> &mdash; <span class="to"></span>
          </div>
          <div class="price-slider-bg"><div class="price-slider"></div></div>
          <div class="price-slider-amount">
            <input type="text" class="min-price" id="min_price" name="price_min" data-min="0" placeholder="<?php _e('Min Price','colabsthemes');?>" value="<?php if ( $price_min != '' ) { echo $price_min; } ?>">
            <input type="text" class="max-price" id="max_price" name="price_max" data-max="<?php echo $max_price_properties;?>" placeholder="<?php _e('Max Price','colabsthemes');?>" value="<?php if ( $price_max != '' ) { echo $price_max; } ?>">
          </div>
        </div>
      </div>
			<div class="input-slider column col6 property-size">
			<?php
			if (isset($_GET['size_min'])) { $size_min = $_GET['size_min'];  } else { $size_min = '';  }
			if (isset($_GET['size_max'])) { $size_max = $_GET['size_max'];  } else { $size_max = '';  }
			$max_size_properties = get_option('colabs_property_size');
			if($max_size_properties == '')$max_size_properties = 9999;
			?>
        <div class="price-slider-wrapper">
          <div class="price-slider-label">
            <span class="label-text"><?php _e(get_option('colabs_label_size'), 'colabsthemes'); ?> <?php echo '('.get_option('colabs_unit_measure').')'; ?>:</span> <span class="from"></span> &mdash; <span class="to"></span>
          </div>
          <div class="price-slider-bg"><div class="price-slider"></div></div>
          <div class="price-slider-amount">
            <input type="text" class="min-price" id="size_min" name="size_min" data-min="0" placeholder="<?php _e('Min Size','colabsthemes');?>" value="<?php if ( $size_min != '' ) { echo $size_min; } ?>">
            <input type="text" class="max-price" id="size_max" name="size_max" data-max="<?php echo $max_size_properties;?>" placeholder="<?php _e('Max Size','colabsthemes');?>" value="<?php if ( $size_max != '' ) { echo $size_max; } ?>">
          </div>
        </div>
      </div>
    </div><!-- .advance-search-extra -->
  </div>

  <div class="column col2">
    <input type="submit" value="<?php _e('Search','colabsthemes');?>" name="property-search-submit" class="button button-bold">
  </div>

  <input class="price-panel-identifier" type="hidden" name="is_price_size_shown" value="<?php echo $is_price_panel_shown ? 'true' : 'false'; ?>">

  <?php if( !isset( $_GET['s'] ) ) : ?>
    <input type="hidden" name="propertyorder" value="<?php echo isset( $_GET['propertyorder'] ) ? $_GET['propertyorder'] : '';?>">
  <?php endif; ?>
</form>
</div><!-- #default-search -->

<?php 
if( defined('DSIDXPRESS_OPTION_NAME') ) {
  $idx_options = get_option(DSIDXPRESS_OPTION_NAME);
  if ( $idx_options['Activated'] && ( get_option('colabs_idx_plugin_search') == 'true' ) ) {
  			
  	$pluginUrl = DSIDXPRESS_PLUGIN_URL;

  	$formAction = home_url('/');
  	if (substr($formAction, strlen($formAction), 1) != "/")
  	$formAction .= "/";
  	$formAction .= dsSearchAgent_Rewrite::GetUrlSlug();
  				
  	?>
  	
  	<div class="dsidx-search" id="idx-search" style="display: none">
    <form name="property-mls-search" id="property-mls-search" method="get" action="<?php echo $formAction; ?>">
      			
      <?php	

  		$defaultSearchPanels = dsSearchAgent_ApiRequest::FetchData("AccountSearchPanelsDefault", array(), false, 60 * 60 * 24);
  		$defaultSearchPanels = $defaultSearchPanels["response"]["code"] == "200" ? json_decode($defaultSearchPanels["body"]) : null;

  		$propertyTypes = dsSearchAgent_ApiRequest::FetchData("AccountSearchSetupPropertyTypes", array(), false, 60 * 60 * 24);
  		$propertyTypes = $propertyTypes["response"]["code"] == "200" ? json_decode($propertyTypes["body"]) : null;

  		$requestUri = dsSearchAgent_ApiRequest::$ApiEndPoint . "LocationsByType";
  		//cities
  		$location_cities = explode("\n", get_option('colabs_idx_search_cities'));
  		//communities
  		$location_communities = explode("\n", get_option('colabs_idx_search_communities'));
  		//Tracts
  		$location_tracts = explode("\n", get_option('colabs_idx_search_tracts'));
  		//Zips
  		$location_zips = explode("\n", get_option('colabs_idx_search_zips'));
  		?>
  		
  		<div class="mls-property-type">
      			
  			<label for="idx-q-PropertyTypes"><?php _e('Property Type', 'colabsthemes'); ?>:</label>
  			<select name="idx-q-PropertyTypes" class="dsidx-search-widget-propertyTypes">
  					<option value="All"><?php _e('- All property types -','colabsthemes');?></option>
  					<?php
  					if (is_array($propertyTypes)) {
  						foreach ($propertyTypes as $propertyType) {
  							$name = htmlentities($propertyType->DisplayName);
  							echo "<option value=\"{$propertyType->SearchSetupPropertyTypeID}\">{$name}</option>";
  						}
  					}
  					?>
  			</select>	
  			
  			<label for="idx-q-MlsNumbers"><?php _e('MLS #', 'colabsthemes'); ?>:</label>
  			<input id="idx-q-MlsNumbers" name="idx-q-MlsNumbers" type="text" class="text" />
  			
        <label for="idx-q-Cities"><?php _e('City', 'colabsthemes'); ?>:</label>
  				<select id="idx-q-Cities" name="idx-q-Cities" class="small">
  					<option value=""><?php _e('- Any -','colabsthemes');?></option>
  					<?php if (is_array($location_cities)) {
  						foreach ($location_cities as $city) {
  							$city_name = htmlentities(trim($city));
  							echo "<option value=\"{$city_name}\">$city_name</option>";
  						}
  					} ?>
  				</select>
        
        <label for="idx-q-Communities"><?php _e('Community', 'colabsthemes'); ?>:</label>
  				<select id="idx-q-Communities" name="idx-q-Communities" class="small">
  					<option value=""><?php _e('- Any -','colabsthemes');?></option>
  					<?php if (is_array($location_communities)) {
  						foreach ($location_communities as $community) {
  							$community_name = htmlentities(trim($community));
  							echo "<option value=\"{$community_name}\">$community_name</option>";
  						}
  					} ?>
  				</select>  
  		</div>
  			
  		<div class="mls-area-details">
  		
  			<label for="idx-q-TractIdentifiers"><?php _e('Tract', 'colabsthemes'); ?>:</label>
  				<select id="idx-q-TractIdentifiers" name="idx-q-TractIdentifiers" class="small">
  					<option value=""><?php _e('- Any -','colabsthemes');?></option>
  					<?php if (is_array($location_tracts)) {
  						foreach ($location_tracts as $tract) {
  							$tract_name = htmlentities(trim($tract));
  							echo "<option value=\"{$tract_name}\">$tract_name</option>";
  						}
  					} ?>
  				</select>
  			
  			<label for="idx-q-ZipCodes"><?php _e('Zip', 'colabsthemes'); ?>:</label>
  				<select id="idx-q-ZipCodes" name="idx-q-ZipCodes" class="small">
  					<option value=""><?php _e('- Any -','colabsthemes');?></option>
  					<?php if (is_array($location_zips)) {
  						foreach ($location_zips as $zip) {
  							$zip_name = htmlentities(trim($zip));
  							echo "<option value=\"{$zip_name}\">$zip_name</option>";
  						}
  					} ?>
  				</select>
  			
        <label for="idx-q-BathsMin"><?php _e('Baths', 'colabsthemes'); ?>:</label>
  				<input id="idx-q-BathsMin" name="idx-q-BathsMin" type="text" class="text validate_number" />
        
        <label for="idx-q-BedsMin"><?php _e('Beds', 'colabsthemes'); ?>:</label>
  				<input id="idx-q-BedsMin" name="idx-q-BedsMin" type="text" class="text validate_number" />
          
  		</div>
  				
  		<div class="mls-features">
  					
  			<label for="idx-q-PriceMin"><?php _e('Min Price', 'colabsthemes'); ?>:</label>
  				<input id="idx-q-PriceMin" name="idx-q-PriceMin" type="text" class="text validate_number" />
  			
  			<label for="idx-q-PriceMax"><?php _e('Max Price', 'colabsthemes'); ?>:</label>
  				<input id="idx-q-PriceMax" name="idx-q-PriceMax" type="text" class="text validate_number" />
  			
  			<label for="idx-q-ImprovedSqFtMin"><?php _e('Min Size', 'colabsthemes'); ?> <?php echo '(SQ FT)'; ?>:</label>
  				<input id="idx-q-ImprovedSqFtMin" name="idx-q-ImprovedSqFtMin" type="text" class="text validate_number" />
  			       
        <input type="submit" value="<?php _e('Search','colabsthemes');?>" class="submit button" />  
  						
  		</div>
	
  		<?php
  		if($options["HasSearchAgentPro"] == "yes"){
  			echo 'try our&nbsp;<a href="'.$formAction.'advanced/"><img src="'.$pluginUrl.'assets/adv_search-16.png" /> Advanced Search</a>';
  		}
  		?>      
    </form>
  	</div><!-- .dsidx-search -->
<?php } 
} //endif defined 'DSIDXPRESS_OPTION_NAME' ?>				
    	