<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'admin_menu', 'colabs_report_add_menu', 12 );
function colabs_report_add_menu(){
  $reports_page = add_submenu_page( 'colabsthemes', __( 'Reports', 'colabsthemes' ),  __( 'Reports', 'colabsthemes' ) , 'manage_options', 'foxestate-reports', 'colabs_reports_page' );
  add_action( 'admin_print_styles-' . $reports_page, 'colabs_reports_admin_styles');
  add_action( 'admin_print_scripts-' . $reports_page, 'colabs_reports_admin_scripts' );
}

function colabs_reports_admin_scripts(){
	global $is_IE;
	
	if ($is_IE) // only load this support js when browser is IE
		wp_enqueue_script('excanvas', get_template_directory_uri().'/includes/js/flot/excanvas.min.js', array('jquery'), '1.2');

		wp_enqueue_script('flot', get_template_directory_uri().'/includes/js/flot/jquery.flot.min.js', array('jquery'), '1.2',true);
    wp_enqueue_script( 'jquery-ui-datepicker' );
}

function colabs_reports_admin_styles(){
  wp_enqueue_style( 'jquery-ui-datepicker-style', get_template_directory_uri() . '/functions/css/jquery-ui-datepicker.css' );
  ?>
	<style>
    .colabs-reports-wide.halved{
        margin: 0;
        overflow: hidden;
    }
    .colabs-reports-wide .widefat td{
        padding: 7px;
        vertical-align: top;
    }
    .colabs-reports-wide .widefat td .description{
        margin: 4px 0 0;
    }
    .colabs-reports-wide .postbox:after{
        clear: both;
        content: ".";
        display: block;
        height: 0;
        visibility: hidden;
    }
    .colabs-reports-wide .postbox h3{
        cursor: default !important;
    }
    .colabs-reports-wide .postbox .inside{
        margin: 0 !important;
        padding: 10px;
    }
    .colabs-reports-wide .postbox h3.stats_range{
        border-bottom-color: #dfdfdf;
        padding: 0 !important;
    }
    .colabs-reports-wide .postbox h3.stats_range .export_csv{
        border-left: 1px solid #dfdfdf;
        display: block;
        float: right;
        line-height: 26px;
        padding: 10px;
        text-decoration: none;
    }
    .colabs-reports-wide .postbox h3.stats_range ul{
        background: none repeat scroll 0 0 #f5f5f5;
        list-style: outside none none;
        margin: 0;
        padding: 0;
    }
    .colabs-reports-wide .postbox h3.stats_range ul:after, .colabs-reports-wide .postbox h3.stats_range ul:before{
        content: " ";
        display: table;
    }
    .colabs-reports-wide .postbox h3.stats_range ul:after{
        clear: both;
    }
    .colabs-reports-wide .postbox h3.stats_range ul li{
        float: left;
        line-height: 26px;
        margin: 0;
        padding: 0;
    }
    .colabs-reports-wide .postbox h3.stats_range ul li a{
        border-right: 1px solid #dfdfdf;
        display: block;
        padding: 10px;
        text-decoration: none;
    }
    .colabs-reports-wide .postbox h3.stats_range ul li.active{
        background: none repeat scroll 0 0 #fff;
        box-shadow: 0 4px 0 0 #fff;
    }
    .colabs-reports-wide .postbox h3.stats_range ul li.active a{
        color: #777;
    }
    .colabs-reports-wide .postbox h3.stats_range ul li.custom{
        padding: 9px 10px;
        vertical-align: middle;
    }
    .colabs-reports-wide .postbox h3.stats_range ul li.custom div, .colabs-reports-wide .postbox h3.stats_range ul li.custom form{
        display: inline;
        margin: 0;
    }
    .colabs-reports-wide .postbox h3.stats_range ul li.custom div input.range_datepicker, .colabs-reports-wide .postbox h3.stats_range ul li.custom form input.range_datepicker{
        background: none repeat scroll 0 0 rgba(0, 0, 0, 0);
        border: 0 none;
        box-shadow: none;
        color: #777;
        margin: 0 10px 0 0;
        padding: 0;
        text-align: center;
    }
    .colabs-reports-wide .postbox .chart-with-sidebar{
        margin: 0 !important;
        padding: 12px 12px 12px 249px;
    }
    .colabs-reports-wide .postbox .chart-with-sidebar .chart-sidebar{
        float: left;
        margin-left: -237px;
        width: 225px;
    }
    .colabs-reports-wide .postbox .chart-legend{
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        -moz-border-right-colors: none;
        -moz-border-top-colors: none;
        background: none repeat scroll 0 0 #fff;
        border-color: #dfdfdf;
        border-image: none;
        border-style: solid;
        border-width: 1px 0 0 1px;
        list-style: outside none none;
        margin: 0 0 1em;
        padding: 0;
    }
    .colabs-reports-wide .postbox .chart-legend li{
        border-right: 5px solid #aaa;
        box-shadow: 0 -1px 0 0 #dfdfdf inset;
        color: #aaa;
        display: block;
        margin: 0;
        padding: 1em;
    }
    .colabs-reports-wide .postbox .chart-legend li strong{
        color: #464646;
        display: block;
        font-family: HelveticaNeue-Light,"Helvetica Neue Light","Helvetica Neue",sans-serif;
        font-size: 1.618em;
        font-weight: 400;
        line-height: 1.2em;
    }
    .colabs-reports-wide .postbox .chart-legend li:hover{
        border-right: 5px solid #ea6d37 !important;
        box-shadow: 0 -1px 0 0 #dfdfdf inset, 300px 0 0 rgba(255, 100, 0, 0.1) inset;
        color: #ea6d37;
        padding-left: 1.5em;
    }
    .colabs-reports-wide .postbox .pie-chart-legend{
        margin: 12px 0 0;
        overflow: hidden;
    }
    .colabs-reports-wide .postbox .pie-chart-legend li{
        border-top: 4px solid #999;
        box-sizing: border-box;
        float: left;
        margin: 0;
        padding: 6px 0 0;
        text-align: center;
        width: 50%;
    }
    .colabs-reports-wide .postbox .stat{
        font-size: 1.5em !important;
        font-weight: 700;
        text-align: center;
    }
    .colabs-reports-wide .postbox .chart-placeholder{
        height: 650px;
        overflow: hidden;
        position: relative;
        width: 100%;
    }
    .colabs-reports-wide .postbox .chart-prompt{
        color: #999;
        font-size: 1.2em;
        font-style: italic;
        line-height: 650px;
        margin: 0;
        text-align: center;
    }
    .colabs-reports-wide .postbox .chart-container{
        background: none repeat scroll 0 0 #fff;
        border: 1px solid #dfdfdf;
        border-radius: 3px;
        padding: 12px;
        position: relative;
    }
    .colabs-reports-wide .postbox .main .chart-legend{
        margin-top: 12px;
    }
    .colabs-reports-wide .postbox .main .chart-legend li{
        border-right: 0 none;
        border-top: 4px solid #aaa;
        float: left;
        margin: 0 8px 0 0;
    }
    .colabs-reports-wide .colabs-reports-main{
        float: left;
        min-width: 100%;
    }
    .chart-tooltip {
        display: none;
        line-height: 1;
        position: absolute;
        background: none repeat scroll 0 0 #464646;
        border-radius: 3px;
        box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        color: #fff;
        font-size: 11px;
        max-width: 150px;
        padding: 0.5em;
        text-align: center;
    }
    .new_registration .table {
				float: left;
				width: 50%;
		}
		.new_registration {
				margin-bottom: 30px;
				overflow: hidden;
		}
		#dashboard_right_now .new_registration .sub {
				color: #444;
				font-size: 16px;
        margin: 1.33em 0 0;
				padding: 0 0 6px;
				border-top: none;
				background: none;
        font-weight: 600;
		}
		
		#dashboard_right_now .stats-info ul li a:before, #dashboard_right_now .stats-info ul li span:before {
			content: "\f139";
		}	
		#dashboard_right_now td.count {
				font-size: 18px;
				padding-right: 6px;
		}
		#dashboard_right_now td{
			color:#21759B;
		}
		.js .widget .widget-top, .js .postbox h3 {
				cursor: default;
		}
    #ui-datepicker-div {
        display: none;
    }
    #dashboard_right_now .inside .main{
        padding: 12px;
    }
  </style>
  <?php
}

function colabs_reports_page(){
  Colabs_Admin_Reports::output();
}

if ( ! class_exists( 'Colabs_Admin_Reports' ) ) :

/**
 * Colabs_Admin_Reports Class
 */
class Colabs_Admin_Reports {
  public static function output() {
		$reports        = self::get_reports();
		$first_tab      = array_keys( $reports );
		$current_tab    = ! empty( $_GET['tab'] ) ? sanitize_title( $_GET['tab'] ) : $first_tab[0];
		$current_report = isset( $_GET['report'] ) ? sanitize_title( $_GET['report'] ) : current( array_keys( $reports[ $current_tab ]['reports'] ) );
    ?>
    <div class="wrap">
    <div class="icon32 icon32-foxestate-reports" id="icon-foxestate"><br /></div>
      <h2></h2>

      <?php if ( sizeof( $reports[ $current_tab ]['reports'] ) > 1 ) {
        ?>
        <ul class="subsubsub">
          <li><?php

            $links = array();

            foreach ( $reports[ $current_tab ]['reports'] as $key => $report ) {

              $link = '<a href="admin.php?page=foxestate-reports&tab=' . urlencode( $current_tab ) . '&amp;report=' . urlencode( $key ) . '" class="';

              if ( $key == $current_report ) $link .= 'current';

              $link .= '">' . $report['title'] . '</a>';

              $links[] = $link;

            }

            echo implode(' | </li><li>', $links);

          ?></li>
        </ul>
        <br class="clear" />
        <?php
      }

      if ( isset( $reports[ $current_tab ][ 'reports' ][ $current_report ] ) ) {

        $report = $reports[ $current_tab ][ 'reports' ][ $current_report ];

        if ( ! isset( $report['hide_title'] ) || $report['hide_title'] != true )
          echo '<h3>' . $report['title'] . '</h3>';

        if ( $report['description'] )
          echo '<p>' . $report['description'] . '</p>';

        if ( $report['callback'] && ( is_callable( $report['callback'] ) ) )
          call_user_func( $report['callback'], $current_report );
      }
      ?>
    </div>
    <?php
	}
  
  public static function get_reports() {
		$reports = array(
			'orders'     => array(
				'title'  => __( 'Orders', 'colabsthemes' ),
				'reports' => array(
					"sales_by_date"    => array(
						'title'       => __( 'Sales by date', 'colabsthemes' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' )
					),
				)
			),
	
		);

		$reports = apply_filters( 'colabs_admin_reports', $reports );
		$reports = apply_filters( 'colabs_reports_charts', $reports ); // Backwards compat

		foreach ( $reports as $key => $report_group ) {
			if ( isset( $reports[ $key ]['charts'] ) ) {
				$reports[ $key ]['reports'] = $reports[ $key ]['charts'];
			}

			foreach ( $reports[ $key ]['reports'] as $report_key => $report ) {
				if ( isset( $reports[ $key ]['reports'][ $report_key ]['function'] ) ) {
					$reports[ $key ]['reports'][ $report_key ]['callback'] = $reports[ $key ]['reports'][ $report_key ]['function'];
				}
			}
		}

		return $reports;
	}
  
  public static function get_report( $name ) {
		$name  = sanitize_title( str_replace( '_', '-', $name ) );
		$class = 'Colabs_Report_' . str_replace( '-', '_', $name );

		include_once( apply_filters( 'colabs_admin_reports_path', 'admin/class-colabs-report-' . $name . '.php', $name, $class ) );

		if ( ! class_exists( $class ) )
			return;

		$report = new $class();
		$report->output_report();
	}
}

endif;