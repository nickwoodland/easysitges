<?php
class Colabs_Order{

	protected $id = 0;

	protected $parent = 0;

	protected $state = COLABS_ORDER_PENDING;

	protected $description = '';

	protected $creator = array(
		'user_id' => 0,
		'ip_address' => 0,
    '_post_type_id' => 0,
    '_order_type' => '',
	);
	
	protected $payment = array(
		'total' => 0,
		'currency' => 'USD',
		'gateway' => '',
		'recurring_period' => 0,
	);
  
	protected $items = array();
  
  public function __construct( $post ) {

		$this->id = $post->ID;
		$this->parent = $post->post_parent;
		$this->description = $post->post_title;
		$this->state = $post->post_status;

		$meta_fields = get_post_custom( $post->ID );

		$this->creator['user_id'] = $post->post_author;
		$this->creator['ip_address'] = $this->get_meta_field( '_ip_address', 0, $meta_fields );
    $this->creator['_post_type_id'] = $this->get_meta_field( '_post_type_id', 0, $meta_fields );
    $this->creator['_order_type'] = $this->get_meta_field( '_order_type', 0, $meta_fields );

		$this->payment['currency'] = $this->get_meta_field( 'currency', 'USD', $meta_fields );
		$this->payment['gateway'] = $this->get_meta_field( 'gateway', '', $meta_fields );
		$this->payment['recurring_period'] = $this->get_meta_field( 'recurring_period', 0, $meta_fields );
    
		$this->refresh_total();

	}
  
	static public function create( $items = array(),$description = '' ){
		$order = self::make( $items, $description );

		return $order;
	}

	/**
	 * Prepares and returns a new Order
	 * @return Colabs_Order New Order object
	 */
	static protected function make( $items = array(),$description = '' ) {

		if( empty( $description ) )
			$description = __( 'Transaction ID ', 'colabsthemes' );
      
    $order_id = wp_insert_post( array(
      'post_title' => $description,
      'post_content' => __( 'Transaction Data', 'colabsthemes' ),
      'post_type' => COLABS_ORDER_POST_TYPE,
      'post_status' => COLABS_ORDER_PENDING,
    ) );
    
    if($order_id):
      
      update_post_meta( $order_id, '_order_type', COLABS_POST_TYPE );
      
      if($items){
        foreach ($items as $key => $value):
          update_post_meta( $order_id, $key, $value );
        endforeach;
      }

      if ( isset( $_SERVER['REMOTE_ADDR'] ) )
        update_post_meta( $order_id, '_ip_address', $_SERVER['REMOTE_ADDR'] );


      $order = colabs_get_order( $order_id );
      $order->set_currency(get_option('colabs_currency_code'));
      return $order;
    endif;
	}
  
  public function get_info( $part = '' ){

		$basic = array(
			'id' => $this->id,
			'parent' => $this->parent,
			'description' => $this->description,
			'state' => $this->state
		);

		$fields = array_merge( $basic, $this->creator, $this->payment );

		if( empty( $part ) )
			return $fields;
		else
			return $fields[ $part ];

	}
  
  public function get_items(){
    
    if(COLABS_POST_TYPE==$this->get_order_type()){
      $obj = get_post_type_object( get_post_type($this->get_post_type_id()) );
      $items[]= array('title'         => $obj->labels->singular_name,
                      'price'         => $this->get_post_meta( 'price' ),
                      'post_link'     => get_permalink($this->get_post_type_id()),
                      'post_title'    => get_the_title($this->get_post_type_id())
                      );
                      
      $items[] = array('title'        => __('Featured','colabsthemes'),
                       'price'        => $this->get_post_meta( 'additional_price' ),
                       'post_link'    => get_permalink($this->get_post_type_id()),
                       'post_title'   => get_the_title($this->get_post_type_id())
                      );

    }
    return $items;
  }
  
	public function get_id() {
		return $this->id;
	}

	public function get_description() {
		return $this->description;
	}
  
  public function set_description( $description ){

		if( ! is_string( $description ) )
			trigger_error( 'Description must be a string.', E_USER_WARNING );

		$this->description = $description;
		$this->update_post( array(
			'post_title' => $description
		) );

	}

	private function get_meta_field( $field, $default, $fields ){
		if( isset( $fields[ $field ] ) )
			return $fields[ $field ][0];
		else
			return $default;

	}
  
  protected function refresh_total() {

		// Only update this value if price is 0
    $total_price = $this->get_post_meta( 'total_price' );
    if( !$total_price || $total_price == '' ) {

  		$price = $this->get_post_meta( 'price' );
      $additional_price = $this->get_post_meta( 'additional_price' );

		  $this->payment['total'] = (float) $price + (float) $additional_price;
      $this->update_meta( 'total_price', $this->payment['total'] );
    } else {
      $this->payment['total'] = (float) $total_price;
    }

	}
  
  public function set_currency( $currency_code ) {

		if( ! is_string( $currency_code ) )
			trigger_error( 'Currency code must be string', E_USER_WARNING );

		$this->payment['currency'] = $currency_code;

		$this->update_meta( 'currency', $this->payment['currency'] );
		return true;
	}
  
  public function set_gateway( $gateway_id ) {

		if( ! is_string( $gateway_id ) )
			trigger_error( 'Gateway ID must be a string', E_USER_WARNING );

		if ( $gateway_object = Colabs_Gateway_Registry::get_gateway( $gateway_id ) ){
			$this->payment['gateway'] = $gateway_object->identifier();
			$this->update_meta( 'gateway', $this->payment['gateway'] );
			return true;
		}

		return false;

	}
  
  public function get_gateway() {
		return $this->payment['gateway'];
	}
  
  public function get_currency() {
		return $this->payment['currency'];
	}
  
  public function get_author() {
		return $this->creator['user_id'];
	}
  
  public function get_ip_address() {
		return $this->creator['ip_address'];
	}
  
  public function get_order_type() {
		return $this->creator['_order_type'];
	}
  
  public function get_post_type_id() {
		return $this->creator['_post_type_id'];
	}
  
  protected function get_post_meta( $meta_key ){
    return get_post_meta( $this->get_id(), $meta_key, true );
  }
  
  protected function update_meta( $meta_key, $meta_value = '', $reset_cache = true ){

		if( is_array( $meta_key ) ){
			foreach( $meta_key as $key => $value ){
				$this->update_meta( $key, $value, false );
			}
			$this->reset_cache();
			return;
		}

		update_post_meta( $this->id, $meta_key, $meta_value );

		if( $reset_cache )
			$this->reset_cache();

	}
  
  protected function reset_cache(){

		wp_cache_set( $this->id, $this, 'orders' );

	}
  
  public function get_status() {
		return $this->state;
	}

	/**
	 * Returns a version of the current state for display.
	 * @return string Current state, localized for display
	 */
	public function get_display_status() {

		$statuses = array(
			COLABS_ORDER_PENDING => __( 'Pending', 'colabsthemes' ),
			COLABS_ORDER_FAILED => __( 'Failed', 'colabsthemes' ),
			COLABS_ORDER_COMPLETED => __( 'Completed', 'colabsthemes' ),
			COLABS_ORDER_ACTIVATED => __( 'Activated', 'colabsthemes' ),
		);

		$status = $this->get_status();

		return $statuses[$status];

	}
	public function get_total() {
		return $this->payment['total'];
	}
  
  public function complete($payment_details = array()) {
		$this->set_status( COLABS_ORDER_COMPLETED, $payment_details );
	}
  
  public function failed($payment_details = array()) {
		$this->set_status( COLABS_ORDER_FAILED, $payment_details );
	}
  
  public function pending($payment_details = array()) {
		$this->set_status( COLABS_ORDER_PENDING, $payment_details );
	}
  
  public function activated($payment_details = array()) {
		$this->set_status( COLABS_ORDER_ACTIVATED, $payment_details );
    
    if( $this->is_recurring() ){
    }
	}
  
  protected function set_status( $status, $payment_details ) {
 
		if ( $this->state == $status )
			return;

		$this->state = $status;
  
    $orders = array(
			"post_status" => $status
		);
    
		$this->update_post($orders, $payment_details);

    $statuses = array(
      COLABS_ORDER_COMPLETED => 'completed',
      COLABS_ORDER_FAILED => 'failed',
      COLABS_ORDER_COMPLETED => 'completed',
      COLABS_ORDER_ACTIVATED => 'activated'
    );

    // run hooks
    if( isset( $statuses[ $status ] ) ) {
      do_action( 'colabs_order_' . $statuses[ $status ], $this );
    }

		$this->reset_cache();
	}
  
  protected function update_post( $args, $payment_details = array() ){
    
		$defaults = array(
			'ID' => $this->get_id()
		);
    
		wp_update_post( array_merge( $defaults, $args ) );
    if(!empty($payment_details)):
      if ( isset( $payment_details['currency'] ) )$this->update_meta( 'currency', $payment_details['currency']);
      if ( isset( $payment_details['timestamp'] ) )$this->update_meta( 'pay_date', date('Y-m-d H:i:s', strtotime($args['timestamp'])));
      if ( isset( $payment_details['txn_id'] ) ){
        $this->update_meta( 'pay_date', $payment_details['txn_id']);
      }

      if ( isset( $payment_details['note'] ) )$this->update_meta( 'note', $payment_details['note']);
      
    endif;
    
		$this->reset_cache();

	}
  
  public function clear_gateway() {

		$this->payment['gateway'] = '';
		delete_post_meta( $this->id, 'gateway' );
		$this->reset_cache();
	
	}
  
  static public function get_url( $order_id ){
		if( !is_numeric( $order_id ) )
			trigger_error( 'Invalid order id given. Must be an integer', E_USER_WARNING );
		return apply_filters( 'colabs_order_return_url', get_permalink( $order_id ) );
	}
  
  public function get_return_url() {
		return self::get_url( $this->id );
	}
  
  public function get_cancel_url() {
		return add_query_arg( "cancel", 1, $this->get_return_url() );
	}
  
  /**
	 * Returns true if the order recurrs
	 */
	public function is_recurring(){
		return ! empty( $this->payment['recurring_period'] );
	}

	/**
	 * Sets up the order to recur upon completion
	 */
	public function set_recurring_period( $recurring_period ){
		$this->payment['recurring_period'] = $recurring_period;
		$this->update_meta( 'recurring_period', $this->payment['recurring_period'] );
	}
  
  /**
	 * Returns the order's recurring period
	 */
	public function get_recurring_period(){
		return $this->payment['recurring_period'];
	}
}

abstract class Colabs_Table{

	protected function table( $items, $attributes = array() ){

		$table_body = '';

		$table_body .= html( 'thead', array(), $this->header( $items ) );
		$table_body .= html( 'tbody', array(), $this->rows( $items ) );
		$table_body .= html( 'tfoot', array(), $this->footer( $items ) );

		return html( 'table', $attributes, $table_body );

	}

	protected function header( $data ){}

	protected function footer( $data ){}

	protected function rows( array $items ){

		$table_body = '';
		foreach( $items as $item ){
			$table_body .= $this->row( $item );
		}

		return $table_body;

	}

	abstract protected function row( $item );

	protected function cells( $cells, $type = 'td' ){

		$output = '';
		foreach( $cells as $value ){
			$output .= html( $type, array(), $value );
		}
		return $output;

	}

}

class Colabs_Order_Summary_Table extends Colabs_Table{

	protected $order, $currency;

	public function __construct( $order ){

		$this->order = $order;
		$this->currency = $order->get_currency();

	}

	public function show( $attributes = array() ){
    
		$items = $this->order->get_items();
    
		echo $this->table( $items, $attributes );
	}

	protected function footer( $items ){

		$cells = array(
			__( 'Total', 'colabsthemes' ),
			colabs_get_price( $this->order->get_total(), $this->currency )
		);

		return html( 'tr', array(), $this->cells( $cells, 'td' ) );

	}

	protected function row( $item ){
    $price = 0;
    if($item['price'])$price = $item['price'];
		$cells = array(
			$item['title'],
			colabs_get_price( $price, $this->currency )
		);

		return html( 'tr', array(), $this->cells( $cells ) );

	}

}

class Colabs_Admin_Order_Summary_Table extends Colabs_Order_Summary_Table{

	protected function header( $items ){

		$cells = array(
			__( 'Order Summary', 'colabsthemes' ),
			__( 'Price', 'colabsthemes' ),
			__( 'Affects', 'colabsthemes' ),
		);

		return html( 'tr', array(), $this->cells( $cells, 'th' ) );

	}

	protected function footer( $items ){

		$cells = array(
			__( 'Total', 'colabsthemes' ),
			colabs_get_price( $this->order->get_total(), $this->currency ),
			''
		);

		return html( 'tr', array(), $this->cells( $cells, 'th' ) );

	}

	protected function row( $item ){
    $price = 0;
    if($item['price'])$price = $item['price'];
		$cells = array(
      $item['title'],
			colabs_get_price( $price, $this->currency ),
			html( 'a', array(
				'href' => $item['post_link']
			), $item['post_title'] )
		);

		return html( 'tr', array(), $this->cells( $cells ) );

	}

}