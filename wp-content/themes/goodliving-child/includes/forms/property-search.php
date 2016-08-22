<div id="title-search" class="title-search">
  <h2>Your property search starts here</h2>
  <h4>Browse the EasySitges portfolio of exceptional properties in Sitges, Barcelona and Catalunya</h4>
</div>
<div id="default-search">


<?php
$is_price_panel_shown = isset( $_GET['is_price_size_shown'] ) && $_GET['is_price_size_shown'] == 'true' ? true : false;
$price_panel_form_class = $is_price_panel_shown ? 'active' : '';
?>

<form class="advance-search clearfix <?php echo $price_panel_form_class; ?>" name="property-search" id="property-search" method="get" action="<?php echo home_url('/'); ?>/">
  <div class="column col9">

    <div class="input-select column col4">
      <?php
      // -- property types drop down --
      if (isset($_GET['property_types'])) { $category_ID = $_GET['property_types']; } else { $category_ID = 0; }
      if ($category_ID <= 0) {
        $category_ID = 0;
      }
      $dropdown_options = array	(
                            'show_option_all'	=> __(get_option('colabs_label_property_type_dropdown_view_all')),
                            // 'show_option_all'	=> 'All Property Types',
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

    <div class="input-select column col4">
      <?php
    	// -- property locations drop down --
    	if (isset($_GET['location_names'])) { $category_ID = $_GET['location_names']; } else { $category_ID = 0; }
      if ($category_ID <= 0) {
				$category_ID = 0;
      }
      $dropdown_options = array	(
            								'show_option_all'	=> __(get_option('colabs_label_locations_dropdown_view_all')),
                            // 'show_option_all'	=> 'All Locations',
            								'hide_empty' 			=> 0,
            								'hierarchical' 		=> 1,
														'show_count' 			=> 0,
														'orderby' 				=> 'name',
														'name' 						=> 'location_names',
														'id' 							=> 'location_names',
														'taxonomy' 				=> 'property_location',
                                                        'parent'    => 0,
														'hide_if_empty'		=> 1,
														'selected' 				=> $category_ID
														);
			wp_dropdown_categories($dropdown_options);
    	?>
    </div>


    <?php
    /*
    Note: These dropdowns are updated in JS script:
    /wp-content/themes/goodliving/includes/js/pricerange-changer.js
    when user changes property_types drop-down
    */
    // -- ranges for price drop downs --
    $min_sale_price_options = array("50000","100000","200000","300000","400000","500000","600000","700000","800000","900000","1000000","2000000","3000000","4000000","5000000");
    $max_sale_price_options = array("100000","200000","300000","400000","500000","600000","700000","800000","900000","1000000","2000000","3000000","4000000","5000000","10000000","50000000","100000000");

    $min_longterm_rent_monthly_options = array("250","500","750","1000","1250","1500","2000","2500","3000","4000","5000");
    $max_longterm_rent_monthly_options = array("500","750","1000","1250","1500","2000","2500","3000","4000","5000","10000");

    $min_holiday_rent_daily_options = array("50","100","200","300","400","500","600","700","800","900");
    $max_holiday_rent_daily_options = array("100","200","300","400","500","600","700","800","900","1000");

    $rental_ids = array("142", "23", "22");

    $lowest_price = '1';
    $highest_price = '999999999';
    $price_min = isset($_GET['price_min']) ? $_GET['price_min'] : $lowest_price;
    $price_max = isset($_GET['price_max']) ? $_GET['price_max'] : $highest_price;

    $min_default_text = 'Min Price';
    $max_default_text = 'Max Price';
    $min_options = $min_sale_price_options;
    $max_options = $max_sale_price_options;

    if (in_array($_GET['property_types'], $rental_ids)) {
      $min_default_text = 'Min Rent';
      $max_default_text = 'Max Rent';
      $max_options = $max_longterm_rent_monthly_options;
      $min_options = $min_longterm_rent_monthly_options;
    }
    ?>
    <!-- min price drop down -->
    <div class="input-select column col2" style="margin-left:2%; width:15%">
      <select class="postform" id="price_min" name="price_min">
        <option <?php if ($price_min == $lowest_price) { ?>selected="selected"<?php }?> value="<?php echo $price_min ?>"><?php echo $min_default_text ?></option>
        <?php foreach ($min_options as $option) {
          $currency_option = number_format($option);
        ?>
          <option <?php if ($price_min == $option) { ?>selected="selected"<?php }?> value="<?php echo $option; ?>">&euro;<?php echo $currency_option; ?></option>
        <?php }?>
      </select>
    </div>

    <!-- max price drop down -->
    <div class="input-select column col2" style="margin-left:2%; width:15%">
      <select class="postform" id="price_max" name="price_max">
        <option <?php if ($price_max == $highest_price) { ?>selected="selected"<?php }?> value="<?php echo $price_max ?>"><?php echo $max_default_text ?></option>
        <?php foreach ($max_options as $option) {
          $currency_option = number_format($option);
        ?>
          <option <?php if ($price_max == $option) { ?>selected="selected"<?php }?> value="<?php echo $option; ?>">&euro;<?php echo $currency_option; ?></option>
        <?php }?>
      </select>
    </div>

		<div class="clear"></div>
		<div class="clearfix">

    </div>
</div>

  <div class="column col3">

    <input type="submit" value="<?php _e('Search','colabsthemes');?>" name="property-search-submit" class="button button-bold">
  </div>

  <input class="price-panel-identifier" type="hidden" name="is_price_size_shown" value="<?php echo $is_price_panel_shown ? 'true' : 'false'; ?>">

  <?php if( !isset( $_GET['s'] ) ) : ?>
    <input type="hidden" name="propertyorder" value="<?php echo isset( $_GET['propertyorder'] ) ? $_GET['propertyorder'] : '';?>">
  <?php endif; ?>
  <input type="hidden" name="s" value="<?php if ( $keyword != '' ) { echo $keyword; } ?>" placeholder="<?php _e(get_option('colabs_search_keyword_text'), 'colabsthemes'); ?>">
  <input type="hidden" name="size_min" value="0">
  <input type="hidden" name="size_max" value="99999">
</form>
</div><!-- #default-search -->

<hr>
