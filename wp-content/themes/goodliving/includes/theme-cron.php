<?php
/**
 * FoxEstate Cron Properties
 * This file contains the cron properties used on the theme.
 */

add_action( 'init', 'colabs_schedule_properties_prune' );

/**
* Schedule a cron property for expired properties
* 
* @return void
*/
function colabs_schedule_properties_prune() {
 	if ( !wp_next_scheduled( 'colabs_check_properties_expired' ) )
		wp_schedule_event( time(), 'hourly', 'colabs_check_properties_expired' );
}
