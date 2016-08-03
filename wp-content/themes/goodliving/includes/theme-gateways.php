<?php
/**
 * Payment gateways
 */

add_action( 'after_setup_theme', '_colabs_load_payments', 998 );
function _colabs_load_payments() {

  /// Gateways
  require dirname( __FILE__ ) . '/gateways/gateway-class.php';
  require dirname( __FILE__ ) . '/gateways/gateway-registry.php';
  
  // Default Gateway
  require dirname( __FILE__ ) . '/gateways/paypal/paypal.php';
  require dirname( __FILE__ ) . '/gateways/bank-transfer/bank-transfer.php';
  require dirname( __FILE__ ) . '/gateways/authorizeaim/authorizeaim.php';

}

function colabs_get_currencies(){
  $currencies = array(
      'USD' => array( 
        'symbol' => '&#36;',
        'name' => __( 'US Dollars', 'colabsthemes' ),
      ),
      'EUR' => array( 
        'symbol' => '&euro;',
        'name' => __( 'Euros', 'colabsthemes' ),
      ),
      'GBP' => array( 
        'symbol' => '&pound;', 
        'name' => __( 'Pounds Sterling', 'colabsthemes' ),
      ),
      'AUD' => array( 
        'symbol' => '&#36;', 
        'name' => __( 'Australian Dollars', 'colabsthemes' ),
      ),
      'BRL' => array( 
        'symbol' => 'R&#36;', 
        'name' => __( 'Brazilian Real', 'colabsthemes' ),
      ),
      'CAD' => array( 
        'symbol' => '&#36;', 
        'name' => __( 'Canadian Dollars', 'colabsthemes' ),
      ),
      'CZK' => array( 
        'symbol' => 'K&#269;', 
        'name' => __( 'Czech Koruna', 'colabsthemes' ),
      ),
      'DKK' => array( 
        'symbol' => 'kr', 
        'name' => __( 'Danish Krone', 'colabsthemes' ),
      ),
      'HKD' => array( 
        'symbol' => '&#36;', 
        'name' => __( 'Hong Kong Dollar', 'colabsthemes' ),
      ),
      'HUF' => array( 
        'symbol' => 'Ft', 
        'name' => __( 'Hungarian Forint', 'colabsthemes' ),
      ),
      'ILS' => array( 
        'symbol' => '&#8362;', 
        'name' => __( 'Israeli Shekel', 'colabsthemes' ),
      ),
      'JPY' => array( 
        'symbol' => '&yen;', 
        'name' => __( 'Japanese Yen', 'colabsthemes' ),
      ),
      'MYR' => array( 
        'symbol' => 'RM', 
        'name' => __( 'Malaysian Ringgits', 'colabsthemes' ),
      ),
      'MXN' => array( 
        'symbol' => '&#36;', 
        'name' => __( 'Mexican Peso', 'colabsthemes' ),
      ),
      'NZD' => array( 
        'symbol' => '&#36;', 
        'name' => __( 'New Zealand Dollar', 'colabsthemes' ),
      ),
      'NOK' => array( 
        'symbol' => 'kr', 
        'name' => __( 'Norwegian Krone', 'colabsthemes' ),
      ),
      'PHP' => array( 
        'symbol' => 'P', 
        'name' => __( 'Philippine Pesos', 'colabsthemes' ),
      ),
      'PLN' => array( 
        'symbol' => 'z&#322;', 
        'name' => __( 'Polish Zloty', 'colabsthemes' ),
      ),
      'SGD' => array( 
        'symbol' => '&#36;', 
        'name' => __( 'Singapore Dollar', 'colabsthemes' ),
      ),
      'SEK' => array( 
        'symbol' => 'kr', 
        'name' => __( 'Swedish Krona', 'colabsthemes' ),
      ),
      'CHF' => array( 
        'symbol' => 'Fr', 
        'name' => __( 'Swiss Franc', 'colabsthemes' ),
      ),
      'TWD' => array( 
        'symbol' => '&#36;', 
        'name' => __( 'Taiwan New Dollar', 'colabsthemes' ),
      ),
      'THB' => array( 
        'symbol' => '&#3647;', 
        'name' => __( 'Thai Baht', 'colabsthemes' ),
      ),
      'TRY' => array( 
        'symbol' => '&#8356;', 
        'name' => __( 'Turkish Lira', 'colabsthemes' ),
      ),
    );
  return $currencies;
}
function colabs_register_gateway( $class_name ) {

  Colabs_Gateway_Registry::register_gateway( $class_name ); 
  
}

class Colabs_Payments_Settings_Admin{

  var $admin_page;
  var $token;
  var $default_options_gateways;
  
  function Colabs_Payments_Settings_Admin () {
    $this->admin_page = '';
    $this->token = 'colabsthemes-gateways';
  } // End Constructor
  
  /**
   * init()
   *
   * Initialize the class.
   *
   * @since 1.0.0
   */
  
  function init () {
      // Register the admin screen.
      add_action( 'admin_menu', array( &$this, 'register_admin_screen' ), 20 );
      
      $shortname = 'colabs';
      $options_gateways = array(); 
      $currencies = colabs_get_currencies();
      foreach ($currencies as $key => $currency):
        $options_currency[$key] = $currency['name'].' ('.$currency['symbol'].')'; 
      endforeach;
      $currency_symbol = colabs_get_currency_symbol(get_option('colabs_currency_code'));
      $options_gateways[] = array( 
          "name" => __( 'Payments General', 'colabsthemes' ),
          "icon" => "general",
          "type" => "heading"); 

      $options_gateways[] = array( 
          "name" => __( 'Currency', 'colabsthemes' ),
          "desc" => sprintf( __("This is the currency you want to collect payments in. It applies mainly to PayPal payments since other payment gateways accept more currencies. If your currency is not listed then PayPal currently does not support it. See the list of supported <a target='_new' href='%s'>PayPal currencies</a>.", 'colabsthemes'), 'https://www.paypal.com/cgi-bin/webscr?cmd=p/sell/mc/mc_intro-outside' ),
          "id" => $shortname."_currency_code",
          "options" => $options_currency,
          "type" => "select2"); 
                
      $options_gateways[] = array( 
          "name" => __( "Symbol Position", "colabsthemes" ),
          "desc" => __( "Some currencies place the symbol on the right side vs the left. Select how you would like your currency symbol to be displayed.", "colabsthemes" ),
          "id" => $shortname."_currency_position",
          "std" => "left",
          "type" => "select2",
          "options" => array( 'left'          => sprintf(__('Left of Currency (%s100)', 'colabsthemes'),$currency_symbol),
                              'left_space'    => sprintf(__('Left of Currency with Space (%s 100)', 'colabsthemes'),$currency_symbol),
                              'right'         => sprintf(__('Right of Currency (100%s)', 'colabsthemes'),$currency_symbol),
                              'right_space'   => sprintf(__('Right of Currency with Space (100 %s)', 'colabsthemes'),$currency_symbol)
                             ) );     
          
      $this->default_options_gateways = $options_gateways;
  } // End init()
  
  /**
   * register_admin_screen()
   *
   * Register the admin screen within WordPress.
   *
   * @since 1.0.0
   */
  
  function register_admin_screen () {
      
    $this->admin_page = add_submenu_page('edit.php?post_type=transaction', __( 'Settings', 'colabsthemes' ), __( 'Settings', 'colabsthemes' ), 'manage_options', $this->token, array( &$this, 'admin_screen' ) );
      
    add_action( "admin_print_styles-".$this->admin_page, 'colabs_admin_styles' );
    add_action( "admin_print_scripts-".$this->admin_page, 'colabs_load_only' );   
    add_action( "admin_head-".$this->admin_page, array( $this, 'save_options') );  
  } // End register_admin_screen()
  
  function admin_screen () {
    $themename =  get_option( 'colabs_themename' );
    $options_gateways = $this->default_options_gateways;
    $gateways = Colabs_Gateway_Registry::get_gateways();
      
    foreach ( $gateways as $gateway ) {
      $options_gateways = array_merge($options_gateways,$gateway->form());
    }
    ?>
    <div class="wrap colabs_container">
      <h2></h2>
      <form action="" method="post">
      <input name="page" type="hidden" value="gateways" />
      <input name="submitted" type="hidden" value="true" />      
      <div class="clear"></div>
      <div id="colabs-popup-save" class="colabs-save-popup"><div class="colabs-save-save"><?php _e("Options Updated","colabsthemes"); ?></div></div>
      <div id="colabs-popup-reset" class="colabs-save-popup"><div class="colabs-save-reset"><?php _e("Options Reset","colabsthemes"); ?></div></div>
      <div class="clear"></div>
      <?php $return = colabsthemes_machine($options_gateways); ?>
      <div id="main" class="menu-item-settings metabox-holder">
        <div id="panel-header">
          <?php colabsthemes_options_page_header(array('save_button'=>'true')); ?>
        </div><!-- #panel-header -->
        <div id="sidebar-nav">
          <ul>
            <?php echo $return[1]; ?>
          </ul>
        </div>
        <div id="panel-content">
          <div class="group help-block"> <p><?php _e("Drag icon on the left and Drop it here to customize","colabsthemes"); ?></p> </div>
          <?php echo $return[0]; ?>
          <div class="clear"></div>
        </div>
        <div id="panel-footer">
          <ul>
            <li class="docs"><a title="Theme Documentation" href="http://colorlabsproject.com/documentation/<?php echo strtolower( str_replace( " ","",$themename ) ); ?>" target="_blank" ><?php _e('View Documentation','colabsthemes');?></a></li>
            <li class="forum"><a href="http://colorlabsproject.com/resolve/" target="_blank"><?php _e('Submit a Support Ticket','colabsthemes');?></a></li>
            <li class="idea"><a href="http://ideas.colorlabsproject.com/" target="_blank"><?php _e('Suggest a Feature','colabsthemes');?></a></li>
          </ul>
        </div><!-- #panel-footer -->
      </div><!-- #main -->
      </form>
    </div><!--wrap-->
    <?php
  }
  
  function save_options() {

    if (isset($_POST['submitted']) && $_POST['submitted'] == 'true') {
      $options_gateways = $this->default_options_gateways;
      $gateways = Colabs_Gateway_Registry::get_gateways();
        
      foreach ( $gateways as $gateway ) {
        $options_gateways = array_merge($options_gateways,$gateway->form());
      }
      foreach ( $options_gateways as $value ) {
        if ( isset($_POST[$value['id']]) ) {
            update_option( $value['id'], $_POST[$value['id']] );
        } else {
            @delete_option( $value['id'] );
        }
      }

    } 
  }
}

$colabs_payments_settings_admin = new Colabs_Payments_Settings_Admin();
$colabs_payments_settings_admin->init();

/**
 * Displays a dropdown form with currently active gateways
 * @param  string $input_name Name of the input field
 * @return void
 */
function colabs_list_gateway_dropdown( $input_name = 'payment_gateway' ) {

  $available_gateways = array();

  if ( $available_gateways = Colabs_Gateway_Registry::get_active_gateways() ) {
    ?>
    <ul class="payment_methods methods">
    <?php foreach ( $available_gateways as $gateway ) {?>
        <li class="payment_method_<?php echo $gateway->identifier(); ?>">
          <input id="payment_method_<?php echo $gateway->identifier(); ?>" type="radio" class="input-radio" name="colabs_payment_method" value="<?php echo esc_attr( $gateway->identifier() ); ?>" data-order_button_text="<?php echo esc_attr( $gateway->display_name( 'dropdown' ) ); ?>" />
          <label for="payment_method_<?php echo $gateway->identifier(); ?>"><?php echo $gateway->display_name( 'dropdown' ); ?></label>
          <?php
            if ( $gateway->has_fields() || $gateway->get_description() ) {
              echo '<div class="payment_box payment_method_' . $gateway->identifier() . '" style="display:none;">';
              $gateway->payment_fields();
              echo '</div>';
            }
          ?>
        </li>
    <?php }?>
    </ul>
  <?php 
  }else{
    echo '<p>'.__( 'No Gateways are currently available', 'colabsthemes' ).'</p>';
  }

}