<?php
define('COLABS_POST_TYPE', 'property');
define('COLABS_TAX_TYPE', 'property_type');
define('COLABS_TAX_FEATURE', 'property_features');
define('COLABS_TAX_STATUS', 'property_status');

define('COLABS_DASHBOARD_PAGE', get_permalink(get_option('colabs_dashboard_url')));
define('COLABS_PROFILE_PAGE', get_permalink(get_option('colabs_profile_url')));
define('COLABS_EDIT_PAGE', get_permalink(get_option('colabs_edit_url')));
define('COLABS_SUBMIT_PAGE', get_permalink(get_option('colabs_submit_url')));

// Classes
include( get_template_directory() . '/includes/admin/order-class.php' );
include( get_template_directory() . '/includes/admin/admin-permalink-settings.php' );
include( get_template_directory() . '/includes/admin/admin-statistics.php' );
include( get_template_directory() . '/includes/theme-reports.php' );

// Admin Only Functions
if (is_admin()) :
    require( get_template_directory() . '/includes/admin/admin-panel.php');
    require( get_template_directory() . '/includes/theme-upgrade.php');
endif;

//Require Property Functions
require( get_template_directory() . '/includes/theme-geolocation.php');
require( get_template_directory() . '/includes/theme-orders.php');
require( get_template_directory() . '/includes/theme-gateways.php');
require( get_template_directory() . '/includes/theme-security.php');
require( get_template_directory() . '/includes/theme-emails.php');
require( get_template_directory() . '/includes/theme-cron.php');


add_action( 'init', 'colabs_register_new_post_statuses' );

function colabs_register_new_post_statuses(){
  register_post_status( 'expired', array(
    'label' => _x( 'Expired', 'post', 'colabsthemes' ),
    'public' => true,
    'exclude_from_search' => false,
    'show_in_admin_all_list' => true,
    'show_in_admin_status_list' => true,
    'label_count' => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>' ),
  ) );
}

add_action('admin_footer-post.php', 'colabs_append_post_status_list');
function colabs_append_post_status_list(){
     global $post;
     $complete = '';
     $label = '';
     if($post->post_type == COLABS_POST_TYPE ){
          if($post->post_status == 'expired'){
               $complete = ' selected="selected"';
               $label = '<span id="post-status-display"> '.__('Expired','colabsthemes').'</span>';
          }
          echo '
          <script>
          jQuery(document).ready(function($){
               $("select#post_status").append(\'<option value="expired" '.$complete.'>'.__('Expired','colabsthemes').'</option>\');
               $(".misc-pub-section label").append(\''.$label.'\');
          });
          </script>
          ';
     }
}

function colabs_init_roles() {
  global $wp_roles;

  if (class_exists('WP_Roles'))
    if ( ! isset( $wp_roles ) )
      $wp_roles = new WP_Roles();

  if (is_object($wp_roles)) :
    $wp_roles->add_cap( 'administrator', 'can_submit_property' );
    $wp_roles->add_cap( 'administrator', 'edit_properties' );

    $wp_roles->add_cap( 'editor', 'can_submit_property' );
    $wp_roles->add_cap( 'editor', 'edit_properties' );

    $wp_roles->add_cap( 'contributor', 'can_submit_property' );
    $wp_roles->add_cap( 'contributor', 'edit_properties' );

    $wp_roles->add_cap( 'author', 'can_submit_property' );
    $wp_roles->add_cap( 'author', 'edit_properties' );

    $wp_roles->add_cap( 'subscriber', 'can_submit_property' );
    $wp_roles->add_cap( 'subscriber', 'edit_properties' );
    $wp_roles->add_cap( 'subscriber', 'upload_files' );

  endif;
}

function colabs_load_template( $templates, $data = array() ) {
  global $posts, $post, $wp_query, $wp_rewrite, $wpdb, $comment;
  
  $located = locate_template( $templates );

  if ( ! $located )
    return;

  extract( $data, EXTR_SKIP );

  if ( is_array( $wp_query->query_vars ) )
    extract( $wp_query->query_vars, EXTR_SKIP );  

  require $located;
}

function colabs_get_next_step( $start = 1 ) {

  $step =  _colabs_next_step( colabs_get_listing_error_obj(), $start );

  return $step;
}

// dinamically return the next step
function _colabs_next_step( $errors, $start ) {

  $previous_step = _colabs_curr_step( $start );

  $step = $previous_step;

  if ( ! empty($_POST) && ! $errors->get_error_codes() ) {
    if ( empty($_POST['goback']) )
      $step++;
    else
      $step = $start;
  } elseif ( $errors->get_error_codes() ) {
    $step = _colabs_curr_step( $start );
  }

  if ( $step > _colabs_steps_get_last() ) {
    $step = $previous_step;
  }

  return apply_filters( 'colabs_next_property_submit_step', $step, $previous_step );

}

function _colabs_curr_step( $start ) {
  if ( get_query_var('step') ) {
    return get_query_var('step');
  } else {
    return $start;
  }
}

function _colabs_steps_get_last( $steps = '' ) {
  if ( ! $steps  ) 
    $steps = colabs_steps();
  return max( array_keys( $steps ) );
}

function colabs_steps() {
  $steps = _colabs_property_submit_steps();

  if ( colabs_charge_listings() ) {
    $description = __('Pay/Thank You', 'colabsthemes');
  } else {
    $description = __('Confirm', 'colabsthemes');
  }

  $steps[] = _colabs_confirm_step( $description );

  return apply_filters( 'colabs_property_submit_steps', $steps );
}

function _colabs_property_submit_steps() {

  $steps = array(
    1 => array (
      'name'  => 'submit_form',
      'description' => __('Enter Property Details', 'colabsthemes'),
      'template' => '/includes/forms/submit-form.php',
    ),
    2 => array (
      'name'  => 'preview_form',
      'description' => __('Preview', 'colabsthemes'),
      'template' => '/includes/forms/preview-form.php',
    ),
  );

  return $steps;
}

function _colabs_confirm_step( $description ) {

  $step = array (
    'name'  => 'confirm_form',
    'description' => $description,
    'template' => '/includes/forms/confirm-form.php',
  );
  return $step;
}

function colabs_charge_listings() {
  if(get_option( 'colabs_property_listing_cost' )>0)
  return true;
}

function colabs_get_listing_error_obj(){
  static $errors;

  if ( !$errors ){
    $errors = new WP_Error();
  }
  return $errors;
}

add_action('init','colabs_add_query_vars');
function colabs_add_query_vars(){
  global $wp;
  
  //Submit Page
  $wp->add_query_var('property_id');
  $wp->add_query_var('order_id');
  $wp->add_query_var('step');
  $wp->add_query_var('property_relist');

  //Dashboard Page
  $wp->add_query_var('order_cancel');
  $wp->add_query_var('cancel');
  $wp->add_query_var('confirm');
  $wp->add_query_var('confirm_order_cancel');
  $wp->add_query_var('order_status');
  $wp->add_query_var('property_end');
  $wp->add_query_var('property_delete');
  $wp->add_query_var('property_marksold');
  $wp->add_query_var('property_unsold');
 
  //Edit Page
  $wp->add_query_var('property_edit');
  
  //Single Property
  $wp->add_query_var('update_success');

  $actions = array( 'edit-property', 'new-property', 'relist-property' );
  if ( empty($_POST['action']) || !in_array( $_POST['action'], $actions ) )
    return;
  
  if ( ! wp_verify_nonce( $_POST['nonce'], 'submit_property' ) ) {
    $errors = colabs_get_listing_error_obj();
    $errors->add( 'submit_error', __( '<strong>ERROR</strong>: Sorry, your nonce did not verify.', 'colabsthemes' ) );
    return;
  }
}

add_action('parse_query', 'colabs_property_parse_query');
function colabs_property_parse_query( $wp_query ) {
  
  if(( $wp_query->is_main_query()) &&(is_page(get_option('colabs_dashboard_page_id')))):
    
    if ( get_query_var('order_cancel') || get_query_var('order_status') ) {
      $wp_query->set( 'tab', 'orders' );
    }

    if ( get_query_var('order_status') ) {
      $wp_query->set( 'order_status', array_map( 'wp_strip_all_tags', get_query_var('order_status') ) );
    }
    
    if ( get_query_var('order_cancel') ) {

      $order = colabs_get_order( intval(get_query_var('order_cancel')) );
      
      if ( get_current_user_id() != $order->get_author() ) {
        $wp_query->set( 'order_cancel_msg', -1 );
        return;
      }

      if ( COLABS_ORDER_COMPLETED == $order->get_status() ) {
        $wp_query->set( 'order_cancel_msg', -2 );
        return;
      }

      if ( !empty($order) && get_query_var('confirm_order_cancel') ) {
        $order->failed();
        $wp_query->set( 'order_cancel_success', 1 );
      }

    } elseif ( get_query_var('property_end') && get_query_var('confirm') ) { 

      $property_id = intval( get_query_var('property_end') );
      $property = get_post( $property_id );
      
      if ( $property->ID != $property_id || $property->post_author != get_current_user_id() ) :
        $wp_query->set( 'property_action', -1 );
        return;
      endif;
      
      if ( get_query_var('cancel') ) {
        $pending_payment = colabs_get_pending_payment( $property_id );
        
        $order = colabs_get_order($pending_payment[$property_id]['order_id']);
        if ( $order && ! in_array( $order->get_status(), array( COLABS_ORDER_ACTIVATED, COLABS_ORDER_COMPLETED ) ) ) {
          $order->failed();
        } else{
          _colabs_end_property( $property_id, $cancel = true );
        }

        $wp_query->set( 'property_action', 1 );
      } else {
        
        _colabs_end_property( $property_id );

        $wp_query->set( 'property_action', 2 );
      }
    

    // Property delete
    } elseif( get_query_var( 'property_delete' ) ) {
      _colabs_delete_property( get_query_var( 'property_delete' ) );

    // Property mark as sold
    } elseif( get_query_var( 'property_marksold' ) ) {
      $property_id = get_query_var( 'property_marksold' );
      update_post_meta( $property_id, 'colabs_property_sold', 'true' );
      $sold_term = get_term_by( 'slug', 'sold', COLABS_TAX_STATUS );
      if( $sold_term ) {
        // Save previous property_status into a post meta
        update_post_meta( $property_id, '_colabs_previous_property_status', wp_get_post_terms( $property_id, COLABS_TAX_STATUS, array('fields' => 'ids') ) );
        
        wp_set_post_terms( $property_id, $sold_term->term_id, COLABS_TAX_STATUS );
      }


    // Property set unsold
    } elseif( get_query_var( 'property_unsold' ) ) {
      $property_id = get_query_var( 'property_unsold' );
      update_post_meta( $property_id, 'colabs_property_sold', 'false' );

      $previous_status = get_post_meta( $property_id, '_colabs_previous_property_status', true );
      if( $previous_status ) {
        wp_set_post_terms( $property_id, $previous_status, COLABS_TAX_STATUS );
      } else {
        $sell_term = get_term_by( 'slug', 'sell', COLABS_TAX_STATUS );
        if( $sell_term ) {
          wp_set_post_terms( $id, $sell_term->term_id, COLABS_TAX_STATUS );
        }
      }
    }
  endif;
  
  if(( $wp_query->is_main_query()) && (is_page(get_option('colabs_submit_page_id')))):
    if($wp_query->get( 'property_relist' )){
      $property_id = $wp_query->get( 'property_relist' );
  
      // if ( is_user_logged_in() && ! current_user_can('can_submit_property') ) {
      //   wp_redirect( home_url() );
      //   exit();
      // }
  
      if ( ! colabs_allow_relist() )
        redirect_myproperties();
  
      $wp_query->set( 'property_id', $property_id );
    }
  endif;
  
}

function colabs_container_show_notice( $class, $msgs ) {
  if ( is_string( $msgs ) )
    $msgs = (array) $msgs;
  elseif( is_wp_error( $msgs ) )
    $msgs = $msgs->get_error_messages();

  if ( ! is_array( $msgs ) )
    return false;
?>
  <div class="alert alert-<?php echo esc_attr( $class ); ?>">
    <?php foreach ( $msgs as $msg ) { ?>
      <div><?php echo $msg; ?></div>
    <?php } ?>
  </div>
<?php
}

add_action( 'colabs_show_notices', 'colabs_action_notices' );

if ( !function_exists('colabs_action_notices') ):
function colabs_action_notices() {
  global $post, $message, $errors;

  $dashboard_page = get_option('colabs_dashboard_page_id');
  if ( $err_obj = colabs_get_listing_error_obj() ) {
    if( $err_obj->get_error_codes() ){
      $errors = $err_obj;
    }
  }

  if ( is_wp_error( $errors ) ) { colabs_show_errors($errors); return; }
  elseif ( !empty($message) ) { colabs_container_show_notice( 'success', strip_tags(stripslashes($message)) ); return; }

  if (isset($post)):

    // dashboard notices
    if ( $post->ID == $dashboard_page ) {

      if ( isset($_GET['relist_success']) && is_numeric($_GET['relist_success']) )
        colabs_container_show_notice( 'success', __('Property relisted successfully','colabsthemes') );
      else
        if ( isset($_GET['edit_success']) && is_numeric($_GET['edit_success']) )
          colabs_container_show_notice( 'success', __('Property edited successfully','colabsthemes') );
      else
        if ( isset($_POST['payment_status']) && strtolower($_POST['payment_status'])=='completed' )
          colabs_container_show_notice( 'success', __('Thank you for your Order!','colabsthemes') );

    }

  endif;
  
  if(is_singular( COLABS_POST_TYPE )):
    $status = get_post_status( get_queried_object() );

    switch( $status ){
      case 'pending' :
        colabs_container_show_notice( 'warning', __( 'This property is currently pending and must be approved by an administrator.', 'colabsthemes' ) );
        break;
      case 'draft' :
        colabs_container_show_notice( 'warning', __( 'This is a draft property and must be approved by an administrator.', 'colabsthemes' ) );
        break;
      default:
        break;
    }

    switch( get_query_var('update_success') ){
      case 1 :
        colabs_container_show_notice( 'success', __( 'The property has been successfully updated.', 'colabsthemes' ) );
        break;
    }
  
  endif;
  
}
endif;

function _colabs_end_property( $property_id, $canceled = false, $canceled_desc = 'user_canceled' ) {
  
  if ( ! $property_id )
    return;

  do_action( 'colabs_property_expired', $property_id, $canceled, $canceled_desc );
}

add_action( 'colabs_property_expired' , '_colabs_expire_property', 10, 3 );
function _colabs_expire_property( $property_id, $canceled = false, $canceled_desc = 'user_canceled' ) {
  
  if ( $canceled ) {
    update_post_meta( $property_id, '_colabs_canceled_property', $canceled_desc );
  }
  colabs_update_post_status( $property_id, 'expired' );

}


/**
 * Delete Property
 */
function _colabs_delete_property( $id ) {
  $attachments = get_children( array(
    'post_parent' => $id,
    'post_type' => 'attachment',
    'posts_per_page' => -1,
  ) );

  // Delete all associated attachments
  if( count($attachments) > 0 ) {
    foreach( $attachments as $attachment_id => $attachment ) {
      wp_delete_attachment( $attachment_id, true );
    }
  }

  // Delete the post
  if( wp_delete_post( $id, true ) ) {
    return true;
  } else {
    return false;
  }
}


function colabs_allow_relist() {
  return ( 'true' == get_option( 'colabs_allow_relist' ) );
}

if (!function_exists('redirect_myproperties')) {
function redirect_myproperties( $query_string = '' ) {
  $url = COLABS_DASHBOARD_PAGE;
  if (is_array($query_string)) $url = add_query_arg( $query_string, $url );
    wp_redirect($url);
    exit();
}
}

//Handle Submit Property
add_action( 'wp_loaded', 'colabs_handle_property_submit_form' );

function colabs_handle_property_submit_form() {
  
  if ( ! isset($_POST['property_submit']) )
    return;
    
  $actions = array( 'edit-property', 'new-property', 'relist-property' );
  if ( empty($_POST['action']) || !in_array( $_POST['action'], $actions ) )
      return;
  
  // if ( !current_user_can( 'can_submit_property' ) )
  //   return;
  
  $property = colabs_handle_update_property_listing();
  if ( ! $property ) {
    // there are errors, return to current page
    return;
  }
 
  if ( 'edit-property' == $_POST['action'] ) {
    
    // maybe update property status
    if( _colabs_edited_property_requires_moderation( $property ) ) {
      colabs_update_post_status( $property->ID, 'pending' );
      
      // send notification email
      //colabs_edited_property_pending( $property->ID );
    }

    wp_redirect( add_query_arg( 'update_success', '1', get_permalink( $property->ID ) ) );
    exit();
  } 

  $args = array( 
    'property_id' => $property->ID, 
    'step ' => colabs_get_next_step()
  );

  if ( !empty($_POST['relist']) ) {
    $args['property_relist'] = $property->ID;
  }

  if ( !empty($_POST['order_id']) ) {
    $args['order_id'] = intval($_POST['order_id']);
  }

  // redirect to next step
  wp_redirect( add_query_arg( $args, COLABS_SUBMIT_PAGE ) );
  exit();
}

function _colabs_edited_property_requires_moderation( $property ) {
  return ( in_array( $property->post_status, array( 'publish', 'draft' )) && 'true' == get_option('colabs_property_require_moderation') );
}

function colabs_get_listing_tax( $name, $taxonomy ) {

  if ( isset( $_REQUEST[$name] ) && $_REQUEST[$name] != -1 ) {
    $listing_tax = get_term( $_REQUEST[$name], $taxonomy );
    $term_id = is_wp_error( $listing_tax ) ? false : $listing_tax->term_id;
  } else {
    $term_id = false;
  }

  return $term_id;
}

function colabs_get_ip() {
  if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
    $ip = $_SERVER['HTTP_CLIENT_IP']; // ip from share internet
  } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; // ip from proxy
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}

/**
 * Convert all array content into integer
 */
function colabs_convert_arr_int( $key ) {
  return (int) $key;
}

function colabs_handle_update_property_listing() {
  
  $errors = apply_filters( 'colabs_listing_validate_fields', colabs_get_listing_error_obj() );
  if( $errors->get_error_codes() ){
    return false;
  }
   
  if ( isset($_POST['ID']) ) {
    $property_id = intval($_POST['ID']);
    $property = get_post( $property_id );
  }
  
  if ( empty($property) ) {
    $action = 'insert';
  } elseif( isset($_POST['relist']) ) {
    $action = 'relist';
  } else {
    $action = 'update';
  }
  
  // Create post object
  $args = array(
    'post_title'    => wp_strip_all_tags( $_POST['post_title'] ),
    'post_content'  => $_POST['post_content'],
    'post_type'     => COLABS_POST_TYPE,
    'post_author'   => get_current_user_id(),
  );
  
  if ( empty($property) ) {

    // Insert the post into the database
    $property_id = wp_insert_post( $args );
  
  }else {
    $args['ID'] = $_POST['ID'];
    
    if ( 'expired' == $property->post_status ) 
      $args['post_status'] = 'draft';

    $property_id = wp_update_post( $args );
  }

  // If inserted successfully
  if($property_id) {
    
    // Featured Image
    if ( isset( $_POST['_thumbnail_id'] ) ) update_post_meta( $property_id, '_thumbnail_id', absint( $_POST['_thumbnail_id'] ) );
    
    update_post_meta($property_id, "property_price", $_POST['property_price']); 
    update_post_meta($property_id, "property_beds", $_POST['property_beds']); 
    update_post_meta($property_id, "property_baths", $_POST['property_baths']);
    update_post_meta($property_id, "property_size", $_POST['property_size']); 
    update_post_meta($property_id, "property_garage", $_POST['property_garage']);
    update_post_meta($property_id, "property_furnished", $_POST['property_furnished']); 
    update_post_meta($property_id, "property_mortgage", $_POST['property_mortgage']);
    update_post_meta($property_id, "property_price_periode", $_POST['property_price_periode']);
    update_post_meta($property_id, "property_agent", $_POST['property_agent']); 

    // Location
    if($_POST['property_location_new']!=''){
      $get_property_location = wp_insert_term( $_POST['property_location_new'], 'property_location' );
      wp_set_post_terms( $property_id, $get_property_location['term_id'], 'property_location' );  
    }else{
      wp_set_post_terms( $property_id, $_POST['property_location'], 'property_location' );  
    }

    //Taxonomy
    $property_type = $_POST['property_type'];
    if( !is_array( $property_type ) ) {
      $property_type = array( $property_type );
    }

    wp_set_object_terms( $property_id, array_map('colabs_convert_arr_int', $property_type), COLABS_TAX_TYPE );  
    wp_set_object_terms( $property_id, (int) $_POST['property_status'], COLABS_TAX_STATUS );
    wp_set_object_terms( $property_id, array_map('colabs_convert_arr_int', $_POST['property_features']), COLABS_TAX_FEATURE );
    
    // store the user IP
    update_post_meta( $property_id, '_user_IP', colabs_get_ip(), true );
    
    // Gallery Images
    $attachment_ids = array_filter( explode( ',', $_POST['_property_image_gallery'] ) );
    update_post_meta( $property_id, '_property_image_gallery', implode( ',', $attachment_ids ) );
    
    //Google Maps
    $colabs_map_input_names = array('colabs_maps_enable','colabs_maps_streetview','colabs_maps_address','colabs_maps_from','colabs_maps_to','colabs_maps_long','colabs_maps_lat','colabs_maps_zoom','colabs_maps_type','colabs_maps_mode','colabs_maps_pov_pitch','colabs_maps_pov_yaw','colabs_maps_walking');
    foreach ($colabs_map_input_names as $name) {
      $var = $name;
      
      if (isset($_POST[$var])) {            
        if( get_post_meta( $property_id, $name ) == "" ) {
          add_post_meta($property_id, $name, $_POST[$var], true );
        }
        elseif($_POST[$var] != get_post_meta($property_id, $name, true)) {
          update_post_meta($property_id, $name, $_POST[$var]);
        }
        elseif($_POST[$var] == "") {
          delete_post_meta($property_id, $name, get_post_meta($property_id, $name, true));
        }
      } elseif(!isset($_POST[$var]) && $name == 'colabs_maps_enable') {
        update_post_meta($property_id, $name, 'false'); 
      } else {
        delete_post_meta($property_id, $name, get_post_meta($property_id, $name, true));      
      }
    }

    
    //Featured
    if ($_POST['featureit']=='true') :
      $stickypost = get_option('sticky_posts');
      $key = array_search($property_id, $stickypost);
      if (false == $key) {
          $stickypost[] = $property_id;
      }
      update_option('sticky_posts', $stickypost);
    endif;
    if ($_POST['action']=='relist-property') :
      if((is_sticky($property_id))&&(($_POST['featureit']!='true'))):
        $stickypost = get_option('sticky_posts');
        $key = array_search($property_id, $stickypost);
        if (false != $key) {
          unset($stickypost[$key]);
        }
        update_option('sticky_posts', $stickypost);  
      endif;  
    endif;
  
    return apply_filters( 'colabs_handle_update_listing', get_post( $property_id ) );
  }
}

function colabs_update_post_status( $post_id, $status ) {
  wp_update_post( array(
    'ID' => $post_id,
    'post_status' => $status
  ) );
}

function _colabs_moderate_posts() {
  return (bool) ( 'true' == get_option('colabs_property_require_moderation') );
}

function _colabs_needs_publish( $post ){
  return in_array( $post->post_status, array( 'draft', 'expired' ) );
}

function _colabs_set_post_duration( $post_id, $duration = '' ) {
  if (( '' == $duration ) && ( colabs_charge_listings() )) $duration = get_option( 'colabs_prun_period' );
  update_post_meta( $post_id, '_colabs_property_duration', $duration );
}

function colabs_auth_redirect_login() {
  if ( !is_user_logged_in() ) {
    nocache_headers();
    global $wp;
    $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );  
    wp_redirect( wp_login_url( $current_url ) );
    exit();
  }
}

if (!function_exists('redirect_profile')) {
function redirect_profile( $query_string = '' ) {
  $url = COLABS_PROFILE_PAGE;
  if (is_array($query_string)) $url = add_query_arg( $query_string, $url );
    wp_redirect($url);
    exit();
}
}

// Remaining days function
function colabs_seconds_to_days( $seconds ) {
  $days = $seconds / 24 / 60 / 60;
  return $days;
}

function colabs_days_between_dates( $date1, $date2 = '', $precision = 1 ) {
  if ( empty( $date2 ) )
    $date2 = current_time('mysql');

  if ( ! is_string( $date1 ) || ! is_string( $date2 ) )
    return false;

  $date1 = strtotime( $date1 );
  $date2 = strtotime( $date2 );

  $days = round( colabs_seconds_to_days( $date1 - $date2 ), $precision );
  return $days;
}

if (!function_exists('colabs_remaining_days')) {
function colabs_remaining_days($post_id) { 
  $post = get_post( $post_id );
  $remain_days = __( 'No Expires', 'colabsthemes' );

  $days = get_post_meta($post->ID, '_colabs_property_duration', true);
  
  if( get_post_meta($post->ID, 'expires', true) && !$days ) {
    $expire_timestamp = get_post_meta($post->ID, 'expires', true);
    $days = colabs_days_between_dates( date('y-m-d', $expire_timestamp ), date( 'y-m-d', strtotime('NOW') ), $precision = 0 ); 
  }

  if ( $days ) {
    if ( $days >= 1 ) {
      $expire_date = strtotime( $post->post_date . '+' . $days . ' days' );
      $days = colabs_days_between_dates( date('y-m-d', $expire_date ), date( 'y-m-d', strtotime('NOW') ), $precision = 0 ); 
      if ( $days >= 1 ){
        if( $days > 30 ){
          $remain_days = __('Valid Until','colabsthemes').'<small>( '. date_i18n(get_option('date_format'), $expire_date) .' )</small>';
        }else{
          $remain_days = __('Valid Until','colabsthemes').'<small>( '. $days . ' ' . _n( 'Day', 'Days', $days, 'colabsthemes' ).' )</small>';
        }
      }else{
        // $remain_days = __('Valid Until','colabsthemes').'<small>( '.human_time_diff( strtotime('NOW'), $expire_date ).' )</small>';
        $remain_days = __('Expired', 'colabsthemes');
      }  
    } else
      $remain_days = __('Expired', 'colabsthemes');
  }

  return apply_filters( 'colabs_remaining_days', $remain_days );
  
}
}

add_action( 'colabs_order_failed', '_colabs_maybe_cancel_property', 10 );

function _colabs_maybe_cancel_property( $order ) {
  $post_id = $order->get_post_type_id();
  if ( COLABS_POST_TYPE == get_post_type( $post_id ) ) {
    _colabs_end_property( $post_id, $cancel = true, 'order_failed' );
  }

}

function colabs_get_expired_property_days_notify() {
  $days_notify = array( 1, 5 );
  return apply_filters( 'colabs_expired_property_days_notify', $days_notify );
}

add_action( 'colabs_check_properties_expired', 'colabs_check_properties_expired' );

function colabs_check_properties_expired() {

  // expired
  $expired_posts = new WP_Query( array(
    'post_type' => COLABS_POST_TYPE,
    'post_status' => 'publish',
    'expiring_properties' => true,
    'nopaging' => true,
  ) );

  foreach ( $expired_posts->posts as $post ) {
    _colabs_end_property( $post->ID );
  }

  // expiring soon

  if ( 'true' == get_option('colabs_expired_property_email_owner') ) {

    // notify users when the property is about to expire - default is 1 day before and 5 days before
    $days_notify = colabs_get_expired_property_days_notify();

    if ( ! $days_notify )
      return;

    $notified_ids = array();

    foreach ( $days_notify as $key => $days ) :

      $notification_list = array();

      // retrieve properties expiring within n days
      $expiring_soon = new WP_Query( array(
        'post_type' => COLABS_POST_TYPE,
        'post_status' => 'publish',
        'expiring_properties' => true,
        'expire_days' => $days,
        'meta_query' => array(
          'relation' => 'AND',
          array(
            'key' => '_colabs_days_expire_reminder_email_sent',
            'compare' => 'NOT EXISTS'
          ),
          // only notify about properties whose duration is superior to the notification day
          array(
            'key' => '_colabs_property_duration',
            'compare' => '>',
            'value' => $days,
          )
        ),
        'nopaging' => true,
      ) );

      $expiring_notified = array();
      if ( isset($days_notify[$key+1]) && $days < $days_notify[$key+1] ) {

        // retrieve expiring properties with notifications sent to user that are greater then the current expiration time interval
        $expiring_notified = new WP_Query( array(
          'post_type' => COLABS_POST_TYPE,
          'post_status' => 'publish',
          'expiring_properties' => true,
          'expire_days' => $days,
          'meta_query' => array(
            array(
              'key' => '_colabs_days_expire_reminder_email_sent',
              'value' => $days_notify[$key+1],
              'compare' => '='
            )
          ),
          'nopaging' => true,
        ) );

      }

      if ( $expiring_soon->post_count > 0 ) {
        foreach ( $expiring_soon->posts as $post ) {
          $notification_list[] = $post->ID;
        }
      }

      if ( sizeof($expiring_notified) > 0 ) {
        foreach ( $expiring_notified->posts as $post ) {
          $notification_list[] = $post->ID;
        }
      }

      if ( sizeof($notification_list) > 0 ) {

        foreach ( $notification_list as $id ) {
          if ( ! in_array( $id, $notified_ids ) ) {
            do_action( 'colabs_property_expiring_soon', $id, $days );
            $notified_ids[] = $id;
          }
        }

      }

    endforeach;

  }

}

add_action( 'colabs_property_expiring_soon' , '_colabs_set_expire_reminder_meta', 11, 2 );

function _colabs_set_expire_reminder_meta( $post_id, $days = 0 ) {
  update_post_meta( $post_id, '_colabs_days_expire_reminder_email_sent', $days );
}

add_filter( 'posts_clauses', 'colabs_expired_properties_sql', 10, 2 );

function colabs_expired_properties_sql( $clauses, $wp_query ) {
  global $wpdb;

  if ( $wp_query->get( 'expiring_properties' ) ) {
    $clauses['join'] .= " INNER JOIN " . $wpdb->postmeta ." AS exp1 ON (" . $wpdb->posts .".ID = exp1.post_id)";

    if ( $wp_query->get( 'expire_days' ) ) {
      $days = $wp_query->get( 'expire_days' );
    }
    if ( ! empty($days) ) {
      $clauses['where'] .= " AND ( exp1.meta_key = '_colabs_property_duration' AND DATE_ADD(post_date, INTERVAL exp1.meta_value DAY) < DATE_ADD('".current_time( 'mysql' )."', INTERVAL $days DAY) AND exp1.meta_value > 0 )";
    } else {
      $clauses['where'] .= " AND ( exp1.meta_key = '_colabs_property_duration' AND DATE_ADD(post_date, INTERVAL exp1.meta_value DAY) < '" . current_time( 'mysql' ) . "' AND exp1.meta_value > 0 )";
    }
  }

  return $clauses;
}

add_action( 'transition_post_status', '_colabs_clear_meta_flags', 10, 3 );

function _colabs_clear_meta_flags( $new_status, $old_status, $post ) {

  if ( COLABS_POST_TYPE != $post->post_type )
    return;

  if ( 'publish' == $new_status ) {
    delete_post_meta( $post->ID, '_colabs_days_expire_reminder_email_sent' );
    delete_post_meta( $post->ID, '_colabs_canceled_property' );
  }

}

add_action( 'expired_to_publish', 'colabs_update_property_start_date' );
add_action( 'draft_to_publish', 'colabs_update_property_start_date' );

function colabs_update_property_start_date( $post ) {

  if ( $post->post_type == COLABS_POST_TYPE ) {
    wp_update_post( array(
      'ID' => $post->ID,
      'post_date' => current_time( 'mysql' )
    ) );
  }
}

function colabs_get_property_price($post_id){
  $price = get_post_meta($post_id, 'property_price',true);
  if($price){
    $price = colabs_get_price($price, '', get_option('colabs_currency_symbol'));
    $terms = get_the_terms( $post_id, COLABS_TAX_STATUS );
    $property_status = ! empty( $terms ) && isset( current( $terms )->slug ) ? sanitize_title( current( $terms )->slug ) : 'sell';
    if('rent' == $property_status){
      $periode = get_post_meta($post_id,'property_price_periode',true);
      switch($periode):
        case 'day':
          $price = $price.'/'.__('Day','colabsthemes');
        break;
        case 'year':
          $price = $price.'/'.__('Year','colabsthemes');
        break;
        default:
          $price = $price.'/'.__('Month','colabsthemes');
        break;
      endswitch;
    }
  }else{
    $price = colabs_get_price(0);
  }
  return $price;
}

add_action( 'wp_loaded', 'colabs_handle_property_confirmation' );

// handle free properties - update the property status after user confirmation
function colabs_handle_property_confirmation() {

  if ( empty($_POST['property_confirm']) )
    return; 

  if ( ! $property_id = intval($_POST['ID']) ) {
    $errors = colabs_get_listing_error_obj();
    $errors->add( 'submit_error', __( '<strong>ERROR</strong>: Cannot update property status. Property ID not found.', 'colabsthemes' ) );
    return;
  }
  colabs_update_post_status( $property_id );

  _colabs_set_post_duration( $property_id );
  
  do_action( 'colabs_activate_post', $property_id );

  wp_redirect( get_permalink( $property_id ) );
  exit();
}


/**
 * Process Upload Avatar
 */
add_action( 'init', 'colabs_process_upload_avatar' );

function colabs_process_upload_avatar() {

  $posted = array();
  $results_ajax = array(
    'error' => true,
    'messages' => array()
  );
  $errors = new WP_Error();

  if ( isset($_POST['colabs_upload_avatar']) ) {

    // Security check
    check_admin_referer( 'colabs-change-avatar' );
    
    $file_array = $_FILES['user_avatar'];
    if( $file_array['name'] == '' ) {
      $errors->add( 'avatar_upload_error', __('Please choose image you want to upload', 'colabsthemes') );
    }

    // User upload the image
    else {

      // Check file extensions
      $allowed = array(
        'jpg',
        'jpeg',
        'png',
        'gif'
      );
      $file_extension = strtolower(substr(strrchr( $file_array['name'], "."), 1));
      if ( !in_array($file_extension, $allowed) ) {
        $errors->add( 'avatar_upload_error', __('Only png, gif and jpg/jpeg files are allowed.', 'colabsthemes'));
      }

      // All is okay!!
      if ($errors && sizeof($errors)>0 && $errors->get_error_code()) {
      } else {
        $media_file = wp_upload_bits( $file_array['name'], null, @file_get_contents($file_array['tmp_name']) );

        if( $media_file !== false ) {
          
          // These files need to be included as depedencies when on front end
          require_once( ABSPATH . 'wp-admin/includes/image.php' );
          require_once( ABSPATH . 'wp-admin/includes/file.php' );
          require_once( ABSPATH . 'wp-admin/includes/media.php' );

          $media_id = media_handle_upload( 'user_avatar', 0 );

          // Image successfully uploaded
          if( !is_wp_error( $media_id ) ) {
            update_user_meta( $_POST['user_id'], 'user_custom_avatar_id', $media_id );
            $results_ajax['media_id'] = $media_id;
            $results_ajax['user_avatar'] = colabs_get_user_avatar( $_POST['user_id'], 130 );
          } else {
            $errors->add( 'avatar_upload_error', __('Error uploading file, please try again', 'colabsthemes') );
          }
        }
      }
    }

    // Check if request from ajax 
    if( is_doing_ajax() ) {
      if ($errors && sizeof($errors)>0 && $errors->get_error_code()) {
        foreach ($errors->errors as $error) {
          $results_ajax['messages'][] = $error[0];
        }
      } else {
        $results_ajax['error'] = false;
        $results_ajax['messages'][] = __('Your application has been sent successfully.', 'colabsthemes');
      }

      header('Content-Type: application/json');
      echo json_encode( $results_ajax );
      exit;
    }
  }
}



/*-----------------------------------------------------------------------------------*/
/* CoLabs Google Mapping */
/*-----------------------------------------------------------------------------------*/

function colabs_maps_single_output($args){

  $key = get_option('colabs_maps_apikey');
  
  // No More API Key needed
  
  if ( !is_array($args) ) 
    parse_str( $args, $args );
    
  extract($args); 
  $map_height = get_option('colabs_maps_single_height');
     
  $lang = get_option('colabs_maps_directions_locale');
  $locale = '';
  if(!empty($lang)){
    $locale = ',locale :"'.$lang.'"';
  }
  $extra_params = ',{travelMode:G_TRAVEL_MODE_WALKING,avoidHighways:true '.$locale.'}';
  
  if(empty($map_height)) { $map_height = 250;}
  
  ?>

    <script type="text/javascript">
    jQuery(document).ready(function(){
      function initialize() {
        
        
      <?php if($streetview == 'on'){ ?>

        var location = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
        
        <?php 
        // Set defaults if no value
        if ($yaw == '') { $yaw = 20; }
        if ($pitch == '') { $pitch = -20; }
        ?>
        
        var panoramaOptions = {
            position: location,
            pov: {
              heading: <?php echo $yaw; ?>,
              pitch: <?php echo $pitch; ?>,
              zoom: 1
            }
        };
        
        var map = new google.maps.StreetViewPanorama(document.getElementById("single_map_canvas"), panoramaOptions);
        window.prop_map = map;
        
        google.maps.event.addListener(map, 'error', handleNoFlash);
        
        <?php if(get_option('colabs_maps_scroll') == 'true'){ ?>
          map.scrollwheel = false;
        <?php } ?>
        
      <?php } else { ?>
        
          <?php switch ($type) {
              case 'G_NORMAL_MAP':
                $type = 'ROADMAP';
                break;
              case 'G_SATELLITE_MAP':
                $type = 'SATELLITE';
                break;
              case 'G_HYBRID_MAP':
                $type = 'HYBRID';
                break;
              case 'G_PHYSICAL_MAP':
                $type = 'TERRAIN';
                break;
              default:
                $type = 'ROADMAP';
                break;
          } ?>
          
          myLatlng = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
          var myOptions = {
            zoom: <?php echo $zoom; ?>,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.<?php echo $type; ?>,
            streetViewControl: false
          };
          var map = new google.maps.Map(document.getElementById("single_map_canvas"),  myOptions);
          
          window.prop_map = map;
          <?php if(get_option('colabs_maps_scroll') == 'true'){ ?>
          map.scrollwheel = false;
          <?php } ?>
          
          <?php if($mode == 'directions'){ ?>
            directionsPanel = document.getElementById("featured-route");
            directions = new GDirections(map, directionsPanel);
            directions.load("from: <?php echo $from; ?> to: <?php echo $to; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);
          <?php
          } else { ?>
       
            var root = "<?php echo get_template_directory_uri(); ?>";
            var the_link = '<?php echo get_permalink(get_the_id()); ?>';
            <?php $title = str_replace(array('&#8220;','&#8221;'),'"',get_the_title(get_the_id())); ?>
            <?php $title = str_replace('&#8211;','-',$title); ?>
            <?php $title = str_replace('&#8217;',"`",$title); ?>
            <?php $title = str_replace('&#038;','&',$title); ?>
            var the_title = '<?php echo html_entity_decode($title) ?>'; 
            
            
            var color = '<?php echo get_option('colabs_google_pin'); ?>';
            createMarker(map,myLatlng,root,the_link,the_title,color);
            
            <?php 
        
            if(isset($_POST['colabs_maps_directions_search'])){ ?>
            
            directionsPanel = document.getElementById("featured-route");
            directions = new GDirections(map, directionsPanel);
            directions.load("from: <?php echo htmlspecialchars($_POST['colabs_maps_directions_search']); ?> to: <?php echo $address; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);
                        
            directionsDisplay = new google.maps.DirectionsRenderer();
            directionsDisplay.setMap(map);
              directionsDisplay.setPanel(document.getElementById("featured-route"));
            
            <?php if($walking == 'on'){ ?>
            var travelmodesetting = google.maps.DirectionsTravelMode.WALKING;
            <?php } else { ?>
            var travelmodesetting = google.maps.DirectionsTravelMode.DRIVING;
            <?php } ?>
            var start = '<?php echo htmlspecialchars($_POST['colabs_maps_directions_search']); ?>';
            var end = '<?php echo $address; ?>';
            var request = {
                  origin:start, 
                  destination:end,
                  travelMode: travelmodesetting
              };
              directionsService.route(request, function(response, status) {
                  if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                  }
                }); 
                
            <?php } ?>

        <?php } ?>
      <?php } ?>
      

      }
      function createMarker(map,point,root,the_link,the_title,color) {

        var blueIcon = root + "/images/google-pins/blue-dot.png";
        var redIcon = root + "/images/google-pins/red-dot.png"; 
        var greenIcon = root + "/images/google-pins/green-dot.png";   
        var yellowIcon = root + "/images/google-pins/yellow-dot.png";         
        var tealIcon = root + "/images/google-pins/teal-dot.png"; 
        var blackIcon = root + "/images/google-pins/black-dot.png"; 
        var whiteIcon = root + "/images/google-pins/white-dot.png"; 
        var purpleIcon = root + "/images/google-pins/purple-dot.png"; 
        var pinkIcon = root + "/images/google-pins/pink-dot.png"; 
        var customIcon = color;
        
        var image = root + "/images/google-pins/red-dot.png";
        
        if(color == 'blue')     { image = blueIcon } 
        else if(color == 'red')   { image = redIcon } 
        else if(color == 'green') { image = greenIcon } 
        else if(color == 'yellow')  { image = yellowIcon } 
        else if(color == 'teal')  { image = tealIcon } 
        else if(color == 'black') { image = blackIcon }  
        else if(color == 'white') { image = whiteIcon } 
        else if(color == 'purple')  { image = purpleIcon } 
        else if(color == 'pink')  { image = pinkIcon } 
        else { image = customIcon } 
          
        var marker = new google.maps.Marker({
            map:map,
            draggable:false,
            animation: google.maps.Animation.DROP,
            position: point,
            icon: image,
            title: the_title
          });
          
          /* google.maps.event.addListener(marker, 'click', function() {
            window.location = the_link;
          }); */
          
          return marker;
        
      }
      function handleNoFlash(errorCode) {
        if (errorCode == FLASH_UNAVAILABLE) {
        alert("Error: Flash doesn't appear to be supported by your browser");
        return;
        }
      }
      
      // initialize();
      window.map_init = initialize; 
      
      jQuery('#property-maps').on('tabsshow', function(){
        google.maps.event.trigger(prop_map, 'resize');
        prop_map.setCenter(myLatlng);
      });

    });

    function loadScript() {
      var script = document.createElement('script');
          script.type = 'text/javascript';
          script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&' +
              'callback=map_init';
          document.body.appendChild(script);
    }

    jQuery(window).load(function(){
      if( typeof google == 'undefined' ) {      
        loadScript();
      } else {      
        initialize();
      }
    });
  
  </script>
  <div id="single_map_canvas" style="width:500px; height: <?php echo $map_height; ?>px"></div>
<?php
}

function colabsthemes_metabox_maps_header($id = ''){  
  global $post;  
  $pID = $id;
  
  if('' == $pID) {
    $pID = $post->ID;
  }

  // If on admin
  if( is_admin() && empty($pID) ) {
    $pID = $_GET['post'];
  }

  if( isset( $_GET['property_relist'] ) ) {
    $pID = $_GET['property_relist'];
  }

  if( isset( $_GET['property_edit'] ) ) {
    $pID = $_GET['property_edit'];
  }
  
  ?>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript">
      jQuery(document).ready(function(){
        var map;
        var geocoder;
        var address;
        var pano;
        var location;
        var markersArray = [];
        
        <?php 
        $mode = get_post_meta($pID,'colabs_maps_mode',true);
        if($mode == 'directions'){ ?>
        var mode = 'directions';
        <?php } else { ?>
        var mode = 'plot';
        <?php } ?>
        
        jQuery('#map_mode a').click(function(){
        
          var mode_set = jQuery(this).attr('id');
          if(mode_set == 'colabs_directions_map'){
            mode = 'directions';
            jQuery('.colabs_plot').hide();
            jQuery('.colabs_directions').show();
            jQuery('#colabs_maps_mode').val('directions');

          }
          else {
            mode = 'plot';
            jQuery('.colabs_plot').show();
            jQuery('.colabs_directions').hide();
            jQuery('#colabs_maps_mode').val('plot');
          }
          
          jQuery('#map_mode a').removeClass('active');
          jQuery(this).addClass('active');
        
          return false;
        });
        
        jQuery('#colabs_maps_to').focus(function(){
          jQuery('#colabs_maps_from').removeClass('current_input');
          jQuery(this).addClass('current_input');
        });
        jQuery('#colabs_maps_from').focus(function(){
          jQuery('#colabs_maps_to').removeClass('current_input');
          jQuery(this).addClass('current_input');
        });
      
        function initialize() {
          
          <?php 
          $lat = get_post_meta($pID,'colabs_maps_lat',true);
          $long = get_post_meta($pID,'colabs_maps_long',true);
          $yaw = get_post_meta($pID,'colabs_maps_pov_yaw',true);
          $pitch = get_post_meta($pID,'colabs_maps_pov_pitch',true);
          $type = get_post_meta($pID,'colabs_maps_default_maptype',true);
         
          if(empty($long) && empty($lat)){
            //Defaults...
          $lat = '40.7142691';
          $long = '-74.0059729';
          $zoom = get_option('colabs_maps_default_mapzoom');
          } else { 
            $zoom = get_post_meta($pID,'colabs_maps_zoom',true); 
          }
          if(empty($yaw) OR empty($pitch)){
            $pov = 'yaw:20,pitch:-20';
          } else {
            $pov = 'yaw:' . $yaw . ',pitch:' . $pitch;
          }
          
          ?>
          
          // Manage API V2 existing data
          <?php switch ($type) {
            case 'G_NORMAL_MAP':
              $type = 'ROADMAP';
              break;
            case 'G_SATELLITE_MAP':
              $type = 'SATELLITE';
              break;
            case 'G_HYBRID_MAP':
              $type = 'HYBRID';
              break;
            case 'G_PHYSICAL_MAP':
              $type = 'TERRAIN';
              break;
            default:
              $type = 'ROADMAP';
                break;
          } ?>
          
          // Create Standard Map
          location = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
          var myOptions = {
              zoom: <?php echo $zoom; ?>,
              center: location,
              mapTypeId: google.maps.MapTypeId.<?php echo $type; ?>,
              streetViewControl: false
          };
          map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
          
              <?php
              // Set defaults if no value
          if ($yaw == '') { $yaw = 20; }
          if ($pitch == '') { $pitch = -20; }
          ?>
          
          // Create StreetView Map    
          var panoramaOptions = {
              position: location,
            pov: {
              heading: <?php echo $yaw; ?>,
              pitch: <?php echo $pitch; ?>,
              zoom: 1
            }
          };  
          pano = new google.maps.StreetViewPanorama(document.getElementById("pano"), panoramaOptions);
          
          // Set initial Zoom Levels
          var z = map.getZoom();        
              // jQuery('#colabs_maps_zoom option').removeAttr('selected');
              // jQuery('#colabs_maps_zoom option[value="'+z+'"]').attr('selected','selected');
              
              // Event Listener - StreetView POV Change
              google.maps.event.addListener(pano, 'pov_changed', function(){
                var headingCell = document.getElementById('heading_cell');
              var pitchCell = document.getElementById('pitch_cell');
                jQuery("#colabs_maps_pov_yaw").val(pano.getPov().heading);
              jQuery("#colabs_maps_pov_pitch").val(pano.getPov().pitch);
              
              });
              
              // Event Listener - Standard Map Zoom Change
              google.maps.event.addListener(map, 'zoom_changed', function(){
                var z = map.getZoom();        
                jQuery('#colabs_maps_zoom option').removeAttr('selected');
                jQuery('#colabs_maps_zoom option[value="'+z+'"]').attr('selected','selected');
              });
              
              // Event Listener - Standard Map Click Event
              geocoder = new google.maps.Geocoder();
              google.maps.event.addListener(map, "click", getAddress);
            
        } // End initialize() function
        
        // Adds the overlays to the map, and in the array
        function addMarker(location) {
            marker = new google.maps.Marker({
              position: location,
              map: map
            });
            markersArray.push(marker);
        } // End addMarker() function
          
        // Removes the overlays from the map, but keeps them in the array
        function clearOverlays() {
            if (markersArray) {
              for (i in markersArray) {
                  markersArray[i].setMap(null);
              }
            }
        } // End clearOverlays() function
        
        // Deletes all markers in the array by removing references to them
        function deleteOverlays() {
          if (markersArray) {
              for (i in markersArray) {
                  markersArray[i].setMap(null);
              }
              markersArray.length = 0;
            }
        } // End deleteOverlays() function

        // Shows any overlays currently in the array
        function showOverlays() {
            if (markersArray) {
              for (i in markersArray) {
                  markersArray[i].setMap(map);
              }
            }
        } // End showOverlays() function
        
        // Sets initial marker on centre point
        function setSavedAddress() {
          point = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
          addMarker(point);
          } // End setSavedAddress() function
        
        // Click event for address
        function getAddress(event) {
            
            clearOverlays();
            point = new google.maps.LatLng(event.latLng.lat(),event.latLng.lng());
          addMarker(point);
            if(mode == 'directions'){
            jQuery('#colabs_maps_lat').attr('value',event.latLng.lat());
            jQuery('#colabs_maps_long').attr('value',event.latLng.lng());

          } else {
            jQuery('#colabs_maps_lat').attr('value',event.latLng.lat());
            jQuery('#colabs_maps_long').attr('value',event.latLng.lng());
          }
          
            if (event.latLng != null) {
            address = event.latLng;
            geocoder.geocode( { 'location': address}, showAddress);
            }
            if (event.latLng) {
              pano.setPosition(event.latLng);
              pano.setPov({heading:<?php echo $yaw; ?>,pitch:<?php echo $pitch; ?>,zoom:1});
            }
        } // End getAddress() function
        
        // Updates fields with address data
        function showAddress(results, status) {
          
          if (status == google.maps.GeocoderStatus.OK) {
                deleteOverlays();
                
                map.setCenter(results[0].geometry.location);
                  
                addMarker(results[0].geometry.location);
                    
                place = results[0].formatted_address;
                latlngplace = results[0].geometry.location;
                    
            if(mode == 'directions'){
              jQuery('.current_input').attr('value',place);
            } else {
              jQuery('#colabs_maps_address').attr('value',place);
            }
                      
              } else {
                alert("Status Code:" + status);
                
              }
        } // End showAddress() function
        
        // addAddressToMap() is called when the geocoder returns an
        // answer.  It adds a marker to the map.
        function addAddressToMap(results, status) {
          
          deleteOverlays();
          if (status != google.maps.GeocoderStatus.OK) {
          alert("Sorry, we were unable to geocode that address");
          } else {
          place = results[0].formatted_address;
          point = results[0].geometry.location;         
          
          addMarker(point);
      
          map.setCenter(point, <?php echo $zoom; ?>);
          pano.setPosition(point);
            pano.setPov({heading:<?php echo $yaw; ?>,pitch:<?php echo $pitch; ?>,zoom:1});
                    
          if(mode == 'directions'){
            
            jQuery('.current_input').attr('value',place);
            jQuery('#colabs_maps_lat').attr('value',point.lat());
            jQuery('#colabs_maps_long').attr('value',point.lng());
        
          } else {
            jQuery('#colabs_maps_address').attr('value',place);
            jQuery('#colabs_maps_lat').attr('value',point.lat());
            jQuery('#colabs_maps_long').attr('value',point.lng());
          }
          
          }
        }
      
        // >> PLOT
        // showLocation() is called when you click on the Search button
        // in the form.  It geocodes the address entered into the form
        // and adds a marker to the map at that location.
        function showLocation() {
          var address = jQuery('#colabs_maps_search_input').attr('value');
          geocoder.geocode( { 'address': address}, addAddressToMap);
        }
        initialize();
        setSavedAddress();
        
        // >> PLOT
        //Click on the "Plot" button  
        jQuery('#colabs_maps_search').click(function(){
        
          showLocation();
      
        })
        
      });
    </script>

    <style type="text/css">
      #map_canvas { margin:20px 0}
      .colabs_maps_bubble_address { font-size:16px}
      .colabs_maps_style { padding: 10px; background: none repeat scroll 0 0 #f9f9f9;}
      .colabs_maps_style ul li label { width: 150px; float:left; display: block}
      .colabs_maps_search { border-bottom:1px solid #e1e1e1; padding: 10px}
      
      #colabs_maps_holder .not-active{ display:none }
      
      #map_mode { height: 38px; margin: 10px 0; background: #f1f1f1; padding-top: 10px}
      #map_mode ul li { float:left;  margin-bottom: 0;}
      #map_mode ul li a {padding: 10px 15px; display: block;text-decoration: none;   margin-left: 10px }
      #map_mode a.active { color: black;background: #fff;border: solid #e1e1e1; border-width: 1px 1px 0px 1px; }
      .current_input { background: #E9F2FA!important}
    </style>
  
  <?php
}

function colabsthemes_metabox_maps_create($post_id) {
  global $post;
  
  // Check if $post_id is object
  if( is_object($post_id) ) {
    $post_id = $post_id->ID;
  }


  $enable = get_post_meta($post_id,'colabs_maps_enable',true);
  $streetview = get_post_meta($post_id,'colabs_maps_streetview',true);
  $address = get_post_meta($post_id,'colabs_maps_address',true);
  $long = get_post_meta($post_id,'colabs_maps_long',true);
  $lat = get_post_meta($post_id,'colabs_maps_lat',true);
  $zoom = get_post_meta($post_id,'colabs_maps_zoom',true);
  $type = get_post_meta($post_id,'colabs_maps_type',true);
  $walking = get_post_meta($post_id,'colabs_maps_walking',true);
  
  $yaw = get_post_meta($post_id,'colabs_maps_pov_yaw',true);
  $pitch = get_post_meta($post_id,'colabs_maps_pov_pitch',true);
  
  $from = get_post_meta($post_id,'colabs_maps_from',true);
  $to = get_post_meta($post_id,'colabs_maps_to',true);
  
  if(empty($zoom)) $zoom = get_option('colabs_maps_default_mapzoom');
  if(empty($type)) $type = get_option('colabs_maps_default_maptype');
  if(empty($pov)) $pov = 'yaw:0,pitch:0';
  
  $key = get_option('colabs_maps_apikey');
  // No More API Key needed 
  ?>
 
    <?php
    $mode = get_post_meta($post->ID,'colabs_maps_mode',true); 
    if($mode == 'plot'){ $directions = 'not-active'; $plot = 'active'; }
    elseif($mode == 'directions'){ $directions = 'active'; $plot = 'not-active'; }
    else {$directions = 'not-active'; $plot = 'active';}

    ?>

  <div class="clear"></div>
  <table class="maps-post-options"><tr><td><strong>Enable map on this post: </strong></td>
    <td><input class="address_checkbox" type="checkbox" name="colabs_maps_enable" id="colabs_maps_enable" <?php if($enable == 'on'){ echo 'checked=""';} ?> /></td></tr>
    <tr><td><strong>This map will be in Streetview: </strong></td>
    <td><input class="address_checkbox" type="checkbox" name="colabs_maps_streetview" id="colabs_maps_streetview" <?php if($streetview == 'on'){ echo 'checked=""';} ?> /></td></tr>
    
    </table>
    
  <div class="colabs-maps-search-wrapper">
    <div class="colabs_maps_search">
    <table><tr><td><b>Search for an address:</b></td>
    <td><input class="address_input" type="text" size="40" value="" name="colabs_maps_search_input" id="colabs_maps_search_input"/><span class="button" id="colabs_maps_search">Plot</span>
    </td></tr></table>
    </div>
  <div id="colabs_maps_holder" class="colabs_maps_style" >
    <ul>
      <li class="colabs_plot <?php echo $plot; ?>">
        <label>Address Name:</label>
        <input class="address_input" type="text" size="40" name="colabs_maps_address" id="colabs_maps_address" value="<?php echo $address; ?>" />
      </li>
      <li>
        <label>Latitude: <small class="colabs_directions">Center Point</small></label>
        <input class="address_input" type="text" size="40" name="colabs_maps_lat" id="colabs_maps_lat" value="<?php echo $lat; ?>"/>
      </li>
      <li>
        <label>Longitude: <small class="colabs_directions">Center Point</small></label>
        <input class="address_input" type="text" size="40" name="colabs_maps_long" id="colabs_maps_long" value="<?php echo $long; ?>"/>
      </li>
        <li class="with-button colabs_plot <?php echo $plot; ?>">
        <label>Point of View: Yaw</label>     
        <input class="address_input" type="text" name="colabs_maps_pov_yaw" id="colabs_maps_pov_yaw" size="40" value="<?php echo $yaw;  ?>" />
          <small class="btn">Streetview</small> 
        </li>
        <li class="with-button colabs_plot <?php echo $plot; ?>">
        <label>Point of View: Pitch</label>       
        <input class="address_input" type="text" name="colabs_maps_pov_pitch" id="colabs_maps_pov_pitch" size="40" value="<?php echo $pitch;  ?>">
          <small class="btn">Streetview</small>
        </li>
      <li class="colabs_directions <?php echo $directions; ?>">
        <label>From:</label>
      <input class="address_input current_input" type="text" size="40" name="colabs_maps_from" id="colabs_maps_from" value="<?php echo $from; ?>"/>
      </li>
      <li class="colabs_directions <?php echo $directions; ?>">
        <label>To:</label>
        <input class="address_input" type="text" size="40" name="colabs_maps_to" id="colabs_maps_to" value="<?php echo $to; ?>"/>
      </li>
       <li>
        <label>Zoom Level:</label>
        <select class="address_select" style="width:120px" name="colabs_maps_zoom" id="colabs_maps_zoom">
          <?php 
          for($i = 0; $i < 20; $i++) {
            if($i == $zoom){ $selected = 'selected="selected"';} else { $selected = '';} ?>
            <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
          <?php } ?>
        </select>
      </li>
      <li>
        <label>Map Type:</label>
        <select class="address_select" style="width:120px" name="colabs_maps_type" id="colabs_maps_type">
        <?php
      $map_types = array('Normal' => 'G_NORMAL_MAP','Satellite' => 'G_SATELLITE_MAP','Hybrid' => 'G_HYBRID_MAP','Terrain' => 'G_PHYSICAL_MAP',); 
      foreach($map_types as $k => $v) {
        if($type == $v){ $selected = 'selected="selected"';} else { $selected = '';} ?>
        <option value="<?php echo $v; ?>" <?php echo $selected; ?>><?php echo $k; ?></option>
        <?php } ?>
        </select>
    </li>

  </ul> 
  <input type="hidden" value="<?php echo $mode; ?>" id="colabs_maps_mode" name="colabs_maps_mode" />
    </div>
  </div><!-- .colabs-maps-search-wrapper -->
    
  <div id="map_canvas" style="width: 100%; height: 250px"></div>
  <div name="pano" id="pano" style="width: 100%; height:250px"></div>

<?php }


/**
 * Add Maps Metabox on Single Property
 */
function colabsthemes_metabox_maps_add() {
  if ( function_exists('add_meta_box') ) { 
    $plugin_page_colabs_goodliving = add_meta_box(
      'colabsthemes-maps',
      get_option('colabs_themename').' Custom Maps',
      'colabsthemes_metabox_maps_create',
      'property',
      'normal'
    );
  }
}
add_action('admin_menu', 'colabsthemes_metabox_maps_add'); // Triggers CoLabsthemes_metabox_create

/**
 * Enqueue maps metabox scripts and style
 */
function colabs_maps_enqueue($hook) {
  global $post_type;
  if( 'property' == $post_type && ($hook == 'post.php' OR $hook == 'post-new.php')) {
    add_action('admin_head', 'colabsthemes_metabox_maps_header');
  }
}
add_action('admin_enqueue_scripts','colabs_maps_enqueue',10,1);


/**
 * Handle save map metabox saving
 */
function colabsthemes_metabox_maps_handle(){   
  global $globals;
  $pID = $_POST['post_ID'];
  $colabs_map_input_names = array('colabs_maps_enable','colabs_maps_streetview','colabs_maps_address','colabs_maps_from','colabs_maps_to','colabs_maps_long','colabs_maps_lat','colabs_maps_zoom','colabs_maps_type','colabs_maps_mode','colabs_maps_pov_pitch','colabs_maps_pov_yaw','colabs_maps_walking');  
    
  if ($_POST['action'] == 'editpost'){
    foreach ($colabs_map_input_names as $name) { // On Save.. this gets looped in the header response and saves the values submitted
      $var = $name;
      if (isset($_POST[$var])) {            
        if( get_post_meta( $pID, $name ) == "" )
          add_post_meta($pID, $name, $_POST[$var], true );
        elseif($_POST[$var] != get_post_meta($pID, $name, true))
          update_post_meta($pID, $name, $_POST[$var]);
        elseif($_POST[$var] == "") {
           delete_post_meta($pID, $name, get_post_meta($pID, $name, true));
        }
      }

      elseif(!isset($_POST[$var]) && $name == 'colabs_maps_enable') { 
        update_post_meta($pID, $name, 'false'); 
      }     

      else {
        delete_post_meta($pID, $name, get_post_meta($pID, $name, true)); // Deletes check boxes OR no $_POST
      }  
    }
  }
}
add_action('edit_post', 'colabsthemes_metabox_maps_handle');



// Output errors
if (!function_exists('colabs_show_errors')) {
function colabs_show_errors( $errors, $id = '' ) {
  if ($errors && sizeof($errors)>0 && $errors->get_error_code()) :
    echo '<ul class="alert alert-danger" id="'.$id.'">';
    foreach ($errors->errors as $error) {
      echo '<li>'.$error[0].'</li>';
    }
    echo '</ul>';
  endif;
}
}