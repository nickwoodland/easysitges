<?php
/*
Template Name: Dashboard
*/

### Prevent Caching
nocache_headers();

colabs_auth_redirect_login();

$current_user = wp_get_current_user();

$display_user_name = $current_user->display_name;

get_header(); ?>

<?php colabs_breadcrumbs(array(
  'separator' => '&mdash;', 
  'before' => '',
));?><!-- .colabs-breadcrumbs -->

<div class="main-content column col9">

  <article <?php post_class('single-entry-post');?>>
    <header class="entry-header">
      <h2 class="entry-title"><?php printf(__("%s's Property", 'colabsthemes'), $display_user_name); ?></h2>
    </header>

    <div class="property-details">
      <?php do_action( 'colabs_show_notices' ); ?>

      <ul class="block-tab-nav clearfix">
        <li><a href="#property-list"><?php _e('Properties','colabsthemes');?></a></li>
        <?php if ( colabs_charge_listings() ) : ?><li><a href="#order-list"><?php _e('Orders','colabsthemes');?></a></li><?php endif;?>
      </ul>

      <div id="property-list" class="tab-panel property-details-panel entry-content">
        <?php get_template_part('content','dashboard-properties');?>
      </div>
      <div id="order-list" class="tab-panel">
        <?php get_template_part('content','dashboard-orders');?>
      </div>
    </div>
  </article>
  
</div><!-- .main-content -->

<?php get_sidebar('user');?><!-- .property-sidebar -->
<?php get_footer(); ?>