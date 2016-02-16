<?php
// Register Custom Post Type
function colabs_register_post_type() {
  
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

// Hook into the 'init' action
add_action( 'init', 'colabs_register_post_type', 0 );

// Register Custom Taxonomy
function colabs_taxonomy_register() {

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
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => false,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
	);
	register_taxonomy( 'property_location', array( 'property' ), $property_location_args );

}

// Hook into the 'init' action
add_action( 'init', 'colabs_taxonomy_register', 0 );

add_filter("manage_edit-agent_columns", "agent_edit_columns");
function agent_edit_columns($columns){  
  $columns = array(  
      "cb" => "<input type=\"checkbox\" />", 
      "photo" => __("","colabsthemes"),
      "title" => __("Name","colabsthemes"), 
      "email" => __("Email","colabsthemes"), 
      "date" => __("Date","colabsthemes"),  
  );  
  
  return $columns;  
}  

add_action("manage_agent_posts_custom_column",  "agent_custom_columns"); 
function agent_custom_columns($column){  
  global $post;  
  switch ($column){    
      case "email":  
        echo get_post_meta($post->ID,'colabs_email_agent',true);  
        break; 	  
      case "photo":
        if(has_post_thumbnail()) the_post_thumbnail(array(50,50));
        break;	
  }  
}

add_filter("manage_edit-property_columns", "property_edit_columns");     
function property_edit_columns($columns){  
  $columns = array(  
      "cb" => "<input type=\"checkbox\" />",
      "photo" => __("","colabsthemes"),
      "title" => __("Property","colabsthemes"), 
      "property_type" => __("Type","colabsthemes"), 
      "property_location" => __("Location","colabsthemes"),
      "property_features" => __("Features","colabsthemes"),
      "property_status" => __("Status","colabsthemes"), 
      "date" => __("Date","colabsthemes"),          
  );  
  
  return $columns;  
}  

add_action("manage_property_posts_custom_column",  "property_custom_columns"); 
function property_custom_columns($column){  
  global $post;  
  switch ($column){    
      case "property_type":  
          echo get_the_term_list($post->ID, 'property_type', '', ', ','');  
          break; 	  
      case "property_location":  
          echo get_the_term_list($post->ID, 'property_location', '', ', ','');  
          break; 	  
      case "property_features":  
          echo get_the_term_list($post->ID, 'property_features', '', ', ','');  
          break; 	   	
      case "photo":
          if(has_post_thumbnail()) the_post_thumbnail(array(50,50));
          break;
      case "property_status":  
          echo get_the_term_list($post->ID, 'property_status', '', ', ','');  
          break;	
  }  
}

//style for property table
add_action('admin_head', 'colabs_admin_styling');
function colabs_admin_styling() {
echo '<style type="text/css">
		th#photo.column-photo{width:60px;}
		.attachment-50x50.wp-post-image{-webkit-border-radius: 60px;-moz-border-radius: 60px;-ms-border-radius: 60px;border-radius: 60px;}
		.attachment-50x50.wp-post-image:hover{width:55px;height:55px;}
	  </style>';  
}

// action for featured
function colabs_property_featured_action() {

	if ( ! is_admin() ) die;

	if ( ! current_user_can('edit_posts') ) wp_die( __('You do not have sufficient permissions to access this page.', 'colabsthemes') );

	if ( ! check_admin_referer('property-featured')) wp_die( __('You have taken too long. Please go back and retry.', 'colabsthemes') );

	$post_id = isset( $_GET['id'] ) && (int) $_GET['id'] ? (int) $_GET['id'] : '';

	if (!$post_id) die;

	$post = get_post($post_id);

	if ( ! $post || $post->post_type !== COLABS_POST_TYPE ) die;

	//Featured
  if ($_GET['action']=='property-featured') :
    $stickypost = get_option('sticky_posts');
    $key = array_search($post_id, $stickypost);
    if (false == $key) {
      $stickypost[] = $post_id;
    }else{
      unset($stickypost[$key]);
    }
    update_option('sticky_posts', $stickypost);
  endif;

	wp_safe_redirect( remove_query_arg( array('trashed', 'untrashed', 'deleted', 'ids'), wp_get_referer() ) );
}

add_action('wp_ajax_property-featured', 'colabs_property_featured_action');

// CREATE FILTERS WITH CUSTOM TAXONOMIES
if ( isset($_GET['post_type']) ) {
	$post_type = $_GET['post_type'];
}
else {
	$post_type = '';
}

if ( $post_type == 'property' ) {
	add_action( 'restrict_manage_posts','property_type_filter_list' );
	add_filter('posts_where', 'colabs_property_posts_where');
}

function property_type_filter_list() {
  $screen = get_current_screen();
  global $wp_query;
  if ( $screen->post_type == 'property' ) {
    wp_dropdown_categories(array(
						'show_option_all' => 'Show All Property Types',
						'taxonomy' => 'property_type',
						'name' => 'property_type',
						'orderby' => 'name',
						'selected' =>( isset( $wp_query->query['property_type'] ) ?
						$wp_query->query['property_type'] : '' ),
					  'hierarchical' => false,
					  'depth' => 3,
					  'show_count' => false,
					  'hide_empty' => true,
			));
		wp_dropdown_categories(array(
						'show_option_all' => 'Show All Property Locations',
						'taxonomy' => 'property_location',
						'name' => 'property_location',
						'orderby' => 'name',
						'selected' =>( isset( $wp_query->query['property_location'] ) ?
						$wp_query->query['property_location'] : '' ),
					  'hierarchical' => false,
					  'depth' => 3,
					  'show_count' => false,
					  'hide_empty' => true,
			));	
		wp_dropdown_categories(array(
						'show_option_all' => 'Show All Property Features',
						'taxonomy' => 'property_features',
						'name' => 'property_features',
						'orderby' => 'name',
						'selected' =>( isset( $wp_query->query['property_features'] ) ?
						$wp_query->query['property_features'] : '' ),
					  'hierarchical' => false,
					  'depth' => 3,
					  'show_count' => false,
					  'hide_empty' => true,
			));
		wp_dropdown_categories(array(
						'show_option_all' => 'Show All Property Status',
						'taxonomy' => 'property_status',
						'name' => 'property_status',
						'orderby' => 'name',
						'selected' =>( isset( $wp_query->query['property_status'] ) ?
						$wp_query->query['property_status'] : '' ),
					  'hierarchical' => false,
					  'depth' => 3,
					  'show_count' => false,
					  'hide_empty' => true,
			));	
	}
}

// Custom Query to filter edit grid
function colabs_property_posts_where($where) {
    if( is_admin() ) {
        global $wpdb;
        if (isset($_GET['location_names'])) { $location_ID = $_GET['location_names'];  } else { $location_ID = '';  }
        if (isset($_GET['type_names'])) { $type_ID = $_GET['type_names'];  } else { $type_ID = '';  }
		if (isset($_GET['feature_names'])) { $feature_ID = $_GET['feature_names'];  } else { $feature_ID = '';  }
		if ( ($location_ID > 0) || ($type_ID > 0) || ($feature_ID > 0) ) {

			$location_tax_names =  &get_term( $location_ID, 'property_location' );
			$type_tax_names =  &get_term( $type_ID, 'property_type' );
			$feature_tax_names =  &get_term( $feature_ID, 'property_features' );
			$string_post_ids = '';
 			//locations
			if ($location_ID > 0) {
				$location_tax_name = $location_tax_names->slug;
				$location_myposts = get_posts('nopaging=true&post_type=property&property_location='.$location_tax_name);
				foreach($location_myposts as $post) {
					$string_post_ids .= $post->ID.',';
				}
			}
			//property types
			if ($type_ID > 0) {
				$type_tax_name = $type_tax_names->slug;
				$type_myposts = get_posts('nopaging=true&post_type=property&property_type='.$type_tax_name);
				foreach($type_myposts as $post) {
					$string_post_ids .= $post->ID.',';
				}
			}
			//additional features
			if ($feature_ID > 0) {
				$feature_tax_name = $feature_tax_names->slug;
				$feature_myposts = get_posts('nopaging=true&post_type=property&property_features='.$feature_tax_name);
				foreach($feature_myposts as $post) {
					$string_post_ids .= $post->ID.',';
				}
   			}
 			$string_post_ids = chop($string_post_ids,',');
   			$where .= "AND ID IN (" . $string_post_ids . ")";
		}
    }
    return $where;
}

add_filter( 'parse_query','perform_filtering' );

function perform_filtering( $query )
 {
    $qv = &$query->query_vars;
    if ( isset( $qv['property_type'] ) && is_numeric( $qv['property_type'] ) ) {
      $term = get_term_by( 'id', $qv['property_type'], 'property_type' ); 
			$qv['property_type'] = $term->slug;
		}
		if ( isset( $qv['property_location'] ) && is_numeric( $qv['property_location'] ) ) {
      $term = get_term_by( 'id', $qv['property_location'], 'property_location' ); 
			$qv['property_location'] = $term->slug;
		}
		if ( isset( $qv['property_features'] ) && is_numeric( $qv['property_features'] ) ) {
      $term = get_term_by( 'id', $qv['property_features'], 'property_features' ); 
			$qv['property_features'] = $term->slug;
		}
		if ( isset( $qv['property_status'] ) && is_numeric( $qv['property_status'] ) ) {
      $term = get_term_by( 'id', $qv['property_status'], 'property_status' ); 
			$qv['property_status'] = $term->slug;
		}
}

/*-----------------------------------------------------------------------------------*/
/* Taxonomy Search Functions */
/*-----------------------------------------------------------------------------------*/

//search taxonomies for a match against a search term and returns array of success count
function colabs_taxonomy_matches($term_name, $term_id, $post_id = 0, $keyword_to_search = '') {
	$return_array = array();
	$return_array['success'] = false;
	$return_array['keywordcount'] = 0;
	$terms = get_the_terms( $post_id , $term_name );
	$success = false;
	$keyword_count = 0;
	if ($term_id == 0) {
		$success = true;
	}
	$counter = 0;
	// Loop over each item
	if ($terms) {
		foreach( $terms as $term ) {

			if ($term->term_id == $term_id) {
				$success = true;
			}
			if ( $keyword_to_search != '' ) {
				$keyword_count = substr_count( strtolower( $term->name ) , strtolower( $keyword_to_search ) );
				if ( $keyword_count > 0 ) {
					$success = true;
					$counter++;
				}
			} else {
				//If search term is blank
				$location_tax_names =  get_term_by( 'id', $term_id, $term_name );
 				//locations
				if ($location_tax_names) {
					if (isset($location_tax_names->slug)) { $location_tax_name = $location_tax_names->slug; } else { $location_tax_name = ''; }
					if ($location_tax_name != '') {
						$location_myposts = get_posts('nopaging=true&post_type=property&'.$term_name.'='.$location_tax_name);
						foreach($location_myposts as $location_mypost) {
							if ($location_mypost->ID == $post_id) {
								$success = true;
	        					$counter++;
							} 
						}
					}
				}
			}
		}
	}
	$return_array['success'] = $success;
	if ($counter == 0) {
		$return_array['keywordcount'] = $keyword_count;
	} else { 
		$return_array['keywordcount'] = $counter;
	}
	
	return $return_array;
}



/*-----------------------------------------------------------------------------------*/
/* Property Search Function 
/*-----------------------------------------------------------------------------------*/

function colabs_property_search_result_set($query_args,$keyword_to_search, $location_id, $propertytypes_id, $propertystatus_id, $advanced_search = null, $search_type = '') {
	
	$search_results = array();
	$query_args['showposts'] = -1;$query_args['post_type'] = 'property';
	$the_query = new WP_Query($query_args);
	
	//Prepare Garages, Beds, Baths variables
	
	if ($advanced_search['beds'] == '10+') { 
		$advanced_beds = 10;
	} else {
		$advanced_beds = $advanced_search['beds'];
	}
	if ($advanced_search['baths'] == '10+') { 
		$advanced_baths = 10;
	} else {
		$advanced_baths = $advanced_search['baths'];
	}
	if ($advanced_search['garages'] == '10+') { 
		$advanced_garages = 10;
	} else {
		$advanced_garages = $advanced_search['garages'];
	}
	
	//Get matching method
	$matching_method = get_option('colabs_feature_matching_method');
	
	if ($the_query->have_posts()) : $count = 0;

	while ($the_query->have_posts()) : $the_query->the_post();

		global $post;
    $post_type = $post->post_type;
		
		
	  //Check Locations for matches
	  $location_terms = colabs_taxonomy_matches('property_location', $location_id, $post->ID, $keyword_to_search);
	  $success_location = $location_terms['success'];
	  $location_keyword_count = $location_terms['keywordcount'];

	  //Secondary Location Check
	  if ( (!$success_location) || ($location_keyword_count == 0) ) {
	    $location_tax_names =  get_term_by( 'name', $keyword_to_search, 'property_location' );

			if ($location_tax_names) {
				$location_tax_name = $location_tax_names->slug;
				if ($location_tax_name != '') {
					$location_myposts = get_posts('nopaging=true&post_type=property&property_location='.$location_tax_name);
					foreach($location_myposts as $location_mypost) {
						if ($location_mypost->ID == $post->ID) {
							$success_location = true;
							$location_keyword_count++;
						} 
					}
				}
			} 
	  }
	        
	  //Check Property Types for matches
	  $propertytypes_terms = colabs_taxonomy_matches('property_type', $propertytypes_id, $post->ID, $keyword_to_search);
	  $success_propertytype = $propertytypes_terms['success'];
	  $propertytype_keyword_count = $propertytypes_terms['keywordcount'];
	  
	  //Secondary Property Type Check
	  if ( (!$success_propertytype) || ($propertytype_keyword_count == 0) ) {
	  	$propertytype_tax_names =  get_term_by( 'name', $keyword_to_search, 'property_type' );

			if ($propertytype_tax_names) {
				$propertytype_tax_name = $propertytype_tax_names->slug;
				if ($propertytype_tax_name != '') {
					$propertytype_myposts = get_posts('nopaging=true&post_type=property&property_type='.$propertytype_tax_name);
					foreach($propertytype_myposts as $propertytype_mypost) {
						if ($propertytype_mypost->ID == $post->ID) {
							$success_propertytype = true;
	       			$propertytype_keyword_count++;
						} 
					}
				}
			} 
	  }
	  
		//Check Property Status for matches
	  $propertystatus_terms = colabs_taxonomy_matches('property_status', $propertystatus_id, $post->ID, $keyword_to_search);
	  $success_propertystatus = $propertystatus_terms['success'];
	  $propertystatus_keyword_count = $propertystatus_terms['keywordcount'];
	  
	  //Secondary Property Status Check
	  if ( (!$success_propertystatus) || ($propertystatus_keyword_count == 0) ) {
	  	$propertystatus_tax_names =  get_term_by( 'name', $keyword_to_search, 'property_type' );

			if ($propertystatus_tax_names) {
				$propertystatus_tax_name = $propertystatus_tax_names->slug;
				if ($propertystatus_tax_name != '') {
					$propertystatus_myposts = get_posts('nopaging=true&post_type=property&property_type='.$propertystatus_tax_name);
					foreach($propertystatus_myposts as $propertystatus_mypost) {
						if ($propertystatus_mypost->ID == $post->ID) {
							$success_propertystatus = true;
	       			$propertystatus_keyword_count++;
						} 
					}
				}
			} 
	  }	
		
	  //Check Additional Features for matches
	  $propertyfeatures_terms = colabs_taxonomy_matches('property_features', 0, $post->ID, $keyword_to_search);
	  $success_propertyfeatures = $propertyfeatures_terms['success'];
	  $propertyfeatures_keyword_count = $propertyfeatures_terms['keywordcount'];
		//Do custom meta boxes comparisons here
	  $property_address = get_post_meta($post->ID,'property_address',true);
	  $property_garages = get_post_meta($post->ID,'property_garages',true);
	  if ($property_garages == '10+' ) {
	  	$property_garages = 10;
	  }
		$property_garages_success = false;
		if ($advanced_garages == 'all') {
			$property_garages_success = true;
		} else {
				//Matching Method
				if ($matching_method == 'minimum') {
					//Minimum Value
					if ($property_garages >= $advanced_garages) {
						$property_garages_success = true;
					} else {
						$property_garages_success = false;
					}
				} else {
					//Exact Matching
					if ($property_garages == $advanced_garages) {
						$property_garages_success = true;
					} else {
						$property_garages_success = false;
					}
				}
		}
	  $property_beds = get_post_meta($post->ID,'property_beds',true);
	  if ($property_beds == '10+' ) {
	    $property_beds = 10;
	  }
		$property_beds_success = false;
		if ($advanced_beds == 'all') {
				$property_beds_success = true;
		} else {
				//Matching Method
				if ($matching_method == 'minimum') {
					//Minimum Value
					if ($property_beds >= $advanced_beds) {
						$property_beds_success = true;
					} else {
						$property_beds_success = false;
						
					}
				} else {
					//Exact Matching
					if ($property_beds == $advanced_beds) {
						$property_beds_success = true;
					} else {
						$property_beds_success = false;$property_beds_success = get_post_meta($post->ID,'property_beds',true);;
					}
				}
		}
	  $property_baths = get_post_meta($post->ID,'property_bathrooms',true);
	  if ($property_baths == '10+' ) {
	    $property_baths = 10;
	  }
		$property_baths_success = false;
		if ($advanced_baths == 'all') {
			$property_baths_success = true;
		} else {
				//Matching Method
				if ($matching_method == 'minimum') {
					//Minimum Value
					if ($property_baths >= $advanced_baths) {
						$property_baths_success = true;
					} else {
						$property_baths_success = false;
					}
				} else {
					//Exact Matching
					if ($property_baths == $advanced_baths) {
						$property_baths_success = true;
					} else {
						$property_baths_success = false;
					}
				}
		}
			
		// SIZE COMPARISON SCENARIO(S)
	  $property_size = get_post_meta($post->ID,'property_size',true);
		$property_size_success = false;
		//scenario 1 - only size min
		if ( ($advanced_search['size_min'] != '') && ( ($advanced_search['size_max'] == '') || ($advanced_search['size_max'] == 0) ) ) { 
				if ( ($property_size >= $advanced_search['size_min']) ) {
					$property_size_success = true;
				} else {
					$property_size_success = false;
				}
		}
		//scenario 2 - only size max
		elseif ( ( ($advanced_search['size_max'] != '') || ($advanced_search['size_max'] != 0) ) && ($advanced_search['size_min'] == '') ) { 
				if ( ($property_size <= $advanced_search['size_max']) ) {
					$property_size_success = true;
				} else {
					$property_size_success = false;
				}
		}
		//scenario 3 - size min and max are zero
		elseif ( ($advanced_search['size_min'] == '0') && ($advanced_search['size_max'] == 0) ) { 
				$property_size_success = true;
		}
		//scenario 4 - both min and max
		else {
				if ( ($property_size >= $advanced_search['size_min']) && ($property_size <= $advanced_search['size_max']) ) {
					$property_size_success = true;
				} else {
					$property_size_success = false;
				}
		}
			
		// PRICE COMPARISON SCENARIO(S)
	   $property_price = get_post_meta($post->ID,'property_price',true);
		$property_price_success = false;
		//scenario 1 - only price min
		if ( ($advanced_search['price_min'] != '') && ( ($advanced_search['price_max'] == '') || ($advanced_search['price_max'] == 0) ) ) { 
				if ( ($property_price >= $advanced_search['price_min']) ) {
					$property_price_success = true;
				} else {
					$property_price_success = false;
				}
		}
		//scenario 2 - only price max
		elseif ( ( ($advanced_search['price_max'] != '') || ($advanced_search['price_max'] != 0) ) && ($advanced_search['price_min'] == '') ) { 
				if ( ($property_price <= $advanced_search['price_max']) ) {
					$property_price_success = true;
				} else {
					$property_price_success = false;
				}
		}
		//scenario 3 - price min and max are zero
		elseif ( ($advanced_search['price_min'] == '0') && ($advanced_search['price_max'] == 0) ) { 
				$property_price_success = true;
		}
		//scenario 4 - both min and max
		else {
				if ( ($property_price >= $advanced_search['price_min']) && ($property_price <= $advanced_search['price_max']) ) {
					$property_price_success = true;
				} else {
					$property_price_success = false;
				}
		}
		//format price
		$property_price = number_format($property_price , 0 , '.', ',');
			
	  if ( $success_location && $success_propertytype && $success_propertystatus ) {  
	    //Search against post data
	    if ( $keyword_to_search != '' ) {
	    	//Default WordPress Content
	    	$raw_title = get_the_title();
	    	$raw_content = get_the_content();
	    	$raw_excerpt = get_the_excerpt();
	    	//Comparison
	    	$title_keyword_count = substr_count( strtolower( $raw_title ) , strtolower( $keyword_to_search ) );
	    	$content_keyword_count = substr_count( strtolower( $raw_content ) , strtolower( $keyword_to_search ) );
	    	$excerpt_keyword_count = substr_count( strtolower( $raw_excerpt ) , strtolower( $keyword_to_search ) );
	    	$property_address_count = substr_count( strtolower( $property_address ) , strtolower( $keyword_to_search ) );
	    }
	    //Check for matches or blank keyword
	    		
	    if ( $keyword_to_search == '') {
	    			
	    	if ( ( $location_keyword_count > 0 ) || ( $propertytype_keyword_count > 0 ) || ( $propertystatus_keyword_count > 0 ) || ( $propertyfeatures_keyword_count > 0 ) ) { 

						if ( (count($advanced_search) > 0) && ( ($advanced_search['garages'] != 'all') || ($advanced_search['beds'] != 'all') || ($advanced_search['baths'] != 'all') || ($advanced_search['price_min'] != '0') || ($advanced_search['price_max'] != '0') || ($advanced_search['size_min'] != '0') || ($advanced_search['size_max'] != '0') ) ) {
								
								if ($property_garages_success && $property_beds_success && $property_baths_success && $property_price_success && $property_size_success ) {
									//increment post counter
									
									$count++; 
									$has_results = true;
	    			
									//setup array data here
									array_push($search_results,$post->ID);
								}
							
						} else {
							//increment post counter
							$count++; 
							$has_results = true;
	    			
							//setup array data here
							array_push($search_results,$post->ID);
						}
						
	    	}elseif ( ( $location_keyword_count == 0 ) && ( $propertytype_keyword_count == 0 ) && ( $propertystatus_keyword_count == 0 ) && ( $propertyfeatures_keyword_count == 0 ) ) { 
						
						if ( (count($advanced_search) > 0) && ( ($advanced_search['garages'] != 'all') || ($advanced_search['beds'] != 'all') || ($advanced_search['baths'] != 'all') || ($advanced_search['price_min'] != '0') || ($advanced_search['price_max'] != '0') || ($advanced_search['size_min'] != '0') || ($advanced_search['size_max'] != '0') ) ) {
								
								if ($property_garages_success && $property_beds_success && $property_baths_success && $property_price_success && $property_size_success ) {
									//increment post counter
									$count++; 
									$has_results = true;
	    			
									//setup array data here
									array_push($search_results,$post->ID); 
								}
								$search_results = $property_beds_success;
						} else {
							//increment post counter
							$count++; 
							$has_results = true;
	    			
							//setup array data here
							array_push($search_results,$post->ID);
						}
						
				}
	    			
	    } else {
	    		
	    	if ( ( $title_keyword_count > 0 ) || ( $content_keyword_count > 0 ) || ( $excerpt_keyword_count > 0 ) || ( $location_keyword_count > 0 ) || ( $property_address_count > 0 ) || ( $propertytype_keyword_count > 0 ) || ( $propertystatus_keyword_count > 0 ) || ( $propertyfeatures_keyword_count > 0 ) ) {
	    			if ( (count($advanced_search) > 0) && ( ($advanced_search['garages'] != 'all') || ($advanced_search['beds'] != 'all') || ($advanced_search['baths'] != 'all') || ($advanced_search['price_min'] != '0') || ($advanced_search['price_max'] != '0') || ($advanced_search['size_min'] != '0') || ($advanced_search['size_max'] != '0') ) ) {
								
								if ($property_garages_success && $property_beds_success && $property_baths_success && $property_price_success && $property_size_success ) {
									//increment post counter
									$count++; 
									$has_results = true;
	    			
									//setup array data here
									array_push($search_results,$post->ID);
								}
						} else {
							//increment post counter
							$count++; 
							$has_results = true;
	    			
							//setup array data here
							array_push($search_results,$post->ID);
						}
	    	} 			
	    }   		 		
	  }		
	endwhile; else:
    	//no posts	    	
  endif;
	return $search_results;
}

// custom user page columns
function colabs_manage_users_columns( $columns ) {
  $columns['colabs_property_count'] = __('Property', 'colabsthemes');
	$columns['registered'] = __('Registered', 'colabsthemes');
  return $columns;
}
add_action('manage_users_columns', 'colabs_manage_users_columns');

// display the coumn values for each user
function colabs_manage_users_custom_column( $r, $column_name, $user_id ) {

	// count the total jobs for the user
	if ( 'colabs_property_count' == $column_name ) {
		global $property_counts;

		if ( !isset( $property_counts ) )
			$property_counts = colabs_count_custom_post_types( 'property' );

		if ( !array_key_exists( $user_id, $property_counts ) )
			$property_counts = colabs_count_custom_post_types( 'property' );

		if ( $property_counts[$user_id] > 0 ) {
			$r .= "<a href='edit.php?post_type=property&author=$user_id' title='" . esc_attr__( 'View property by this author', 'colabsthemes' ) . "' class='edit'>";
			$r .= $property_counts[$user_id];
			$r .= '</a>';
		} else {
			$r .= 0;
		}
	}
	
	// get the user registration date	
	if ('registered' == $column_name) {
		$user_info = get_userdata($user_id);
		$r = $user_info->user_registered;
	}

	return $r;
}
//Display the custom column data for each user
add_action( 'manage_users_custom_column', 'colabs_manage_users_custom_column', 10, 3 );

// count the number of property for the user
function colabs_count_custom_post_types( $post_type ) {
	global $wpdb, $wp_list_table;

	$users = array_keys( $wp_list_table->items );
	$userlist = implode( ',', $users );
	$result = $wpdb->get_results( "SELECT post_author, COUNT(*) FROM $wpdb->posts WHERE post_type = '$post_type' AND post_author IN ($userlist) GROUP BY post_author", ARRAY_N );
	foreach ( $result as $row ) {
		$count[ $row[0] ] = $row[1];
	}

	foreach ( $users as $id ) {
		if ( ! isset( $count[ $id ] ) )
			$count[ $id ] = 0;
	}

	return $count;
}

//Add Featured Options for Property
add_action( 'post_submitbox_misc_actions', 'colabs_add_featured_option_to_submitbox' );

function colabs_add_featured_option_to_submitbox(){
  global $post;
  
  if(COLABS_POST_TYPE == $post->post_type):
    ?>
    <style>
      .curtime .expire-date:before {
        content: '\f145';
        top: -1px;
        color: #888;
      }
      .curtime .expire-date:before {
          display: inline-block;
          font: 400 20px/1 dashicons;
          left: -1px;
          padding: 0 2px 0 0;
          position: relative;
          text-decoration: none !important;
          top: 0;
          vertical-align: top;
      }
      .curtime .expire-date {
          display: inline !important;
          height: auto !important;
          padding: 2px 0 1px;  
      }
    </style>
    <?php
    if ($post->post_status<>'publish') :
			$expire_date = __('Post is not yet published','colabsthemes');
		else :
			$post_duration = get_post_meta($post->ID, '_colabs_property_duration', true);
      $publish_date = $post->post_date;
      $expire_date = date(get_option( 'date_format' ),strtotime($publish_date.' +'.$post_duration.' days'));
		endif;
    ?>
    <div class="misc-pub-section curtime misc-pub-curtime">
      <span class="expire-date">
        <?php echo __('Property Expiry date:', 'colabsthemes');?> <b><?php echo $expire_date;?></b>
      </span>
      <span class="screen-reader-text">
        <?php echo __('The date/time the property expires.', 'colabsthemes');?>
      </span>
    </div>
    <div class="misc-pub-section featured-action">
      <input id="sticky" name="sticky" type="checkbox" value="sticky" <?php checked(is_sticky($post->ID)); ?> tabindex="4" /> <label for="sticky" class="selectit"><?php _e('Featured Property', 'colabsthemes') ?></label>
    </div>
    <?php
  endif;
  
  if('page' == $post->post_type):
    ?>
    <div class="misc-pub-section featured-action">
      <input id="sticky" name="sticky" type="checkbox" value="sticky" <?php checked(is_sticky($post->ID)); ?> tabindex="4" /> <label for="sticky" class="selectit"><?php _e('Featured Page', 'colabsthemes') ?></label>
    </div>
    <?php
  endif;
}

function colabs_featured_option_to_quickedit() {
	global $post;
	
	//if post is a custom post type and only during the first execution of the action quick_edit_custom_box
	if ($post->post_type == COLABS_POST_TYPE && did_action('bulk_edit_custom_box') === 1): ?>
	<fieldset class="inline-edit-col-right">
		<div class="inline-edit-col">
			<label class="alignleft">
				<input type="checkbox" name="sticky" value="sticky" />
				<span class="checkbox-title"><?php _e('Featured Property', 'colabsthemes'); ?></span>
			</label>
		</div>	
	</fieldset>
<?php
	endif;
  
  if ($post->post_type == 'page' && did_action('bulk_edit_custom_box') === 1): ?>
	
	<fieldset class="inline-edit-col-right">
		<div class="inline-edit-col">
			<label class="alignleft">
				<input type="checkbox" name="sticky" value="sticky" />
				<span class="checkbox-title"><?php _e('Featured Page', 'colabsthemes'); ?></span>
			</label>
		</div>	
	</fieldset>
<?php
	endif;
}
//Add the sticky option to the quick edit area
add_action('bulk_edit_custom_box', 'colabs_featured_option_to_quickedit');
?>