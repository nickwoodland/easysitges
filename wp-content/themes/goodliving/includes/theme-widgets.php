<?php
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
/*---------------------------------------------------------------------------------*/
/* Loads all the .php files found in /includes/widgets/ directory */
/*---------------------------------------------------------------------------------*/

include( get_template_directory() . '/includes/widgets/widget-colabs-tabs.php' );
include( get_template_directory() . '/includes/widgets/widget-colabs-flickr.php' );
include( get_template_directory() . '/includes/widgets/widget-colabs-socialnetwork.php' );
include( get_template_directory() . '/includes/widgets/widget-colabs-ad-sidebar.php' );
include( get_template_directory() . '/includes/widgets/widget-colabs-embed.php' );
include( get_template_directory() . '/includes/widgets/widget-colabs-twitter.php' );
include( get_template_directory() . '/includes/widgets/widget-colabs-fbfriends.php' );

?>