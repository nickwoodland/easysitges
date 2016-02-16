<?php get_header(); ?>

<div class="single-prop-top clearfix">
	<?php colabs_breadcrumbs(array('separator' => '&mdash;', 'before' => ''));?><!-- .colabs-breadcrumbs -->
	<?php echo colabs_share(); ?>
</div>

<div class="main-content column col9">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<?php
	$id = get_the_ID();
	$address = get_post_meta(get_the_ID(),'colabs_maps_address',true);
	$beds = get_post_meta($id, 'property_beds',true);
	$baths = get_post_meta($id, 'property_baths',true);
	$size = get_post_meta($id, 'property_size',true);
	$garage = get_post_meta($id, 'property_garage',true);
	$furnished= get_post_meta($id, 'property_furnished',true);
	$mortgage= get_post_meta($id, 'property_mortgage',true);

	$maps_active = get_post_meta(get_the_ID(),'colabs_maps_enable',true);

	$new_attachment = get_post_meta($post->ID,'_property_image_gallery',true);
	?>
	<article <?php post_class('single-entry-post');?>>
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title();?></h1>
			<span class="property-location"><?php echo $address;?></span>
		</header>

		<?php
			$attachments = get_children( array(
				'post_parent' => get_the_ID(),
				'numberposts' => 100,
				'post_type' => 'attachment',
				'post_mime_type' => 'image' )
			);
		?>

		<div class="property-info">
			<ul class="property-info-tabs clearfix">
				<?php if(( !empty($attachments) )||('' != $new_attachment))  : ?>
					<li><a href="#property-gallery"><i class="icon-camera"></i> <?php _e('Gallery','colabsthemes');?></a></li>
				<?php endif; ?>

				<?php if($maps_active == 'on') { ?>
					<li><a href="#property-maps"><i class="icon-map-marker"></i> <?php _e('Google Maps','colabsthemes');?></a></li>
				<?php }?>

				<li><a href="#property-facilities"><i class="icon-tasks"></i> <?php _e('Facilities','colabsthemes');?></a></li>
			</ul>

			<?php
			if (( !empty($attachments) )||('' != $new_attachment)) :?>
			<div class="property-info-panel" id="property-gallery">
				<div class="property-gallery-large"></div>
				<div class="property-gallery-thumb-wrapper">
					<div class="property-gallery-thumb">
					<?php
            if( !empty($attachments) ):
							foreach ( $attachments as $att_id => $attachment ) {
								$url = wp_get_attachment_image_src($att_id, 'full', true);
								$image_thumb = vt_resize( $att_id, '', 74, 74, true );

								if( $image_thumb && isset( $image_thumb['url'] ) ) {
									echo '<a href="'.$url[0].'" >';
									echo '<img src="'. $image_thumb['url'] .'">';
									echo '</a>';
								}
							}
            else:
              $new_attachments = explode(',', $new_attachment);
              foreach ( $new_attachments as $key => $value ) {

                $url = wp_get_attachment_image_src($value, 'full', true);
                $image_thumb = vt_resize( $value, '', 74, 74, true );

                if( $image_thumb['url'] ) {
                echo '<a href="'.$url[0].'">';
                echo    '<img src="'. $image_thumb['url'] .'">';
                echo '</a>';
                }

              }
            endif;
					?>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php if($maps_active == 'on') { ?>
			<div class="property-info-panel" id="property-maps">
				<?php
				if($maps_active == 'on'){
					$mode = get_post_meta(get_the_ID(),'colabs_maps_mode',true);
					$streetview = get_post_meta(get_the_ID(),'colabs_maps_streetview',true);
					$address = get_post_meta(get_the_ID(),'colabs_maps_address',true);
					$long = get_post_meta(get_the_ID(),'colabs_maps_long',true);
					$lat = get_post_meta(get_the_ID(),'colabs_maps_lat',true);
					$pov = get_post_meta(get_the_ID(),'colabs_maps_pov',true);
					$from = get_post_meta(get_the_ID(),'colabs_maps_from',true);
					$to = get_post_meta(get_the_ID(),'colabs_maps_to',true);
					$zoom = get_post_meta(get_the_ID(),'colabs_maps_zoom',true);
					$type = get_post_meta(get_the_ID(),'colabs_maps_type',true);
					$yaw = get_post_meta(get_the_ID(),'colabs_maps_pov_yaw',true);
					$pitch = get_post_meta(get_the_ID(),'colabs_maps_pov_pitch',true);

					if(!empty($lat) OR !empty($from)){
						colabs_maps_single_output("mode=$mode&streetview=$streetview&address=$address&long=$long&lat=$lat&pov=$pov&from=$from&to=$to&zoom=$zoom&type=$type&yaw=$yaw&pitch=$pitch");
					}
				}
				?>
			</div>
			<?php }?>
			<div class="property-info-panel" id="property-facilities">
				<?php
					 echo '<ul class="property-facility">';
						if($size!='') echo '<li class="prop-size"><span>'.$size.' '.__("sq ft","colabsthemes").'</span></li>';
						if($beds!='') echo '<li class="prop-beds"><span>'.$beds.' '.__("beds","colabsthemes").'</span></li>';
						if($baths!='') echo '<li class="prop-baths"><span>'.$baths.' '.__("baths","colabsthemes").'</span></li>';
						if($garage!='') echo '<li class="prop-garage"><span>'.$garage.' '.__("garage","colabsthemes").'</span></li>';
						if($mortgage=='true') echo '<li class="prop-mortgage"><span>'.__("Mortgage","colabsthemes").'</span></li>';
						if($furnished=='true') echo '<li class="prop-furnished"><span>'.__("Furnished","colabsthemes").'</span></li>';
					 echo '</ul>';
				?>
				<div class="property-facilities-info">
					<?php echo get_the_term_list($post->ID, 'property_features', '<div class="entry-features">'.__("Features","colabsthemes").' : ', ', ','</div>');   ?>
					<?php echo get_the_term_list($post->ID, 'property_type', '<div class="entry-type">'.__("Type","colabsthemes").' : ', ', ','</div>');   ?>
					<?php echo get_the_term_list($post->ID, 'property_location', '<div class="entry-location">'.__("Location","colabsthemes").' : ', ', ','</div>');   ?>
					<?php echo get_the_term_list($post->ID, 'property_status', '<div class="entry-status">'.__("Status","colabsthemes").' : ', ', ','</div>');   ?>
				</div>
			</div>
		</div><!-- .peroperty-info -->

		<div class="property-details">
			<ul class="property-details-tabs clearfix">
				<li><a href="#property-details"><?php _e('Description','colabsthemes');?></a></li>
			</ul>

			<div class="property-details-panel entry-content" id="property-details">
				<?php the_content();?>
			</div>
		</div><!-- .property-details -->

	</article><!-- .single-entry-post -->
	<?php endwhile;endif;?>
</div><!-- .main-content -->

<?php get_sidebar();?><!-- .property-sidebar -->
<?php get_footer(); ?>
