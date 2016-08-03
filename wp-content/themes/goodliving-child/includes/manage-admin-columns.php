<?php
//
// function my_additional_property_columns( $columns ) {
//   $columns["reference"] = "Reference Code";
//   return $columns;
// }
// // add_filter('manage_posts_columns', 'my_additional_columns');
//
// add_filter('manage_edit-property_columns', 'my_additional_property_columns');
//
// function my_additional_property_column_data( $colname, $cptid ) {
//   if ( $colname == 'reference') {
//     echo get_post_meta( $cptid, 'property_unique_key', true );
//   }
// }
// add_action('manage_property_posts_custom_column', 'my_additional_property_column_data', 10, 2);
//http://justintadlock.com/archives/2011/06/27/custom-columns-for-custom-post-types

add_filter( 'manage_edit-property_sortable_columns', 'my_property_sortable_columns' );
function my_property_sortable_columns( $columns ) {
	$columns['reference'] = 'reference';
	return $columns;
}
/* Only run our customization on the 'edit.php' page in the admin. */
add_action( 'load-edit.php', 'my_edit_property_load' );
function my_edit_property_load() {
	add_filter( 'request', 'my_sort_properties' );
}
// http://easysitges.com/dev/wp-admin/edit.php?s=LVT-&post_status=all&post_type=property&action=-1&m=0&property_type=0&property_location=0&property_features=0&property_status=0&paged=1&action2=-1
/* Sorts the property. */
function my_sort_properties( $vars ) {

	/* Check if we're viewing the 'property' post type. */
	if ( isset( $vars['post_type'] ) && 'property' == $vars['post_type'] ) {

		/* Check if 'orderby' is set to 'reference'. */
		// if ( isset( $vars['orderby'] ) && 'reference' == $vars['orderby'] && !isset($vars['s'])) {
		if ( isset( $vars['orderby'] ) && 'reference' == $vars['orderby'] ) {

          /* Merge the query vars with our custom variables. */
          $vars = array_merge(
            $vars,
            array(
              'meta_key' => "property_unique_key",
              'orderby' => 'meta_value'
            )
          );

		}
    }
  // print_r($vars);
	return $vars;
}
