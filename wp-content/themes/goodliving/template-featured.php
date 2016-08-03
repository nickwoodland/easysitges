<?php
/*
Template Name: Featured Property
*/
?>			

<?php get_header(); ?>

<?php
	$args = array(
		'post_type' 	=> 'property',
		'post__in'		=> get_option('sticky_posts'),
		'paged'				=> $paged,
		'post_status' => 'published',
		'ignore_sticky_posts' => true
	);

	$featuredproperties = new WP_Query($args);
	if ( $featuredproperties->have_posts() ) :
?>
<div class="post-list post-grid loading">
	<?php while ( $featuredproperties->have_posts() ) : $featuredproperties->the_post(); ?>
  <?php get_template_part('content','property');?>
	<?php endwhile;wp_reset_postdata();?>        
</div><!-- .post-list -->

<div class="post-loader">
	<a href="#" class="button button-grey"><?php _e('Load More', 'colabsthemes'); ?></a>
</div>

<?php colabs_content_nav($featuredproperties);?>
<?php endif;?>
<?php get_footer(); ?>