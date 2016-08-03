<?php get_header(); ?>
<?php
	if ( have_posts() ) :
?>
<div class="post-list post-grid loading">
	<?php while ( have_posts() ) : the_post(); ?>
  <?php get_template_part('content','property');?>
	<?php endwhile;?>        
</div><!-- .post-list -->

<div class="post-loader">
	<a href="#" class="button button-grey"><?php _e('Load More', 'colabsthemes'); ?></a>
</div>

<?php colabs_content_nav($wp_query);?>
<?php endif;?>
<?php get_footer(); ?>