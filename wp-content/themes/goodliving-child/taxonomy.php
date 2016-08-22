<?php get_header(); ?>
<?php global $wp_query; ?>

<?php

$term_object = get_queried_object();

// find out what we're looking at and preps some vars for our query
if(!is_wp_error($term_object)):
    $term_id = $term_object->term_id;
    $term_tax = $term_object->taxonomy;
else:
    $term_object = false;
endif;

/// holiday rental use a different price meta field. handle this.
if($term_object && $term_object->slug == 'holiday-rental'):
    $meta_key = 'property_price_day_high';
else:
    $meta_key = 'property_price';
endif;

if ( isset( $_GET['propertyorder'] ) && $_GET['propertyorder'] != '' ) {
	if($_GET['propertyorder']=='sort-price_asc'){
		$args = array(
			'post_type' => 'property',
			'meta_key'	=> $meta_key,
			'orderby'		=> 'meta_value_num',
			'order'			=> 'asc',
			'paged'			=> $paged,
            'posts_per_page' => 10,
			'ignore_sticky_posts' => 1
		);

	} elseif($_GET['propertyorder']=='sort-price_desc'){
		$args = array(
			'post_type' => 'property',
			'meta_key'	=> $meta_key,
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
		'posts_per_page' => 10,
		'ignore_sticky_posts' => 1,
        'meta_key'	=> $meta_key,
        'meta_value'   => 'NULL',
    	'meta_compare' => '!=',
		'orderby'		=> 'meta_value_num',
		'order'			=> 'desc',
	);
}
//append our tax query
if($term_object):
    $args['tax_query'] =  array(
        //'relation' => 'AND',
		array(
			'taxonomy' => $term_tax,
			'field'    => 'id',
			'terms'    => $term_id,
		),
      array(
          'taxonomy' => 'property_status',
          'field' => 'slug',
          'terms' => array( 'rented', 'sold' ),
          'operator'=> 'NOT IN'
      )
	);
endif;

$latestproperties = new WP_Query($args);
if ( $latestproperties->have_posts() ) :
?>

    <div class="results-count">
        <?php echo $latestproperties->found_posts.' properties match your search.'; ?>
    </div>

    <div class="post-list post-grid loading">
	    <?php while ( $latestproperties->have_posts() ) : $latestproperties->the_post(); ?>
            <?php get_template_part('content','property');?>
        <?php endwhile;wp_reset_postdata();?>
    </div><!-- .post-list -->

    <div class="post-loader">
    	<a href="#" class="button button-grey"><?php _e('Load More', 'colabsthemes'); ?></a>
    </div>

    <?php colabs_content_nav($latestproperties);?>

<?php endif; ?>
<?php get_footer(); ?>
