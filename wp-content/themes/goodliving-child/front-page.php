<?php get_header(); ?>
<?php if('true' == get_option('colabs_announcement')) : ?>
	<?php if( !isset( $_COOKIE['hide_announcement'] ) ) : ?>
		<div class="alert alert-info">
		  <h4 class="alert-title"><?php echo get_option('colabs_announcement_title');?></h4>
		  <p><?php echo get_option('colabs_announcement_text');?></p>
		  <button class="close">&times;</button>
		</div>
	<?php endif; ?>
<?php endif;?>

<?php

	if ( isset( $_GET['propertyorder'] ) && $_GET['propertyorder'] != '' ) {
    	if($_GET['propertyorder']=='sort-price_asc'){
    		$args = array(
    			'post_type' => 'property',
    			'meta_key'	=> 'property_price',
    			'orderby'		=> 'meta_value_num',
    			'order'			=> 'asc',
    			'paged'			=> $paged,
                'posts_per_page' => 10,
    			'ignore_sticky_posts' => 1
    		);

    	} elseif($_GET['propertyorder']=='sort-price_desc'){
    		$args = array(
    			'post_type' => 'property',
    			'meta_key'	=> 'property_price',
                'meta_value'   => 'NULL',
            	'meta_compare' => '!=',
    			'orderby'		=> 'meta_value_num',
    			'order'			=> 'desc',
    			'paged'			=> $paged,
                'posts_per_page' => 10,
    			'ignore_sticky_posts' => 1
    		);

    	} elseif ( $_GET['propertyorder']=='sort-title' ) {
    		$args = array(
    			'post_type' => 'property',
    			'orderby'		=> 'title',
    			'order'			=> 'ASC',
    			'paged'			=> $paged,
                'posts_per_page' => 10,
    			'ignore_sticky_posts' => 1
    		);

    	} elseif ( $_GET['propertyorder']=='sort-popular' ) {
    		$args = array(
    			'post_type' => 'property',
    			'orderby'		=> 'comment_count',
    			'paged'			=> $paged,
                'posts_per_page' => 10,
    			'ignore_sticky_posts' => 1
    		);

    	} elseif( $_GET['propertyorder']=='latest' ) {

    		$args = array(
    			'post_type' => 'property',
    			'orderby'		=> 'date',
    			'paged'			=> $paged,
                'posts_per_page' => 10,
    			'ignore_sticky_posts' => 1,
                'order'			=> 'desc',
    		);

    	}
    } else {
        $args = array(
            'post_type' => 'property',
            'paged'			=> $paged,
            // -- added by RF, to show only featured properties --
            'posts_per_page' => 10,
            'post__in'  => get_option( 'sticky_posts' ),
            'ignore_sticky_posts' => 1
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
	<a href="#" class="button button-orange"><?php _e('Load More Properties', 'colabsthemes'); ?></a>
</div>

<?php colabs_content_nav($latestproperties);?>
<?php endif;?>
<?php get_footer(); ?>
