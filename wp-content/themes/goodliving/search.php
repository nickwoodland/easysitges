<?php get_header(); ?>

<?php if ( have_posts() ) : ?>

  <div class="post-list post-grid loading post-blog">
    <?php while ( have_posts() ) : the_post(); ?>

      <?php
      // Users are searching property
      if( isset($_GET['property-search-submit']) && 'Search' == $_GET['property-search-submit'] ) {
        get_template_part('content','property');
      }

      // Search all post type
      else {
        get_template_part('content','post');
      } ?>
      
    <?php endwhile;?>        
  </div><!-- .post-list -->

  <div class="post-loader">
    <a href="#" class="button button-grey"><?php _e('Load More', 'colabsthemes'); ?></a>
  </div>

  <?php colabs_content_nav( $wp_query );?>

<?php else:?>

  <?php 
  if( isset($_GET['property-search-submit']) && 'Search' == $_GET['property-search-submit'] ) {
    get_template_part('content','property-noresult');
  }

  else {
    get_template_part('content','nopost');
  }
  ?>

<?php endif;?>

<?php get_footer(); ?>