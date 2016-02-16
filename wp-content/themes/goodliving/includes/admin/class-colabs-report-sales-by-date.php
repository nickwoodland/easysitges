<?php
class Colabs_Report_Sales_By_Date {

  public $chart_interval;
	public $group_by_query;
	public $chart_groupby;
  public $barwidth;
	public $start_date;
	public $end_date;
	public $chart_colours = array();
  
  public function get_export_button() {

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : 'last_month';
		?>
		<a
			href="#"
			download="report-<?php echo esc_attr( $current_range ); ?>-<?php echo date_i18n( 'Y-m-d', current_time('timestamp') ); ?>.csv"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php _e( 'Date', 'colabsthemes' ); ?>"
			data-exclude_series="1"
			data-groupby="<?php echo $this->chart_groupby; ?>"
		>
			<?php _e( 'Export CSV', 'colabsthemes' ); ?>
		</a>
		<?php
	}
  
  public function get_chart_legend() {

		$legend   = array();

		$order_totals = $this->get_order_report_data( array(
			'data' => array(
				'total_price' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_sales'
				),

			),
			'filter_range' => true
		) );

		$total_sales    = $order_totals->total_sales;

		$total_orders   = absint( $this->get_order_report_data( array(
			'data' => array(
				'ID' => array(
					'type'     => 'post_data',
					'function' => 'COUNT',
					'name'     => 'total_orders'
				)
			),
			'query_type'   => 'get_var',
			'filter_range' => true,
		) ) );


		$this->average_sales = $total_sales / ( $this->chart_interval + 1 );

		switch ( $this->chart_groupby ) {

			case 'day' :
				$average_sales_title = sprintf( __( '%s average daily sales', 'colabsthemes' ), '<strong>' . colabs_get_price( $this->average_sales ) . '</strong>' );
			break;

			case 'month' :
				$average_sales_title = sprintf( __( '%s average monthly sales', 'colabsthemes' ), '<strong>' . colabs_get_price( $this->average_sales ) . '</strong>' );
			break;
		}

		$legend[] = array(
			'title' => sprintf( __( '%s sales in this period', 'colabsthemes' ), '<strong>' . colabs_get_price( $total_sales ) . '</strong>' ),
			'color' => $this->chart_colours['sales_amount'],
			'highlight_series' => 2
		);

		$legend[] = array(
			'title' => $average_sales_title,
			'color' => $this->chart_colours['average'],
			'highlight_series' => 1
		);

		$legend[] = array(
			'title' => sprintf( __( '%s orders placed', 'colabsthemes' ), '<strong>' . $total_orders . '</strong>' ),
			'color' => $this->chart_colours['order_count'],
			'highlight_series' => 0
		);

		return $legend;
	}
  
  public function get_main_chart() {
		global $wp_locale;

		// Get orders and dates in range - we want the SUM of order totals, COUNT of order items, COUNT of orders, and the date
		$orders = $this->get_order_report_data( array(
			'data' => array(
				'total_price' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_sales'
				),
        'ID' => array(
					'type'     => 'post_data',
					'function' => 'COUNT',
					'name'     => 'total_orders',
					'distinct' => true,
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date'
				),
			),
			'group_by'     => $this->group_by_query,
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'filter_range' => true
		) );

		// Prepare data for report
		$order_counts      = $this->prepare_chart_data( $orders, 'post_date', 'total_orders', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$order_amounts     = $this->prepare_chart_data( $orders, 'post_date', 'total_sales', $this->chart_interval, $this->start_date, $this->chart_groupby );

		// Encode in json format
		$chart_data = json_encode( array(
			'order_counts'      => array_values( $order_counts ),
			'order_amounts'     => array_values( $order_amounts ),
		) );

    ?>
    <div class="chart-container">
			<div class="chart-placeholder main" ></div>
		</div>
		<script type="text/javascript">

			var main_chart;

			jQuery(function($){
				var order_data = jQuery.parseJSON( '<?php echo $chart_data; ?>' );
				var drawGraph = function( highlight ) {
					var series = [
						{
							label: "<?php echo esc_js( __( 'Number of orders', 'colabsthemes' ) ) ?>",
							data: order_data.order_counts,
							color: '<?php echo $this->chart_colours['order_count']; ?>',
							bars: { fillColor: '<?php echo $this->chart_colours['order_count']; ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'center' },
							shadowSize: 0,
							hoverable: false
						},
            {
							label: "<?php echo esc_js( __( 'Average sales amount', 'colabsthemes' ) ) ?>",
							data: [ [ <?php echo min( array_keys( $order_amounts ) ); ?>, <?php echo $this->average_sales; ?> ], [ <?php echo max( array_keys( $order_amounts ) ); ?>, <?php echo $this->average_sales; ?> ] ],
							yaxis: 2,
							color: '<?php echo $this->chart_colours['average']; ?>',
							points: { show: false },
							lines: { show: true, lineWidth: 2, fill: false },
							shadowSize: 0,
							hoverable: false
						},
						{
							label: "<?php echo esc_js( __( 'Sales amount', 'colabsthemes' ) ) ?>",
							data: order_data.order_amounts,
							yaxis: 2,
							color: '<?php echo $this->chart_colours['sales_amount']; ?>',
							points: { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 4, fill: false },
							shadowSize: 0,
							prepend_tooltip: "<?php echo colabs_get_currency_symbol(get_option('colabs_currency_code')); ?>"
						}
					];

					if ( highlight !== 'undefined' && series[ highlight ] ) {
						highlight_series = series[ highlight ];

						highlight_series.color = '#ea6d37';

						if ( highlight_series.bars )
							highlight_series.bars.fillColor = '#ea6d37';

						if ( highlight_series.lines ) {
							highlight_series.lines.lineWidth = 5;
						}
					}

					main_chart = jQuery.plot(
						jQuery('.chart-placeholder.main'),
						series,
						{
							legend: {
								show: false
							},
							grid: {
								color: '#aaa',
								borderColor: 'transparent',
								borderWidth: 0,
								hoverable: true
							},
							xaxes: [ {
								color: '#aaa',
								position: "bottom",
								tickColor: 'transparent',
								mode: "time",
								timeformat: "<?php if ( $this->chart_groupby == 'day' ) echo '%d %b'; else echo '%b'; ?>",
								monthNames: <?php echo json_encode( array_values( $wp_locale->month_abbrev ) ) ?>,
								tickLength: 1,
								minTickSize: [1, "<?php echo $this->chart_groupby; ?>"],
								font: {
									color: "#aaa"
								}
							} ],
							yaxes: [
								{
									min: 0,
									minTickSize: 1,
									tickDecimals: 0,
									color: '#d4d9dc',
									font: { color: "#aaa" }
								},
								{
									position: "right",
									min: 0,
									tickDecimals: 2,
									alignTicksWithAxis: 1,
									color: 'transparent',
									font: { color: "#aaa" }
								}
							],
						}
					);

					jQuery('.chart-placeholder').resize();
				}

				drawGraph();

				jQuery('.highlight_series').hover(
					function() {
						drawGraph( jQuery(this).data('series') );
					},
					function() {
						drawGraph();
					}
				);
        
        function showTooltip(x, y, contents) {
            jQuery('<div class="chart-tooltip">' + contents + '</div>').css( {
                top: y - 16,
              left: x + 20
            }).appendTo("body").fadeIn(200);
        }

        var prev_data_index = null;
        var prev_series_index = null;

        jQuery(".chart-placeholder").bind( "plothover", function (event, pos, item) {
            if (item) {
                if ( prev_data_index != item.dataIndex || prev_series_index != item.seriesIndex ) {
                    prev_data_index   = item.dataIndex;
                    prev_series_index = item.seriesIndex;

                    jQuery( ".chart-tooltip" ).remove();

                    if ( item.series.points.show || item.series.enable_tooltip ) {

                        var y = item.series.data[item.dataIndex][1];

                        tooltip_content = '';

                        if ( item.series.prepend_label )
                            tooltip_content = tooltip_content + item.series.label + ": ";

                        if ( item.series.prepend_tooltip )
                            tooltip_content = tooltip_content + item.series.prepend_tooltip;

                        tooltip_content = tooltip_content + y;

                        if ( item.series.append_tooltip )
                            tooltip_content = tooltip_content + item.series.append_tooltip;

 
                          showTooltip( item.pageX, item.pageY, tooltip_content );


                    }
                }
            }
            else {
                jQuery(".chart-tooltip").remove();
                prev_data_index = null;
            }
        });
        
        var dates = jQuery( ".range_datepicker" ).datepicker({
            changeMonth: true,
            changeYear: true,
            defaultDate: "",
            dateFormat: "yy-mm-dd",
            numberOfMonths: 1,
            maxDate: "+0D",
            showButtonPanel: true,
            showOn: "focus",
            buttonImageOnly: true,
            onSelect: function( selectedDate ) {
                var option = jQuery(this).is('.from') ? "minDate" : "maxDate",
                    instance = jQuery( this ).data( "datepicker" ),
                    date = jQuery.datepicker.parseDate(
                        instance.settings.dateFormat ||
                        jQuery.datepicker._defaults.dateFormat,
                        selectedDate, instance.settings );
                dates.not( this ).datepicker( "option", option, date );
            }
        });
        
        var a = document.createElement('a');

        if ( typeof a.download == "undefined" ) {
          $('.export_csv').hide();
        }
        
        // Export
        $('.export_csv').click(function(){
            var exclude_series = $(this).data( 'exclude_series' ) || '';
            exclude_series     = exclude_series.toString();
            exclude_series     = exclude_series.split(',');
            var xaxes_label    = $(this).data('xaxes');
            var groupby        = $(this).data('groupby');
            var export_format  = $(this).data('export');
            var csv_data       = "data:application/csv;charset=utf-8,"

            if ( export_format == 'table' ) {

                $(this).closest('div').find('thead tr,tbody tr').each(function() {
                    $(this).find('th,td').each(function() {
                        value = $(this).text();
                        value = value.replace( '[?]', '' );
                        csv_data += '"' + value + '"' + ",";
                    });
                    csv_data = csv_data.substring( 0, csv_data.length - 1 );
                    csv_data += "\n";
                });

                $(this).closest('div').find('tfoot tr').each(function() {
                    $(this).find('th,td').each(function() {
                        value = $(this).text();
                        value = value.replace( '[?]', '' );
                        csv_data += '"' + value + '"' + ",";
                        if ( $(this).attr('colspan') > 0 )
                            for ( i = 1; i < $(this).attr('colspan'); i++ )
                                csv_data += '"",';
                    });
                    csv_data = csv_data.substring( 0, csv_data.length - 1 );
                    csv_data += "\n";
                });

            } else {

                if ( ! window.main_chart )
                    return false;

                var the_series = window.main_chart.getData();
                var series     = [];
                csv_data   += xaxes_label + ",";

                $.each(the_series, function( index, value ) {
                    if ( ! exclude_series || $.inArray( index.toString(), exclude_series ) == -1 )
                        series.push( value );
                });

                // CSV Headers
                for ( var s = 0; s < series.length; ++s ) {
                    csv_data += series[s].label + ',';
                }

                csv_data = csv_data.substring( 0, csv_data.length - 1 );
                csv_data += "\n";

                // Get x axis values
                var xaxis = {}

                for ( var s = 0; s < series.length; ++s ) {
                    var series_data = series[s].data;
                    for ( var d = 0; d < series_data.length; ++d ) {
                        xaxis[series_data[d][0]] = new Array();
                        // Zero values to start
                        for ( var i = 0; i < series.length; ++i ) {
                            xaxis[series_data[d][0]].push(0);
                        }
                    }
                }

                // Add chart data
                for ( var s = 0; s < series.length; ++s ) {
                    var series_data = series[s].data;
                    for ( var d = 0; d < series_data.length; ++d ) {
                        xaxis[series_data[d][0]][s] = series_data[d][1];
                    }
                }

                // Loop data and output to csv string
                $.each( xaxis, function( index, value ) {
                    var date = new Date( parseInt( index ) );

                    if ( groupby == 'day' )
                        csv_data += date.getFullYear() + "-" + parseInt( date.getMonth() + 1 ) + "-" + date.getDate() + ',';
                    else
                        csv_data += date.getFullYear() + "-" + parseInt( date.getMonth() + 1 ) + ',';

                    for ( var d = 0; d < value.length; ++d ) {
                        val = value[d];

                        if( Math.round( val ) != val )
                            val = val.toFixed(2);

                        csv_data += val + ',';
                    }
                    csv_data = csv_data.substring( 0, csv_data.length - 1 );
                    csv_data += "\n";
                } );

            }

            // Set data as href and return
            $(this).attr( 'href', encodeURI( csv_data ) );
            return true;
        });
			});
		</script>
		<?php
  }  
  
  public function prepare_chart_data( $data, $date_key, $data_key, $interval, $start_date, $group_by ) {

		$prepared_data = array();
		$time          =  '';

		// Ensure all days (or months) have values first in this range
		for ( $i = 0; $i <= $interval; $i ++ ) {

			switch ( $group_by ) {

				case 'day' :
					$time = strtotime( date( 'Ymd', strtotime( "+{$i} DAY", $start_date ) ) ) . '000';
				break;

				case 'month' :
					$time = strtotime( date( 'Ym', strtotime( "+{$i} MONTH", $start_date ) ) . '01' ) . '000';
				break;
			}

			if ( ! isset( $prepared_data[ $time ] ) ) {
				$prepared_data[ $time ] = array( esc_js( $time ), 0 );
			}
		}

		foreach ( $data as $d ) {
			switch ( $group_by ) {

				case 'day' :
					$time = strtotime( date( 'Ymd', strtotime( $d->$date_key ) ) ) . '000';
				break;

				case 'month' :
					$time = strtotime( date( 'Ym', strtotime( $d->$date_key ) ) . '01' ) . '000';
				break;
			}

			if ( ! isset( $prepared_data[ $time ] ) ) {
				continue;
			}

			if ( $data_key ) {
				$prepared_data[ $time ][1] += $d->$data_key;
			} else {
				$prepared_data[ $time ][1] ++;
			}
		}

		return $prepared_data;
	}
  
	/**
	 * Output the report
	 */
	public function output_report() {

		$ranges = array(
			'year'         => __( 'Year', 'colabsthemes' ),
			'last_month'   => __( 'Last Month', 'colabsthemes' ),
			'month'        => __( 'This Month', 'colabsthemes' ),
			'7day'         => __( 'Last 7 Days', 'colabsthemes' )
		);

		$this->chart_colours = array(
			'sales_amount' => '#3498db',
			'average'      => '#75b9e7',
			'order_count'  => '#b8c0c5',
		);

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}
    
    $this->calculate_current_range( $current_range );
    
		?>
    <div id="poststuff" class="colabs-reports-wide">
      <?php colabs_statistics();?>
      <div class="postbox">
        <h3 class="stats_range">
          <?php $this->get_export_button(); ?>
          <ul>
            <?php
              foreach ( $ranges as $range => $name )
                echo '<li class="' . ( $current_range == $range ? 'active' : '' ) . '"><a href="' . remove_query_arg( array( 'start_date', 'end_date' ), add_query_arg( 'range', $range ) ) . '">' . $name . '</a></li>';
            ?>
            <li class="custom <?php echo $current_range == 'custom' ? 'active' : ''; ?>">
              <?php _e( 'Custom:', 'colabsthemes' ); ?>
              <form method="GET">
                <div>
                  <?php
                    // Maintain query string
                    foreach ( $_GET as $key => $value )
                      if ( is_array( $value ) )
                        foreach ( $value as $v )
                          echo '<input type="hidden" name="' . esc_attr( sanitize_text_field( $key ) ) . '[]" value="' . esc_attr( sanitize_text_field( $v ) ) . '" />';
                      else
                        echo '<input type="hidden" name="' . esc_attr( sanitize_text_field( $key ) ) . '" value="' . esc_attr( sanitize_text_field( $value ) ) . '" />';
                  ?>
                  <input type="hidden" name="range" value="custom" />
                  <input type="text" size="9" placeholder="yyyy-mm-dd" value="<?php if ( ! empty( $_GET['start_date'] ) ) echo esc_attr( $_GET['start_date'] ); ?>" name="start_date" class="range_datepicker colabs-input-calendar from" />
                  <input type="text" size="9" placeholder="yyyy-mm-dd" value="<?php if ( ! empty( $_GET['end_date'] ) ) echo esc_attr( $_GET['end_date'] ); ?>" name="end_date" class="range_datepicker colabs-input-calendar to" />
                  <input type="submit" class="button" value="<?php _e( 'Go', 'colabsthemes' ); ?>" />
                </div>
              </form>
            </li>
          </ul>
        </h3>
        <div class="inside chart-with-sidebar">
          <div class="chart-sidebar">
            <?php if ( $legends = $this->get_chart_legend() ) : ?>
              <ul class="chart-legend">
                <?php foreach ( $legends as $legend ) : ?>
                  <li style="border-color: <?php echo $legend['color']; ?>" <?php if ( isset( $legend['highlight_series'] ) ) echo 'class="highlight_series" data-series="' . esc_attr( $legend['highlight_series'] ) . '"'; ?>>
                    <?php echo $legend['title']; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
          <div class="main">
            <?php $this->get_main_chart(); ?>
          </div>
        </div>
      </div>
    </div>
    <?php
	}
  
  public function calculate_current_range( $current_range ) {

		switch ( $current_range ) {

			case 'custom' :
				$this->start_date = strtotime( sanitize_text_field( $_GET['start_date'] ) );
				$this->end_date   = strtotime( 'midnight', strtotime( sanitize_text_field( $_GET['end_date'] ) ) );

				if ( ! $this->end_date ) {
					$this->end_date = current_time('timestamp');
				}

				$interval = 0;
				$min_date = $this->start_date;

				while ( ( $min_date = strtotime( "+1 MONTH", $min_date ) ) <= $this->end_date ) {
					$interval ++;
				}

				// 3 months max for day view
				if ( $interval > 3 ) {
					$this->chart_groupby = 'month';
				} else {
					$this->chart_groupby = 'day';
				}
			break;

			case 'year' :
				$this->start_date    = strtotime( date( 'Y-01-01', current_time('timestamp') ) );
				$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
				$this->chart_groupby = 'month';
			break;

			case 'last_month' :
				$this->start_date    = strtotime( date( 'Y-m-01', strtotime( '-1 MONTH', current_time('timestamp') ) ) );
				$this->end_date      = strtotime( date( 'Y-m-t', strtotime( '-1 MONTH', current_time('timestamp') ) ) );
				$this->chart_groupby = 'day';
			break;

			case 'month' :
				$this->start_date    = strtotime( date( 'Y-m-01', current_time('timestamp') ) );
				$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
				$this->chart_groupby = 'day';
			break;

			case '7day' :
				$this->start_date    = strtotime( '-6 days', current_time( 'timestamp' ) );
				$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
				$this->chart_groupby = 'day';
			break;
		}

		// Group by
		switch ( $this->chart_groupby ) {

			case 'day' :
				$this->group_by_query = 'YEAR(post_date), MONTH(post_date), DAY(post_date)';
				$this->chart_interval = ceil( max( 0, ( $this->end_date - $this->start_date ) / ( 60 * 60 * 24 ) ) );
				$this->barwidth       = 60 * 60 * 24 * 1000;
			break;

			case 'month' :
				$this->group_by_query = 'YEAR(post_date), MONTH(post_date)';
				$this->chart_interval = 0;
				$min_date             = $this->start_date;

				while ( ( $min_date   = strtotime( "+1 MONTH", $min_date ) ) <= $this->end_date ) {
					$this->chart_interval ++;
				}

				$this->barwidth = 60 * 60 * 24 * 7 * 4 * 1000;
			break;
		}
	}
  
  public function get_order_report_data( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'data'         => array(),
			'where'        => array(),
			'where_meta'   => array(),
			'query_type'   => 'get_row',
			'group_by'     => '',
			'order_by'     => '',
			'limit'        => '',
			'filter_range' => false,
      'nocache'      => false,
			'debug'        => false,
		);

		$args = apply_filters( 'colabs_reports_get_order_report_data_args', wp_parse_args( $args, $defaults ) );

		extract( $args );
    
    $select = array();
    
    if ( empty( $data ) ) {
			return false;
		}
    
    foreach ( $data as $key => $value ) {
			$distinct = '';

			if ( isset( $value['distinct'] ) )
				$distinct = 'DISTINCT';

			if ( $value['type'] == 'meta' ) {
				$get_key = "meta_{$key}.meta_value";
			} elseif( $value['type'] == 'post_data' ) {
				$get_key = "posts.{$key}";
			}

			if ( $value['function'] ) {
				$get = "{$value['function']}({$distinct} {$get_key})";
			} else {
				$get = "{$distinct} {$get_key}";
			}

			$select[] = "{$get} as {$value['name']}";
		}
    
    $query['select'] = "SELECT " . implode( ',', $select );
		$query['from']   = "FROM {$wpdb->posts} AS posts";
    
    // Joins
		$joins = array();

		foreach ( $data as $key => $value ) {

			if ( $value['type'] == 'meta' ) {

				$joins["meta_{$key}"] = "LEFT JOIN {$wpdb->postmeta} AS meta_{$key} ON posts.ID = meta_{$key}.post_id";

			} 
		}
    
    $query['join'] = implode( ' ', $joins );
    
		$query['where']  = "
			WHERE 	posts.post_type 	IN ( '". COLABS_ORDER_POST_TYPE ."' )
			AND 	posts.post_status 	IN ( '" . COLABS_ORDER_COMPLETED."','".COLABS_ORDER_ACTIVATED . "')
			";
    
    if ( $filter_range ) {

			$query['where'] .= "
				AND 	post_date >= '" . date('Y-m-d', $this->start_date ) . "'
				AND 	post_date < '" . date('Y-m-d', strtotime( '+1 DAY', $this->end_date ) ) . "'
			";
		}
    
    foreach ( $data as $key => $value ) {

			if ( $value['type'] == 'meta' ) {

				$query['where'] .= " AND meta_{$key}.meta_key = '{$key}'";

			}
		}
    
    if ( $group_by ) {
			$query['group_by'] = "GROUP BY {$group_by}";
		}

		if ( $order_by ) {
			$query['order_by'] = "ORDER BY {$order_by}";
		}

		if ( $limit ) {
			$query['limit'] = "LIMIT {$limit}";
		}
    
    $query          = apply_filters( 'colabs_reports_get_order_report_query', $query );
		$query          = implode( ' ', $query );
		$query_hash     = md5( $query_type . $query );
		$cached_results = get_transient( strtolower( get_class( $this ) ) );
    
    if ( $debug ) {
			var_dump( $query );
		}

		if ( $debug || $nocache || false === $cached_results || ! isset( $cached_results[ $query_hash ] ) ) {
			$cached_results[ $query_hash ] = apply_filters( 'colabs_reports_get_order_report_data', $wpdb->$query_type( $query ), $data );
			set_transient( strtolower( get_class( $this ) ), $cached_results, DAY_IN_SECONDS );
		}
    
		$result = $cached_results[ $query_hash ];
 
		return $result;
  }  
}
