<?php
class colabs_orders {

	var $orders;
	var $count;
	var $completed_count;
	var $pending_count;
	var $cancelled_count;
	
	function colabs_orders() {
		global $wpdb;
		
		$this->orders = array();
		$this->pending_count = 0;
		$this->completed_count = 0;
		$this->cancelled_count = 0;
		$this->count = 0;
		
		$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."colabs_orders;");
		if ($results) :
			foreach ($results as $result) :
				$order = new colabs_order();
				$order->id = $result->id; 
				$order->user_id = $result->user_id; 	 	 	 	 	 	
				$order->status = $result->status;	 	 	 	 
				$order->cost = $result->cost; 	 	 	 
				$order->property_id = $result->property_id; 	
				$order->featured = $result->featured;
				$order->order_date = $result->order_date;	
				$order->payment_date = $result->payment_date;
				$order->payer_first_name = $result->payer_first_name; 	 	 	 	 	 
				$order->payer_last_name = $result->payer_last_name; 	 	 	 
				$order->payer_email = $result->payer_email;	 	 	 
				$order->payment_type = $result->payment_type;	 	 	 	 	 	 
				$order->approval_method = $result->approval_method; 	 	 	 	 	 
				$order->payer_address = $result->payer_address;	 	 	 	 	 
				$order->transaction_id = $result->transaction_id;
				$order->order_key = $result->order_key;
				$this->orders[] = $order;
				
				switch ($order->status) :
					case "completed" :
						$this->completed_count++;
					break;
					case "pending_payment" :
						$this->pending_count++;
					break;
					case "cancelled" :
						$this->cancelled_count++;
					break;
				endswitch;
			endforeach;
		endif;
		$this->count = sizeof($this->orders);
	}
	
	function get_orders( $status = '', $offset = 0, $limit = 20, $orderby = 'id', $order = 'ASC' ) {
		global $wpdb;
		$this->orders = array();
		if (!$orderby) $orderby = 'id';
		if (!$order) $order = 'ASC';
		
		if ($status) :
			$results = $wpdb->get_results("
				SELECT * 
				FROM ".$wpdb->prefix."colabs_orders 
				WHERE status = '".$status."'
				ORDER BY ".$orderby." ".$order." 
				LIMIT $offset, $limit;");
		else :
			$results = $wpdb->get_results("
				SELECT * 
				FROM ".$wpdb->prefix."colabs_orders 
				ORDER BY ".$orderby." ".$order." 
				LIMIT $offset, $limit;");
		endif;
			
		if ($results) :
			foreach ($results as $result) :
				$order = new colabs_order();
				$order->id = $result->id; 
				$order->user_id = $result->user_id; 	 	 	 	 	 	
				$order->status = $result->status;	 	 	 	 
				$order->cost = $result->cost; 	 	 	 
				$order->property_id = $result->property_id; 
				$order->featured = $result->featured;
				$order->order_date = $result->order_date;	
				$order->payment_date = $result->payment_date;
				$order->payer_first_name = $result->payer_first_name; 	 	 	 	 	 
				$order->payer_last_name = $result->payer_last_name; 	 	 	 
				$order->payer_email = $result->payer_email;	 	 	 
				$order->payment_type = $result->payment_type;	 	 	 	 	 	 
				$order->approval_method = $result->approval_method; 	 	 	 	 	 
				$order->payer_address = $result->payer_address;	 	 	 	 	 
				$order->transaction_id = $result->transaction_id;
				$order->order_key = $result->order_key;
				$this->orders[] = $order;
			endforeach;
		endif;
	}
}

class colabs_order {

	var $id;	 	 	 	 	 	 	
	var $user_id;		 	 	 	 	 	 	
	var $status;	 	 	 	 	 	 	 
	var $cost;			 	 	 	 	 	 	 
	var $property_id;	 	 	 	 	 	 		 	 	 	 	 	
	var $featured;	 	 	
	var $order_date;	 	 	 	 	 	 	
	var $payment_date;	 	 	 	
	var $payer_first_name;	 	 	 	 	 	 	 
	var $payer_last_name;	 	 	 	 	 	 	 
	var $payer_email;	 	 	 	 	 	 	 
	var $payment_type;		 	 	 	 	 	 	 
	var $approval_method;		 	 	 	 	 	 	 
	var $payer_address;		 	 	 	 	 	 	 
	var $transaction_id;
	var $order_key;
	
	function colabs_order( $id='', $user_id='', $cost='', $property_id='', $featured = 0, $status='pending_payment' ) {
		if ($id>0) :
			$this->id = $id;
			$this->get_order();
		elseif ($user_id) :
			$this->user_id = $user_id; 	 	 	 	 	 	
			$this->status = $status;	 	 	 	 
			$this->cost = $cost; 	 	 	 
			$this->property_id = $property_id; 		
			$this->featured = $featured;
		endif;
	}
	
	function find_order_for_property( $property_id ) {
		global $wpdb;
		$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."colabs_orders WHERE property_id = ".$property_id.' ORDER BY id DESC LIMIT 1;');
		if ($result) :	
			$this->id = $result->id; 	 	 	 	 	 	
			$this->user_id = $result->user_id; 	 	 	 	 	 	
			$this->status = $result->status;	 	 	 	 
			$this->cost = $result->cost; 	 	 	 
			$this->property_id = $result->property_id; 
			$this->featured = $result->featured;
			$this->order_date = $result->order_date;	
			$this->payment_date = $result->payment_date;
			$this->payer_first_name = $result->payer_first_name; 	 	 	 	 	 
			$this->payer_last_name = $result->payer_last_name; 	 	 	 
			$this->payer_email = $result->payer_email;	 	 	 
			$this->payment_type = $result->payment_type;	 	 	 	 	 	 
			$this->approval_method = $result->approval_method; 	 	 	 	 	 
			$this->payer_address = $result->payer_address;	 	 	 	 	 
			$this->transaction_id = $result->transaction_id;
			$this->order_key = $result->order_key;
			return true;
		endif;
		return false;
	}
	
	function get_order() {
		global $wpdb;
		$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."colabs_orders WHERE id = ".$this->id.';');
		if ($result) : 	 	 	 	 	
			$this->user_id = $result->user_id; 	 	 	 	 	 	
			$this->status = $result->status;	 	 	 	 
			$this->cost = $result->cost; 	 	 	 
			$this->property_id = $result->property_id; 	
			$this->featured = $result->featured;
			$this->order_date = $result->order_date;	
			$this->payment_date = $result->payment_date;
			$this->payer_first_name = $result->payer_first_name; 	 	 	 	 	 
			$this->payer_last_name = $result->payer_last_name; 	 	 	 
			$this->payer_email = $result->payer_email;	 	 	 
			$this->payment_type = $result->payment_type;	 	 	 	 	 	 
			$this->approval_method = $result->approval_method; 	 	 	 	 	 
			$this->payer_address = $result->payer_address;	 	 	 	 	 
			$this->transaction_id = $result->transaction_id;
			$this->order_key = $result->order_key;
			return true;
		endif;
		return false;
	}
	
	function generate_paypal_link( $item_name = '',$post_id = '' ) {
		global $wpdb;
		$paypal_email = get_option('colabs_property_paypal_email');
		$currency = get_option('colabs_currency_code');
		$notify_url = '';
		$paypal_adr = '';
		$item_number = '';
		$return = '';
		$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM ".$wpdb->prefix."colabs_orders WHERE property_id=$post_id;" ) );
		$cbt = __('Click here to publish your property on','colabsthemes').' '.get_bloginfo('name');
		if (empty($item_name)) $item_name = urlencode('Order#' . $this->id);
		
		$item_name = strip_tags($item_name);
		
		if (get_option('colabs_use_paypal_sandbox')=='true') :
			$paypal_adr = 'https://www.sandbox.paypal.com/cgi-bin/webscr?';
		else :
			$paypal_adr = 'https://www.paypal.com/webscr?';
		endif; 

		if(get_option('colabs_enable_paypal_ipn') == 'true') :
			$notify_url = '&notify_url=' . urlencode(trailingslashit(site_url()).'?paypalListener=IPN'); // FOR IPN - notify_url
			$return = urlencode( get_permalink(get_option('colabs_dashboard_url')) ); // Thank you page - return
		else :
			$return = urlencode( get_permalink(get_option('colabs_dashboard_url')).'?oid='.$order_id.'&pid='.$post_id ); // Add new confirm page - return
		endif;
		
		return $paypal_adr.'cmd=_xclick&business='.$paypal_email.'&item_name='.$item_name.'&amount='.$this->cost.'&no_shipping=1&no_note=1&item_number='.$this->order_key.'&currency_code='.$currency.'&charset=UTF-8&return='.$return.''.$notify_url.'&rm=2&cbt='.$cbt.'&custom='.$this->id.'';
	}
	
	function complete_order( $approval_method = '' ) {
		
		$propertys_last = '';
	
		
		if (!$propertys_last) $propertys_last = 30; // 30 day default
		
		// Caculate expirey date from today
		$date = strtotime('+'.$propertys_last.' day', current_time('timestamp'));
		
		if ($this->property_id > 0) :
			// Update post
			$property_post = get_post($this->property_id); 
			if ($property_post->post_status !== 'private') :

	    		$property_post = array();
	        	$property_post['ID'] = $this->property_id;
	        	$property_post['post_status'] = 'publish';
	        	$property_post['post_date'] = date('Y-m-d H:i:s');
	        	$property_post['post_date_gmt'] = get_gmt_from_date(date('Y-m-d H:i:s'));
	        		
				wp_update_post( $property_post );
				
				// Update expiry date
				update_post_meta( $this->property_id, 'expires', $date);
				
			endif;
		endif;
		
		// Change order status to completed
		$this->status = 'completed';
		$this->payment_date = date('Y-m-d H:i:s');
		$this->approval_method = $approval_method;
		
		// Send out mail
		colabs_order_complete( $this );

		$this->update_order();
		return true;
	}
	
	function cancel_order() {
		
		if ($this->property_id > 0) :
			$property_post = array();
        	$property_post['ID'] = $this->property_id;
        	$property_post['post_status'] = 'trash';
			wp_update_post( $property_post );
		endif;

		$this->status = 'cancelled';
		
		// Send out mail
		colabs_order_cancelled( $this );
		
		$this->update_order();
		return true;
	}
	
	function add_payment( $payment_data ) {

		$this->payment_date 	= $payment_data['payment_date'];
		$this->payer_first_name = $payment_data['payer_first_name'];
		$this->payer_last_name 	= $payment_data['payer_last_name'];
		$this->payer_email 		= $payment_data['payer_email'];
		$this->payment_type 	= $payment_data['payment_type'];
		$this->approval_method 	= $payment_data['approval_method'];
		$this->payer_address 	= $payment_data['payer_address'];
		$this->transaction_id 	= $payment_data['transaction_id'];

		$this->update_order();
	}
	
	function insert_order() {
		global $wpdb;
		
		$this->order_key = uniqid('order_'.$this->id.'_');
		
		$wpdb->insert( $wpdb->prefix . 'colabs_orders', array( 
			'user_id' 		=> $this->user_id,
			'status' 		=> $this->status,
			'cost' 			=> $this->cost,
			'property_id' 		=> $this->property_id,
			'featured'		=> $this->featured,
			'order_key'		=> $this->order_key
		), array( '%s','%s','%s','%s','%s','%s','%s' ) );
		
		colabs_new_order( $this );
		
		$this->id = $wpdb->insert_id;
	}
	
	function update_order() {
		global $wpdb; 
		$wpdb->update( $wpdb->prefix."colabs_orders", array( 
			'status' 			=> $this->status, 
			'payment_date' 		=> $this->payment_date,
			'payer_first_name' 	=> $this->payer_first_name,
			'payer_last_name' 	=> $this->payer_last_name,
			'payer_email' 		=> $this->payer_email,
			'payment_type' 		=> $this->payment_type,
			'approval_method'	=> $this->approval_method,
			'payer_address' 	=> $this->payer_address,
			'transaction_id' 	=> $this->transaction_id
		), array( 'id' => $this->id ), array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ), array( '%d' ) );
	}
}	
?>