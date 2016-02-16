<?php
/**
 * Upgrade functions from Goodliving 1.1.8 or lower
 *
 * @version 1.2.0
 * @author Colorlabs
 * @package Goodliving
 * @copyright 2015 all rights reserved
 *
 */

class Goodliving_Theme_Upgrade {

  var $db_version = '1.0.0';
  var $current_theme;
  var $errors;

  /**
   * Constructor
   */
  public function __construct() {
    $this->current_theme = wp_get_theme();
    $this->errors = new WP_Error();

    add_action( 'admin_notices', array( $this, 'colabs_upgrade_notices' ) );
    add_action( 'admin_init', array( $this, 'update_actions' ) );
  }


  /**
   * Update Actions
   */
  public function update_actions() {
    // Update button
    if ( ! empty( $_GET['do_upgrade'] ) ) {
      $this->update();
    }
  }


  /**
   * Upgrade Notices
   */
  public function colabs_upgrade_notices() {
    // If upgrade failed
    if( $error = get_option( 'goodliving_upgrade_fail' ) ) {
      $message = '<p>' . sprintf( __( '<strong>Warning:</strong> There were errors upgrading the theme to %s - \'%s\'.', 'colabsthemes' ), $this->current_theme->get( 'Version' ), $error ) . '</p>';

      $this->output_message( $message );
    }

    // Is theme need database update
    if( $this->is_need_update() ) {
      $message = '<p>' . __( '<strong>Goodliving Data Update Required</strong> &#8211; It is strongly recommended that you backup your database before proceeding.', 'colabsthemes' ) . '</p>';
      $message .= '<p class="submit"><a href="' . esc_url( add_query_arg( 'do_upgrade', 'true', admin_url( 'admin.php?page=colabsthemes' ) ) ) . '" class="button-primary">' . __('Run the updater', 'colabsthemes' ) . '</a></p>';
      $this->output_message( $message );
    }
  }


  /**
   * Update the database
   */
  public function update() {
    global $wpdb;
    
    if( !$this->is_need_update() )
      return;

    if ( ! get_option( 'goodliving_upgrade_fail' ) && ! get_option( 'goodliving_upgrade_success' ) && $this->is_need_update() ){    
      // Colabs_log()->write_log( 'Theme Upgrade: Start' );

      // skip the upgrade if the main Orders legacy table does not exist
      if ( ! $wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix."colabs_orders' ") ) {
        // Colabs_log()->write_log( 'Theme Upgrade: Legacy orders table not found - Skipping theme upgrade' );
        return;
      }

      delete_option( 'goodliving_upgrade_fail' );

      // Upgrade the property duration
      $this->upgrade_property_duration();

      // Upgrade featured property key
      $this->upgrade_featured_properties();

      // Upgrade theme options
      $this->upgrade_theme_options();

      // Upgrade old orders
      $this->upgrade_orders();

      if( $this->errors->get_error_code() ) {
        update_option( 'goodliving_upgrade_fail', true ); ;
        update_option( 'goodliving_upgrade_success', false );
        add_action( 'admin_notices', array( $this, 'colabs_upgrade_failed_notices' ) );
      } else {
        update_option( 'goodliving_upgrade_success', true );
        update_option( 'goodliving_db_version', $this->db_version );
        add_action( 'admin_notices', array( $this, 'colabs_upgrade_success_notices' ) );
      }
    }
  }


  /**
   * Upgrade Failed Notices
   */
  public function colabs_upgrade_failed_notices() {
    $messages = $this->errors->get_error_messages();
    $message = '';
    foreach( $messages as $error_msg ) {
      $message .= "<p>Error: $error_msg</p>";
    }
    $this->output_message( $message, 'error' );
  }


  /**
   * Upgrade Success Notices
   */
  public function colabs_upgrade_success_notices() {
    foreach( $messages as $error_msg ) {
      $message .= '<p>'. __('Database successfully upgraded', 'colabsthemes') .'</p>';
    }
    $this->output_message( $message );
  }


  /**
   * Upgrade property duration
   *
   * Replace old 'expires' meta key containing the expiration date 
   * with the new property duration key
   */
  public function upgrade_property_duration() {
    // Colabs_log()->write_log( 'Theme Upgrade: Update Property Duration' );

    $args = array(
      'post_type' => COLABS_POST_TYPE,
      'meta_key' => 'expires',
      'post_status' => 'any',
      'nopaging' => true,
    );
    $legacy_expire = new WP_Query( $args );

    foreach( $legacy_expire->posts as $post ) {
      $expires = get_post_meta( $post->ID, 'expires', true );
      $expire_date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime($expires) );
      $duration = colabs_days_between_dates( $expire_date, $post->post_date, 0 );

      if ( intval($duration) < 0 ) {
        // set the default property duration for expired properties
        $duration = 30;
        _colabs_expire_property( $post->ID );
      }

      update_post_meta( $post->ID, '_colabs_property_duration', $duration );

      // Colabs_log()->write_log( sprintf( 'Theme Upgrade: Update Property Durations - Updated property #%d duration to %d', $post->ID, $duration ) );
    }
  }


  /**
   * Upgrade featured properties
   */
  public function upgrade_featured_properties() {
    $args = array(
      'post_type' => COLABS_POST_TYPE,
      'nopaging' => true,
      'meta_key' => 'property_as_featured',
      // 'meta_value' => 'true',
    );

    $legacy_featured = new WP_Query( $args );
    $stickypost = get_option('sticky_posts');

    foreach( $legacy_featured->posts as $post ) {
      $key = array_search($post->ID, $stickypost);
      if ( false == $key ) {
        $stickypost[] = $post->ID;
      }

      // Colabs_log()->write_log( sprintf( 'Theme Upgrade: Update Featured Properties - Updated featured property #%d', $post->ID ) );
    }

    update_option( 'sticky_posts', $stickypost );
  }


  /**
   * Upgrade Theme Options
   */
  public function upgrade_theme_options() {
    $options_need_update = array(
      'colabs_property_paypal_currency' => 'colabs_currency_code',
      'colabs_property_paypal_email' => 'colabs_paypal_email',
      'colabs_use_paypal_sandbox' => 'colabs_paypal_sandbox',
    );

    foreach( $options_need_update as $previous_option => $new_option ) {
      $previous_value = get_option( $previous_option );
      if( $previous_value ) {
        update_option( $new_option, $previous_value );
        // Colabs_log()->write_log( sprintf( 'Theme Upgrade: Update Theme Options - Updated options %s to %s', $previous_option, $new_option ) );
      }
    }
  }


  /**
   * Upgrade Orders
   */
  public function upgrade_orders() {
    global $wpdb;

    $legacy_orders = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}colabs_orders");

    // old => new
    $status_relation = array(
      'completed'     => COLABS_ORDER_COMPLETED,
      'pending_payment' => COLABS_ORDER_PENDING,
      'cancelled'     => COLABS_ORDER_FAILED
    );

    // old => new
    $gateways_relation = array(
      'paypal'    => 'paypal',
      'authorize.net' => 'authorize-net',
      '2checkout'   => '2checkout',
      'google'    => 'google-wallet',
      'manual'    => 'bank-transfer',
    );

    $errors_total = 0;
    if( $legacy_orders ) {
      foreach( $legacy_orders as $legacy_order ) {

        // payment type
        $payment_type = strtolower( $legacy_order->payment_type );

        if ( $payment_type && isset($gateways_relation[$payment_type]) ) {
          $payment_type = $gateways_relation[$payment_type];
        } else {
          $payment_type = __( 'N/A', 'colabsthemes' );
        }

        // status
        if ( ! isset( $status_relation[$legacy_order->status] ) ) {
          $legacy_order->status = 'cancelled';
        }

        // If featured
        if( $legacy_order->featured == true ){
          $additional_price = get_option( 'colabs_cost_to_feature', 0 );
         }else{   
          $additional_price = 0;
        }

        $items = array(
          '_post_type_id' => $legacy_order->property_id,
          'price' => $legacy_order->cost,
          'additional_price' => $additional_price
        );

        $order = Colabs_Order::create( $items );

        if( $legacy_order->property_id ) {
          $post_id = $legacy_order->property_id;
        } else {
          $post_id = 0;
        }

        if ( empty($post_id) ) 
          $post_id = $order->get_id();

        if ( $post_id != $order->get_id() ) {
          // restore trashed posts from cancelled orders and set their status to 'expired', instead
          if ( COLABS_ORDER_FAILED == $status_relation[$legacy_order->status] ) {
            wp_untrash_post( $post_id );
            _colabs_expire_property( $post_id, $canceled = true, 'order_failed' );
          }
        }

        $order_post_data = array(
          'ID' => $order->get_id(),
          'post_type' => COLABS_ORDER_POST_TYPE,
          'post_status'   => $status_relation[$legacy_order->status],
          'post_author' => $legacy_order->user_id,
          'post_date'   => $legacy_order->order_date,
        );

        $updated_post_id = wp_insert_post( $order_post_data, true );

        if ( is_wp_error( $updated_post_id ) ) {
          // Colabs_log()->write_log( sprintf( 'Upgrade Orders: Error updating Order - %d - "%s"', $order->get_id(), $updated_post_id->get_error_message() ) );

          $this->errors->add( 'upgrade-error', sprintf( 'Upgrade Orders: Error updating Order - %d -', $order->get_id() ) );
          $errors_total++;
          continue;
        }

        if( $order ) {
          update_post_meta( $order->get_id(), 'gateway', $payment_type );
        }
      }
    }

    if( $errors_total ) {
      $this->errors->add( 'upgrade-error', sprintf( 'Could not upgrade all legacy orders' ) );
      return $this->errors;
    }

    return true;
  }


  /**
   * Is theme database need update 
   *
   * @return Boolean
   */
  public function is_need_update() {
    $current_db_version = get_option( 'goodliving_db_version', '0' );
    return version_compare( $current_db_version, $this->db_version, '<');
  }


  /**
   * Render messages
   */
  public function output_message( $message = '', $type = 'updated' ) {
    if( empty( $message ) )
      return;

    ?>
    <div class="<?php echo $type; ?>">
      <?php echo $message; ?>
    </div>
    <?php
  }

}

new Goodliving_Theme_Upgrade;