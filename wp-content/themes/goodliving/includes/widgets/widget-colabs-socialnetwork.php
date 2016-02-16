<?php
/*---------------------------------------------------------------------------------*/
/* Social widget */
/*---------------------------------------------------------------------------------*/
class CoLabs_Social extends WP_Widget {

	function __construct() {
		$widget_ops = array('description' => 'set your social account on your theme panel.' );
		parent::__construct(false, __('Colorlabs - Social', 'colabsthemes'),$widget_ops);      
	}

	/**
	 * Widget Render
	 */
	function widget($args, $instance) {  
		extract( $args );
		$title = apply_filters('widget_title', $instance['title'] ); 
		$title2 = $instance['title2'];
		$feedid = $instance['feedid'];
		$width = $instance['width'];
    $height = $instance['height'];
		$desc = $instance['desc'];
		$subscribe = ! empty( $instance['subscribe'] ) ? '1' : '0';
		$social_link = array(
			'rss'				=> $instance['rss'],
			'twitter'		=> $instance['twitter'],
			'facebook' 	=> $instance['facebook'],
			'pinterest' => $instance['pinterest'],
			'youtube' 	=> $instance['youtube'],
			'linkedin' 	=> $instance['linkedin'],
			'vimeo'			=> $instance['vimeo']
		);
		?>

		<?php echo $before_widget; ?>
			<?php if($title != '')echo $before_title .$title. $after_title;?>
			<div class="social-link clearfix <?php echo $class; ?>">
				<?php foreach( $social_link as $name => $link ) : ?>
					<?php if( $link && '' != $link ) : ?>
						<a href="<?php echo $link; ?>" class="social-<?php echo $name; ?>"><i class="icon-<?php echo $name; ?>"></i></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div><!-- .social-link -->
			<?php
				$fblike_args = 
				array(	
				    'float' => 'left',
				    'url' => site_url(),
				    'showfaces' => 'false',
				    'verb' => 'like',
				    'colorscheme' => 'light',
				    'font' => 'arial'
			    );
		    echo colabs_shortcode_fblike($fblike_args);    
			?>
		<?php echo $after_widget; ?>   
		<?php 
	} // end widget

	/**
	 * Widget Save method
	 */
	function update($new_instance, $old_instance) {                
		return $new_instance;
	}

	/**
	 * Widget Form
	 */
	function form($instance) {        
		$title = esc_attr($instance['title']); 
		$rss = esc_attr($instance['rss']);
		$twitter = esc_attr($instance['twitter']);
		$facebook = esc_attr($instance['facebook']);
		$pinterest = esc_attr($instance['pinterest']);
		$dribbble = esc_attr($instance['dribbble']);
		$youtube = esc_attr($instance['youtube']);
		$linkedin = esc_attr($instance['linkedin']);
		$vimeo = esc_attr($instance['vimeo']);
		$title2 = esc_attr($instance['title2']);
    $feedid = esc_attr($instance['feedid']);
    $width = esc_attr($instance['width']);
		$height = esc_attr($instance['height']);
		$desc = esc_attr($instance['desc']);
		$subscribe = isset( $instance['subscribe'] ) ? (bool) $instance['subscribe'] : false;
		
		if(empty($width)) $width = 300;
		if(empty($height)) $height = 220;
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (optional):','colabsthemes'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('rss'); ?>">Feed URL</label>
		<input type="text" name="<?php echo $this->get_field_name('rss'); ?>" value="<?php echo $rss; ?>" id="<?php echo $this->get_field_id('rss'); ?>" class="widefat">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('twitter'); ?>">Twitter</label>
		<input type="text" name="<?php echo $this->get_field_name('twitter'); ?>" value="<?php echo $twitter; ?>" id="<?php echo $this->get_field_id('twitter'); ?>" class="widefat">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('facebook'); ?>">Facebook</label>
		<input type="text" name="<?php echo $this->get_field_name('facebook'); ?>" value="<?php echo $facebook; ?>" id="<?php echo $this->get_field_id('facebook'); ?>" class="widefat">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('pinterest'); ?>">Pinterest</label>
		<input type="text" name="<?php echo $this->get_field_name('pinterest'); ?>" value="<?php echo $pinterest; ?>" id="<?php echo $this->get_field_id('pinterest'); ?>" class="widefat">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('youtube'); ?>">Youtube</label>		
		<input type="text" name="<?php echo $this->get_field_name('youtube'); ?>" value="<?php echo $youtube; ?>" id="<?php echo $this->get_field_id('youtube'); ?>" class="widefat">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('linkedin'); ?>">LinkedIn</label>
		<input type="text" name="<?php echo $this->get_field_name('linkedin'); ?>" value="<?php echo $linkedin; ?>" id="<?php echo $this->get_field_id('linkedin'); ?>" class="widefat">		
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('vimeo'); ?>">Vimeo</label>
		<input type="text" name="<?php echo $this->get_field_name('vimeo'); ?>" value="<?php echo $vimeo; ?>" id="<?php echo $this->get_field_id('vimeo'); ?>" class="widefat">		
		</p>
	<?php
	}

}

register_widget('CoLabs_Social');
?>