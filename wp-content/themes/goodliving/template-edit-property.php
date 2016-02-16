<?php
/*
Template Name: Edit Property
*/

### Prevent Caching
nocache_headers();

colabs_auth_redirect_login();
// if ( !current_user_can('can_submit_property') ) 
//   redirect_profile();

// retrieve the property details for new / edit / relist
if ( $property_id = get_query_var('property_edit') ) {
  $property = get_post($property_id);
}

if ( ! $property->ID ) {
  wp_redirect( home_url() );
  exit();
}

$params = array(
  'step' => 1,
  'property' => $property,
  'order_id'  => 0,
  'post_action' => 'edit-property',
  'form_action' => $_SERVER['REQUEST_URI'],
  'submit_text'   => __( 'Save' , 'colabsthemes' ),
  'property_id' => $property_id
);

?>
<?php get_header(); ?>


<div class="main-content column col9">
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?> 
  
    <article <?php post_class('single-entry-post');?>>
      <header class="entry-header">
        <h2 class="entry-title"><?php echo the_title();?></h2>
      </header>

      <div class="property-details">
        <div class="property-details-panel entry-content" id="property-details">
          <?php do_action( 'colabs_show_notices' ); ?>
          <?php colabs_load_template( '/includes/forms/submit-form.php', $params ); ?>
        </div><!-- #property-details -->
      </div><!-- .property-details -->
    </article>

  <?php endwhile; endif; ?>
</div><!-- .main-content -->

<?php get_sidebar('user');?><!-- .property-sidebar -->

<?php get_footer(); ?>