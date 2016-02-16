<?php
add_action( 'admin_menu', 'property_order_create_menu', 20 );

function property_order_create_menu() { 
	
	$order_page = add_submenu_page('colabsthemes', __( 'Property Orders', 'colabsthemes' ), __( 'Property Orders', 'colabsthemes' ), 'manage_options', 'orders', 'colabs_orders' );
	// add_action( 'admin_print_styles-' . $order_page, 'order_admin_style');
	// add_action( 'admin_print_scripts-' . $order_page, 'order_admin_scripts' );
}

function colabs_orders() {
    global $wpdb;
    
    $message = '';
    
    if (isset($_GET['export'])) :
    	
    	ob_end_clean();
    	header("Content-type: text/plain");
		header("Content-Disposition: attachment; filename=royaleroom_export_".date('Ymd').".csv");

    	$colabs_orders = new colabs_orders();
    	
    	$csv = array();
    	
    	$row = array("ID","User","Property","Featured","Cost","Order Date","Payment Date","Payer","Payment type","Txn ID","Approval Method","Order Status");
    	
    	$csv[] = '"'.implode('","', $row).'"';
	            
	    $row = array();

        if (sizeof($colabs_orders->orders) > 0) :
        
            foreach( $colabs_orders->orders as $order ) :

            $user_info = get_userdata($order->user_id);

                $row[] = $order->id;

                $row[] = '#'.$user_info->ID.' - '.$user_info->first_name.' '.$user_info->last_name.' ('.$user_info->user_email.')';
                 
            	if ($order->property_id>0) :
            		$property_post = get_post( $order->property_id );
            		$row[] = '#'.$order->property_id.' - '.$property_post->post_title;
            	else :
            		$row[] = '';
            	endif;
                
                if ($order->featured) $row[] = __('Yes','colabsthemes'); else $row[] = __('No','colabsthemes');
                
                if ($order->cost) $row[] = colabs_get_currency($order->cost); else $row[] = __('Free', 'colabsthemes');
                
                $row[] = mysql2date(get_option('date_format') .' '. get_option('time_format'), $order->order_date);
                    
                if ($order->payment_date) $row[] = mysql2date(get_option('date_format') .' '. get_option('time_format'), $order->payment_date); else $row[] = '';
	            
	            if ($order->payer_first_name || $order->payer_last_name) $row[] = trim($order->payer_first_name.' '.$order->payer_last_name).', '.trim($order->payer_address); else $row[] = '';
	            
	            if ($order->payment_type) $row[] = trim($order->payment_type); else $row[] = '';
	            
	            if ($order->transaction_id) $row[] = trim($order->transaction_id); else $row[] = '';
	                    
	            if ($order->approval_method) $row[] = trim($order->approval_method); else $row[] = '';
	            
	            $row[] = $order->status;
	            
	            $row = array_map('trim', $row);
	            $row = array_map('html_entity_decode', $row);
	            $row = array_map('addslashes', $row);
	            
	            $csv[] = '"'.implode('","', $row).'"';
	            
	            $row = array();
                    
			endforeach;
              
		endif;
		
		echo implode("\n", $csv);
		exit;
    	
    endif;
    
    if (isset($_GET['paid'])) :
    	
    	$paid_listing = (int) $_GET['paid'];
    	
    	if ($paid_listing>0) :
    	
    		$order = new colabs_order( $paid_listing );

    		$order->complete_order( __('Manual', 'colabsthemes') );
    		
    		$message = __('Order complete.','colabsthemes');

    	endif;
    	
    endif;
    
    if (isset($_GET['cancel'])) :
    	
    	$cancelled_listing = (int) $_GET['cancel'];
    	
    	if ($cancelled_listing>0) :
    	
    		$order = new colabs_order( $cancelled_listing );
    		
    		$order->cancel_order();
    		
    		$message = __('Order cancelled.','colabsthemes');
    	  		
    	endif;
    	
    endif;
?>
<div class="wrap royaleroom">
    <div class="icon32" id="icon-themes"><br/></div>
    <h2><?php _e('Orders','colabsthemes') ?> <a href="admin.php?page=orders&amp;export=true" class="button" title=""><?php _e('Export CSV', 'colabsthemes'); ?></a></h2>
    
    <?php 
		if (isset($_GET['message'])) $message = stripslashes(urldecode($_GET['message']));
	
		if (isset($message) && !empty($message)) {
			echo '<p class="success">'.$message.'</p>';
		}
	?>
	
	<?php
		$colabs_orders = new colabs_orders();
		
		if (isset($_GET['p'])) $page = $_GET['p']; else $page = 1;
		
		$dir = 'ASC';
		$sort = 'ID';
		
		$per_page = 20;
		$total_pages = 1;
			
		$show = 'pending_payment';
		
		if (isset($_GET['show'])) :
			switch ($_GET['show']) :
				case "completed" :
					$show = 'completed';
					$total_pages = ceil($colabs_orders->completed_count/20);
				break;
				case "cancelled" :
					$show = 'cancelled';
					$total_pages = ceil($colabs_orders->cancelled_count/20);
				break;
				default :
					$total_pages = ceil($colabs_orders->pending_count/20);
				break;
			endswitch;
		else :
			$_GET['show'] = '';
		endif;	
		
		if (isset($_GET['dir'])) $posteddir = $_GET['dir']; else $posteddir = '';
		if (isset($_GET['sort'])) $postedsort = $_GET['sort']; else $postedsort = '';
	
		$colabs_orders->get_orders($show, $per_page*($page-1), $per_page, $postedsort, $posteddir);
	?>
	<div class="tablenav">
		<div class="tablenav-pages alignright">
			<?php
				if ($total_pages>1) {
				
					echo paginate_links( array(
						'base' => 'admin.php?page=orders&show='.$_GET['show'].'%_%&sort='.$postedsort.'&dir='.$posteddir,
						'format' => '&p=%#%',
						'prev_text' => __('&laquo; Previous', 'colabsthemes'),
						'next_text' => __('Next &raquo;', 'colabsthemes'),
						'total' => $total_pages,
						'current' => $page,
						'end_size' => 1,
						'mid_size' => 5,
					));
				}
			?>	
	    </div> 
	    
	    <ul class="subsubsub">
			<li><a href="admin.php?page=orders" <?php if ($show == 'pending_payment') echo 'class="current"'; ?>><?php _e('Pending' ,'colabsthemes'); ?> <span class="count">(<?php echo $colabs_orders->pending_count; ?>)</span></a> |</li>
			<li><a href="admin.php?page=orders&show=completed" <?php if ($show == 'completed') echo 'class="current"'; ?>><?php _e('Completed' ,'colabsthemes'); ?> <span class="count">(<?php echo $colabs_orders->completed_count; ?>)</span></a> |</li>
			<li><a href="admin.php?page=orders&show=cancelled" <?php if ($show == 'cancelled') echo 'class="current"'; ?>><?php _e('Cancelled' ,'colabsthemes'); ?> <span class="count">(<?php echo $colabs_orders->cancelled_count; ?>)</span></a></li>
		</ul>
	</div>
	
	<div class="clear"></div>

    <table class="widefat fixed">

        <thead>
            <tr>
                <th scope="col" style="width:3em;"><a href="<?php echo colabs_echo_ordering_link('id', 'DESC'); ?>"><?php _e('ID','colabsthemes') ?></a></th>
                <th scope="col"><a href="<?php echo colabs_echo_ordering_link('user_id', 'ASC'); ?>"><?php _e('User','colabsthemes') ?></a></th>
                <th scope="col"><a href="<?php echo colabs_echo_ordering_link('property_id', 'ASC'); ?>"><?php _e('Property','colabsthemes') ?></a></th>
                <th scope="col"><a href="<?php echo colabs_echo_ordering_link('featured', 'DESC'); ?>"><?php _e('Featured','colabsthemes') ?></a></th>
                <th scope="col"><a href="<?php echo colabs_echo_ordering_link('cost', 'DESC'); ?>"><?php _e('Total Cost','colabsthemes') ?></a></th>
                <th scope="col"><a href="<?php echo colabs_echo_ordering_link('order_date', 'DESC'); ?>"><?php _e('Order Date','colabsthemes') ?></a></th>
                
                <?php if ($show!=='pending_payment' && $show!=='cancelled') : ?>
	                <th scope="col"><a href="<?php echo colabs_echo_ordering_link('payment_date', 'DESC'); ?>"><?php _e('Payment Date','colabsthemes') ?></a></th>
	                <th scope="col"><?php _e('Payer','colabsthemes') ?></th>
	                <th scope="col"><a href="<?php echo colabs_echo_ordering_link('payment_type', 'ASC'); ?>"><?php _e('Payment type','colabsthemes') ?></a></th>
	                <th scope="col"><a href="<?php echo colabs_echo_ordering_link('transaction_id', 'ASC'); ?>"><?php _e('Txn ID','colabsthemes') ?></a></th>
	                <th scope="col"><a href="<?php echo colabs_echo_ordering_link('approval_method', 'ASC'); ?>"><?php _e('Approval Method','colabsthemes') ?></a></th>
                <?php endif; ?>
                
                <th scope="col"><?php _e('Actions','colabsthemes') ?></th>
            </tr>
        </thead>
	<?php if (sizeof($colabs_orders->orders) > 0) :
            $rowclass = '';
            ?>
            <tbody id="list">
            <?php
                foreach( $colabs_orders->orders as $order ) :

                $rowclass = 'even' == $rowclass ? 'alt' : 'even';
                
                if ($order->user_id) $user_info = get_userdata($order->user_id);
				?>
                <tr class="<?php echo $rowclass ?>">
                    <td><?php echo $order->id ?></td>

                    <td><?php if ($user_info) : ?>#<?php echo $user_info->ID; ?> &ndash; <strong><?php echo $user_info->first_name ?> <?php echo $user_info->last_name ?></strong><br/><a href="mailto:<?php echo $user_info->user_email ?>"><?php echo $user_info->user_email ?></a><?php endif; ?></td>
                    <td>
                    	<?php 
                    	if ($order->property_id>0) :
                    		$property_post = get_post( $order->property_id );
                    		if ($property_post) :
                    			echo '<a href="post.php?action=edit&post='.$order->property_id.'">';
                    			echo '#'.$order->property_id.' &ndash; '.$property_post->post_title;
                    			echo '</a>';
                    		else :
                    			echo '#'.$order->property_id;
                    		endif;
                    	else :
                    		_e('N/A', 'colabsthemes');
                    	endif;
                    ?>
                    </td>
                    <td><?php if ($order->featured) echo __('Yes','colabsthemes'); else echo __('No','colabsthemes'); ?></td>
                    <td><?php if ($order->cost) echo colabs_get_currency($order->cost); else _e('Free', 'colabsthemes'); ?></td>
                    <td><?php echo mysql2date(get_option('date_format') .' '. get_option('time_format'), $order->order_date) ?></td>
                    
                    <?php if ($show!=='pending_payment' && $show!=='cancelled') : ?>
                    
	                    <td><?php if ($order->payment_date) echo mysql2date(get_option('date_format') .' '. get_option('time_format'), $order->payment_date); else echo __('N/A','colabsthemes'); ?></td>
	                    <td><?php if ($order->payer_first_name || $order->payer_last_name) echo trim($order->payer_first_name.' '.$order->payer_last_name).'<br/>'.trim($order->payer_address); else echo __('N/A','colabsthemes'); ?></td>
	                    <td><?php if ($order->payment_type) echo trim($order->payment_type); else echo __('N/A','colabsthemes'); ?></td>
	                    <td><?php if ($order->transaction_id) echo trim($order->transaction_id); else echo __('N/A','colabsthemes'); ?></td>
	                    
	                    <td><?php if ($order->approval_method) echo trim($order->approval_method); else echo __('N/A','colabsthemes'); ?></td>
                    
                    <?php endif; ?>
                    
                    <td>
                    	<?php if ($order->status=='pending_payment') : ?>
                    		<a href="admin.php?page=orders&amp;paid=<?php echo $order->id; ?>" class="button button-primary">Mark as paid</a> 
                    		<a href="admin.php?page=orders&amp;cancel=<?php echo $order->id; ?>" class="button cancel">Cancel</a>
                    	<?php else : ?>
                    		<?php _e('N/A', 'colabsthemes'); ?>
                    	<?php endif; ?>
                    </td>
                </tr>
              <?php endforeach; ?>

              </tbody>

        <?php else : ?>
            <tr><td colspan="<?php if ($show!=='pending_payment' && $show!=='cancelled') : ?>15<?php else : ?>8<?php endif; ?>"><?php _e('No orders found.','colabsthemes') ?></td></tr>
        <?php endif; ?>        
    </table>
    <br />
    <script type="text/javascript">
    /* <![CDATA[ */
    	jQuery('a.cancel').click(function(){
    		var answer = confirm ("<?php _e('Are you sure you want to cancel this order? The order will be cancelled and the Property Post will be deleted from the system.', 'colabsthemes'); ?>");
			if (answer) return true;
			return false;
    	});
    /* ]]> */
    </script>
</div><!-- end wrap -->
<?php
}

function colabs_echo_ordering_link( $sort = 'id', $dir = 'ASC' ) {
	
	if (isset($_GET['show'])) $show = $_GET['show']; else $show = 'pending_payment';
	if (isset($_GET['p'])) $page = $_GET['p']; else $page = 1;
	if (isset($_GET['dir'])) $posteddir = $_GET['dir']; else $posteddir = '';
	if (isset($_GET['sort'])) $postedsort = $_GET['sort']; else $postedsort = '';
	
	echo 'admin.php?page=orders&amp;show='.$show.'&amp;p='. $page .'&amp;sort='.$sort.'&amp;dir=';
	
	if ($sort==$postedsort) :
		if ($posteddir==$dir) :
			if ($posteddir=='ASC') echo 'DESC';
			else echo 'ASC';
		else :
			echo $dir;
		endif;
	else :
		echo $dir;
	endif;
}