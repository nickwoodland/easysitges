<?php
function colabs_statistics() {

		$stats = array();

		$listings = colabs_get_listing_counts();

		$totals[ __( 'Total Properties', 'colabsthemes' ) ] = array(
			'text' => $listings['all'],
			'url' => 'edit.php?post_type='.COLABS_POST_TYPE
		);

		

		$stats[ __( 'New Properties (last 7 days)', 'colabsthemes' ) ] = $listings['new'];
		if( isset( $listings['publish'] ) ){
			$stats[ __( 'Total Live Properties', 'colabsthemes' ) ] = array(
				'text' => $listings['publish'],
				'url' => 'edit.php?post_type='.COLABS_POST_TYPE.'&post_status=publish'
			);
		} else {
			$stats[ __( 'Total Live Properties', 'colabsthemes' ) ] = 0;
		}
		if( isset( $listings['pending'] ) ){
			$stats[ __( 'Total Pending Properties', 'colabsthemes' ) ] = array(
				'text' => $listings['pending'],
				'url' => 'edit.php?post_type='.COLABS_POST_TYPE.'&post_status=pending'
			);
		} else {
			$stats[ __( 'Total Pending Properties', 'colabsthemes' ) ] = 0;
		}

		$orders = colabs_get_order_counts();
		$stats[ __( 'Revenue (7 days)', 'colabsthemes' ) ] = colabs_get_price( $orders['revenue'] );

		?>
    
		<div id="dashboard_right_now" class="postbox " >
			<h3 class="hndle"><span><?php _e('Info', 'colabsthemes') ?></span></h3>
			<div class="inside">
				<div class="main">
					<div class="versions">
					<?php colabs_output_list( $totals );?>
					<?php colabs_output_list( $stats );?>
					</div>
				</div>
			</div>
		</div>
		<?php
}

function colabs_get_listing_counts(){

		$listings = (array) wp_count_posts( COLABS_POST_TYPE );

		$all = 0;
		foreach( (array) $listings as $type => $count ){
			$all += $count;
		}
		$listings['all'] = $all;

		$yesterday_posts = new WP_Query( array(
			'post_type' => COLABS_POST_TYPE,
			'past_days' => 7
		) );
		$listings['new'] = $yesterday_posts->post_count;

		return $listings;

}

function colabs_output_list( $array, $begin = '<ul>', $end = '</ul>', $echo = true ){
		
		$html = '';
		foreach( $array as $title => $value ){
			if( is_array( $value ) ){
				$html .= '<li><strong>' . $title . ':</strong> <a href="' . $value['url'] . '">' . $value['text'] . '</a></li>';
			}else{
				$html .= '<li><span class="b">' . $title . ':</span> ' . $value . '</li>';
			}
		}

		$html = $begin . $html . $end;

		$html = '<div class="stats-info">'.$html.'</div>';

		if( $echo ) 
			echo $html;
		else
			return $html;

}

function colabs_get_order_counts(){
  $orders = (array) wp_count_posts( COLABS_ORDER_POST_TYPE );

	$week_orders = new WP_Query( array(
		'post_type' => COLABS_ORDER_POST_TYPE,
		'post_status' => array( COLABS_ORDER_COMPLETED,COLABS_ORDER_ACTIVATED ),
		'past_days' => 7,
	) );

	$revenue = 0;
	foreach( $week_orders->posts as $post ){
		// payments framework meta key
		$revenue += (float) get_post_meta( $post->ID, 'total_price', true );
	}

	$orders['revenue'] = $revenue;
	return $orders;

}
