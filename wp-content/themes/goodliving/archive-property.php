<?php get_header(); ?>

<?php
	if($_GET['propertyorder']=='sort-price'){
		$args = array(
									'post_type' => 'property',
									'meta_key'	=> 'property_price',
									'orderby'		=> 'meta_value',
									'order'			=> 'DESC',
									'paged'			=> $paged,
									);
	}elseif($_GET['propertyorder']=='sort-title'){
		$args = array(
									'post_type' => 'property',
									'orderby'		=> 'title',
									'order'			=> 'ASC',
									'paged'			=> $paged,
									);
	}elseif($_GET['propertyorder']=='sort-popular'){
		$args = array(
									'post_type' => 'property',
									'orderby'		=> 'comment_count',
									'paged'			=> $paged,
									);									
	}else{
		$args = array('post_type' => 'property',
									'meta_key'	=> 'property_price',
									'paged'			=> $paged,
									);
	}
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