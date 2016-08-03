<?php
/**
 * Sidebar User Template
 */


$layout = get_option('colabs_layout');
if(get_post_meta($post->ID,'layout',true))$layout = get_post_meta($post->ID,'layout',true);
if( 'colabs-one-col' != $layout ):
?>
	<div class="property-sidebar sidebar column col3">
		<?php 
    $current_user = wp_get_current_user();
    $display_user_name = $current_user->display_name;

		// calculate the total count of live ads for current user
		$rows = $wpdb->get_results( $wpdb->prepare( "
			SELECT post_status, COUNT(ID) as count
			FROM $wpdb->posts
			WHERE post_author = %d
			AND post_type = 'property'
			GROUP BY post_status", $current_user->ID
		) );

		$stats = array();
		foreach ( $rows as $row )
			$stats[ $row->post_status ] = $row->count;

		$post_count_live = (int) @$stats['publish'];
		$post_count_pending = (int) @$stats['pending'];
		$post_count_offline = (int) @$stats['draft'];
		$post_count_total = $post_count_live + $post_count_pending + $post_count_offline;
		$date_format = get_option('date_format') . ' ' . get_option('time_format');
		$the_reg_date = date_i18n($date_format, strtotime($current_user->user_registered));
    ?>
		<aside class="widget widget_user_profile">
      <?php if ( !is_user_logged_in() ) : ?>
				<p>
					<a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=register" class="button"><?php _e('Join Now', 'colabsthemes') ?></a>
					<span>or</span>
          <a href="<?php echo get_option('siteurl'); ?>/wp-login.php" class="button"><?php _e('Log In', 'colabsthemes') ?></a>
        </p>  
      <?php else: ?>
        <h4 class="widget-title"><span><?php _e('Your Profile', 'colabsthemes');?></span></h4>

        <div class="user-profile-wrapper">
	        <div class="avatar"><?php echo get_avatar($current_user->user_email, 100);?></div>
					<div class="user">
					
	        <p class="welcome-back"><?php _e('Welcome,','colabsthemes'); ?> <a href="<?php echo get_author_posts_url($current_user->ID); ?>"><strong><?php echo $display_user_name; ?></strong></a></p>
	        <ul class="user-info">	
						<li><?php _e('Member Since:','colabsthemes')?> <strong><?php echo $the_reg_date; ?></strong></li>
						<li><?php _e('Live Property:','colabsthemes')?> <strong><?php echo $post_count_live; ?></strong></li>
						<li><?php _e('Pending Property:','colabsthemes')?> <strong><?php echo $post_count_pending; ?></strong></li>
						<li><?php _e('Offline Property:','colabsthemes')?> <strong><?php echo $post_count_offline; ?></strong></li>
						<li><?php _e('Total Property:','colabsthemes')?> <strong><?php echo $post_count_total; ?></strong></li>  
	        </ul>
					<?php if(get_option('colabs_submit_url')!=''){?>
					<p class="submit-property"><a class="button button-bold" href="<?php echo get_permalink(get_option('colabs_submit_url'));?>"><?php _e('Submit Property', 'colabsthemes') ?></a></p>
					<?php }?>

					<?php if( get_option('colabs_bookmark_property') ) : ?>
						<p class="my-bookmark">
							<a href="<?php echo get_permalink( get_option( 'colabs_bookmark_property' ) ); ?>" class="button button-bold"><?php _e('My Bookmarks', 'colabsthemes'); ?></a>
						</p>
					<?php endif; ?>

		      </div><!-- /user --> 
	      </div><!-- .user-profile-wrapper -->
	    <?php endif; ?>
    </aside>
		<aside class="widget widget_user_option">
      <h4 class="widget-title"><span><?php _e('User Options', 'colabsthemes');?></span></h4>
			<ul class="users-listing">
			<?php if ( is_user_logged_in() ) : 
			$logout_url = wp_logout_url( home_url() );
			?>
				<li><a href="<?php echo CL_DASHBOARD_URL ?>"><?php _e('My Dashboard','colabsthemes')?></a></li>
				<li><a href="<?php echo CL_PROFILE_URL ?>"><?php _e('Edit Profile','colabsthemes')?></a></li>
				<?php if (current_user_can('edit_others_posts')) { ?><li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/"><?php _e('WordPress Admin','colabsthemes')?></a></li><?php } ?>
				<li><a href="<?php echo $logout_url; ?>"><?php _e('Log Out','colabsthemes')?></a></li>
				
			<?php else: ?>
				<li><?php _e('Welcome,','colabsthemes'); ?> <strong><?php _e('visitor!','colabsthemes'); ?></strong></li>
							<li><a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=register"><?php _e('Register','colabsthemes'); ?></a></li>
							<li><a href="<?php echo get_option('siteurl'); ?>/wp-login.php"><?php _e('Log in','colabsthemes'); ?></a></li>
			<?php endif; ?>
			</ul>
    </aside>
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-user') ) :  ?>
			
		<?php endif; ?>
	</div>
<!-- .sidebar -->
<?php endif;?>

