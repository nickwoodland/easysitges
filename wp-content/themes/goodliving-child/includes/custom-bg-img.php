<?php
function child_theme_support_bg() {
    add_theme_support('custom-background', array(
            'default-color'    => '333333',
            'wp-head-callback' => 'goodliving_child_custom_background'
    ));
}
add_action('after_setup_theme', 'child_theme_support_bg');

function goodliving_child_custom_background() {
   // the fallback – our current active theme's default bg image
    $page_bg_image_url = get_background_image();
    $repeat = get_theme_mod( 'background_repeat', 'repeat' );
    $position = get_theme_mod( 'background_position_x', 'left' );
    $attachment = get_theme_mod( 'background_attachment', 'scroll' );

    /* And below, spit out the <style> tag... */
    echo '<style type="text/css" id="custom-background-css-override">';
    echo'header.hero-image { background-image: url('.$page_bg_image_url.') }';
    echo'header.hero-image { background-position: '.$position.' }';
    echo'header.hero-image { background-repeat: '.$repeat.' }';
    // echo'header.hero-image { background-attachment: '.$attachment.' }';
    echo'header.hero-image { background-size: cover }';
    echo '</style>';
}
