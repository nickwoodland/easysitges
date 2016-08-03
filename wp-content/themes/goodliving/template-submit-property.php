<?php
/*
Template Name: Submit Property Template
*/

### Prevent Caching
nocache_headers();

colabs_auth_redirect_login();
// if ( !current_user_can('can_submit_property') ) 
//   redirect_profile();

if ( empty($params) ) {
   $params = array();
}

$property = '';
if ( $property_id = get_query_var('property_id') ) {
  $property = get_post($property_id);
}
$default_params = array(
  'step'        => colabs_get_next_step(),
  'property'    => $property,
  'order_id'    => get_query_var('order_id'),
  'post_action' => get_query_var('property_relist') ? 'relist-property' : 'new-property',
  'form_action' => $_SERVER['REQUEST_URI'],
  'submit_text' => __( 'Next', 'colabsthemes' ),
);
$params = wp_parse_args( $params, $default_params );

$step = $params['step'];
$steps = colabs_steps();

if ( get_query_var('property_relist') ) {
  $title = sprintf( __( 'Relisting %s', 'colabsthemes' ), html('a', array( 'href' => get_permalink( $property->ID )), get_the_title( $property->ID ) ) );
} else {
  $title = get_the_title();
}
?>
<?php get_header(); ?>


<div class="main-content column col9">
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?> 
  
    <article <?php post_class('single-entry-post');?>>
      <header class="entry-header">
        <h2 class="entry-title"><?php echo $title;?></h2>
      </header>

      <div class="property-details">
        <div class="property-details-panel entry-content" id="property-details">
          <?php do_action( 'colabs_show_notices' ); ?>

          <ul class="block-tab-nav clearfix">
            <?php
            foreach ( $steps as $i => $value ) {
              echo '<li><span class="btn-uppercase btn-bold ';
              if ($step==$i) { 
                echo 'active ';
                $template = $value['template'];
              }
              if (($step-1)==$i) echo 'previous ';
              if ($i<$step) echo 'done';
              if ($i==1) echo 'first';
              if ($i==count($steps)) echo 'last';
              echo '">';
              echo $value['description'];
              echo '</span></li>';
            }
            
            ?>
          </ul>
          <?php colabs_load_template( $template, $params ); ?>

        </div><!-- #property-details -->
      </div><!-- .property-details -->
    </article>

  <?php endwhile; endif; ?>
</div><!-- .main-content -->

<?php get_sidebar('user');?><!-- .property-sidebar -->

<?php get_footer(); ?>