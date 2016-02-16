<?php
/*
Template Name: Property by Agent
*/
?>			

<?php get_header(); ?>

<?php
	$args = array('post_type' => 'property',
								'paged'			=> $paged,
								'meta_key'	=> '',
								'meta_value'=> $_GET['agent_id'],
								);


	$latestproperties = new WP_Query($args);
	if ( $latestproperties->have_posts() ) :
?>
<div class="post-list post-grid loading">
	<?php while ( $latestproperties->have_posts() ) : $latestproperties->the_post(); ?>
  <?php get_template_part('content','property');?>
	<?php endwhile;wp_reset_postdata();?>        
</div><!-- .post-list -->

<div class="post-loader">
	<a href="#" class="button button-grey"><?php _e('Load More', 'colabsthemes'); ?></a>
</div>

<?php colabs_content_nav($latestproperties);?>
<?php endif;?>
<?php get_footer(); ?>