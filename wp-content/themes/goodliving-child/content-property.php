<article class="entry-post property">
    <div class="entry-inner">
			<?php if('display_message'==get_option('colabs_expired_action')){?>
				<?php if (strtotime(date('Y-m-d')) > strtotime(get_post_meta($post->ID, 'expires', true))) {?>
				<span class="as-feature"><?php _e('Not Available','colabsthemes');?></span>
				<?php }?>
			<?php }elseif('true'==get_post_meta($post->ID,'colabs_property_sold',true)){?>
			<span class="as-feature"><?php _e('Sold','colabsthemes');?></span>
			<?php }elseif( is_sticky( $post->ID ) ){?>
			<span class="as-feature"><?php _e('Featured','colabsthemes');?></span>
			<?php }?>
			<?php colabs_image('width=300&before=<figure class=entry-media>&after=</figure>');?>
      <header class="entry-header">
        <h2 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
        <?php if( get_post_meta($post->ID,'colabs_property_sold',true) != 'true' ) : ?>
				  <?php echo get_the_term_list($post->ID, 'property_status', '<span class="property-label">', ', ','</span>');?>
        <?php endif; ?>
      </header>
      <footer class="entry-footer clearfix">
        <span class="property-price">
          <?php if( $property_price = get_post_meta($post->ID,'property_price',true) ) : ?>
            <?php echo get_option('colabs_currency_symbol').' '. number_format( $property_price );?>
          <?php endif; ?>
        </span>
				<?php echo get_the_term_list($post->ID, 'property_location', '<span class="property-location">', ', ','</span>');?>
      </footer>
    </div>
</article>
	
