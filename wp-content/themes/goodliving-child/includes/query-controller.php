<?php

// Hooks the main query object before it retrieves posts, and allows us to modify it here instead of in the
// templates themselves.

function site_query_controller($query) {

    //don't want to affect admin pages!
    if(!is_admin()) :



        if($query->is_main_query()) : // don't affect custom queries that we might be running

            //// override the default theme search ordering slighty to order by meta_value_num as the behaves more predictably than the default meta_value
            if(is_search()):

                $tax_query = array(
                    array(
                      'taxonomy' => 'property_status',
                      'field' => 'slug',
                      'terms' => array( 'rented', 'sold' ),
                      'operator'=> 'NOT IN'
                    )
                );

                //print_r($query->query_vars['tax_query']);
                $current_tax_q = $query->query_vars['tax_query'];

                if($current_tax_q) :
                    $tax_query['relation'] = 'AND';
                    $tax_query[] = $current_tax_q;

                endif;

                $query->set('tax_query', $tax_query);

                if(null == $query->query_vars['meta_key'] || $query->$query_vars['meta_key'] == ''):
                    $query->set('orderby', 'meta_value_num');
                    $query->set('meta_key', 'property_price');
                endif;
                if(isset($query->query_vars['meta_key']) && $query->query_vars['meta_key'] == 'property_price'):
                    $query->set('orderby', 'meta_value_num');
                endif;
            endif;


            //Global params, will affect all querys on the site. Set these to fairly ubiquitous values.
            // We use alphabetical ordering for most post types.
/*
            $query->set('posts_per_page','12');
            $query->set('orderby','menu_order title');
            $query->set('order','ASC');
            $query->set('post_status','publish');
*/
            //Further conditionalise based on archive type

            /*if(is_home() || is_category()) : // In the context of WP, 'home' is the main posts archive page

                $query->set('orderby', 'date');
                $query->set('order','DESC');
                $query->set('posts_per_page', 9);

            endif;*/


            // Conditionalise for a CPT archive

            /*if(is_post_type_archive('products') ) :
                //$query->set('posts_per_page','9');
                $query->set('orderby','title');
                $query->set('order','DESC');
            endif;*/
            /*if(is_home()) :
                $query->set('posts_per_page', 2);
            endif; */

            // Conditionalise for a CPT Tax/term archive

            /*if(is_tax('property_type') || is_front_page() || is_home() || is_post_type_archive('property') || is_search()) :

                $tax_query = array(
                     array(
                         'taxonomy' => 'property_status',
                         'field' => 'slug',
                         'terms' => array( 'rented' ),
                         'operator'=> 'NOT IN'
                     )
                 );
                $query->set('posts_per_page','4');
                $query->set('tax_query',$tax_query);
                //$query->set('orderby','menu_order title');
            endif;*/



           // endif; //service archive check

        endif; //main query check

    endif;  //admin check

    return $query;
}
add_action('pre_get_posts', 'site_query_controller',11);
