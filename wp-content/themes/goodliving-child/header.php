<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 ie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8 ie7" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9 ie8" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
	<title><?php colabs_title(); ?></title>

	<!--[if lt IE 9]>
		<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/includes/js/html5shiv.js"></script>
	<![endif]-->

	<?php
		$site_title = get_bloginfo( 'name' );
		$site_url = home_url( '/' );
		$site_description = get_bloginfo( 'description' );
		$hero_img = get_background_image();
		wp_head();
	?>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
</head>

<body <?php body_class(); ?>>
<div class="main-wrapper">
<div class="navbar container">
  <div class="row navbar--inner">
	<?php /*<a href="<?php echo get_permalink(get_option('colabs_submit_url'));?>" class="button button-green button-bold submit-listing"><i class="icon-cloud-download"></i> <?php _e('Submit Listing','colabsthemes');?></a>*/ ?>
    <a class="btn-navbar" href="#top-slide-menu">
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </a>

  	<div class="nav-collapse">
	    <nav class="top-menu clearfix">
				<?php wp_nav_menu( array(
							'theme_location' => 'top-menu',
							'container_class' => '',
							'menu_class' => 'menu'
				) ); ?>
	    </nav><!-- .top-menu -->
   	</div><!-- .nav-collapse -->

	<div class="logo-wrapper">
	  <h1 class="logo">
		  <a href="<?php echo home_url('/'); ?>">
			<?php
					if ( 'logo' == get_option('colabs_logotitle') ) {
						echo '<img src="' . get_option('colabs_logo') . '" alt="' . $site_title . '" />';
					} else {
						echo $site_title;
					}
			?>
			</a>
		</h1>
	</div><!-- .logo-wrapper -->

  </div>
  <div class="row">
	<?php $phone = of_get_option('contact_telephone'); ?>
	<?php if($phone): ?>
		<h2 class="navbar__phone"><a href="tel:<?php echo $phone;?>"><?php echo $phone; ?></a></h2>
	<?php endif; ?>
  	<h2 class="navbar__subtitle">
	  	Make It Easy
  	</h2>
  </div>
</div>
<header class="header-section container hero-image">
      <div class="row">
          <div class="search-wrapper clearfix">
            <?php if( get_option('colabs_show_advance_search') === false || get_option('colabs_show_advance_search') == 'true' ) : ?>
              <?php if( defined('DSIDXPRESS_OPTION_NAME') ) : ?>
                <?php $idx_options = get_option(DSIDXPRESS_OPTION_NAME);
                if ( $idx_options['Activated'] && ( get_option('colabs_idx_plugin_search') == 'true' ) ) : ?>
      						<ul class="search-tabs clearfix">
      							<li><a href="#default-search"><?php echo get_option('colabs_search_header'); ?></a></li>
      							<li><a href="#idx-search"><?php echo get_option('colabs_search_mls_header'); ?></a></li>
      						</ul>
                <?php endif; ?>
              <?php endif; ?>

              <?php get_template_part( 'includes/forms/property-search' ); ?>
            <?php endif; ?>
          </div><!-- .search-wrapper -->
      </div>
</header>
<!-- .header-section -->

<div class="main-menu-wrapper container">
  <div class="row">
    <nav class="main-menu clearfix">
			<?php wp_nav_menu( array(
						'theme_location' => 'main-menu',
						'container_class' => '',
						'menu_class' => 'menu'
			) ); ?>
    </nav><!-- .main-menu -->
    <?php if ( is_home() ):?>
    <div class="property-ordering">
          <select id="propertyorder" name="propertyorder">

            <?php $sortby = array(
              '' => __('Sort by Latest', 'colabsthemes'),
              'sort-price_asc' => __('Sort by Price - Low to High', 'colabsthemes'),
              'sort-price_desc' => __('Sort by Price - High to Low', 'colabsthemes'),
              'sort-title' => __('Sort by Title', 'colabsthemes'),
              'sort-popular' => __('Sort by Popular', 'colabsthemes')
            ); ?>

            <?php foreach( $sortby as $sort_value => $sort_title ) : ?>
              <?php
              if( isset( $_GET['propertyorder'] ) ) {
                $selected = selected( $_GET['propertyorder'], $sort_value );
              } else {
                $selected = '';
              }?>
              <option value="<?php echo $sort_value; ?>" <?php echo $selected; ?>><?php echo $sort_title; ?></option>
            <?php endforeach; ?>

          </select>
					<script type="text/javascript"><!--
							var dropdown = document.getElementById("propertyorder");
							function onOrderChange() {
                if ( dropdown.options[dropdown.selectedIndex].value != '' ) {
                  // If on search page
                  if( window.location.search.indexOf('?s=') != -1 ) {
								    window.location.href = window.location.href + '&propertyorder=' + dropdown.options[dropdown.selectedIndex].value;
                  } else {
                    window.location.href = window.location.origin + window.location.pathname + '?propertyorder=' + dropdown.options[dropdown.selectedIndex].value;
                  }
                } else if ( dropdown.options[dropdown.selectedIndex].value == '' ) {
                  window.location.href = window.location.href;
                }
							}
							dropdown.onchange = onOrderChange;
					--></script>
    </div><!-- .property-ordering -->
		<?php endif;?>
  </div>
</div>
<!-- .main-menu-wrapper -->

<div class="main-content-wrapper container">
	<div class="row">
	<?php if (isset($_GET['property-search-submit'])) : get_template_part('search'); exit; endif;?>
