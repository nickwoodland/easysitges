<?PHP
// Register Custom Taxonomy
function colabs_child_taxonomy_register() {

	$labels = array(
		'name'                       => _x( 'Property Types', 'Taxonomy General Name', 'colabsthemes' ),
		'singular_name'              => _x( 'Property Type', 'Taxonomy Singular Name', 'colabsthemes' ),
		'menu_name'                  => __( 'Property Types', 'colabsthemes' ),
		'all_items'                  => __( 'All Property Types', 'colabsthemes' ),
		'parent_item'                => __( 'Parent Property Type', 'colabsthemes' ),
		'parent_item_colon'          => __( 'Parent Property Type:', 'colabsthemes' ),
		'new_item_name'              => __( 'New Property Type Name', 'colabsthemes' ),
		'add_new_item'               => __( 'Add New Property Type', 'colabsthemes' ),
		'edit_item'                  => __( 'Edit Property Type', 'colabsthemes' ),
		'update_item'                => __( 'Update Property Type', 'colabsthemes' ),
		'separate_items_with_commas' => __( 'Separate property types with commas', 'colabsthemes' ),
		'search_items'               => __( 'Search Property Types', 'colabsthemes' ),
		'add_or_remove_items'        => __( 'Add or remove property types', 'colabsthemes' ),
		'choose_from_most_used'      => __( 'Choose from the most used property types', 'colabsthemes' ),
		'not_found'                  => __( 'Not Found', 'colabsthemes' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'property_type', array( 'property' ), $args );

  $property_features_labels = array(
		'name'                       => _x( 'Property Features', 'Taxonomy General Name', 'colabsthemes' ),
		'singular_name'              => _x( 'Property Feature', 'Taxonomy Singular Name', 'colabsthemes' ),
		'menu_name'                  => __( 'Property Features', 'colabsthemes' ),
		'all_items'                  => __( 'All Property Features', 'colabsthemes' ),
		'parent_item'                => __( 'Parent Property Feature', 'colabsthemes' ),
		'parent_item_colon'          => __( 'Parent Property Feature:', 'colabsthemes' ),
		'new_item_name'              => __( 'New Property Feature Name', 'colabsthemes' ),
		'add_new_item'               => __( 'Add New Property Feature', 'colabsthemes' ),
		'edit_item'                  => __( 'Edit Property Feature', 'colabsthemes' ),
		'update_item'                => __( 'Update Property Feature', 'colabsthemes' ),
		'separate_items_with_commas' => __( 'Separate property features with commas', 'colabsthemes' ),
		'search_items'               => __( 'Search Property Features', 'colabsthemes' ),
		'add_or_remove_items'        => __( 'Add or remove property features', 'colabsthemes' ),
		'choose_from_most_used'      => __( 'Choose from the most used property features', 'colabsthemes' ),
		'not_found'                  => __( 'Not Found', 'colabsthemes' ),
	);
	$property_features_args = array(
		'labels'                     => $property_features_labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'property_features', array( 'property' ), $property_features_args );

  $property_status_labels = array(
		'name'                       => _x( 'Property Status', 'Taxonomy General Name', 'colabsthemes' ),
		'singular_name'              => _x( 'Property Status', 'Taxonomy Singular Name', 'colabsthemes' ),
		'menu_name'                  => __( 'Property Status', 'colabsthemes' ),
		'all_items'                  => __( 'All Property Status', 'colabsthemes' ),
		'parent_item'                => __( 'Parent Property Status', 'colabsthemes' ),
		'parent_item_colon'          => __( 'Parent Property Status:', 'colabsthemes' ),
		'new_item_name'              => __( 'New Property Status Name', 'colabsthemes' ),
		'add_new_item'               => __( 'Add New Property Status', 'colabsthemes' ),
		'edit_item'                  => __( 'Edit Property Status', 'colabsthemes' ),
		'update_item'                => __( 'Update Property Status', 'colabsthemes' ),
		'separate_items_with_commas' => __( 'Separate property status with commas', 'colabsthemes' ),
		'search_items'               => __( 'Search Property Status', 'colabsthemes' ),
		'add_or_remove_items'        => __( 'Add or remove property status', 'colabsthemes' ),
		'choose_from_most_used'      => __( 'Choose from the most used property status', 'colabsthemes' ),
		'not_found'                  => __( 'Not Found', 'colabsthemes' ),
	);
	$property_status_args = array(
		'labels'                     => $property_status_labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => false,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
	);
	register_taxonomy( 'property_status', array( 'property' ), $property_status_args );

  $property_location_labels = array(
		'name'                       => _x( 'Property Locations', 'Taxonomy General Name', 'colabsthemes' ),
		'singular_name'              => _x( 'Property Location', 'Taxonomy Singular Name', 'colabsthemes' ),
		'menu_name'                  => __( 'Property Locations', 'colabsthemes' ),
		'all_items'                  => __( 'All Property Locations', 'colabsthemes' ),
		'parent_item'                => __( 'Parent Property Location', 'colabsthemes' ),
		'parent_item_colon'          => __( 'Parent Property Location:', 'colabsthemes' ),
		'new_item_name'              => __( 'New Property Location Name', 'colabsthemes' ),
		'add_new_item'               => __( 'Add New Property Location', 'colabsthemes' ),
		'edit_item'                  => __( 'Edit Property Location', 'colabsthemes' ),
		'update_item'                => __( 'Update Property Location', 'colabsthemes' ),
		'separate_items_with_commas' => __( 'Separate property locations with commas', 'colabsthemes' ),
		'search_items'               => __( 'Search Property Locations', 'colabsthemes' ),
		'add_or_remove_items'        => __( 'Add or remove property location', 'colabsthemes' ),
		'choose_from_most_used'      => __( 'Choose from the most used property location', 'colabsthemes' ),
		'not_found'                  => __( 'Not Found', 'colabsthemes' ),
	);
	$property_location_args = array(
		'labels'                     => $property_location_labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => false,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
	);
	register_taxonomy( 'property_location', array( 'property' ), $property_location_args );

}

function remove_my_tax() {
    remove_action( 'init',  'colabs_taxonomy_register', 0);
}
add_action( 'after_setup_theme' , 'remove_my_tax' );

// Hook into the 'init' action
add_action( 'init', 'colabs_child_taxonomy_register', 0 );
