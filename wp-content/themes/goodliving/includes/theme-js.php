<?php
/**
 * Load stylesheet and javascripts on frontend
 */
if ( ! is_admin() ) {
	add_action( 'wp_enqueue_scripts', 'colabsthemes_script_and_style' );
}

if( ! function_exists( 'colabsthemes_script_and_style' ) ) {
	function colabsthemes_script_and_style() {

		// Styles
		wp_enqueue_style( 'framework', get_template_directory_uri() . '/includes/css/framework.css' );
		wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.css', array( 'framework' ) );
    	wp_enqueue_style( 'mmenu-style', get_stylesheet_directory_uri().'/includes/css/jquery.mmenu.css' );

		// Scripts
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-slider' );

		// Only load fancybox on single property
		if( 'property' == get_post_type() ) {
			wp_enqueue_script( 'fancybox', get_template_directory_uri() . '/includes/js/fancybox/jquery.fancybox-1.3.4.pack.js', array('jquery'), '', true );
			wp_enqueue_style( 'fancybox', get_template_directory_uri() . '/includes/js/fancybox/jquery.fancybox-1.3.4.css' );
		}

		wp_enqueue_script( 'plugins', trailingslashit( get_template_directory_uri() ) . 'includes/js/plugins.js', array('jquery'), '', true );
		wp_enqueue_script( 'scripts', trailingslashit( get_template_directory_uri() ) . 'includes/js/scripts.js', array('jquery'), '', true );

		/* Script for threaded comments. */
		if ( is_singular() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
    
    	wp_localize_script('scripts','translationstring',array('nomorepost'=>__('No More Post','colabsthemes')));
    
    	// Translatable string for form builder
    	$rent_term_obj = get_term_by( 'slug', 'rent', COLABS_TAX_STATUS );
    	$term_id = (isset($rent_term_obj->term_id)) ? $rent_term_obj->term_id : '';
    
		$formbuilder_string = array(
			'media_upload' => array(
			  'title'=> __('Upload', 'colabsthemes'),
			  'button_text'=> __('Use this file', 'colabsthemes')
			),
			'gallery_upload' => array(
			  'title'=> __('Add Images to Gallery', 'colabsthemes'),
			  'button_text'=> __('Add to gallery', 'colabsthemes')
			),
			'file_upload' => array(
			  'title'=> __('Choose a File', 'colabsthemes'),
			  'button_text'=> __('Insert file URL', 'colabsthemes')
			),
			'validator' => array(
			  'required'=> __('This field is required', 'colabsthemes'),
			  'terms'=> __('You need to aggree with our terms', 'colabsthemes')
			),
			'delete_confirm' => __("Are your sure you want to delete this? It can't be undone.", 'colabsthemes' ),
			'cancel_order_confirm' => __("Are you sure you want to cancel this order? It can't be undone.", 'colabsthemes'),
			'rent_term_id' => $term_id
		);
		wp_localize_script( 'scripts', 'formbuilder_string', $formbuilder_string );

		if ( is_page_template( 'template-profile.php' ) ){
	      wp_enqueue_script( 'password-strength-meter' );
	      wp_enqueue_script( 'zxcvbn-async' );
	      wp_enqueue_script( 'custom-strengthmeter', trailingslashit( get_template_directory_uri() ) . 'includes/js/custom-strengthmeter.js');
	    }

	}
}