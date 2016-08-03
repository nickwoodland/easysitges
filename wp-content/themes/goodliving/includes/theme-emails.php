<?php
/**
 * Emails that get called and sent out
 * Handle all email functions
 */

if (!defined('PHP_EOL')) define ('PHP_EOL', strtoupper(substr(PHP_OS,0,3) == 'WIN') ? "\r\n" : "\n");

add_action( 'transition_post_status', 'colabs_email_notifications', 10, 3 );
add_action( 'colabs_order_completed', 'colabs_order_complete_notify_owner', 11 );
add_action( 'colabs_order_failed', 'colabs_order_canceled', 12 );
add_action( 'colabs_order_failed', 'colabs_order_canceled_notify_owner', 13 );

add_action( 'colabs_property_expiring_soon', 'colabs_owner_property_expiring_soon', 10, 2 );
add_action( 'colabs_property_expired', 'colabs_owner_property_expired', 12, 2 );


// replace all \n with just <br />
if( !function_exists( 'colabsthemes_nl2br' ) ) {
	function colabsthemes_nl2br($text) {
	  return strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />'));
	}
}

// replace all <br /> with just \r\n
if( !function_exists( 'colabsthemes_br2nl' ) ) {
	function colabsthemes_br2nl($text) {
	  return preg_replace('#<br\s*/?>#i', "\r\n", $text);
	}
}

function colabs_send_email( $address, $subject, $content ){

	// Strip 'www.' from URL
	$domain = preg_replace( '#^www\.#', '', strtolower( $_SERVER['SERVER_NAME'] ) );

	$headers = array(
		'from' => sprintf( 'From: %1$s <%2$s', get_bloginfo( 'name' ), "wordpress@$domain" ),
		'mime' => 'MIME-Version: 1.0',
		'type' => 'Content-Type: text/html; charset="' . get_bloginfo( 'charset' ) . '"',
		'reply_to' => "Reply-To: noreply@$domain",
	);
  $body = '<html>';
  $body .= '<head>';
  $body .= '</head>';
  $body .= '<body>';
  $body .= $content;
  $body .= '</body>';
  $body .= '</html>';

	wp_mail( $address, $subject, $body, implode( "\n", $headers ) );

}

// set the generic email header for emails not using 'colabs_send_email()'
function _colabs_email_headers( $headers ='' ) {
	// Strip 'www.' from URL
	$domain = preg_replace( '#^www\.#', '', strtolower( $_SERVER['SERVER_NAME'] ) );

	if ( ! $headers ) $headers = sprintf( 'From: %1$s <%2$s', get_bloginfo( 'name' ), "wordpress@$domain" ) . PHP_EOL;
	return apply_filters( 'colabs_email_headers', $headers ) ;
}

function colabs_email_signature( $identifier, $content_type = 'plain' ) {

	$text = __( 'Regards,', 'colabsthemes' );
	$sitename = get_bloginfo('name');

	if ( 'html' == $content_type ) {
		$signature = html( 'p', sprintf( '%s', $text ) );
		$signature .= html( 'p', sprintf( '%s', $sitename ) );
		$signature .= html( 'p', home_url() );
	} else {
		$signature = $text . PHP_EOL . PHP_EOL;
		$signature .= sprintf( '%s', $sitename ) . PHP_EOL;
		$signature .= home_url();
	}
	return apply_filters( 'colabs_email_signature', $signature, $identifier, $content_type );
}

function colabs_email_notifications( $new_status, $old_status, $post ) {
	if ( COLABS_POST_TYPE != $post->post_type || 'new' == $old_status || $old_status == $new_status )
		return;

	if ( in_array( $new_status, array( 'publish', 'pending' ) ) && ! in_array( $old_status, array( 'publish', 'expired' ) ) ) {

		if ( 'true' == get_option('colabs_new_property_email') ) colabs_new_property_notify_admin( $post->ID, $new_status );
		colabs_new_property_notify_owner( $post->ID, $new_status );

	} elseif ( 'draft' != $new_status && 'pending' != $new_status ) {
		colabs_property_status_change_notify_owner( $new_status, $old_status, $post );
	}

}

// notify admins on new posted property
function colabs_new_property_notify_admin( $post_id, $status = 'publish' ) {
	$post_info = get_post($post_id);

	$post_title = stripslashes($post_info->post_title);
	$post_author = stripslashes(get_the_author_meta('user_login', $post_info->post_author));
	$post_author_email = stripslashes(get_the_author_meta('user_email', $post_info->post_author));
	$post_status = stripslashes($post_info->post_status);
	$post_slug = get_permalink($post_id);
	$adminurl = admin_url("post.php?action=edit&post=$post_id");

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$mailto = get_option('admin_email');

	if ( 'publish' == $status )
		$subject = sprintf( __( '[%s] New Property Submitted', 'colabsthemes' ), $blogname );
	else
		$subject = sprintf( __( '[%s] New Property Pending Approval', 'colabsthemes' ), $blogname );

	// Message

	$message  = __('Dear Admin,', 'colabsthemes') . PHP_EOL . PHP_EOL;
	$message .= sprintf(__('The following property listing has just been submitted on your %s website.', 'colabsthemes'), $blogname) . PHP_EOL . PHP_EOL;
	$message .= __('Property Details', 'colabsthemes') . PHP_EOL;
	$message .= '-----------------' . PHP_EOL;
	$message .= __('Title: ', 'colabsthemes') . $post_title . PHP_EOL;
	$message .= __('Author: ', 'colabsthemes') . $post_author . PHP_EOL;
	$message .= '-----------------' . PHP_EOL . PHP_EOL;
	$message .= __('View Property: ', 'colabsthemes') . $post_slug . PHP_EOL;
	$message .= sprintf(__('Edit Property: %s', 'colabsthemes'), $adminurl) . PHP_EOL . PHP_EOL . PHP_EOL;

	$message .= colabs_email_signature( 'new_property_admin' );

	// ok let's send the email
	wp_mail( $mailto, $subject, $message, _colabs_email_headers() );

}

function colabs_new_property_notify_owner( $post_id, $status = 'publish' ) {
	$post_info = get_post($post_id);

	$post_title = stripslashes($post_info->post_title);
	$post_author = stripslashes(get_the_author_meta('user_login', $post_info->post_author));
	$post_author_email = stripslashes(get_the_author_meta('user_email', $post_info->post_author));

	$dashurl = trailingslashit( COLABS_DASHBOARD_PAGE );

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$mailto = $post_author_email;
	$subject = sprintf( __( '[%s] Your Property Submission', 'colabsthemes' ), $blogname );

	// Message
	$message  = sprintf(__('Hi %s,', 'colabsthemes'), $post_author) . PHP_EOL . PHP_EOL;
	$message .= __( 'Thank you for your recent submission! ', 'colabsthemes' ); 

	if ( 'publish' == $status )
		$message .= __( 'Your property listing has been approved and is now live on our site .', 'colabsthemes' ) . PHP_EOL . PHP_EOL;
	else
		$message .= __( 'Your property listing has been submitted for review and will not appear live on our site until it has been approved.', 'colabsthemes' ) . PHP_EOL . PHP_EOL;

	$message .= sprintf( __( 'Below you will find a summary of your property listing on the %s website.', 'colabsthemes' ), $blogname ) . PHP_EOL . PHP_EOL;

	$message .= __('Property Details', 'colabsthemes') . PHP_EOL;
	$message .= '-----------------' . PHP_EOL;
	$message .= __('Title: ', 'colabsthemes') . $post_title . PHP_EOL;
	$message .= __('Author: ', 'colabsthemes') . $post_author . PHP_EOL;
	$message .= '-----------------' . PHP_EOL . PHP_EOL;

	if ( 'publish' == $status ) {
		$message .= __('You can view your property by clicking on the following link:', 'colabsthemes' ) . PHP_EOL;
		$message .= get_permalink($post_id) . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
	} else {
		$message .= __('You may check the status of your property(s) at anytime by logging into the "Dashboard" page.', 'colabsthemes') . PHP_EOL;
		$message .= $dashurl . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
	}

	$message .= colabs_email_signature( 'new_property' );

	// ok let's send the email
	wp_mail( $mailto, $subject, $message, _colabs_email_headers() );
}

function colabs_property_status_change_notify_owner( $new_status, $old_status, $post ) {
	global $wpdb;

	$post_info = get_post($post->ID);

	if ( COLABS_POST_TYPE != $post_info->post_type )
		return;

	$post_title = stripslashes($post_info->post_title);
	$post_author_id = $post_info->post_author;
	$post_author = stripslashes(get_the_author_meta('user_login', $post_info->post_author));
	$post_author_email = stripslashes(get_the_author_meta('user_email', $post_info->post_author));

	$mailto = $post_author_email;

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	// make sure the admin wants to send emails
	$send_approved_email = get_option('colabs_new_property_email_owner');

	// if the property has been approved send email to ad owner only if owner is not equal to approver
	// admin approving own properties or property owner pausing and reactivating ad on his dashboard don't need to send email
	if ( $old_status == 'pending' && $new_status == 'publish' && get_current_user_id() != $post_author_id && $send_approved_email == 'true' ) {

		$subject = sprintf( __( '[%s] Your Property Has Been Approved', 'colabsthemes' ), $blogname );

		$message  = sprintf(__('Hi %s,', 'colabsthemes'), $post_author) . PHP_EOL . PHP_EOL;
		$message .= sprintf(__('Your property listing, "%s" has been approved and is now live on our site.', 'colabsthemes'), $post_title) . PHP_EOL . PHP_EOL;

		$message .= __('You can view your property by clicking on the following link:', 'colabsthemes') . PHP_EOL;
		$message .= get_permalink($post->ID) . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;

		$message .= colabs_email_signature( 'property_approved' );

		// ok let's send the email
		wp_mail( $mailto, $subject, $message, _colabs_email_headers() );

	}

}

function colabs_bank_transfer_pending_email( $post ) {

	$content = '';

	$content .= html( 'p', __( 'A new order is waiting to be processed. Once you recieve payment, you should mark the order as completed.', 'colabsthemes' ) );
  
  $content .= html( 'h3', __( 'Order Summary', 'colabsthemes' ) );
  
  $gateway = Colabs_Gateway_Registry::get_gateway( $post['colabs_payment_method'] );
  
  $content .= '<ul>';
  $content .= '<li><strong>'.__('Order:','colabsthemes').'</strong> '.$post['order_id'].'</li>';
  $content .= '<li><strong>'.__('Property Name:','colabsthemes').'</strong> '.get_the_title($post['post_id']).'</li>';
  $content .= '<li><strong>'.__('Amount:','colabsthemes').'</strong> '.colabs_get_price($post['item_amount']).'</li>';
  $content .= '<li><strong>'.__('Payment:','colabsthemes').'</strong> '.$gateway->display_name( 'dropdown' ).'</li>';
  $content .= '</ul>';
  
  $order_link = html( 'a', array( 'href' => get_edit_post_link( $post['order_id'] ) ), __( 'Review this order', 'colabsthemes' ) );
  
  $all_orders = html( 'a', array( 'href' => admin_url( 'edit.php?post_status=transaction_pending&post_type=transaction' ) ), __( 'review all pending orders', 'colabsthemes' ) );

	// translators: <Single Order Link> or <Link to All Orders>
	$content .= html( 'p',  sprintf( __( '%1$s or %2$s', 'colabsthemes' ), $order_link, $all_orders ) );

	$subject = sprintf( __( '[%1$s] Pending Order #%2$d', 'colabsthemes' ), get_bloginfo( 'name' ), $post['order_id'] );

	if( ! function_exists( 'colabs_send_email' ) )
		return false;

	$email = array( 'to' => get_option( 'admin_email' ), 'subject' => $subject, 'message' => $content );

	colabs_send_email( $email['to'], $email['subject'], $email['message'] );
}

// Edited Property that require moderation
function colabs_edited_property_pending( $post_id ) {
	$post_info = get_post($post_id);
  
	$post_title = stripslashes($post_info->post_title);
	$post_author = stripslashes(get_the_author_meta('user_login', $post_info->post_author));
	$adminurl = admin_url("post.php?action=edit&post=$post_id");

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$mailto = get_option('admin_email');
	$subject = sprintf( __( '[%s] Edited Property Pending Approval', 'colabsthemes' ), $blogname );

	// Message

	$message  = __('Dear Admin,', 'colabsthemes') . PHP_EOL . PHP_EOL;
	$message .= sprintf(__('The following property listing has just been edited on your %s website.', 'colabsthemes'), $blogname) . PHP_EOL . PHP_EOL;
	$message .= __('Property Details', 'colabsthemes') . PHP_EOL;
	$message .= '-----------------' . PHP_EOL;
	$message .= __('Title: ', 'colabsthemes') . $post_title . PHP_EOL;
	$message .= __('Author: ', 'colabsthemes') . $post_author . PHP_EOL;
	$message .= '-----------------' . PHP_EOL . PHP_EOL;
	$message .= __('Preview Property: ', 'colabsthemes') . get_permalink($post_id) . PHP_EOL;
	$message .= sprintf(__('Edit Property: %s', 'colabsthemes'), $adminurl) . PHP_EOL . PHP_EOL . PHP_EOL;

	$message .= colabs_email_signature( 'edited_property_admin' );

	// ok let's send the email
	wp_mail( $mailto, $subject, $message, _colabs_email_headers() );

}

// notify admins on new orders
function colabs_new_order_notify_admin( $order ) {

	if ( get_transient( 'colabs_notified_admin_order_' . $order->get_id() ) )
		return;

	$recipient = get_bloginfo('admin_email');

	$orders_url = admin_url('edit.php?post_type='.COLABS_ORDER_POST_TYPE.'&post_status='.COLABS_ORDER_PENDING);

	$post_id = $order->get_post_type_id();

	$item = '';
	if ( $post_id ) {
		foreach ( $order->get_items() as $item ) {
			$item = html( 'a', array( 'href' => get_permalink( $item['post']->ID ) ), $item['post']->post_title );
			break;
		}
	}

	$table = new Colabs_Order_Summary_Table( $order );
	ob_start();
	$table->show();
	$table_output = ob_get_clean();

	$content = '';
	$content .= html( 'p', __( 'Dear Admin,', 'colabsthemes' ) );
	$content .= html( 'p', sprintf( __( 'A new Order #%d has just been submitted on your %s website.', 'colabsthemes' ), $order->get_id(), get_bloginfo( 'name' ) ) );
	if ( $item ) $content .= $item;
	$content .= html( 'p', __( 'Order Summary:', 'colabsthemes' ) );
	$content .= $table_output;

	$content .= html( 'p', sprintf( __( '<a href="%s">View all pending Orders</a>', 'colabsthemes' ), esc_url( $orders_url ) ) );
	$content .= html( 'p', '&nbsp;' );

	$content .= colabs_email_signature( 'new_order_admin', 'html' );

	$subject = sprintf( __( '[%s] New Order #%d', 'colabsthemes' ), get_bloginfo( 'name' ), $order->get_id() );

	// avoid sending duplicate emails while the order is being processed by gateways
	set_transient( 'colabs_notified_admin_order_' . $order->get_id(), $order->get_id(), 60 * 5  );

	colabs_send_email( $recipient, $subject, $content );
}


function colabs_new_order_notify_owner( $order ) {

	if ( get_transient( 'colabs_notified_author_new_order_' . $order->get_id() ) )
		return;

	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$recipient = get_user_by( 'id', $order->get_author() );

	$item = '';
	foreach ( $order->get_items() as $item ) {
		$item = html( 'a', array( 'href' => get_permalink( $item['post']->ID ) ), $item['post']->post_title );
		break;
	}
	
	$table = new Colabs_Order_Summary_Table( $order );
	ob_start();
	$table->show();
	$table_output = ob_get_clean();
		
	$content = '';
	$content .= html( 'p', sprintf( __( 'Hello %s,', 'colabsthemes' ), $recipient->display_name ) );
	$content .= html( 'p', __( 'Your order has been submitted with success and will be available as soon as the payment clears.', 'colabsthemes' ) );
	$content .= $item;
	$content .= html( 'p', __( 'Order Summary:', 'colabsthemes' ) );
	$content .= $table_output;
	$content .= html( 'p', '&nbsp;' );

	$content .= colabs_email_signature( 'new_order', 'html' );

	$subject = sprintf( __( '[%s] Pending Order #%d', 'colabsthemes' ), get_bloginfo( 'name' ), $order->get_id() );

	// avoid sending duplicate emails while the order is being processed by gateways
	set_transient( 'colabs_notified_author_new_order_' . $order->get_id(), $order->get_id(), 60 * 5  );

	colabs_send_email( $recipient->user_email, $subject, $content );
}

function colabs_order_complete_notify_owner( $order ) {

	$recipient = get_user_by( 'id', $order->get_author() );

	$item = '';
	foreach ( $order->get_items() as $item ) {
		$item = html( 'a', array( 'href' => get_permalink( $item['post']->ID ) ), $item['post']->post_title );
		break;
	}
	
	$table = new Colabs_Order_Summary_Table( $order );
	ob_start();
	$table->show();
	$table_output = ob_get_clean();
		
	$content = '';
	$content .= html( 'p', sprintf( __( 'Hello %s,', 'colabsthemes' ), $recipient->display_name ) );
	$content .= html( 'p', __( 'This email confirms that you have purchased the following items:', 'colabsthemes' ) );
	$content .= $item;
	$content .= html( 'p', __( 'Order Summary:', 'colabsthemes' ) );
	$content .= $table_output;
	$content .= html( 'p', '&nbsp;' );

	$content .= colabs_email_signature( 'order_complete', 'html' );

	$subject = sprintf( __( '[%s] Receipt for Your Order #%d', 'colabsthemes' ), get_bloginfo( 'name' ), $order->get_id() );

	colabs_send_email( $recipient->user_email, $subject, $content );
}

function colabs_order_canceled( $order ) {

	$recipient = get_bloginfo('admin_email');

	$orders_url = admin_url('edit.php?post_type='.COLABS_ORDER_POST_TYPE.'&post_status='.COLABS_ORDER_FAILED);

	$content = '';
	$content .= html( 'p', __( 'Dear Admin,', 'colabsthemes' ) );
	$content .= html( 'p', sprintf( __( 'Order number #%d has just been canceled on your %s website.', 'colabsthemes' ), $order->get_id(), get_bloginfo( 'name' ) ) );
	$content .= html( 'p', sprintf( __( '<a href="%s">View all canceled Orders</a>', 'colabsthemes' ), esc_url( $orders_url ) ) );
	$content .= html( 'p', '&nbsp;' );

	$content .= colabs_email_signature( 'order_canceled_admin', 'html' );

	$subject = sprintf( __( '[%s] Order canceled #%d', 'colabsthemes' ), get_bloginfo( 'name' ), $order->get_id() );

	colabs_send_email( $recipient, $subject, $content );
}

// notify admins on canceled orders
function colabs_order_canceled_notify_owner( $order ) {

	$recipient = get_user_by( 'id', $order->get_author() );

	$item = '';
	foreach ( $order->get_items() as $item ) {
    $link = html( 'a', array( 'href' => get_permalink( $item['post']->ID ) ), $item['post']->post_title );
		$item = html( 'p', $link );
		break;
	}

	$table = new Colabs_Order_Summary_Table( $order );
	ob_start();
	$table->show();
	$table_output = ob_get_clean();

	$content = '';
	$content .= html( 'p', sprintf( __( 'Hello %s,', 'colabsthemes' ), $recipient->display_name ) );
	$content .= html( 'p', sprintf( __( 'Your Order #%d has just been canceled.', 'colabsthemes' ), $order->get_id() ) );

	$content .= $item;
	$content .= html( 'p', __( 'Order Summary:', 'colabsthemes' ) );
	$content .= $table_output;
	$content .= html( 'p', '&nbsp;' );

	$content .= colabs_email_signature( 'order_canceled', 'html' );

	$subject = sprintf( __( '[%s] Order canceled #%d', 'colabsthemes' ), get_bloginfo( 'name' ), $order->get_id() );

	colabs_send_email( $recipient->user_email, $subject, $content );
}

// Property will expire soon
function colabs_owner_property_expiring_soon( $post_id, $days_remaining ) {

	$post_info = get_post($post_id);

	$days_text = sprintf( '%d %s', $days_remaining, _n( 'day' , 'days', $days_remaining ) );

	$post_title = stripslashes($post_info->post_title);
	$post_author = stripslashes(get_the_author_meta('user_login', $post_info->post_author));
	$post_author_email = stripslashes(get_the_author_meta('user_email', $post_info->post_author));

	$dashurl = trailingslashit( COLABS_DASHBOARD_PAGE );

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$mailto = $post_author_email;
	$subject = sprintf( __( '[%s] Your Property Submission expires in %s', 'colabsthemes' ), $blogname, $days_text );

	// Message
	$message  = sprintf(__('Hi %s,', 'colabsthemes'), $post_author) . PHP_EOL . PHP_EOL;
	$message .= sprintf( __('Your property listing is set to expire in %s', 'colabsthemes' ), $days_text ) . PHP_EOL . PHP_EOL;
	$message .= __('Property Details', 'colabsthemes') . PHP_EOL;
	$message .= '-----------------' . PHP_EOL;
	$message .= __('Title: ', 'colabsthemes') . $post_title . PHP_EOL;
	$message .= __('Author: ', 'colabsthemes') . $post_author . PHP_EOL;
	$message .= '-----------------' . PHP_EOL . PHP_EOL;
	$message .= __('You may check the status of your property(s) at anytime by logging into the "Dashboard" page.', 'colabsthemes') . PHP_EOL;
	$message .= $dashurl . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;

	$message .= colabs_email_signature( 'property_expiring' );

	// ok let's send the email
	wp_mail( $mailto, $subject, $message, _colabs_email_headers() );
}

function colabs_owner_property_expired( $post_id, $canceled = false ) {

	$send_expired_email = get_option( 'colabs_expired_property_email_owner' );
	if ( 'true' != $send_expired_email && ! $canceled )
		return;

	$post_info = get_post($post_id);
  
	$post_title = stripslashes($post_info->post_title);
	$post_author = stripslashes(get_the_author_meta('user_login', $post_info->post_author));
	$post_author_email = stripslashes(get_the_author_meta('user_email', $post_info->post_author));

	$dashurl = trailingslashit( COLABS_DASHBOARD_PAGE );

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$mailto = $post_author_email;

	if ( $canceled ) {
		$subject = sprintf( __( '[%s] Your Property was Canceled', 'colabsthemes' ), $blogname );

		$message  = sprintf(__('Hi %s,', 'colabsthemes'), $post_author) . PHP_EOL . PHP_EOL;
		$message .= sprintf(__('Your property listing, "%s" was canceled.', 'colabsthemes'), $post_title) . PHP_EOL . PHP_EOL;
		$message .= __('You can still access your property on your dashboard and submit it again, if you wish.', 'colabsthemes') . PHP_EOL . PHP_EOL;
		$message .= $dashurl . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;

	} else {
		$subject = sprintf( __( '[%s] Your Property Has Expired', 'colabsthemes' ), $blogname );

		$message  = sprintf( __('Hi %s,', 'colabsthemes'), $post_author ) . PHP_EOL . PHP_EOL;
		$message .= sprintf( __( 'Your property listing, "%s" has expired.', 'colabsthemes'), $post_title ) . PHP_EOL . PHP_EOL;
		
		if ( colabs_allow_relist() ) {
			$message .= __('If you would like to relist your property please go to your Dashboard, click the "relist" link.', 'colabsthemes') . PHP_EOL;
			$message .= $dashurl . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
		}
	}

	$message .= colabs_email_signature( 'expired_property' );

	// ok let's send the email
	wp_mail( $mailto, $subject, $message, _colabs_email_headers() );

}