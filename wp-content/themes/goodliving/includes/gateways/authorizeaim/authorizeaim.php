<?php

/**
 * Payment Gateway for processing payments via Bank Transfer
 * or other manual method
 */
class Colabs_AuthorizeNet_AIM_Gateway extends Colabs_Gateway{

	/**
	 * Sets up the gateway
	 */
	public function __construct() {
		parent::__construct( 'authorize_aim', array(
			'dropdown' => __( 'Authorize.net AIM', 'colabsthemes' ),
			'admin' => __( 'Authorize.net AIM', 'colabsthemes' ),
		) );
    
    $this->has_fields     = true;
    $this->description 		= get_option( 'colabs_authorizeaim_description' );
    
	}

	/**
	 * Builds the administration settings form
	 * @return array scbForms style form
	 */
	public function form() {
    $shortname = 'colabs';
    
		$options_gateways[] = array( 
        "name" => __( 'Authorize.net AIM Options', 'colabsthemes' ),
        "icon" => "general",
        "type" => "heading");	
    $options_gateways[] = array( 
        "name" => __( 'Enable Authorize.net AIM', 'colabsthemes' ),
        "desc" => __('Enable Authorize.net AIM Payment Module.','colabsthemes'),
        "id" => $shortname."_enable_authorize_aim",
        "class" => "collapsed",
        "type" => "checkbox",
        "std" => "false");
        
    $options_gateways[] = array( 
        "name" => __( 'Description', 'colabsthemes' ),
        "desc" => __("This controls the description which the user sees during checkout.",'colabsthemes'),
        "id" => $shortname."_authorizeaim_description",
        "class" => "hidden",
        "std"   => __('Pay securely by Credit or Debit Card through Authorize.net AIM Secure Servers.', 'colabsthemes'),
        "type" => "textarea");  
        
    $options_gateways[] = array( 
        "name" => __( 'API Login ID', 'colabsthemes' ),
        "desc" => __("Enter your Authorize.net API Login ID. This is where your money gets sent.",'colabsthemes'),
        "id" => $shortname."_authorize_id",
        "class" => "hidden",
        "type" => "text");
        
    $options_gateways[] = array( 
        "name" => __( 'Transaction Key', 'colabsthemes' ),
        "desc" => __("Enter your Authorize.net transaction key. This is where your money gets sent.",'colabsthemes'),
        "id" => $shortname."_authorize_key",
        "class" => "hidden",
        "type" => "text");
        
    $options_gateways[] = array( 
        "name" => __( 'Sandbox Mode', 'colabsthemes' ),
        "desc" => __("By default Authorize.net is set to live mode. If you would like to test and see if payments are being processed correctly, check this box to switch to sandbox mode.",'colabsthemes'),
        "id" => $shortname."_authorize_sandbox",
        "class" => "hidden last",
        "type" => "checkbox");

		return $options_gateways;

	}
  
  /**
  *  Fields for Authorize.net AIM
  **/
  function payment_fields()
  {
     if ( $this->description ) 
        echo wpautop(wptexturize($this->description));
        echo '<label>Credit Card :</label> <input type="text" name="aim_creditcard" /><br/>';
        echo '<label>Expiry (MMYY) :</label> <input type="text" name="aim_ccexpdate" maxlength="4" />';
        echo '<label>CVV :</label> <input type="text" name="aim_ccvnumber"  maxlength=4 />';
  }
  
  /*
  * Basic Card validation
  */
  public function validate_fields()
  {
    $errors = array();
    if (!$this->isCreditCardNumber($_POST['aim_creditcard'])) 
      $errors[] = __('(Credit Card Number) is not valid.', 'colabsthemes'); 


    if (!$this->isCorrectExpireDate($_POST['aim_ccexpdate']))    
      $errors[] = __('(Card Expiry Date) is not valid.', 'colabsthemes'); 

    if (!$this->isCCVNumber($_POST['aim_ccvnumber'])) 
      $errors[] = __('(Card Verification Number) is not valid.', 'colabsthemes'); 
      
    return $errors;  
  }
  
  /*
  * Check card 
  */
  private function isCreditCardNumber($toCheck) 
  {
     if (!is_numeric($toCheck))
        return false;
    
    $number = preg_replace('/[^0-9]+/', '', $toCheck);
    $strlen = strlen($number);
    $sum    = 0;

    if ($strlen < 13)
        return false; 
        
    for ($i=0; $i < $strlen; $i++)
    {
        $digit = substr($number, $strlen - $i - 1, 1);
        if($i % 2 == 1)
        {
            $sub_total = $digit * 2;
            if($sub_total > 9)
            {
                $sub_total = 1 + ($sub_total - 10);
            }
        } 
        else 
        {
            $sub_total = $digit;
        }
        $sum += $sub_total;
    }
    
    if ($sum > 0 AND $sum % 10 == 0)
        return true; 

    return false;
  }
    
  private function isCCVNumber($toCheck) 
  {
     $length = strlen($toCheck);
     return is_numeric($toCheck) AND $length > 2 AND $length < 5;
  }
  
  /*
  * Check expiry date
  */
  private function isCorrectExpireDate($date) 
  {
      
     if (is_numeric($date) && (strlen($date) == 4)){
        return true;
     }
     return false;
  }
  
	/**
	 * Processes a Order to display
	 * instructions to the user
	 * @param  Colabs_Order $order   Order to display information for
	 * @return void
	 */
	public function process( $order ) {
    
    // if gateway wasn't selected then exit
    if ( $order['colabs_payment_method'] != 'authorize_aim' ) 
        return;
    
    if ( is_array($order) ):
    
      // is this a test transaction?
      if ( get_option( 'colabs_authorize_sandbox' ) == true )
          $process_url = 'https://test.authorize.net/gateway/transact.dll';
      else
          $process_url = 'https://secure.authorize.net/gateway/transact.dll';
            
      $authorizeaim_args = array(
                'x_login'                  => get_option('colabs_authorize_id'),
                'x_tran_key'               => get_option('colabs_authorize_key'),
                'x_version'                => '3.1',
                'x_delim_data'             => 'TRUE',
                'x_delim_char'             => '|',
                'x_relay_response'         => 'FALSE',
                'x_type'                   => 'AUTH_CAPTURE',
                'x_method'                 => 'CC',
                'x_card_num'               => $_POST['aim_creditcard'],
                'x_exp_date'               => $_POST['aim_ccexpdate' ],
                'x_description'            => 'Order #'.$order['order_id'],
                'x_amount'                 => $order['item_amount'],
                'x_card_code'              => $_POST['aim_cvvnumber'],            
                 );
      
      $post_response = wp_remote_retrieve_body( wp_remote_post ( $process_url, array(
        'body'        => $authorizeaim_args,
        'sslverify'   => false,
      )));

      $response_array = explode('|',$post_response);
      $order_class = colabs_get_order($order['order_id']);
      $error = 0;
      if ( count($response_array) > 1 ){
          
        if($response_array[0] == '1' ){
          $payment_details = array(
                      'currency' => get_option('colabs_currency_code'),
                      'timestamp' => current_time('mysql'),
                      'txn_id' => $response_array[4],
                      'payment' => 'Authorize AIM',
                      'note' => $response_array[51]
                      );
          $order_class->complete($payment_details);
        }else{
          $payment_details['note'] = $response_array[3];
          $order_class->pending($payment_details);
          $error = 1;
        }
        
        $info_message = $response_array[3];
         
      }else{
        $order_class->failed();
        $error = 1;
      }
      
    endif;
    if($error == 0){
      echo '<p class="alert alert-success">'.$info_message.'</p>';
    }else{
      echo '<p class="alert alert-danger">'.$info_message.'</p>';
    }
    ?>
    
    <h4><?php _e('Your Transaction Details', 'colabsthemes') ?></h4>

    <ul>
      <li><span><?php _e('Transaction ID:', 'colabsthemes') ?></span><?php echo esc_html( $order['order_id'] ); ?></li>
      <li><span><?php _e('Reference #:', 'colabsthemes') ?></span><?php echo esc_attr( $order['post_id'] ); ?></li>
      <li><span><?php _e('Total Amount:', 'colabsthemes') ?></span><?php echo colabs_get_price($order['item_amount']); ?></li>
    </ul>
    <?php
	}
}

colabs_register_gateway( 'Colabs_AuthorizeNet_AIM_Gateway' );
