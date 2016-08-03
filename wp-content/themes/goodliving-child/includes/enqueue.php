<?php
// hook in late to make sure the parent theme's registration
// has fired so you can undo it. Otherwise the parent will simply
// enqueue its script anyway.
function dequeue_borked_scripts_styles()
{
    wp_dequeue_style('mmenu-style');
    wp_deregister_style('mmenu-style');
    wp_enqueue_style( 'mmenu-style', get_template_directory_uri().'/includes/css/jquery.mmenu.css' );

    wp_dequeue_script('plugins');
    wp_deregister_script( 'plugins' );
    wp_enqueue_script( 'plugins', trailingslashit( get_stylesheet_directory_uri() ) . 'includes/js/plugins.js', array('jquery'), '', true );

    wp_dequeue_script('scripts');
    wp_deregister_script( 'scripts' );
    wp_enqueue_script( 'scripts', trailingslashit( get_stylesheet_directory_uri() ) . 'includes/js/scripts.js', array('jquery'), '', true );

    // -- added by RF --
    wp_enqueue_script( 'pricerange-changer', get_stylesheet_directory_uri().'/includes/js/pricerange-changer.js' );
}
add_action('wp_enqueue_scripts', 'dequeue_borked_scripts_styles', 9999);
add_action( 'wp_head', 'dequeue_borked_scripts_styles', 9999 );
