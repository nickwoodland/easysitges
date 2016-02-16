<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
?>
<?php
add_action( 'wp_enqueue_scripts', 'child_theme_enqueue_styles' );
function child_theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/assets/styles/css/site.css' );
}
?>
<?php require_once('includes/enqueue.php'); ?>
<?php include('includes/custom-search.php'); ?>
<?php include('includes/cpt-override.php'); ?>
<?php include('includes/theme-options.php'); ?>
<?php require_once('includes/post-meta-override.php'); ?>
<?php require_once('includes/custom-permalink-structure.php'); ?>
