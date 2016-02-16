<?php
function custom_property_link( $post_link, $id = 0 ){
    $post = get_post($id);
    if ( is_object( $post ) ){
        $terms = wp_get_object_terms( $post->ID, 'property_status' );
        if( $terms ){
            return str_replace( '%property_status%' , $terms[0]->slug , $post_link );
        }
    }
    return $post_link;
}
add_filter( 'post_type_link', 'custom_property_link', 1, 3 );

/*
UNTESTED just speculative code to build post title from property ID
also check http://www.tcbarrett.com/2011/10/generate-post-name-and-slug-from-meta-data/ 

add_action('save_post', 'set_slug');

function set_slug($post_id){
    $new_slug = get_post_meta($post_id,'custom-slug', true);
    $post_args = array(
        'ID' => $post_id,
        'post_name' => $new_slug,
    );

    wp_update_post($post_args);
} */
