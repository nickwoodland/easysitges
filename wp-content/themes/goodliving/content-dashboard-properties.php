<?php
$publish_args = array(
  'ignore_sticky_posts' => true,
  'posts_per_page' => -1,
  'author' => get_current_user_id(),
  'post_type' => COLABS_POST_TYPE,
  'post_status' => array('publish', 'pending', 'draft', 'expired'),
);
$publish_query = new WP_Query($publish_args);
if ($publish_query->have_posts()) :
?>
<table class="table-my-property">
  <thead>
    <tr>
      <th class="text-center" colspan="2"><?php _e('Title','colabsthemes');?></th>
      <th class="text-center" width="120px"><?php _e('Status','colabsthemes');?></th>
      <th class="text-center" width="120px"><?php _e('Expired','colabsthemes');?></th>
      <th width="90px"><?php _e('Actions','colabsthemes');?></th>
    </tr>
  </thead>

  <tbody>
    <?php 
    while ($publish_query->have_posts()) : $publish_query->the_post(); 
    $pending_payment = colabs_get_pending_payment($post->ID);
    $expire_date = '';
    $poststatus = colabs_get_post_status( $post );

    global $wpdb;   
    $total_cost = $wpdb->get_var( $wpdb->prepare( "SELECT cost FROM ".$wpdb->prefix."colabs_orders WHERE property_id=%d;", $post->ID ) );
    ?> 
    <tr>
      <td class="property-col-image text-center"><?php colabs_image('key=property_image&width=100&height=100');?></td>
      
      <td class="property-col-title">
        <h5>
          <?php if ($post->post_status == 'pending' || $post->post_status == 'draft' || $poststatus == 'ended' || $poststatus == 'offline') { ?>
            <?php the_title(); ?>
          <?php } else { ?>
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          <?php } ?>    
        </h5>
        <div class="meta"><span class="folder"><?php echo get_the_term_list( $post->ID, COLABS_TAX_TYPE, '', ', ' ); ?></span><span class="clock"><span><?php the_time(get_option('date_format'))?></span></span></div>
      </td>

      <td class="property-col-status text-center ads-status" data-title="<?php _e('Status', 'colabsthemes'); ?>">
        <h6 class="property-status">
        <?php
        $post_status = colabs_get_post_status( $post );

        if ( $order_status = colabs_get_post_order_status( $post->ID, $pending_payment ) ){
          echo $order_status;
        }else{
          echo $post_status;
        }
        ?>
        </h6>
      </td>

      <td class="property-col-expired text-center">
        <h6><?php echo colabs_remaining_days( $post->ID ); ?></h6>
      </td>

      <td class="property-col-actions ads-actions text-center">
        <?php 
        // Active Property
        if ( get_post_status ( $post->ID ) == 'publish' ) :?>
          <?php if (get_option('colabs_allow_editing')=='true') : ?>
            <a class="edit-property" href="<?php echo add_query_arg( array( 'property_edit' => $post->ID ), COLABS_EDIT_PAGE );?>" title="<?php _e('Edit Property', 'colabsthemes'); ?>"><i class="icon-edit"></i></a>&nbsp;&nbsp;
          <?php endif; ?>
          <a class="delete-property" onclick="return confirm_before_delete()" href="<?php echo COLABS_DASHBOARD_PAGE; ?>?property_delete=<?php the_id(); ?>" title="<?php _e('Delete Property', 'colabsthemes'); ?>"><i class="icon-trash"></i></a>&nbsp;&nbsp;
          <a class="" href="<?php echo add_query_arg( array( 'property_end' => $post->ID, 'confirm' => true ), COLABS_DASHBOARD_PAGE );?>" title="<?php _e('Stop list this property', 'colabsthemes'); ?>">
            <i class="icon-stop"></i>
          </a>
            
          <?php
          // Mark sold
          if ( get_post_meta( $post->ID, 'colabs_property_sold', true) != 'true' ) : ?>
            <a class="button button-bold mark-property" href="<?php echo COLABS_DASHBOARD_PAGE; ?>?property_marksold=<?php echo $post->ID; ?>"><?php _e('Mark Sold', 'colabsthemes'); ?></a>
          <?php else : ?>
            <a class="button button-bold mark-property" href="<?php echo COLABS_DASHBOARD_PAGE; ?>?property_unsold=<?php echo $post->ID; ?>"><?php _e('Unmark Sold', 'colabsthemes'); ?></a>
          <?php endif; ?>

        <?php 
        // Pending
        elseif (( get_post_status ( $post->ID ) == 'pending' )||( get_post_status ( $post->ID ) == 'draft' )):?>
          <?php
          $string = '';
          if ( ! isset( $pending_payment[ $post->ID ] ) ) {
            $string =  __( 'Continue', 'colabsthemes' );
            $pending_args = array( 'property_id' => $post->ID );
          }elseif ( ! empty( $pending_payment[ $post->ID ]) && 'undecided' == $pending_payment[ $post->ID ]['status'] ) {
            if ( colabs_charge_listings() ) {
              $string =  __( 'Pay', 'colabsthemes' );
              $pending_args = array( 'property_id' => $post->ID, 'order_id' => $pending_payment[$post->ID]['order_id'], 'step' => 3 );
            }else{
              $string =  __( 'Continue', 'colabsthemes' );
              $pending_args = array( 'property_id' => $post->ID );
            }
          }
          if( '' != $string ):
          ?>
            <a class="button button-primary button-bold button-block" href="<?php echo add_query_arg( $pending_args, COLABS_SUBMIT_PAGE );?>">
              <i class="icon-play"></i>&nbsp;<?php echo $string; ?>
            </a>
          <?php endif;?>
            <a class="button button-red button-bold button-block" href="<?php echo add_query_arg( array( 'property_end' => $post->ID, 'cancel' => true, 'confirm' => true ), COLABS_DASHBOARD_PAGE );?>">
              <i class="icon-stop"></i>&nbsp;<?php _e('Cancel','colabsthemes'); ?>
            </a>

        <?php 
        // Expired property
        elseif ( get_post_status ( $post->ID ) == 'expired' ) : 
          $canceled_property = get_post_meta( $post->ID, '_colabs_canceled_property', true );
          if ( $canceled_property ) : ?>
            <a class="button button-bold button-green" href="<?php echo add_query_arg( array( 'property_id' => $post->ID ), COLABS_SUBMIT_PAGE );?>">
              <i class="icon-play"></i>&nbsp;<?php _e('Continue','colabsthemes'); ?>
            </a>
          <?php else : ?>
            <?php if ( colabs_allow_relist() ): ?>
              <a class="button button-bold button-red" href="<?php echo add_query_arg( array( 'property_relist' => $post->ID ), COLABS_SUBMIT_PAGE );?>">
                <i class="icon-play"></i>&nbsp;<?php _e('Relist','colabsthemes'); ?>
              </a>
            <?php endif; ?>
          <?php endif; ?>
        <?php endif; ?>
      </td>
    </tr>
    <?php endwhile;?>
  </tbody>

  <tfoot>
    <tr>
      <th class="text-center" colspan="2"><?php _e('Title','colabsthemes');?></th>
      <th class="text-center" width="120px"><?php _e('Status','colabsthemes');?></th>
      <th class="text-center" width="120px"><?php _e('Expired','colabsthemes');?></th>
      <th width="90px"><?php _e('Actions','colabsthemes');?></th>
    </tr>
  </tfoot>
</table>
<?php else:?>
  <p><?php _e('You currently have no property.','colabsthemes');?></p>
<?php endif;?>

<script type="text/javascript">
  /* <![CDATA[ */
  function confirm_before_delete() { return confirm("<?php _e('Are you sure you want to delete this property?', 'colabsthemes'); ?>"); }
  /* ]]> */
</script> 