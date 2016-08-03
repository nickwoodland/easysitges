<?php
/*
Template Name: Bookmark
*/
nocache_headers();
colabs_auth_redirect_login(); // if not logged in, redirect to login page
?>			

<?php get_header(); ?>
<?php colabs_breadcrumbs(array('separator' => '&mdash;', 'before' => ''));?><!-- .colabs-breadcrumbs -->
<?php 
	if($success==true){
		echo '<div class="alert alert-success">'.__('Successfully Removed','colabsthemes').'</div>';
	}
?>        
<div class="main-content column col9">
	
  <article <?php post_class('single-entry-post');?>>
    <header class="entry-header">
      <h2 class="entry-title"><?php the_title();?></h2>
    </header>

    <div class="property-details">

      <div class="property-details-panel entry-content">
        <?php
					global $wpdb;
					$current_user = wp_get_current_user();
					$sql ="SELECT * FROM ". $wpdb->prefix . "colabs_bookmarks WHERE user_id=$current_user->ID";
					$results = $wpdb->get_results($sql);
					if (sizeof($results)>0) :
				?>
				<table cellspacing="0" class="bookmark-table table-my-property">
				<thead>
					<tr>
						<th class="property-remove"></th>
						<th class="property-name" colspan="2"><?php _e('Property', 'colabsthemes'); ?></th>
						<th class="property-price"><?php _e('Price', 'colabsthemes'); ?></th>
						<th class="property-price"><?php _e('Status', 'colabsthemes'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php		 
						foreach ($results as $values) :
							$id = $values->post_id;
							$address = get_post_meta($id , 'property_address',true);
							$citystate = get_post_meta($id , 'property_citystate',true);
							?>
							<tr class="item_property_bookmark" id="bookmark-row-<?php echo $id; ?>">
							<td class="item-remove"><a href="javascript:void(0)" onClick="remove_item_from_bookmark(bookmark_ajax_web_url,<?php echo $values->ID;?>, <?php echo $id; ?>);" class="remove" title="<?php _e('Remove this item','colabsthemes'); ?>">&times;</a></td>
							<td class="aligncenter"><?php colabs_image('width=100&height=100&single=true&id='.$id);?></td>
							<td class="center"><h5><a href="<?php echo get_permalink($id);?>"><?php echo get_the_title($id);?></a></h5><p><?php echo get_the_term_list($id, 'property_type', '', ', ', ''); ?></p><p><?php echo $address.' '.$citystate; ?></p></td>
							<td class="item-price"><?php echo get_option('colabs_currency_symbol').' '.get_post_meta($id,'property_price',true);?></td>
							<td><span class="item-label"><?php echo get_the_term_list($id, 'property_status', '', ', ','');?></span></td>
							</tr>
							<?php
						endforeach;						
					?>
				</tbody>
				</table>
				<?php else:?>
					<p><?php _e('No property were added to bookmark list','colabsthemes'); ?></p>
				<?php endif;?>
      </div>

    </div><!-- .property-details -->

  </article><!-- .single-entry-post -->

</div><!-- .main-content -->

<?php get_sidebar('user');?><!-- .property-sidebar -->
<?php get_footer(); ?>