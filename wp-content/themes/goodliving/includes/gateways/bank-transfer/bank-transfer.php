<?php

/**
 * Payment Gateway for processing payments via Bank Transfer
 * or other manual method
 */
class Colabs_Bank_Transfer_Gateway extends Colabs_Gateway{

	/**
	 * Sets up the gateway
	 */
	public function __construct() {
		parent::__construct( 'bank', array(
			'dropdown' => __( 'Bank Transfer', 'colabsthemes' ),
			'admin' => __( 'Bank Transfer', 'colabsthemes' ),
		) );
    
    $this->has_fields     = false;
    $this->description 		= get_option( 'colabs_bank_description' );
    
	}

	/**
	 * Builds the administration settings form
	 * @return array scbForms style form
	 */
	public function form() {
    $shortname = 'colabs';
    
		$options_gateways[] = array( 
      "name" => __( 'Bank Transfer Options', 'colabsthemes' ),
      "icon" => "general",
      "type" => "heading");	
    
    $options_gateways[] = array( 
      "name" => __( 'Enable Bank Transfer', 'colabsthemes' ),
      "desc" => __('Set this to yes if you want to offer cash payments via bank transfer as a payment option on your site.','colabsthemes'),
      "id" => $shortname."_enable_bank",
      "class" => "collapsed",
      "type" => "checkbox",
      "std" => "false"); 
      
    $options_gateways[] = array( 
        "name" => __( 'Description', 'colabsthemes' ),
        "desc" => __("This controls the description which the user sees during checkout.",'colabsthemes'),
        "id" => $shortname."_bank_description",
        "class" => "hidden",
        "std"   => __('Make your payment directly into our bank account. Please use your Order ID as the payment reference.', 'colabsthemes'),
        "type" => "textarea");
        
    $options_gateways[] = array( 
      "name" => __( 'Wire Instructions', 'colabsthemes' ),
      "desc" => __('Enter your specific bank wire instructions here. HTML can be used.','colabsthemes'),
      "id" => $shortname."_bank_instructions",
      "class" => "hidden last",
      "type" => "textarea");

		return $options_gateways;

	}

	/**
	 * Processes a Bank Transfer Order to display
	 * instructions to the user
	 * @param  Colabs_Order $order   Order to display information for
	 * @param  array $options     User entered options
	 * @return void
	 */
	public function process( $order ) {
    
    // if gateway wasn't selected then exit
    if ( $order['colabs_payment_method'] != 'bank' ) 
      return;
      
    $sent = get_post_meta( $order['order_id'], 'bank-sentemail', true );
		if ( empty( $sent ) ){
			colabs_bank_transfer_pending_email( $order );
			update_post_meta( $order['order_id'], 'bank-sentemail', true );
		}
    
    if ( !empty( $order['post_id'] ) ) {
      $info_message = __('Please include the following details when sending the bank transfer. Once your transfer has been verified, we will then approve your order.', 'colabsthemes');

    }
 
    ?>
    
    <p class="alert alert-success"><?php echo $info_message; ?></p>
    
    <h4><?php _e('Your Transaction Details', 'colabsthemes') ?></h4>

    <ul>
      <li><span><?php _e('Transaction ID:', 'colabsthemes') ?></span><?php echo esc_html( $order['order_id'] ); ?></li>
      <li><span><?php _e('Reference #:', 'colabsthemes') ?></span><?php echo esc_attr( $order['post_id'] ); ?></li>
      <li><span><?php _e('Total Amount:', 'colabsthemes') ?></span><?php echo esc_html( colabs_get_price($order['item_amount']) ); ?></li>
    </ul>

    <h4><?php _e('Bank Transfer Instructions', 'colabsthemes') ?></h4>

    <p><?php echo stripslashes( colabsthemes_nl2br( get_option('colabs_bank_instructions') ) ); ?></p>

    <p><?php _e('For questions or problems, please contact us directly at', 'colabsthemes') ?> <?php echo get_option('admin_email'); ?></p> 
    
    <?php
	}
}

colabs_register_gateway( 'Colabs_Bank_Transfer_Gateway' );
