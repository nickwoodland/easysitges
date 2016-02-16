<?php
/*-----------------------------------------------------------------------------------*/
/* SET GLOBAL CoLabs VARIABLES
/*-----------------------------------------------------------------------------------*/
add_role('member', 'Member', array(
  'read' => true, // True allows that capability
  'edit_posts' => true,
  'delete_posts' => false, // Use false to explicitly deny,
  'upload_files' => true
));

$member_role = get_role( 'member' );
if( !$member_role->capabilities['upload_files'] || $member_role->capabilities['upload_files'] == NULL ) {
  $member_role->add_cap( 'upload_files' );
}

add_theme_support( 'automatic-feed-links' );

add_theme_support( 'custom-background', array('default-color' => 'f7f7f7') );

/**
 * ===================================================================
 * Custom Excerpt
 * ===================================================================
 */

// Add excerpt on pages
// --------------------
if(function_exists('add_post_type_support'))
add_post_type_support('page', 'excerpt');

// Set excerpt length
// ------------------
function colabs_excerpt_length( $length ) {
	if( get_option('colabs_excerpt_length') != '' ){
		return get_option('colabs_excerpt_length');
	}else{
		return 45;
	}
}
add_filter('excerpt_length', 'colabs_excerpt_length');

// Remove [..] in excerpt
// ----------------------
function colabs_trim_excerpt($text) {
	return rtrim($text,'[...]');
}
add_filter('get_the_excerpt', 'colabs_trim_excerpt');

// Add excerpt more
// ----------------
function colabs_excerpt_more($more) {
	global $post;
	return '<span class="more"><a href="'. get_permalink($post->ID) . '">'. __( 'Read more', 'colabsthemes' ) . '&hellip;</a></span>';
}
add_filter('excerpt_more', 'colabs_excerpt_more');

/**
 * ===================================================================
 * Register Custom Menus
 * ===================================================================
 */
if ( function_exists('register_nav_menus') ) {
	add_theme_support( 'nav-menus' );
	register_nav_menus( array(
	'top-menu' => __( 'Top Menu','colabsthemes' ),
	'main-menu' => __( 'Main Menu','colabsthemes' )
	) );
}

/**
 * ===================================================================
 * WP 3.0 post thumbnails compatibility
 * ===================================================================
 */
if(function_exists( 'add_theme_support')){
	if( get_option('colabs_post_image_support') ){
		add_theme_support( 'post-thumbnails' );		
		// set height, width and crop if dynamic resize functionality isn't enabled
		if ( get_option( 'colabs_pis_resize') <> "true" ) {
			$hard_crop = get_option( 'colabs_pis_hard_crop' );
			if( 'true' == $hard_crop ) {$hard_crop = true; } else { $hard_crop = false;} 
			add_image_size( 'headline-thumb', 978, 99999, $hard_crop);
		}
	}
}
/*-----------------------------------------------------------------------------------*/
/* CoLabs - User Meta */
/*-----------------------------------------------------------------------------------*/ 
function new_user_meta( $contactmethods ) {
$contactmethods['address'] = 'Address';
$contactmethods['phone'] = 'Phone';
return $contactmethods;
}
add_filter('user_contactmethods','new_user_meta',10,1);

/*-----------------------------------------------------------------------------------*/
/* CoLabs - Footer Credit */
/*-----------------------------------------------------------------------------------*/
function colabs_credit(){
global $themename,$colabs_options;
if( $colabs_options['colabs_footer_credit'] != 'true' ){ ?>
            Copyright &copy; 2013 <a href="http://colorlabsproject.com/themes/<?php echo get_option('colabs_themename'); ?>/" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php echo get_option('colabs_themename'); ?></a> by <a href="http://colorlabsproject.com/" title="Colorlabs">ColorLabs & Company</a>. All rights reserved.
<?php }else{ echo stripslashes( $colabs_options['colabs_footer_credit_txt'] ); } 
}

/*-----------------------------------------------------------------------------------*/
/*  colabs_share - Twitter, FB & Google +1    */
/*-----------------------------------------------------------------------------------*/

if ( !function_exists( 'colabs_share' ) ) {
function colabs_share() {
    
$return = '';


$colabs_share_twitter = get_option('colabs_share_twitter');
$colabs_share_fblike = get_option('colabs_share_fblike');
$colabs_share_google_plusone = get_option('colabs_share_google_plusone');
$colabs_share_pinterest = get_option('colabs_share_pinterest');
$colabs_share_linkedin = get_option('colabs_share_linkedin');


    //Share Button Functions 
    global $colabs_options;
    $url = get_permalink();
    $share = '';
    
    //Twitter Share Button
    if(function_exists('colabs_shortcode_twitter') && $colabs_share_twitter == "true"){
        $tweet_args = array(  'url' => $url,
   							'style' => 'horizontal',
   							'source' => ( $colabs_options['colabs_twitter_username'] )? $colabs_options['colabs_twitter_username'] : '',
   							'text' => '',
   							'related' => '',
   							'lang' => '',
   							'float' => 'fl'
                        );

        $share .= colabs_shortcode_twitter($tweet_args);
    }
    
   
        
    //Google +1 Share Button
    if( function_exists('colabs_shortcode_google_plusone') && $colabs_share_google_plusone == "true"){
        $google_args = array(
						'size' => 'medium',
						'language' => '',
						'count' => '',
						'href' => $url,
						'callback' => '',
						'float' => 'left',
						'annotation' => 'bubble'
					);        

        $share .= colabs_shortcode_google_plusone($google_args);       
    }
	
	 //Facebook Like Button
    if(function_exists('colabs_shortcode_fblike') && $colabs_share_fblike == "true"){
    $fblike_args = 
    array(	
        'float' => 'left',
        'url' => '',
        'style' => 'button_count',
        'showfaces' => 'false',
        'width' => '80',
        'height' => '',
        'verb' => 'like',
        'colorscheme' => 'light',
        'font' => 'arial'
        );
        $share .= colabs_shortcode_fblike($fblike_args);    
    }
    
		global $post;
	if (is_attachment()){
	$att_image = wp_get_attachment_image_src( $post->id, "thumbnail");
	$image = $att_image[0];
	}else{
    $image = colabs_image('return=true&link=url&id='.$post->ID);
	}
	//Pinterest Share Button
	if( function_exists('colabs_shortcode_pinterest') && $colabs_share_pinterest == "true"){
        $pinterest_args = array(
						'count' => 'horizontal',
						'float' => 'left',  
						'use_post' => 'true',
						'image_url' => $image,
						'url' => $url
					);        

        $share .= colabs_shortcode_pinterest($pinterest_args);       
    } 
	
	//Linked Share Button
    if( function_exists('colabs_shortcode_linkedin_share') && $colabs_share_linkedin == "true"){
        $linkedin_args = array(
						'url' 	=> $url,
						'style' => 'right', 
						'float' => 'left'
					);        

        $share .= colabs_shortcode_linkedin_share($linkedin_args);       
    }
		
    $return .= '<div class="social_share">'.$share.'</div><div class="clear"></div>';
    
    return $return;
}
}

/*-----------------------------------------------------------------------------------*/
/* CoLabs Advertisement - colabs_ad_gen */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'colabs_ad_gen' ) ) {
	function colabs_ad_gen() { 
	   
    global $colabs_options;
    global $post;
    
    //default
    $colabs_ad_single = isset($colabs_options['colabs_ad_single']) ? $colabs_options['colabs_ad_single'] : '';
    $colabs_ad_single_adsense = isset($colabs_options['colabs_ad_single_adsense']) ? $colabs_options['colabs_ad_single_adsense'] : '';
    $colabs_ad_single_image = isset($colabs_options['colabs_ad_single_image']) ? $colabs_options['colabs_ad_single_image'] : '';
    $colabs_ad_single_url = isset($colabs_options['colabs_ad_single_url']) ? $colabs_options['colabs_ad_single_url'] : '';
    $width = 728;
    $height = 90;
    
    //Single Custom Ad
    $colabs_ad_single_custom = get_post_meta($post->ID, 'colabs_ad_single', true); //none, general_ad, custom_ad
    
    if( 'custom_ad' == $colabs_ad_single_custom ){
        $colabs_ad_single = 'true';
        $colabs_ad_single_adsense = get_post_meta($post->ID, 'colabs_ad_single_adsense', true);
        $colabs_ad_single_image = get_post_meta($post->ID, 'colabs_ad_single_image', true);
        $colabs_ad_single_url = get_post_meta($post->ID, 'colabs_ad_single_url', true);
        }
    
        if ( 'true' == $colabs_ad_single && 'none' != $colabs_ad_single_custom && ( '' != $colabs_ad_single_adsense || '' != $colabs_ad_single_image ) ) { ?>
	    <div id="singlead">
            <?php if ("" <> $colabs_ad_single_adsense) { echo stripslashes($colabs_ad_single_adsense);  } else { ?>
                <a href="<?php echo $colabs_ad_single_url; ?>"><img src="<?php echo $colabs_ad_single_image; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="advert" /></a>
            <?php } ?>		   	
        </div><!-- /#topad -->
        <?php }
	}
}

/*-----------------------------------------------------------------------------------*/
/* Add layout to body_class output */
/*-----------------------------------------------------------------------------------*/
add_filter( 'body_class','colabs_layout_body_class', 10 );					// Add layout to body_class output
if ( ! function_exists( 'colabs_layout_body_class' ) ) {
	function colabs_layout_body_class( $classes ) {
		$layout = '';
		// Set main layout
		if ( is_singular() ) {
			global $post;
			$layout = get_post_meta($post->ID, 'layout', true); }
        //set $colabs_option
        if ( $layout != '' ) {
			global $colabs_options;
            $colabs_options['colabs_layout'] = $layout; } else {
                $layout = get_option( 'colabs_layout' );
				if ( $layout == '' ) $layout = "colabs-two-col-left";
            }
		// Add classes to body_class() output 
		$classes[] = $layout;
		return apply_filters('colabs_layout_body_class', $classes);
	}
}

function colabs_content_nav( $query ) {
	global $wp_query;
	
	if(empty($query))$query = $wp_query;

	if ( $query->max_num_pages > 1 ) : ?>
		<nav class="navigation" role="navigation">
			<?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'colabsthemes' ),$query->max_num_pages ); ?>
			<?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'colabsthemes' ), $query->max_num_pages ); ?>
		</nav>
	<?php endif;
}

/*-----------------------------------------------------------------------------------*/
/* WordPress Customizer
/*-----------------------------------------------------------------------------------*/
function colabs_customize_register( $wp_customize ) {
	class Colabs_Customize_Textarea_Control extends WP_Customize_Control {
			public $type = 'textarea';
	 
			public function render_content() {
					?>
					<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
					</label>
					<?php
			}
	}
  $wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
  $wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
	
	$wp_customize->add_setting('colabs_logo', array(
    'default'      => '',
    'capability'   => 'edit_theme_options',
    'type'         => 'option',
	));
	 
	$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'colabs_logo', array(
			'label'    => __('Upload Logo', 'colabsthemes'),
			'section'  => 'title_tagline',
			'settings' => 'colabs_logo',
			'priority' => 5,
	)));
	
  // Layout Settings
  // -----------------------------
  $wp_customize->add_section( 'layout_settings', array(
    'title'    => __( 'Layout', 'colabsthemes' ),
    'priority' => 50,
  ) );
  
  $wp_customize->add_setting( 'colabs_layout', array(
    'default'    => 'one-col',
    'type'       => 'option',
    'capability' => 'edit_theme_options',
  ) );

  $choices = array(
		'one-col'  			=> __('Fullwidth', 'colabsthemes'),
    'two-col-left'  => __('Content on left', 'colabsthemes'),
    'two-col-right' => __('Content on right', 'colabsthemes')
  );

  $wp_customize->add_control( 'colabs_layout', array(
    'label'    => __( 'Select main content and sidebar alignment. Choose between left or right sidebar layout or fullwidth', 'colabsthemes' ),
    'section'  => 'layout_settings',
    'settings' => 'colabs_layout',
    'type'     => 'radio',
    'choices'  => $choices,
    'priority' => 5,
  ) );  
	 
	
	
	// Footer Settings
  // -----------------------------
  $wp_customize->add_section( 'footer_settings', array(
    'title'    => __( 'Footer', 'colabsthemes' ),
    'priority' => 60,
  ) );
	
	$wp_customize->add_setting('colabs_footer_credit', array(
			'capability' => 'edit_theme_options',
			'type'       => 'option',
	));
	 
	$wp_customize->add_control('colabs_footer_credit', array(
			'settings' => 'colabs_footer_credit',
			'label'    => __('Enable / Disable Custom Credit','colabsthemes'),
			'section'  => 'footer_settings',
			'type'     => 'checkbox',
	));
	
	$wp_customize->add_setting( 'colabs_footer_credit_txt', array(
			'type' 			=> 'option',
			'default'   => '',
	) );
	 
	$wp_customize->add_control( new Colabs_Customize_Textarea_Control( $wp_customize, 'colabs_footer_credit_txt', array(
			'label'   	=> 'Footer Credit',
			'section' 	=> 'footer_settings',
			'settings'  => 'colabs_footer_credit_txt',
	) ) );
}
add_action( 'customize_register', 'colabs_customize_register' );

/**
 * Bind JS handlers to make Theme Customizer preview reload changes asynchronously.
 * Used with blogname and blogdescription.
 * 
 */
function colabs_customize_preview_js() {
  wp_enqueue_script( 'colabs-customizer', get_template_directory_uri() . '/includes/js/theme-customizer.js', array( 'customize-preview' ), '20120620', true );
}
add_action( 'customize_preview_init', 'colabs_customize_preview_js' );

//Fix for home page navigation error on WP 3.4
function colabs_query_for_homepage( $query ) {
global $paged;
	if ( ! is_preview() && ! is_admin() && ! is_singular() && ! is_404() ) {
		if ( $query->is_feed ) {
		// As always, handle your feed post types here.
		} else {
		$my_post_type = get_query_var( 'post_type' );
		if ( empty( $my_post_type ) ) {
		$args = array(
		'public' => true ,
		'_builtin' => false
		);
		$output = 'names';
		$operator = 'and';

		// Get all custom post types automatically.
		$post_types = get_post_types( $args, $output, $operator );
		// Or uncomment and edit to explicitly state which post types you want. */
		// $post_types = array( 'event', 'location' );

		// Add 'link' and/or 'page' to array() if you want these included.
		// array( 'post' , 'link' , 'page' ), etc.
		$post_types = array_merge( $post_types, array( 'post' ) );
		$query->set( 'post_type', $post_types );
		}
		}
	}
}
add_action( 'pre_get_posts', 'colabs_query_for_homepage' );


/**
 * Override Search Page
 */
function search_page_template( $template ) {
  global $wp;
  
  if( isset( $wp->query_vars['s'] ) ) {
    $template = locate_template( array( 'search.php' ) );
  }

  return $template;
}
add_filter( 'template_include', 'search_page_template' );


/**
 * Hook into pre_get_posts on Search Page
 */
function filter_search_property( $q ) {
  global $wp;

  // We only want to affect the main query
  if ( ! $q->is_main_query() )
    return;

  // If on search page
  if( isset( $wp->query_vars['s'] ) ) {
    if( isset($_GET['property-search-submit']) && 'Search' == $_GET['property-search-submit'] ) {
      colabs_search_set_query( $q );
    }
  }

  return $q;
}
add_filter('pre_get_posts', 'filter_search_property');


/**
 * Setup query for Search page
 */
function colabs_search_set_query( $q ) {
  // Set post type
  $q->set('post_type', 'property');

  //Get matching method
  $matching_method = get_option('colabs_feature_matching_method');

  // Setup search variables
  $min_price         = isset($_GET['price_min']) ? $_GET['price_min'] : 0;
  $max_price         = isset($_GET['price_max']) ? $_GET['price_max'] : 0;
  $min_size          = isset($_GET['size_min']) ? $_GET['size_min'] : 0;
  $max_size          = isset($_GET['size_max']) ? $_GET['size_max'] : 0;
  $no_garages        = isset($_GET['no_garages']) ? $_GET['no_garages'] : 'all';
  $no_beds           = isset($_GET['no_beds']) ? $_GET['no_beds'] : 'all';
  $no_baths          = isset($_GET['no_baths']) ? $_GET['no_baths'] : 'all';
  $location_id       = isset($_GET['location_names']) ? $_GET['location_names'] : 0;
  $propertytypes_id  = isset($_GET['property_types']) ? $_GET['property_types'] : 0;
  $propertystatus_id = isset($_GET['property_status_id']) ? $_GET['property_status_id'] : 0;

  // Tax Query
  $tax_query = array();

  // Location
  if ( $location_id > 0 ) { 
    $tax_query[] = array(
      'taxonomy' => 'property_location',
      'terms' => $location_id
    );
  }

  // Property Type
  if ( $propertytypes_id > 0 ) { 
    $tax_query[] = array(
      'taxonomy' => 'property_type',
      'terms' => $propertytypes_id
    );
  }

  // Property Status
  if ( $propertystatus_id > 0 ) { 
    $tax_query[] = array(
      'taxonomy' => 'property_status',
      'terms' => $propertystatus_id
    );
  }

  // Meta Query, for querying number of garages, bed and bathrooms
  $meta_query = array(
    // 'relation' => 'OR'
  );

  // Garages
  if( $no_garages != 'all' ) {
    $meta_query[] = array(
      'key' => 'property_garage',
      'value' => intval( $no_garages ),
      'compare' => $matching_method == 'minimum' ? '>=' : '=',
      'type' => 'NUMERIC'
    );  
  }

  // Bedrooms
  if( $no_beds != 'all' ) {
    $meta_query[] = array(
      'key' => 'property_beds',
      'value' => $no_beds,
      'compare' => $matching_method == 'minimum' ? '>=' : '=',
      'type' => 'NUMERIC'
    );  
  }

  // Bathrooms
  if( $no_baths != 'all' ) {
    $meta_query[] = array(
      'key' => 'property_baths',
      'value' => $no_baths,
      'compare' => $matching_method == 'minimum' ? '>=' : '=',
      'type' => 'NUMERIC'
    );  
  }

  // Search Price range  
  $meta_query[] = array(
    'key' => 'property_price',
    'value' => array( intval($min_price), intval($max_price) ),
    'type' => 'numeric',
    'compare' => 'BETWEEN'
  );

  // Search Size range
  $meta_query[] = array(
    'key' => 'property_size',
    'value' => array( intval($min_size), intval($max_size) ),
    'type' => 'numeric',
    'compare' => 'BETWEEN'
  );

  $keyword_to_search_raw = get_search_query();
  if ( ($keyword_to_search_raw == get_option('colabs_search_keyword_text')) || 
       ($keyword_to_search_raw == 'Your Keywords') ) { 
    $keyword_to_search = ''; 
  } else { 
    $keyword_to_search = $keyword_to_search_raw;
  }

  $q->set( 'tax_query', $tax_query );
  $q->set( 'meta_query', $meta_query );
  $q->set( 's', $keyword_to_search );
  
  // Set ordering
  // ------------
  if( isset( $_GET['propertyorder'] ) ) {
    $order_query = explode('_', $_GET['propertyorder']);
    $orderby = 'date';
    $order_metakey = '';
    $order = 'desc';

    // Price
    if( $order_query[0] == 'sort-price' ) {
      $orderby = 'meta_value';
      $order_metakey = 'property_price';
    } 

    // Title
    elseif( $order_query[0] == 'sort-title' ) {
      $orderby = 'title';
    }

    // Popular
    elseif( $order_query[0] == 'sort-popular' ) {
      $orderby = 'comment_count';
    }

    // Order
    if( isset( $order_query[1] ) ) {
      $order = $order_query[1];
    }

    $q->set( 'meta_key', $order_metakey );
    $q->set( 'orderby', $orderby );
    $q->set( 'order', $order );
  }
} 

/**
 * Render Recaptcha
 */
function colabsthemes_recaptcha() {
  if ('true' == get_option('colabs_captcha_enable') && get_option('colabs_captcha_theme') && get_option('colabs_captcha_public_key')) : ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <div class="g-recaptcha" data-sitekey="<?php echo get_option('colabs_captcha_public_key'); ?>" data-theme="<?php echo get_option('colabs_captcha_theme'); ?>"></div>
  <?php endif;
}

/**
 * Verify Recaptcha
 */
function colabs_is_captcha_verified() {
  if( isset( $_POST['g-recaptcha-response'] ) ) {
    $recaptcha_secret = get_option('colabs_captcha_private_key');
    $response = file_get_contents( "https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$_POST['g-recaptcha-response']}" );
    $response = json_decode($response, true);

    if($response["success"] == true) {
      return true;
    } else {
      return false;
    }
  } else {
    return false;
  }
}


/**
 * Add Editor Styles
 */
function colabs_add_editor_styles() {
  $font_url = 'http://fonts.googleapis.com/css?family=Merriweather:400,700';
  add_editor_style( array(
    'editor-style.css',
    // str_replace( ',', '%2C', $font_url ) 
  ));
}
add_action( 'init', 'colabs_add_editor_styles' );



/**
 * Get user avatar, it can return image from gravatar or custom uploaded
 * image
 *
 * @param $user_id Int User ID
 * @param $size Int Desired image size
 * @return String html img tag
 */
if( !function_exists('colabs_get_user_avatar') ) {
  function colabs_get_user_avatar( $user_id, $size = 130 ) {
    $user_avatar = get_avatar($user_id, $size);
    $user_custom_avatar_id = get_user_meta( $user_id, 'user_custom_avatar_id', true );

    // User use custom uploaded avatar
    if( $user_custom_avatar_id ) {
      $avatar_img = vt_resize( $user_custom_avatar_id, '', $size, $size, true );
      if( isset( $avatar_img['url'] ) ) {
        $user_avatar = '<img src="'. $avatar_img['url'] .'">';
      }
    }

    return $user_avatar;
  }
}


/**
 * Fix title on 404 page
 */
add_filter('colabs_title', 'colabs_404_title', 10, 2);
function colabs_404_title( $title, $sep ) {

  if( is_404() ) {
    $title = __('Page Not Found', 'colabsthemes') . $sep . get_bloginfo( 'name');
  }

  return $title;
}