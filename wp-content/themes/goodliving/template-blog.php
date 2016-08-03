<?php
/*
Template Name: Blog
*/
?>			

<?php get_header(); ?>

<?php
	$latest_post = new WP_Query (array('post_type' => 'post', 'paged' => $paged));
	if ( $latest_post->have_posts() ) :
?>
<div class="post-list post-grid post-blog loading">
	<?php while ( $latest_post->have_posts() ) : $latest_post->the_post(); ?>
  <?php get_template_part('content','post');?>
	<?php endwhile;?>        
</div><!-- .post-list -->

<div class="post-loader">
	<a href="#" class="button button-grey"><?php _e('Load More', 'colabsthemes'); ?></a>
</div>

<?php colabs_content_nav($latest_post);?>
<?php endif;?>
<?php get_footer(); ?>