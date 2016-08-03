<?php
require dirname( __FILE__ ) . '/paypal-bridge.php';
require dirname( __FILE__ ) . '/paypal-notifier.php';
require dirname( __FILE__ ) . '/paypal-pdt.php';
require dirname( __FILE__ ) . '/paypal-ipn-listener.php';
require dirname( __FILE__ ) . '/paypal-form.php';

/**
 * Payment Gateway to process PayPal Payments
 */
class Colabs_Paypal_Gateway extends Colabs_Gateway{
  
  private static $urls = array(
		'https' => array(
			'sandbox' => 'https://www.sandbox.paypal.com/cgi-bin/webscr',
			'live' => 'https://www.paypal.com/cgi-bin/webscr'
		),
		'ssl' => array(
			'sandbox' => 'ssl://www.sandbox.paypal.com',
			'live' => 'ssl://www.paypal.com'
		)
	);
  
	/**
	 * Sets up the gateway
	 */
	public function __construct() {
		parent::__construct( 'paypal', array(
			'dropdown' => __( 'PayPal', 'colabsthemes' ),
			'admin' => __( 'PayPal', 'colabsthemes' ),
		) );
    $this->has_fields     = false;
    $this->description 		= get_option( 'colabs_paypal_description' );
    
    add_action( 'init', array( $this, 'register') );
    //$this->bridge = new Colabs_PayPal_Bridge;
    
    add_action('init',array($this,'colabs_listing_handle_payment_return'));
	}
  
  public function register(){

		if( ! Colabs_Gateway_Registry::is_gateway_enabled( 'paypal' ) )
			return;

		$ipn_enabled = get_option('colabs_enable_paypal_ipn');
		if( 'true' == $ipn_enabled  )
			$this->listener = new Colabs_PayPal_IPN_Listener( array( 'Colabs_PayPal_Notifier', 'handle_response' ) );

	}

	/**
	 * Builds the administration settings form
	 */
	public function form() {
    $shortname = 'colabs';
    
		$options_gateways[] = array( 
        "name" => __( 'PayPal Options', 'colabsthemes' ),
        "icon" => "general",
        "type" => "heading");
        
    $options_gateways[] = array( 
        "name" => __( 'Enable PayPal', 'colabsthemes' ),
        "desc" => sprintf( __("You must have a <a target='_new' href='%s'>PayPal</a> account setup before using this feature.",'colabsthemes'), 'http://www.paypal.com/' ),
        "id" => $shortname."_enable_paypal",
        "class" => "collapsed",
        "type" => "checkbox",
        "std" => "false");
        
    $options_gateways[] = array( 
        "name" => __( 'Description', 'colabsthemes' ),
        "desc" => __("This controls the description which the user sees during checkout.",'colabsthemes'),
        "id" => $shortname."_paypal_description",
        "class" => "hidden",
        "std"   => __('Pay via PayPal you can pay with your credit card if you dont have a PayPal account.', 'colabsthemes'),
        "type" => "textarea");
        
    $options_gateways[] = array( 
        "name" => __( 'PayPal Email', 'colabsthemes' ),
        "desc" => __( 'Enter your PayPal account email address. This is where your money gets sent.', 'colabsthemes' ),
        "id" => $shortname."_paypal_email",
        "class" => "hidden",
        "type" => "text");
        
    $options_gateways[] = array( 
        "name" => __( 'Sandbox Mode', 'colabsthemes' ),
        "desc" => sprintf( __("You must have a <a target='_new' href='%s'>PayPal Sandbox</a> account setup before using this feature.",'colabsthemes'), 'http://developer.paypal.com/' ),
        "id" => $shortname."_paypal_sandbox",
        "class" => "hidden",
        "type" => "checkbox");    
        
    $options_gateways[] = array( 
        "name" => __( 'Enable PayPal IPN', 'colabsthemes' ),
        "desc" => __("Your web host must support fsockopen otherwise this feature will not work. You must also enable IPN within your PayPal account.",'colabsthemes'),
        "id" => $shortname."_enable_paypal_ipn",
        "class" => "hidden",
        "type" => "checkbox",
        "std" => "false");
        
		return $options_gateways;

	}

	/**
	 * Processes a Paypal Order to display
	 * instructions to the user
	 * @param  Colabs_Order $order   Order to display information for
	 * @return void
	 */
	public function process( $order ) {
  
    // if gateway wasn't selected then exit
    if ( $order['colabs_payment_method'] != 'paypal' ) 
        return;
    
    if ( is_array($order) ):
      echo $this->create_form( $order );
    endif;
    
	}
  
  public function create_form( $order ) {
    $order = colabs_get_order($order['order_id']);
		$return_url = $order->get_return_url();
		$cancel_url = $order->get_cancel_url();
    $options['email_address'] = get_option('colabs_property_paypal_email');

		$values =  Colabs_PayPal_Form::create_form( $order, $options, $return_url, $cancel_url );
		if( !$values )
			return;

		list( $form, $fields ) = $values;
		$this->redirect( $form, $fields, __( 'Thank you - your order is now pending payment. You should be automatically redirected to PayPal to make payment.', 'colabsthemes' ) );
	}
  
  protected function redirect( $form_attributes, $values, $message = '' ){

		if( ! is_array( $form_attributes ) )
			trigger_error( 'Form Attributes must be an array', E_USER_WARNING );

		if( ! is_array( $values ) )
			trigger_error( 'Form Values must be an array', E_USER_WARNING );

		if( ! is_string( $message ) )
			trigger_error( 'Redirect Message must be a string', E_USER_WARNING );

		$defaults = array(
			'action' => '',
			'name' => $this->identifier(),
			'id' => $this->identifier(),
			'method' => 'POST'
		);
		$form_attributes = wp_parse_args( $form_attributes, $defaults );

		$form = $this->get_form_inputs( $values );
		$form .= html( 'input', array(
			'type' => 'submit',
			'style' => 'display: none;'
		) );

		if ( empty( $message ) )
			$message = __( 'You are now being redirected.', 'colabsthemes' );

		$form .= html( 'p', array( 'class' => 'alert alert-warning redirect-text' ),  $message );

		echo html( 'form', $form_attributes, $form );
		echo html( 'script', array(), 'jQuery(function(){ document.' . $form_attributes['name'] . '.submit(); });' );

	}
  
  protected function get_form_inputs( $values ){

		if( ! is_array( $values ) )
			trigger_error( 'Form values must be an array', E_USER_WARNING );

		$form = '';
		foreach ( $values as $name => $value ){

			$attributes = array(
				'type' => 'hidden',
				'name' => $name,
				'value' => $value
			);

			$form .= html( 'input', $attributes, '' );

		}

		return $form;

	}
  
  function display_location(){
		$listener_url = Colabs_PayPal_IPN_Listener::get_listener_url();
		return html( 'label', array(), html( 'input', array(
			'type' => 'text',
			'class' => 'regular-text',
			'value' => $listener_url,
			'size' => strlen( $listener_url ),
			'style' => 'width: 35em; background-color: #EEE'
		)));
	}
  
  public static function get_request_url(){
		$options['sandbox_enabled'] = get_option('colabs_use_paypal_sandbox');
		return (  !empty( $options['sandbox_enabled'] ) ) ? self::$urls['https']['sandbox'] : self::$urls['https']['live'];
	}
  
  function colabs_listing_handle_payment_return() {
    // PayPal IPN handling code	
    if ((isset($_POST['payment_status']) || isset($_POST['txn_type'])) && isset($_POST['item_number']) && ('paypal' == $_GET['payment-gateway-api'])) {
      
      //Common variables
      $amount = $_POST['mc_gross'];
      $currency = $_POST['mc_currency'];
      $order_id = $_POST['item_number'];

      if( !empty($order_id) ){
        $order = colabs_get_order($order_id);
        // process PayPal response
        $paypal = array(
                      'currency' => $currency,
                      'timestamp' => $_POST['payment_date'],
                      'status' => $_POST['payment_status'],
                      'txn_id' => $_POST['txn_id'],
                      'payment' => 'Paypal',
                      'note' => ''
                      );
        switch ($_POST['payment_status']) {
          case 'Partially-Refunded':
            break;  
    
          case 'Completed':
          case 'Processed':        
            $order->complete($paypal);          
            break;
          case 'Refunded':
            $note = __('Last transaction has been reversed. Reason: Payment has been refunded','colabsthemes');
            $paypal['note'] = $note;
            $order->failed($paypal);
            break;
          case 'Reversed':
            $note = __('Last transaction has been reversed. Reason: Payment has been reversed (charge back)','colabsthemes');
            $paypal['note'] = $note;
            $order->failed($paypal);
            break;
          case 'Denied':
            $note = __('Last transaction has been reversed. Reason: Payment Denied','colabsthemes');
            $paypal['note'] = $note;
            $order->failed($paypal);
            break;
          case 'In-Progress':
          case 'Pending':
            $pending_str = array(
                'address' => __('Customer did not include a confirmed shipping address','colabsthemes'),
                'authorization' => __('Funds not captured yet','colabsthemes'),
                'echeck' => __('eCheck that has not cleared yet','colabsthemes'),
                'intl' => __('Payment waiting for approval by service provider','colabsthemes'),
                'multi-currency' => __('Payment waiting for service provider to handle multi-currency process','colabsthemes'),
                'unilateral' => __('Customer did not register or confirm his/her email yet','colabsthemes'),
                'upgrade' => __('Waiting for service provider to upgrade the PayPal account','colabsthemes'),
                'verify' => __('Waiting for service provider to verify his/her PayPal account','colabsthemes'),
                'paymentreview' => __('Paypal is currently reviewing the payment and will approve or reject within 24 hours','colabsthemes'),
                '*' => ''
                );
              $reason = @$_POST['pending_reason'];
              $note = __('Last transaction is pending. Reason: ','colabsthemes') . (isset($pending_str[$reason]) ? $pending_str[$reason] : $pending_str['*']);
              $paypal['note'] = $note;
              $order->pending($paypal);
            break;
          default:
        }  
      }
    }
  }
  function is_recurring(){
		$options = Colabs_Gateway_Registry::get_gateway_options( 'paypal' );
		return ! empty( $options['business_account'] );
	}
}

colabs_register_gateway( 'Colabs_Paypal_Gateway' );
