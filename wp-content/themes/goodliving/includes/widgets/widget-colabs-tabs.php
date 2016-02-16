<?php
/*---------------------------------------------------------------------------------*/
/* CoLabsTabs widget */
/*---------------------------------------------------------------------------------*/

class CoLabs_Tabs extends WP_Widget {

	function __construct() {
		$widget_ops = array('description' => 'Tabbed lists containing popular posts, latest posts, recent comments and a tag cloud. Use in Sidebar only.' );
		parent::__construct(false, __('Colorlabs - Tabs', 'colabsthemes'), $widget_ops);    
	}

	/**
	 * Widget Render
	 */
	function widget($args, $instance) {        
		extract( $args );
		
		$number = $instance['number']; if ($number == '') $number = 5;
		$thumb_size = $instance['thumb_size']; if ($thumb_size == '') $thumb_size = 35;
		$order = $instance['order']; if ($order == '') $order = "pop";
		$pop = ''; if ( array_key_exists( 'pop', $instance ) ) $pop = $instance['pop'];
		$latest = ''; if ( array_key_exists( 'latest', $instance ) ) $latest = $instance['latest'];
		$comments = ''; if ( array_key_exists( 'comments', $instance ) ) $comments = $instance['comments'];
		$tags = ''; if ( array_key_exists( 'tags', $instance ) ) $tags = $instance['tags'];
			 
		echo $before_widget; ?>  
		
			<div id="tabs">

				<ul class="colabsTabs clearfix">
					<?php if ( $order == "latest" && !$latest == "on") { ?>
						<li class="latest"><a href="#tab-latest">
							<i class="icon-th-list"></i>
							<span><?php _e('Latest', 'colabsthemes'); ?></span>
						</a></li>
					<?php } elseif ( $order == "comments" && !$comments == "on") { ?>
						<li class="comments"><a href="#tab-comm">
							<i class="icon-comments"></i>
							<span><?php _e('Comments', 'colabsthemes'); ?></span>
						</a></li>
					<?php } elseif ( $order == "tags" && !$tags == "on") { ?>
						<li class="tags"><a href="#tab-tags">
							<i class="icon-tags"></i>
							<span><?php _e('Tags', 'colabsthemes'); ?></span>
						</a></li>
					<?php } ?>

					<?php if (!$pop == "on") { ?>
						<li class="popular"><a href="#tab-pop">
							<i class="icon-heart"></i>
							<span><?php _e('Popular', 'colabsthemes'); ?></span>
						</a></li>
					<?php } ?>

					<?php if ($order <> "latest" && !$latest == "on") { ?>
						<li class="latest"><a href="#tab-latest">
							<i class="icon-th-list"></i>
							<span><?php _e('Latest', 'colabsthemes'); ?></span>
						</a></li>
					<?php } ?>

					<?php if ($order <> "comments" && !$comments == "on") { ?>
						<li class="comments"><a href="#tab-comm">
							<i class="icon-comments"></i>
							<span><?php _e('Comments', 'colabsthemes'); ?></span>
						</a></li>
					<?php } ?>

					<?php if ($order <> "tags" && !$tags == "on") { ?>
						<li class="tags"><a href="#tab-tags">
							<i class="icon-tags"></i>
							<span><?php _e('Tags', 'colabsthemes'); ?></span>
						</a></li>
					<?php } ?>
				</ul>
				<div class="clear"></div>
						
				<div class="boxes box inside">
												
					<?php if ( $order == "latest" && !$latest == "on") { ?>
						<ul id="tab-latest" class="list">
							<?php $this->colabs_tabs_latest($number, $thumb_size); ?>                    
						</ul>
					<?php } elseif ( $order == "comments" && !$comments == "on") { ?>
						<ul id="tab-comm" class="list">
							<?php $this->colabs_tabs_comments($number, $thumb_size); ?>
						</ul>
					<?php } elseif ( $order == "tags" && !$tags == "on") { ?>
						<div id="tab-tags" class="list">
							<?php wp_tag_cloud('smallest=12&largest=20'); ?>
						</div>
					<?php } ?>
					
					<?php if (!$pop == "on") { ?>
						<ul id="tab-pop" class="list">            
							<?php $this->colabs_tabs_popular( $number, $thumb_size ); ?>
						</ul>
					<?php } ?>

					<?php if ($order <> "latest" && !$latest == "on") { ?>
						<ul id="tab-latest" class="list">
							<?php $this->colabs_tabs_latest($number, $thumb_size); ?>                    
						</ul>
					<?php } ?>

					<?php if ($order <> "comments" && !$comments == "on") { ?>
						<ul id="tab-comm" class="list">
							<?php $this->colabs_tabs_comments($number, $thumb_size); ?>                    
						</ul>                
					<?php } ?>
		
					<?php if ($order <> "tags" && !$tags == "on") { ?>
						<div id="tab-tags" class="list">
							<?php wp_tag_cloud('smallest=12&largest=20'); ?>
						</div>                
					<?php } ?>

				</div><!-- .boxes -->
			
			</div><!-- #tabs -->
		
			<?php echo $after_widget;
	}

	/**
	 * Widget Save Method
	 */
	function update($new_instance, $old_instance) {                
		return $new_instance;
	}

	/**
	 * Widget Form
	 */
	function form($instance) {
		$number = esc_attr($instance['number']);
		$thumb_size = esc_attr($instance['thumb_size']);
		$order = esc_attr($instance['order']);
		$pop = esc_attr($instance['pop']);
		$latest = esc_attr($instance['latest']); 
		$comments = esc_attr($instance['comments']);
		$tags = esc_attr($instance['tags']);

		?>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts:','colabsthemes'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
			</label>
		</p>  

		

		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('First Visible Tab:','colabsthemes'); ?></label>
			<select name="<?php echo $this->get_field_name('order'); ?>" class="widefat" id="<?php echo $this->get_field_id('order'); ?>">
					<option value="pop" <?php if($order == "pop"){ echo "selected='selected'";} ?>><?php _e('Popular', 'colabsthemes'); ?></option>
					<option value="latest" <?php if($order == "latest"){ echo "selected='selected'";} ?>><?php _e('Latest', 'colabsthemes'); ?></option>
					<option value="comments" <?php if($order == "comments"){ echo "selected='selected'";} ?>><?php _e('Comments', 'colabsthemes'); ?></option>
					<option value="tags" <?php if($order == "tags"){ echo "selected='selected'";} ?>><?php _e('Tags', 'colabsthemes'); ?></option>
			</select>
		</p>

		<p><strong>Hide Tabs:</strong></p>
		
		<p>
			<input id="<?php echo $this->get_field_id('pop'); ?>" name="<?php echo $this->get_field_name('pop'); ?>" type="checkbox" <?php if($pop == 'on') echo 'checked="checked"'; ?>><?php _e('Popular', 'colabsthemes'); ?></input>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id('latest'); ?>" name="<?php echo $this->get_field_name('latest'); ?>" type="checkbox" <?php if($latest == 'on') echo 'checked="checked"'; ?>><?php _e('Latest', 'colabsthemes'); ?></input>
		</p>
		
		<p>
			<input id="<?php echo $this->get_field_id('comments'); ?>" name="<?php echo $this->get_field_name('comments'); ?>" type="checkbox" <?php if($comments == 'on') echo 'checked="checked"'; ?>><?php _e('Comments', 'colabsthemes'); ?></input>
		</p>
		
		<p>
			<input id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>" type="checkbox" <?php if($tags == 'on') echo 'checked="checked"'; ?>><?php _e('Tags', 'colabsthemes'); ?></input>
		</p>  
		<?php 
	}

	/**
	 * Tabs Popular
	 */
	function colabs_tabs_popular( $posts = 5, $size = 35 ) {
		global $post;
		$i = 1;
		$args=array(
			'post_type' => array('post','review'),
			'post_status' => 'publish',
			'showposts' => $posts,
			'orderby' => 'comment_count',
			'caller_get_posts'=> 1
		);
		$popular = get_posts($args);
		foreach($popular as $post) :
			setup_postdata($post); ?>
		<li>
			<div class="tabs-content">
				<?php colabs_image('width=50&height=50'); ?>
				<div class="tabs-text">
					<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
					<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
				</div>
			</div>
		</li>
		<?php $i++; endforeach;
	}

	/**
	 * Tabs Latest
	 */
	function colabs_tabs_latest( $posts = 5, $size = 35 ) {
		global $post;
		$args=array(
			'post_type' => array('post','review'),
			'post_status' => 'publish',
			'showposts' => $posts,
			'orderby' => 'post_date',
			'order'=> 'desc',
			'caller_get_posts'=> 1
		);
		$latest = get_posts($args);
		foreach($latest as $post) :
			setup_postdata($post); ?>
		<li>
			<div class="tabs-content">
				<?php colabs_image('width=50&height=50'); ?>
				<div class="tabs-text">
					<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
					<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
				</div>
			</div>
		</li>
		<?php endforeach; 
	}

	/**
	 * Tabs Comments
	 */
	function colabs_tabs_comments( $posts = 5, $size = 35 ) {
		global $wpdb;
		$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
						comment_post_ID, comment_author, comment_author_email, comment_date_gmt, comment_approved,
						comment_type,comment_author_url,
						SUBSTRING(comment_content,1,50) AS com_excerpt
						FROM $wpdb->comments
						LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
						$wpdb->posts.ID)
						WHERE comment_approved = '1' AND comment_type = '' AND
						post_password = ''
						ORDER BY comment_date_gmt DESC LIMIT ".$posts;
		$comments = $wpdb->get_results($sql);
		foreach ($comments as $comment) { ?>
		<li>
			<?php echo get_avatar( $comment, $size ); ?>
			<div class="tabs-content">
				<a href="<?php echo get_permalink($comment->ID); ?>#comment-<?php echo $comment->comment_ID; ?>" title="<?php _e('on ', 'colabsthemes'); ?> <?php echo $comment->post_title; ?>">
				<span class="author"><?php echo strip_tags($comment->comment_author); ?></span></a><span class="comment"><?php echo strip_tags($comment->com_excerpt); ?>...</span>
			</div>
		</li>
		<?php
		}
	}

} 
register_widget('CoLabs_Tabs');

// Add Javascript
if(is_active_widget( null,null,'colabs_tabs' ) == true) {
	add_action('wp_footer','colabs_widget_tabs_js');
}

function colabs_widget_tabs_js(){
?>
<!-- CoLabs Tabs Widget -->
<script type="text/javascript">
jQuery(document).ready(function(){
	// UL = .colabsTabs
	// Tab contents = .inside
	
	var tag_cloud_class = '#tagcloud'; 
	
	//Fix for tag clouds - unexpected height before .hide() 
	var tag_cloud_height = jQuery('#tagcloud').height();
	
	//jQuery('.inside ul li:last-child').css('border-bottom','0px'); // remove last border-bottom from list in tab content
	jQuery('.colabsTabs').each(function(){
		jQuery(this).children('li').children('a:first').addClass('selected'); // Add .selected class to first tab on load
	});
	jQuery('.inside > *').hide();
	jQuery('.inside > *:first-child').show();
	
	jQuery('.colabsTabs li a').click(function(evt){ // Init Click funtion on Tabs
	
		var clicked_tab_ref = jQuery(this).attr('href'); // Strore Href value
		
		jQuery(this).parent().parent().children('li').children('a').removeClass('selected'); //Remove selected from all tabs
		jQuery(this).addClass('selected');
		jQuery(this).parent().parent().parent().children('.inside').children('*').hide();
		
		jQuery('.inside ' + clicked_tab_ref).fadeIn(500);
		 
		 evt.preventDefault();
	
	})
})
</script>
<?php
}

?>