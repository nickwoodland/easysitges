<?php 
if ( isset( $_POST['action'] ) && 'payment-process' == $_POST['action'] ){
  if ( !empty($_POST['order_id']) ) {
		$order_id = intval($_POST['order_id']);
		$order = colabs_get_order( $order_id );
	}
  
  if( isset( $_GET['cancel'] ) ){
		$order->clear_gateway();
	}

	$get_gateway = $order->get_gateway();
	if ( !empty( $_POST['colabs_payment_method'] ) && empty( $get_gateway ) ) {
		$order->set_gateway( $_POST['colabs_payment_method'] );
	}

  $gateway_id = $_POST['colabs_payment_method'];
  
  $gateway = Colabs_Gateway_Registry::get_gateway( $gateway_id );
    
  if( Colabs_Gateway_Registry::is_gateway_enabled( $gateway_id )){
    $receipt_order['order_id'] = $order->get_id();
    $receipt_order['post_id'] = $order->get_post_type_id();
    $receipt_order['item_name'] = get_the_title($order->get_post_type_id());
    $receipt_order['item_amount'] = $order->get_total();
    $receipt_order['colabs_payment_method'] = $gateway_id;
    $gateway->process( $receipt_order);
  }

}else{
  
  the_order_summary(); 
  $orders = colabs_get_order($order_id);
  if($orders->get_total() > 0):?>
    <form action="" method="POST" class="payment-form">
      <p><?php _e( 'Please select a method for processing your payment:', 'colabsthemes' ); ?></p>
      <input type="hidden" name="action" value="payment-process" />
      <input type="hidden" name="referer" value="<?php echo esc_url( get_query_var('referer') ); ?>" />
      <input type="hidden" name="order_id" value="<?php echo $order_id;?>" />
      <input type="hidden" name="step" value="<?php echo $step;?>" />
      <?php colabs_list_gateway_dropdown(); ?>
      <input class="button button-primary" name="submitted" type="submit" value="<?php _e('Continue to Pay','colabsthemes');?>">
    </form>
  <?php
  else:
    $redirect_to = colabs_get_redirect_to_url( $orders );
    ?>
    <h5><?php _e('Thank You','colabsthemes');?></h5>
    <p><?php _e('Your order has been completed.','colabsthemes');?></p>
    <form class="main_form">
			<p><input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Continue', 'colabsthemes' ); ?>" onClick="location.href='<?php echo $redirect_to; ?>';return false;"></p>
		</form>
  <?php
  endif; 
}?>
