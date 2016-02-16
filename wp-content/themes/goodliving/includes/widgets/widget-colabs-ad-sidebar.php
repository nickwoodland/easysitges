<?php
/*---------------------------------------------------------------------------------*/
/* Adspace Widget */
/*---------------------------------------------------------------------------------*/

class CoLabs_AdWidget extends WP_Widget {

	function __construct() {
		$widget_ops = array('description' => 'A set of advertisements (full width= 200px), use in Sidebar only.' );
		parent::__construct(false, __('Colorlabs - Ads Widget', 'colabsthemes'),$widget_ops);      
	}

	function widget($args, $instance) {
	  extract( $args );
    $title = apply_filters('widget_title', $instance['title'] );
		$adcode = $instance['adcode'];
		$image = $instance['image'];
		$href = $instance['href'];
		$alt = $instance['alt'];
		$width = $instance['width'];
		$height = $instance['height'];
    echo $before_widget;
    if ( empty($title) ){ ?>
    <script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#<?php echo $this->id; ?>').addClass('notitle');
    })
    </script>
    <?php } ?>
    <div class="adspace-widget">
    <?php

		if($title != '')	
      echo $before_title .$title. $after_title;

		if($adcode != ''){
      echo $adcode;
    } else { ?>
		
			<a href="<?php echo $href; ?>"><img src="<?php echo $image; ?>" width="<?php echo $width; ?>px" height="<?php echo $height; ?>px" alt="<?php echo $alt; ?>" /></a>
	
		<?php
		}
		
		echo '</div>';
    echo $after_widget;
	}

	function update($new_instance, $old_instance) {                
		return $new_instance;
	}

	function form($instance) {        
		$title = esc_attr($instance['title']);
		$adcode = esc_attr($instance['adcode']);
		$image = esc_attr($instance['image']);
		$href = esc_attr($instance['href']);
		$alt = esc_attr($instance['alt']);
		if(empty($width)) $width = 200;
		if(empty($height)) $height = 200;
		?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (optional):','colabsthemes'); ?></label>
      <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
    </p>
		<p>
      <label for="<?php echo $this->get_field_id('adcode'); ?>"><?php _e('Ad Code:','colabsthemes'); ?></label>
      <textarea name="<?php echo $this->get_field_name('adcode'); ?>" class="widefat" id="<?php echo $this->get_field_id('adcode'); ?>"><?php echo $adcode; ?></textarea>
    </p>
    <p><strong>or</strong></p>
    <p>
      <label for="<?php echo $this->get_field_id('image'); ?>"><?php _e('Image Url:','colabsthemes'); ?></label>
      <input type="text" name="<?php echo $this->get_field_name('image'); ?>" value="<?php echo $image; ?>" class="widefat" id="<?php echo $this->get_field_id('image'); ?>" />
    </p>
		<p>
      <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Size:','colabsthemes'); ?></label>
      <input type="text" size="2" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $width; ?>" class="" id="<?php echo $this->get_field_id('width'); ?>" /> W
      <input type="text" size="2" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $height; ?>" class="" id="<?php echo $this->get_field_id('height'); ?>" /> H
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('href'); ?>"><?php _e('Link URL:','colabsthemes'); ?></label>
      <input type="text" name="<?php echo $this->get_field_name('href'); ?>" value="<?php echo $href; ?>" class="widefat" id="<?php echo $this->get_field_id('href'); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('alt'); ?>"><?php _e('Alt text:','colabsthemes'); ?></label>
      <input type="text" name="<?php echo $this->get_field_name('alt'); ?>" value="<?php echo $alt; ?>" class="widefat" id="<?php echo $this->get_field_id('alt'); ?>" />
    </p>
    <?php
	}
} 

register_widget('CoLabs_AdWidget');
?>