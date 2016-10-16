<?php
// Register Custom Post Type
function colabs_child_register_post_type() {

  // make sure the new roles are added to the DB before registering the post types
	colabs_init_roles();

	$labels = array(
		'name'                => _x( 'Properties', 'Post Type General Name', 'colabsthemes' ),
		'singular_name'       => _x( 'Property', 'Post Type Singular Name', 'colabsthemes' ),
		'menu_name'           => __( 'Properties', 'colabsthemes' ),
		'parent_item_colon'   => __( 'Parent Property:', 'colabsthemes' ),
		'all_items'           => __( 'All Properties', 'colabsthemes' ),
		'view_item'           => __( 'View Property', 'colabsthemes' ),
		'add_new_item'        => __( 'Add New Property', 'colabsthemes' ),
		'add_new'             => __( 'Add New', 'colabsthemes' ),
		'edit_item'           => __( 'Edit Property', 'colabsthemes' ),
		'update_item'         => __( 'Update Property', 'colabsthemes' ),
		'search_items'        => __( 'Search Property', 'colabsthemes' ),
		'not_found'           => __( 'Not found', 'colabsthemes' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'colabsthemes' ),
	);
	$args = array(
		'label'               => __( 'Property', 'colabsthemes' ),
		'description'         => __( 'Property Custom Post Type', 'colabsthemes' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'comments', ),
        'taxonomies'          => array( 'property_type', 'property_location', 'property_status', 'property_features' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
        'rewrite' => array('slug' => 'status/%property_status%'),
	);
	register_post_type( 'property', $args );

  $agent_labels = array(
		'name'                => _x( 'Agents', 'Post Type General Name', 'colabsthemes' ),
		'singular_name'       => _x( 'Agent', 'Post Type Singular Name', 'colabsthemes' ),
		'menu_name'           => __( 'Agents', 'colabsthemes' ),
		'name_admin_bar'      => __( 'Agent', 'colabsthemes' ),
		'parent_item_colon'   => __( 'Parent Agent:', 'colabsthemes' ),
		'all_items'           => __( 'All Agents', 'colabsthemes' ),
		'add_new_item'        => __( 'Add New Agent', 'colabsthemes' ),
		'add_new'             => __( 'Add New', 'colabsthemes' ),
		'new_item'            => __( 'New Agent', 'colabsthemes' ),
		'edit_item'           => __( 'Edit Agent', 'colabsthemes' ),
		'update_item'         => __( 'Update Agent', 'colabsthemes' ),
		'view_item'           => __( 'View Agent', 'colabsthemes' ),
		'search_items'        => __( 'Search Agent', 'colabsthemes' ),
		'not_found'           => __( 'Not found', 'colabsthemes' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'colabsthemes' ),
	);
	$agent_args = array(
		'label'               => __( 'Agent', 'colabsthemes' ),
		'description'         => __( 'Agent Post Type', 'colabsthemes' ),
		'labels'              => $agent_labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'agent', $agent_args );

}

function remove_my_cpt() {
    remove_action( 'init',  'colabs_register_post_type', 0);
}
add_action( 'after_setup_theme' , 'remove_my_cpt' );
function remove_expire_cron(){
    remove_action( 'init', 'colabs_schedule_properties_prune' );
}
add_action( 'after_setup_theme' , 'remove_expire_cron' );


// Hook into the 'init' action
add_action( 'init', 'colabs_child_register_post_type', 0 );
