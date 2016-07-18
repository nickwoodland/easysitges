<?php
// hook in late to make sure the parent theme's registration
// has fired so you can undo it. Otherwise the parent will simply
// enqueue its script anyway.
function dequeue_borked_scripts_styles()
{
    wp_dequeue_style('mmenu-style');
    wp_deregister_style('mmenu-style');
    wp_enqueue_style( 'mmenu-style', get_template_directory_uri().'/includes/css/jquery.mmenu.css' );

    // -- added by RF --
    wp_enqueue_script( 'pricerange-changer', get_template_directory_uri().'/includes/js/pricerange-changer.js' );
}
add_action('wp_enqueue_scripts', 'dequeue_borked_scripts_styles', 9999);
add_action( 'wp_head', 'dequeue_borked_scripts_styles', 9999 );
