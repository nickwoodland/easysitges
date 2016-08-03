<?php
$args = array(
  'ignore_sticky_posts' => true,
  'author'        => get_current_user_id(),
  'post_type'       => COLABS_ORDER_POST_TYPE,
  'posts_per_page'    => -1,
);
if ( get_query_var('order_status') ) {
  $args['post_status'] = get_query_var('order_status');
}

$orders = new WP_Query($args);

if ( $orders->have_posts() || get_query_var('order_status') ):?>
  <p><?php _e( 'Below is your Order history. You can use the available filter to filter the results.', 'colabsthemes'); ?></p>
  
  <form class="filter" method="get" action="<?php echo esc_url( COLABS_DASHBOARD_PAGE ) ?>" >
    <input type="hidden" name="tab" value="orders" />
    <?php
    $statuses = array(
      COLABS_ORDER_PENDING => __( 'Pending', 'colabsthemes' ),
      COLABS_ORDER_FAILED => __( 'Failed', 'colabsthemes' ),
      COLABS_ORDER_COMPLETED => __( 'Completed', 'colabsthemes' ),
      COLABS_ORDER_ACTIVATED => __( 'Activated', 'colabsthemes' ),
    );
    foreach ( $statuses as $order_status => $name ) {
      $checked = (bool) ( ! get_query_var('order_status') || in_array( $order_status, get_query_var('order_status') ) );  ?>
      <label class="custom-checkbox">
        <input type="checkbox" name="order_status[]" value="<?php echo esc_attr($order_status); ?>" <?php echo ( $checked ? 'checked="checked"' : '' ) ?> />
        <?php echo $name; ?>
      </label>
    <?php
    }
    ?>
    <br>
    <input type="submit" value="<?php esc_attr_e( 'Filter', 'colabsthemes' ); ?>" class="button button-small">

    <?php if ( get_query_var('order_status') ) { ?>
      <a class="button button-lightred button-small button-uppercase button-bold" href="<?php echo esc_url( COLABS_DASHBOARD_PAGE ); ?>"><?php _e( 'Remove Filters', 'colabsthemes' ); ?></a>
    <?php } ?>
  </form>
  
  <table cellpadding="0" cellspacing="0" class="data_list footable dashed-table">
    <thead>
      <tr>
        <th data-class="expand"><?php _e('ID','colabsthemes'); ?></th>
        <th class="center" data-hide="phone"><?php _e('Date','colabsthemes'); ?></th>
        <th class="left" data-hide="phone"><?php _e('Order Summary','colabsthemes'); ?></th>
        <th class="center" data-hide="phone"><?php _e('Price','colabsthemes'); ?></th>
        <th class="center" data-hide="phone"><?php _e('Payment/Status','colabsthemes'); ?></th>
        <th class="right" data-hide="phone"><?php _e('Actions','colabsthemes'); ?></th>
      </tr>
    </thead>
    <tbody>

    <?php if ( $orders->have_posts() ) : ?>

      <?php while ( $orders->have_posts() ) : $orders->the_post(); ?>

        <?php 
          $order = colabs_get_order( $orders->post->ID );
        ?>
          <tr>
            <td class="order-history-id" data-title="<?php echo 'ID'; ?>">#<?php the_ID(); ?></td>
            <td class="date" data-title="<?php _e('Date', 'colabsthemes'); ?>"><strong><?php the_time(__('j M','colabsthemes')); ?></strong> <span class="year"><?php the_time(__('Y','colabsthemes')); ?></span></td>
            <td class="order-history-summary left" data-title="<?php _e('Order Summary', 'colabsthemes'); ?>">
              <span class="order-history-property">
                <?php 
                $post_id = $order->get_post_type_id();
                if( $post_id ) {
                  $title = get_the_title( $post_id );

                  $html = html( 'a', array( 'href' => get_permalink( $post_id ) ), $title );
                    echo $html;
                }
                ?>
              </span>
              <?php 
              if(is_sticky($post_id)):
                echo '<div>'.__('Featured','colabsthemes').'</div>';
              endif;
              ?>
            </td>
            <td class="order-history-price center" data-title="<?php _e('Price', 'colabsthemes'); ?>"><?php echo colabs_get_price( $order->get_total() ); ?></td>
            <td class="order-history-payment center" data-title="<?php _e('Payment/Status', 'colabsthemes'); ?>"><?php the_orders_history_payment( $order ); ?></td>
            <td class="actions center" data-title="<?php _e('Actions', 'colabsthemes'); ?>">
            <?php
              if ( ! empty($order) && COLABS_ORDER_PENDING == $order->get_status() && ! $order->get_gateway() ) {
                $pending_args = array( 'property_id' => $post->ID, 'order_id' => $order->get_id(), 'step' => 3 );
                ?>
                <a class="button button-uppercase button-bold button-primary button-mini" href="<?php echo add_query_arg( $pending_args, COLABS_SUBMIT_PAGE );?>">
                  <?php _e('Pay','colabsthemes');?>
                </a>
                <?php
              }

              if ( COLABS_ORDER_PENDING == $order->get_status() ) {
                ?>
                <a class="button button-uppercase button-bold button-red button-mini cancel-order" href="<?php echo add_query_arg( array( 'order_cancel' => $order->get_id(), 'confirm_order_cancel' => 'true' ), COLABS_DASHBOARD_PAGE );?>">
                  <?php _e('Cancel','colabsthemes');?>
                </a>
                <?php
              }
            ?>
            </td>
          </tr>

      <?php endwhile; ?>

    <?php else: ?>
      <tr>
        <td colspan="7"><?php _e( 'No Orders found.', 'colabsthemes' ); ?></td>
      </tr>
    <?php endif; ?>

    </tbody>
  </table>

<?php else: ?>
  <p><?php _e( 'You don\'t have any Orders, yet.', 'colabsthemes' ); ?></p>
<?php endif; ?>