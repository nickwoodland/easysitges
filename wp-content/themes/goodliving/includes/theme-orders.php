<?php
define('COLABS_ORDER_POST_TYPE', 'transaction');

// Register Custom Post Type
function transaction_custom_post_type() {

	$labels = array(
		'name'                => _x( 'Orders', 'Post Type General Name', 'colabsthemes' ),
		'singular_name'       => _x( 'Order', 'Post Type Singular Name', 'colabsthemes' ),
		'menu_name'           => __( 'Payments', 'colabsthemes' ),
		'parent_item_colon'   => __( 'Parent Order:', 'colabsthemes' ),
		'all_items'           => __( 'Orders', 'colabsthemes' ),
		'view_item'           => __( 'View Order', 'colabsthemes' ),
		'add_new_item'        => __( 'Add New Order', 'colabsthemes' ),
		'add_new'             => __( 'Add New', 'colabsthemes' ),
		'edit_item'           => __( 'Edit Order', 'colabsthemes' ),
		'update_item'         => __( 'Update Order', 'colabsthemes' ),
		'search_items'        => __( 'Search Order', 'colabsthemes' ),
		'not_found'           => __( 'Not found', 'colabsthemes' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'colabsthemes' ),
	);
	$args = array(
		'label'               => __( 'Transaction Order', 'colabsthemes' ),
		'description'         => __( 'Transaction Order', 'colabsthemes' ),
		'labels'              => $labels,
		'supports'            => false,
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
    'menu_icon'           => get_template_directory_uri().'/images/coin.png',
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'rewrite'             => array('slug' => 'order')
	);
	register_post_type( COLABS_ORDER_POST_TYPE, $args );

}

// Hook into the 'init' action
add_action( 'init', 'transaction_custom_post_type', 0 );

/**
 * Order Statuses
 */
define( 'COLABS_ORDER_PENDING', 'transaction_pending' );
define( 'COLABS_ORDER_FAILED', 'transaction_fail' );
define( 'COLABS_ORDER_COMPLETED', 'transaction_complete' );
define( 'COLABS_ORDER_ACTIVATED', 'transaction_active' );

add_action('init','colabs_setup_orders');
function colabs_setup_orders() {
  $statuses = array(
    COLABS_ORDER_PENDING => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'colabsthemes' ),
    COLABS_ORDER_FAILED => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'colabsthemes' ),
    COLABS_ORDER_COMPLETED => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'colabsthemes' ),
    COLABS_ORDER_ACTIVATED => _n_noop( 'Activated <span class="count">(%s)</span>', 'Activated <span class="count">(%s)</span>', 'colabsthemes' ),
  );
  foreach( $statuses as $status => $translate_string ){
    register_post_status( $status, array(
      'public' => true,
      'show_in_admin_all_list' => true,
      'show_in_admin_status_list' => true,
      'label_count' => $translate_string
    ));
  }
}
function colabs_order_add_meta_box() {
  $colabs_order_action = add_meta_box('colabs-order-action',__( 'Order Actions', 'colabsthemes' ),'colabs_metabox_order_action',COLABS_ORDER_POST_TYPE,'side' );
  $colabs_order_status = add_meta_box('colabs-order-status',__( 'Order Status', 'colabsthemes' ),'colabs_metabox_order_status',COLABS_ORDER_POST_TYPE,'side' );
  $colabs_order_author = add_meta_box('colabs-order-author',__( 'Order Author', 'colabsthemes' ),'colabs_metabox_order_author',COLABS_ORDER_POST_TYPE,'side' );
}
add_action( 'add_meta_boxes_transaction', 'colabs_order_add_meta_box' );
function colabs_metabox_order_action() {
  global $post;
  ?>
  <script type="text/javascript">
    jQuery(document).ready(function($){
      $( ".order-action" ).click( function(){
        var data = {
          'action' : $(this).attr('id'),
          'post' : "<?php echo $post->ID; ?>",
        };
        $.get( ajaxurl, data, function( response ){
          window.location.reload( true );
        });
      } );
    });
  </script>
  <?php 
  if( !in_array( $post->post_status, array( COLABS_ORDER_COMPLETED, COLABS_ORDER_FAILED, COLABS_ORDER_ACTIVATED ) ) ){
    echo html( 'a', array( 'id' => 'complete-order', 'class' => 'button order-action' ), __( 'Mark as Completed', 'colabsthemes' ) );
    echo html( 'a', array( 'id' => 'fail-order', 'class' => 'button order-action' ), __( 'Mark as Failed', 'colabsthemes' ) );
  }else{
    
    if( $post->post_status == COLABS_ORDER_FAILED ){
      echo html( 'a', array( 'id' => 'reset-order', 'class' => 'button order-action' ), __( 'Reset Order', 'colabsthemes' ) );
    }
    else if( $post->post_status == COLABS_ORDER_COMPLETED ){
        echo html( 'a', array( 'id' => 'activate-order', 'class' => 'button order-action' ), __( 'Activate Order', 'colabsthemes' ) );
		}
    else{
      printf( '<em>%s</em>', __( 'No actions available', 'colabsthemes' ) );
    }
  }
}
add_action( 'wp_ajax_reset-order', 'reset_order');
add_action( 'wp_ajax_complete-order', 'complete_order' );
add_action( 'wp_ajax_fail-order', 'fail_order' );
add_action( 'wp_ajax_activate-order', 'activate_order' );
    
function get_action_order(){
  if( ! isset( $_GET['post'] ) )
    die( 'No post included' );
  $post = get_post( $_GET['post'] );
  if( !$post || $post->post_type != COLABS_ORDER_POST_TYPE )
    die( 'Bad post included' );
  return colabs_get_order( $post->ID );
}
function reset_order(){
  $args = array('note'=>__('Manual Action','colabsthemes'));
  $order = get_action_order();
  $order->pending($args);
  $order->clear_gateway();
  die();
}
function complete_order(){
  $args = array('note'=>__('Manual Action','colabsthemes'));
  $order = get_action_order();
  $order->complete($args);
  die();
}
function fail_order(){
  $args = array('note'=>__('Manual Action','colabsthemes'));
  $order = get_action_order();
  $order->get_action_order()->failed($args);
  die();
}
function activate_order(){
  $args = array('note'=>__('Manual Action','colabsthemes'));
  $order = get_action_order();
  $order->activated($args);
  die();
}
  
function colabs_metabox_order_status() {
  global $post;
  $order = colabs_get_order( $post->ID );
  ?>
  <table id="admin-order-status">
    <tbody>
      <tr>
        <th><?php _e( 'ID', 'colabsthemes' ); ?>: </th>
        <td><?php echo $order->get_ID(); ?></td>
      </tr>
      <tr>
        <th><?php _e( 'Status', 'colabsthemes' ); ?>: </th>
        <td><?php echo $order->get_display_status(); ?></td>
      </tr>
      <tr>
        <th><?php _e( 'Gateway', 'colabsthemes' ); ?>: </th>
        <td>
        <?php
        $gateway_id = $order->get_gateway();

				if ( !empty( $gateway_id ) ) {
					$gateway = Colabs_Gateway_Registry::get_gateway( $gateway_id );
					if( $gateway ){
						echo $gateway->display_name( 'admin' );
					}else{
						_e( 'Unknown', 'colabsthemes' );
					}
				}else{
					_e( 'Undecided', 'colabsthemes' );
				}
        ?>
        </td>
      </tr>
      <tr>
        <th><?php _e( 'Currency', 'colabsthemes' ); ?>: </th>
        <td>
          <?php  
          $currencies = colabs_get_currencies();
          $order_curr_code = $order->get_currency();
          echo $currencies[$order_curr_code]['name'].' ('.$currencies[$order_curr_code]['symbol'].')';
          ?>
        </td>
      </tr>
    </tbody>
  </table>
  <style type="text/css">
    #admin-order-status th{
      padding-right: 10px;
      text-align: right;
      width: 40%;
    }
  </style>
  <?php
}
function colabs_metabox_order_author() {
  global $post;
  $order = colabs_get_order( $post->ID );
  $user = get_userdata( $order->get_author() );
  echo colabs_get_user_avatar( $order->get_author(), 72 );
  ?>
  <div id="admin-order-author">
    <span>
    <?php
    $username = $user->user_login;
    $display_name = $user->display_name;
    if( $username == $display_name )
      echo $username;
    else
      echo $display_name . ' (' . $username . ') ';
    ?>
    </span>
    <span><?php echo $user->user_email; ?></span>
    <span><?php echo $order->get_ip_address(); ?></span>
  </div>
  <style type="text/css">
    #admin-order-author{
      padding-left: 10px;
      text-align: left;
      overflow: hidden;
    }
    #admin-order-author span{
      display: block;
      margin-bottom: 5px;
    }
    #colabs-order-author .inside{
      overflow: hidden;
    }
    #colabs-order-author .avatar{
      float: left;
    }
  </style>
  <?php
}
function colabs_display_order_summary_table() {
  global $post;
  if ( COLABS_ORDER_POST_TYPE != $post->post_type )
    return;
  $order = colabs_get_order( $post->ID );
  ?>
  <style type="text/css">
    #admin-order-summary tbody td{
      padding-top: 10px;
      padding-bottom: 10px;
    }
    #normal-sortables{
      display: none;
    }
  </style>
  <?php
  $table = new Colabs_Admin_Order_Summary_Table( $order );
  $table->show( array(
    'class' => 'widefat',
    'id' => 'admin-order-summary'
  ) );
}
add_action( 'edit_form_advanced', 'colabs_display_order_summary_table' );
function colabs_get_order( $order_id ) {
  if( !is_numeric( $order_id ) )
    trigger_error( 'Invalid order id given. Must be an integer', E_USER_WARNING );
  $order = wp_cache_get( $order_id, 'orders' );
  if( ! $order ){
    $order_data = get_post( $order_id );
    if ( !$order_data || $order_data->post_type != COLABS_ORDER_POST_TYPE )
      return false;
    $order = new Colabs_Order( $order_data);
    wp_cache_set( $order_id, $order, 'orders' );
  }
  return $order;
}
function get_order( $order_id = null ){

	if( empty( $order_id ) ){
		$post =  get_queried_object();
		$order_id = $post->ID;
	}
	return colabs_get_order( $order_id );
}

function colabs_get_currency_symbol($currency_code = 'USD'){
  if( $currency_code == '' )
    $currency_code = 'USD';

  $currencies = colabs_get_currencies();
  $symbol = $currencies[$currency_code]['symbol'];
  return $symbol;
}
function colabs_get_price( $price, $currency_code = '', $currency_symbol = '' ){
  if(''==$currency_code)$currency_code = get_option('colabs_currency_code');
  
  if( $currency_symbol == '' ) {
    $currency_symbol = colabs_get_currency_symbol($currency_code);
  }
  
  $position = get_option('colabs_currency_position');
  $price = number_format(floatval( $price ));
  switch ($position){
    case 'left':
      $price = $currency_symbol.$price;
      break;
    case 'left_space':
      $price = $currency_symbol.' '.$price;
      break;
    case 'right':
      $price = $price.$currency_symbol;
      break;
    case 'right_space':
      $price = $price.' '.$currency_symbol;
      break;
  }
  
  return $price;
}
/**
 * Removes Wordpress default metaboxes from the page
 * @return void
 */
function colabs_remove_orders_meta_boxes() {
  remove_meta_box( 'submitdiv', COLABS_ORDER_POST_TYPE, 'side' );
}
add_action( 'admin_menu', 'colabs_remove_orders_meta_boxes' );
function the_order_summary(){
  $order = get_query_var('order_id');
  if($order){
    $orders = colabs_get_order($order);
    echo '<h5>'.__( 'Order Summary', 'colabsthemes' ).'</h5>';
    echo '<div class="order-summary">';
    $table = new Colabs_Order_Summary_Table( $orders );
    $table->show();
    echo '</div>';
  }
}

add_filter( 'manage_transaction_posts_columns', 'colabs_order_manage_columns' );
function colabs_order_manage_columns( $columns ) {
  $columns['status'] = __('Status', 'colabsthemes');
  $columns['order'] = __( 'Order', 'colabsthemes' );
  $columns['item'] = __( 'Purchased', 'colabsthemes' );
  $columns['type'] = __( 'Type', 'colabsthemes' );
  $columns['price'] = __( 'Total', 'colabsthemes' );
  $columns['order_date'] = __( 'Date', 'colabsthemes' );
  $columns['order_action'] = __( 'Action', 'colabsthemes' );
  
  unset( $columns['cb'] );
  unset( $columns['title'] );
  unset( $columns['author'] );
  unset( $columns['date'] );
  return $columns;
}

add_action( 'manage_transaction_posts_custom_column', 'colabs_order_add_column_data', 10, 2 );
function colabs_order_add_column_data( $column_index, $post_id ) {
  $order = colabs_get_order( $post_id );
  switch( $column_index ){
  
    case 'status':
      $status = $order->get_display_status();
      echo '<strong class="payment-'. strtolower($status) .'">' . ucfirst( $status ) . '</strong>';
      break;

    case 'order' : 
      if( $order->get_author() ) {
        $user_info = get_userdata( $order->get_author() );
      }

      if ( ! empty( $user_info ) ) {
        $username = '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';

        if ( $user_info->first_name || $user_info->last_name ) {
          $username .= esc_html( ucfirst( $user_info->first_name ) . ' ' . ucfirst( $user_info->last_name ) );
        } else {
          $username .= esc_html( ucfirst( $user_info->display_name ) );
        }

        $username .= '</a>';
      }

      $order_permalink = '<a href="' . get_edit_post_link( $post_id ) . '"><strong>#' . $post_id . '</strong></a>';

      printf( __( '%s by %s', 'colabsthemes' ), $order_permalink, $username );

      if ( $user_info->user_email ) {
        echo '<br>';
        echo '<small class="meta email"><a href="' . esc_url( 'mailto:' . $user_info->user_email ) . '">' . esc_html( $user_info->user_email ) . '</a></small>';
      }

      if( $order->get_ip_address() ) {
        echo '<small class="meta ip">IP '. $order->get_ip_address() .'</small>';
      }

      break;

    case 'order_author':
      $user = get_userdata( $order->get_author() );
      echo $user->display_name;
      echo '<br>';
      echo $order->get_ip_address();
      break;

    case 'item' :
      $items = array();
      if($order->get_post_type_id())$items[] = $order->get_post_type_id();

      $count = count( $items );
      $string = _n( '%s item', '%s items', $count, 'colabsthems' );
      printf( $string, $count );
      break;
    
    case 'type':

			$obj = get_post_type_object( get_post_type($order->get_post_type_id()) );
			echo $obj->labels->singular_name;
      break;
      
    case 'price':
      $currency = $order->get_currency();
      if( !empty( $currency ) ){
        echo colabs_get_price( $order->get_total(), $order->get_currency() );
      }else{
        echo colabs_get_price( $order->get_total() );
      }

      $gateway_id = $order->get_gateway();
      
			if ( !empty( $gateway_id ) ) {
				$gateway = Colabs_Gateway_Registry::get_gateway( $gateway_id );
				if( $gateway ){
					$gateway_title = $gateway->display_name( 'admin' );
				}else{
					$gateway_title = __( 'Unknown', 'colabsthemes' );
				}
			}else{
				$gateway_title = __( 'Undecided', 'colabsthemes' );
			}
      echo '<small class="meta">'. __('Via ', 'colabsthemes') . $gateway_title .'</small>';

      break;
      
    case 'payment':
    
      $gateway_id = $order->get_gateway();
      
      if ( !empty( $gateway_id ) ) {
        
        echo $gateway_id;
        
      }else{
        _e( 'Undecided', 'colabsthemes' );
      }
      
      echo '<br/>';
      
      $status = $order->get_display_status();
      if( $order->get_status() == COLABS_ORDER_PENDING ){
        echo '<strong>' . ucfirst( $status ) . '</strong>';
      }else{
        echo ucfirst( $status );
      }
      
      break;
    
    case 'order_date':
      $order_post = get_post( $order->get_ID() );
      if ( '0000-00-00 00:00:00' == $order_post->post_date ) {
        $t_time = $h_time = __( 'Unpublished', 'colabsthemes' );
        $time_diff = 0;
      } else {
        $t_time = get_the_time( _x( 'Y/m/d g:i:s A', 'Order Date Format', 'colabsthemes' ) );
        $m_time = $order_post->post_date;
        $time = get_post_time( 'G', true, $order_post );
        $time_diff = time() - $time;
        if ( $time_diff > 0 && $time_diff < 24*60*60 )
          $h_time = sprintf( __( '%s ago', 'colabsthemes' ), human_time_diff( $time ) );
        else
          $h_time = mysql2date( _x( 'Y/m/d', 'Order Date Format', 'colabsthemes' ), $m_time );
      }
      echo '<abbr title="' . $t_time . '">' . $h_time . '</abbr>';
        
      break;

    case 'order_action':
      $edit_permalink = '<a href="' . get_edit_post_link( $post_id ) . '">' . __('Edit', 'colabsthemes') . '</a>';
      $delete_permalink = '<a href="' . get_delete_post_link( $post_id ) . '">' . __('Delete', 'colabsthemes') . '</a>';
      $gateway_id = $order->get_gateway();
      $status = strtolower( $order->get_display_status() );

      echo '<div class="order-actions">';
        echo '<span class="edit">'. $edit_permalink .'</span>' . ' | ';
        echo '<span class="trash">'. $delete_permalink .'</span>';

        /*if( $gateway_id == 'Bank Transfer' && $status == 'pending' ) {
          echo '<br>';
          echo '<a href="#" data-action="complete-order" data-id="'. $post_id .'" class="approve-order">'. __('Approve Order', 'colabsthemes') .'</a>';
        }*/
      echo '</div>';

      break;
  }
}

/**
 * Add custom style for Payment Status
 */
function colabs_payment_admin_styles() { ?>
  <style>
    .payment-failed { color: #a00 }
    .payment-completed { color: #73a724 }
    .payment-pending { color: #999 }
    .widefat small.meta {
      display: block;
      color: #999;
      font-size: inherit;
      margin: 3px 0;
    }
    .order-actions {
      color: #ddd
    }
    .order-actions .trash a {
      color: #a00;
    }
  </style>
<?php }
add_action( 'admin_print_styles-edit.php', 'colabs_payment_admin_styles' );

// hide "add new" on wp-admin menu
function hide_order_add_box() {
  global $submenu;
  unset($submenu['edit.php?post_type=transaction'][10]);
}
// hide "add new" button on edit page
function hide_order_add_buttons() {
  global $pagenow;
  if(is_admin()){
    if($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == COLABS_ORDER_POST_TYPE){
      echo '<style type="text/css">.top .actions:first-child, .bottom .actions:first-child{display: none;}</style>';
      echo '<style type="text/css">h2 a.add-new-h2{display: none;}</style>';
    }
  }
}
add_action('admin_menu', 'hide_order_add_box');
add_action('admin_print_styles','hide_order_add_buttons');


if ( ! function_exists( 'html' ) ):
function html( $tag ) {
	static $SELF_CLOSING_TAGS = array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta' );

	$args = func_get_args();

	$tag = array_shift( $args );

	if ( is_array( $args[0] ) ) {
		$closing = $tag;
		$attributes = array_shift( $args );
		foreach ( $attributes as $key => $value ) {
			if ( false === $value )
				continue;

			if ( true === $value )
				$value = $key;

			$tag .= ' ' . $key . '="' . esc_attr( $value ) . '"';
		}
	} else {
		list( $closing ) = explode( ' ', $tag, 2 );
	}

	if ( in_array( $closing, $SELF_CLOSING_TAGS ) ) {
		return "<{$tag} />";
	}

	$content = implode( '', $args );

	return "<{$tag}>{$content}</{$closing}>";
}
endif;

add_action( 'wp_loaded', 'colabs_handle_new_order', 11 );
 
function colabs_handle_new_order() {
  if ( !isset( $_POST['action'] ) || !empty($_POST['goback']) )
		return;
    
  if ( empty( $_POST['ID'] ) )
		return false;

  $property = get_post( intval($_POST['ID']) );
  $property_id = intval($_POST['ID']); 
  
  if ( !empty($_POST['order_id']) ) {
		$order_id = intval($_POST['order_id']);
		$order = colabs_get_order( $order_id );
	}
  
  if((get_option('colabs_cost_to_feature')>0)&&(is_sticky($property_id))){
    $additional_price = get_option('colabs_cost_to_feature');
  }else{
    $additional_price = 0;
  }
  
  if(('relist-property' == $_POST['action'])&&(colabs_allow_relist())){
    $price = get_option('colabs_property_relisting_cost');
  }else{
    $price = get_option('colabs_property_listing_cost');
  }
  $items = array(
			'_post_type_id' => $property_id,
      'price' => $price,
      'additional_price' => $additional_price
	);
  
  $new_order = true;
	if ( empty($order) ) {
		$order = Colabs_Order::create($items);
    colabs_new_order_notify_admin( $order );
		colabs_new_order_notify_owner( $order );
	} else {
		$new_order = false;
	}
  
  do_action( 'colabs_create_order', $order, $property );
  
  $args = array (
		'order_id' => $order->get_id(),
    'property_id' => $property_id
	);

	// is the property being relisted?
	if ( !empty($_POST['relist']) ) {
		$args['property_relist'] = $property->ID;
	}

	// move form to next step
	if ( !empty($_POST['step']) ) {
		$args['step'] = intval($_POST['step'])+1;
	}
  
  $args['referer'] = urlencode($_SERVER['REDIRECT_URL']);

  // redirect to next step
	wp_redirect( add_query_arg( $args, COLABS_SUBMIT_PAGE ) );
	exit();
}

function colabs_get_redirect_to_url( $order ) {

	$post_id = $order->get_post_type_id();
	if ( $post_id ) {
		$url = get_permalink( $post_id );
	} else {
		$url = COLABS_DASHBOARD_PAGE;
	}

	return $url;

}

add_action( 'colabs_create_order', 'colabs_set_order_description', 12, 2 );
function colabs_set_order_description( $order, $property ) {
	$order_summary = '';
	$property_id = $property->ID;
	if ( $property_id ) {
		$order_summary .= get_the_title( $property_id );
	}
	$order->set_description( $order_summary );
}

add_action( 'colabs_order_completed', 'colabs_handle_completed_transaction', 11 );

function colabs_handle_completed_transaction( $order ) {

	$status = '';
  
  $post_id = $order->get_post_type_id();
  
	// skip moderation on paid
	if ( ! _colabs_moderate_posts() ) {
		$status = 'publish';
	}

	if ( COLABS_POST_TYPE == get_post_type( $post_id ) ) {
		colabs_update_post_status( $post_id, $status );
	}

	if ( COLABS_POST_TYPE != get_post_type( $post_id ) || 'publish' == $status ) {
		$order->activated();
	}

}

add_action( 'colabs_order_activated', '_colabs_activate_pricing', 10 );
function _colabs_activate_pricing( $order ){
  
  $post_id = $order->get_post_type_id();
  $post = get_post($post_id);

	if ( COLABS_POST_TYPE == $post->post_type ) {

		if ( _colabs_needs_publish( $post ) ) {
			colabs_update_post_status( $post_id, 'publish' );
		}

		_colabs_set_post_duration( $post_id );

	}

}

function colabs_get_pending_payment( $id = ''){
  if( '' != $id ){
    $pending_payment = array();
    $order_args = array( 'post_type' => COLABS_ORDER_POST_TYPE, 'meta_key'=> '_post_type_id', 'meta_value' => $id, 'post_status' => 'any' );
    $get_order_posts = get_posts( $order_args );
    foreach ( $get_order_posts as $order_post ) :
      $order = colabs_get_order($order_post->ID);
      $get_gateway = $order->get_gateway();
      $pending_payment[$order->get_post_type_id()] = array ( 
        'status' => ( empty($get_gateway) ? 'undecided' : 'pending' ),
        'order_id' => $order->get_id(),
      );
    endforeach;
    return $pending_payment;
  }
}

function colabs_get_post_order_status( $post_id, $pending_payment = '' ) {

	$order_status = '';

	if ( isset( $pending_payment[ $post_id ] ) ) {
		$order = colabs_get_order($pending_payment[$post_id]['order_id']);
		if ( $order ) {
			if ( COLABS_ORDER_FAILED == $order->get_status() )
				$order_status = __( 'Payment Failed', 'colabsthemes' );
      elseif ( COLABS_ORDER_ACTIVATED == $order->get_status() )
				$order_status = __( 'Active', 'colabsthemes' );  
			elseif ( 'undecided' == $pending_payment[$post_id]['status'] )
				$order_status = __( 'Pending Payment', 'colabsthemes' );
			else
				$order_status = __( 'Pending Approval', 'colabsthemes' );
		}
	}
	return $order_status;

}

function colabs_get_post_status( $post, $pending_payment = '' ) {

	switch(  $post->post_status ) {
		case 'pending':
			if ( ! $pending_payment || ! colabs_get_post_order_status( $post->ID, $pending_payment ) )
				$status = __( 'Pending Approval', 'colabsthemes' );
			else
				$status = __( 'Pending', 'colabsthemes' );
			break;
		case 'draft':
			$status = __( 'Incomplete Draft', 'colabsthemes');
			break;
		case 'expired':
			$canceled_post = get_post_meta( $post->ID, '_colabs_canceled_property', true );
			if ( $canceled_post )
				$status = __( 'Canceled', 'colabsthemes' );
			else
				$status = __( 'Expired', 'colabsthemes' );
			break;
    case 'publish':
			$status = __( 'Publish', 'colabsthemes');
			break;  
		default:
			$status = '';
			break;
	}

	return $status;

}

function the_orders_history_payment( $order ) {
	$gateway_id = $order->get_gateway();

	if ( !empty( $gateway_id ) ) {
		$gateway = Colabs_Gateway_Registry::get_gateway( $gateway_id );
		if( $gateway ){
			$gateway = $gateway->display_name( 'admin' );
		} else {
			$gateway = __( 'Unknown', 'colabsthemes' );
		}
	} else {
		$gateway = __( 'Undecided', 'colabsthemes' );
	}

	$gateway = html( 'div', array( 'class' => 'order-history-gateway' ), $gateway );
	$status = html( 'div', array( 'class' => 'order-history-status' ), $order->get_display_status() );

	echo $gateway . $status;

}