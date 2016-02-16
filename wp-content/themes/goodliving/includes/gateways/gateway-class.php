<?php

/**
 * Base class for Payment Gateways
 */
abstract class Colabs_Gateway {

	/**
	 * Unique identifier for this gateway
	 * @var string
	 */
	private $identifier;

	/**
	 * Display names used for this Gateway
	 * @var array
	 */
	private $display;
  
  /** @var bool True if the gateway shows fields on the checkout. */
	var $has_fields;
  
  /** @var string Description for the gateway. */
	var $description;

	/**
	 * Creates the Gateway class with the required information to display it
	 *
	 * @param string  $display_name The display name
	 * @param string  $identifier   The unique indentifier used to indentify your payment type
	 */
	public function __construct( $identifier, $args = array() ) {

		if( ! is_string( $identifier ) )
			trigger_error( 'Identifier must be a string', E_USER_WARNING );

		if( ! is_array( $args ) && ! is_string( $args ) )
			trigger_error( 'Arguments must be an array or url encoded string.', E_USER_WARNING );

		$defaults = array(
			'dropdown' => $identifier,
			'admin' => $identifier,
			'recurring' => false
		);

		$args = wp_parse_args( $args, $defaults );

		$this->display = array(
			'dropdown' => $args['dropdown'],
			'admin' => $args['admin'],
		);

		$this->identifier = $identifier;
		$this->recurring = (bool) $args['recurring'];
    
    add_action( 'wp_ajax_validate_payment', array($this,'colabs_ajax_validate_payment') );
	}

	/**
	 * Returns an array representing the form to output for admin configuration
	 * @return array scbForms style form array
	 */
	public abstract function form();

	/**
	 * Processes an order payment
	 * @param  Colabs_Order $order   The order to be processed
	 *   							corresponding to the values provided in form()
	 * @return void
	 */
	public abstract function process( $order);

	/**
	 * Process a recurring order
	 * @param Colabs_Order $order The order to be processed
	 * @parama array $options An array of user-entered options 
	 * 				corresponding to the values provided in form()
	 * @return void
	 */
	public function process_recurring( $order, $options ){
		_e( 'This gateway has not implemented a recurring order processor.', 'colabsthemes' );
		die();
	}

	/**
	 * Provides the display name for this Gateway
	 *
	 * @return string
	 */
	public final function display_name( $type = 'dropdown' ) {
		
		if( in_array( $type, array( 'dropdown', 'admin' ) ) )
			return $this->display[$type];
		else
			return $this->display['dropdown'];
	}

	/**
	 * Provides the unique identifier for this Gateway
	 *
	 * @return string
	 */
	public final function identifier() {
		return $this->identifier;
	}

	/**
	 * Returns if the current gateway is able to process
	 * recurring payments
	 * @return bool
	 */
	public function is_recurring(){
		return $this->recurring;
	}
  
  /**
	 * Validate Frontend Fields
	 *
	 * Validate payment fields on the frontend.
	 *
	 * @access public
	 * @return bool
	 */
	public function validate_fields() { return true; }
  
  /**
	 * Return the gateways description
	 *
	 * @access public
	 * @return string
	 */
	public function get_description() {
		return apply_filters( 'classifier_gateway_description', $this->description, $this->identifier );
	}
  
  /**
	 * If There are no payment fields show the description if set.
	 * Override this in your gateway if you have some.
	 *
	 * @access public
	 * @return void
	 */
	public function payment_fields() {
		if ( $description = $this->get_description() ) {
			echo wpautop( wptexturize( $description ) );
		}
	}
  
   /**
	 * has_fields function.
	 *
	 * @access public
	 * @return bool
	 */
	public function has_fields() {
		return $this->has_fields ? true : false;
	}
  
  function colabs_ajax_validate_payment(){
    $return = array(
      success => false,
      errors => array()
    );
  
    if( isset($_REQUEST['colabs_payment_method'] ) ) {
  
      $gateway = Colabs_Gateway_Registry::get_gateway( $_REQUEST['colabs_payment_method'] );

      if( $gateway->validate_fields() === true ) {
        $return['success'] = true;
      } else {
        if(sizeof($gateway->validate_fields())>0){
        $return['errors'] = $gateway->validate_fields();
        }else{
        $return['success'] = true;
        }
      }
  
    } else {
      
       $return['errors'][] = __('Please select payment method', 'colabsthemes');
      
    }
      
    header('Content-Type: application/json');
    echo json_encode( $return );
    exit();
  }
}
