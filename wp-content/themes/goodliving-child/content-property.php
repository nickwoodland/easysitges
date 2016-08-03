<?php $key ='property'; ?>
<?php $prop_term_object = false; ?>
<?php $property_ref = false; ?>
<?php $bullet_1 = false; ?>
<?php $bullet_2 = false; ?>
<?php $bedrooms = false; ?>
<?php $floor_area = false; ?>
<?php $ext_area = false; ?>
<?php $aspect = false; ?>
<?php $daily_cost_low = false; ?>
<?php $daily_cost_med = false; ?>
<?php $daily_cost_high = false; ?>

<?php $prop_term_slug = false; ?>
<?php $prop_term_name = false; ?>
<?php $prop_term_array = wp_get_post_terms($post->ID, 'property_type'); ?>
<?php if(!is_wp_error($prop_term_array)): ?>
    <?php $prop_term_object = $prop_term_array[0]; ?>
    <?php $prop_term_slug = $prop_term_object->slug; ?>
    <?php $prop_term_name = $prop_term_object->name; ?>
<?php endif; ?>

<?php $bullet_1 = get_post_meta($post->ID, $key."_short_desc_bullet_1", true); ?>
<?php $bullet_2 = get_post_meta($post->ID, $key."_short_desc_bullet_2", true); ?>
<?php $property_ref = get_post_meta($post->ID, $key."_unique_key", true); ?>

<?php $enquiry_page_id = get_page_by_path('enquiry')->ID; ?>
<?php $enquiry_page_link = get_permalink($enquiry_page_id); ?>
<?php $enquiry_page_q_vars = add_query_arg(
    array(
        'prop_ref' => $property_ref,
        'prop_type' => $prop_term_name,
    ), $enquiry_page_link
); ?>
<?php if($prop_term_slug): ?>

    <?php if($prop_term_slug == 'commercial'): ?>
        <?php $floor_area = get_post_meta($post->ID, $key."_size", true); ?>
        <?php $ext_area = get_post_meta($post->ID, $key."_size_external", true); ?>
    <?php endif; ?>

    <?php if($prop_term_slug == 'holiday-rental'): ?>
        <?php $daily_cost_low = get_post_meta($post->ID, $key."_price_day_low", true); ?>
        <?php $daily_cost_med = get_post_meta($post->ID, $key."_price_day_med", true); ?>
        <?php $daily_cost_high = get_post_meta($post->ID, $key."_price_day_high", true); ?>
    <?php endif; ?>

    <?php if($prop_term_slug == 'land'): ?>
        <?php $ext_area =  get_post_meta($post->ID, $key."_size", true); ?>
        <?php $aspect = get_post_meta($post->ID, $key."_aspect", true); ?>
    <?php endif; ?>

    <?php if($prop_term_slug == 'long-term-rental'): ?>
    <?php endif; ?>

    <?php if($prop_term_slug == 'masias'): ?>
        <?php $bedrooms = get_post_meta($post->ID, $key."_beds", true); ?>
        <?php $floor_area = get_post_meta($post->ID, $key."_size", true); ?>
    <?php endif; ?>

    <?php if($prop_term_slug == 'residential'): ?>
        <?php $bedrooms = get_post_meta($post->ID, $key."_beds", true); ?>
        <?php $floor_area = get_post_meta($post->ID, $key."_size", true); ?>
    <?php endif; ?>

<?php endif; ?>

<?php /* END VARIABLE INITIALISATION */ ?>

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
            <?php $terms = wp_get_object_terms( $post->ID, 'property_status' ); ?>
            <?php if( $terms ){ ?>

                    <?php echo "<span class='property-label'>" . $terms[0]->name . "</span>";
            } ?>
				  <?php // echo get_the_term_list($post->ID, 'property_type', '<span class="property-label">', ', ','</span>');?>
        <?php endif; ?>
        <h4 class="property-ref">Reference: <?php echo $property_ref;?> </h4>
      </header>

      <?php if(($bullet_1 && 'NULL' != $bullet_1  ) || ($bullet_2 && 'NULL' != $bullet_2  )): ?>
          <div class="property-bullets">
              <ul>
              <?php echo($bullet_1 && 'NULL' != $bullet_1 ? '<li>'.$bullet_1.'</li>' : ''); ?>
              <?php echo($bullet_2 && 'NULL' != $bullet_2 ? '<li>'.$bullet_2.'</li>' : ''); ?>
              </ul>
          </div>
      <?php endif; ?>

      <?php if($prop_term_slug == 'residential' || $prop_term_slug == 'masias' ): ?>

          <?php if($bedrooms && $floor_area): ?>
              <div class="clearfix property-details">
                  <div class="column col6">
                      <?php echo($bedrooms ? '<span>Bedrooms: '.$bedrooms.'</span>' : ''); ?>
                  </div>
                  <div class="column col6">
                      <?php echo($floor_area && 'NULL' != $floor_area ? '<span>Floor Area: '.$floor_area.' '.__("m²","colabsthemes").'</span>' : ''); ?>
                  </div>
              </div>
          <?php elseif($bedrooms || $floor_area): ?>
              <div>
                  <?php echo($bedrooms ? '<span>Bedrooms: '.$bedrooms.'</span>' : ''); ?>
                 <?php echo($floor_area && 'NULL' != $floor_area ? '<span>Floor Area: '.$floor_area.' '.__("m²","colabsthemes").'</span>' : ''); ?>
              </div>
          <?php endif; ?>

     <?php elseif($prop_term_slug == 'holiday-rental') : ?>

         <?php if(($daily_cost_low && 'NULL' != $daily_cost_low  ) || ($daily_cost_med && 'NULL' != $daily_cost_med  ) || ( $daily_cost_high && 'NULL' != $daily_cost_high  ) ): ?>
             <div class="clearfix property-cost">
                 <strong>Daily Cost:</strong>
                 <?php echo($daily_cost_low && 'NULL' != $daily_cost_low ? '<span class="property-cost--low">Low: £'.$daily_cost_low.'</span>' : ''); ?>
                 <?php echo($daily_cost_med && 'NULL' != $daily_cost_med ? '<span class="property-cost--mid">Mid: £'.$daily_cost_med.'</span>' : ''); ?>
                 <?php echo($daily_cost_high && 'NULL' != $daily_cost_high ? '<span class="property-cost--high">High: £'.$daily_cost_high.'</span>' : ''); ?>
            </div>
         <?php endif; ?>

     <?php elseif($prop_term_slug == 'commercial') : ?>

             <?php if($ext_area && $floor_area): ?>
                 <div class="clearfix property-details">
                     <div class="column col6">
                         <?php echo($floor_area && 'NULL' != $floor_area ? '<span>Floor Area: '.$floor_area.' '.__("m²","colabsthemes").'</span>' : ''); ?>
                     </div>
                     <div class="column col6">
                         <?php echo($ext_area && 'NULL' != $ext_area ? '<span>External Area: '.$ext_area.' '.__("m²","colabsthemes").'</span>' : ''); ?>
                     </div>
                 </div>
             <?php elseif($ext_area || $floor_area): ?>
                 <div class="clearfix property-details">
                     <?php echo($floor_area && 'NULL' != $floor_area ? '<span>Floor Area: '.$floor_area.' '.__("m²","colabsthemes").'</span>' : ''); ?>
                      <?php echo($ext_area && 'NULL' != $ext_area ? '<span>External Area: '.$ext_area.' '.__("m²","colabsthemes").'</span>' : ''); ?>
                 </div>
             <?php endif; ?>

      <?php elseif($prop_term_slug == 'land') : ?>

            <?php if($ext_area && $aspect): ?>
                <div class="clearfix property-details">
                    <div class="column col6">
                         <?php echo($ext_area && 'NULL' != $ext_area ? '<span>Land Area: '.$ext_area.' '.__("m²","colabsthemes").'</span>' : ''); ?>
                    </div>
                    <div class="column col6">
                        <?php echo($aspect ? '<span>Aspect: '.$aspect.'</span>' : ''); ?>
                    </div>
                </div>
            <?php elseif($ext_area || $aspect): ?>
                <div class="clearfix property-details">
                    <?php echo($ext_area && 'NULL' != $ext_area ? '<span>Land Area: '.$ext_area.' '.__("m²","colabsthemes").'</span>' : ''); ?>
                    <?php echo($aspect ? '<span class="property-aspect">Aspect: '.$aspect.'</span>' : ''); ?>
                </div>
            <?php endif; ?>

     <?php endif; ?>

      <footer class="entry-footer clearfix <?php echo($prop_term_slug == 'holiday-rental' ? 'entry-footer--holiday' : ''); ?>">
        <div class="clearfix">
            <span class="property-price">
              <?php if( $property_price = get_post_meta($post->ID,'property_price',true) ) : ?>
                <?php echo get_option('colabs_currency_symbol').' '. number_format( $property_price );?> <?php echo($prop_term_slug == 'long-term-rental' ? ' per month' : ''); ?>
              <?php endif; ?>
            </span>
		    <?php echo get_the_term_list($post->ID, 'property_location', '<span class="property-location">', ', ','</span>');?>
        </div>
        <div class="clearfix property-buttons">
            <div class="column col6">
                <a class="button" href="<?php the_permalink(); ?>">View</a>
            </div>
            <div class="column col6">
                <a class="button button-orange" href="<?php echo $enquiry_page_q_vars; ?>">Enquire</a>
            </div>
        </div>
      </footer>
    </div>
</article>
