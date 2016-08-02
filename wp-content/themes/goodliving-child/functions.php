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
<?php require_once('includes/custom-bg-img.php'); ?>
<?php require_once('includes/custom-search.php'); ?>
<?php require_once('includes/cpt-override.php'); ?>
<?php require_once('includes/tax-override.php'); ?>
<?php require_once('includes/theme-options.php'); ?>
<?php require_once('includes/post-meta-override.php'); ?>
<?php require_once('includes/custom-permalink-structure.php'); ?>
<?php//  require_once('includes/add-meta-box.php'); ?>
<?php require_once('includes/manage-admin-columns.php');?>
<?php require_once('includes/query-controller.php');?>
