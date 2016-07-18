<?php
/******************\

add box for viewing agent details

\*******************/

add_action( 'add_meta_boxes', 'view_agent_details' );

function view_agent_details($post) {

  $id = 'view_agent';
  $title = 'Agent Details';
  $callback = 'view_agent_contents';
  $post_type = 'property';
  $context = 'side';
  $priority = 'low';
  $callback_args = null;

  add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args);

}

function view_agent_contents($post) {

  $agent_id = get_post_meta($post->ID, 'property_agent', true);

  if ($agent_id) {

    $agent = get_post($agent_id);
    $name = $agent->post_title;
    $tel = get_post_meta($agent->ID, 'colabs_number_agent', true);
    $address = get_post_meta($agent->ID, 'colabs_address_agent', true);
    $email = get_post_meta($agent->ID, 'colabs_email_agent', true);

    // print_r($agent);

    $link = 'post.php?post='.$agent->ID.'&action=edit';

    $view_agent_contents = '<div class="inside">';
    $view_agent_contents .= '<a href="'.$link.'">'.$name.'</a><br />';
    $view_agent_contents .= 'Tel: '.$tel.'<br />';
    $view_agent_contents .= 'Email: '.$email.'<br />';
    $view_agent_contents .= 'Address: '.$address.'<br />';
    $view_agent_contents .= '</div>';
    echo $view_agent_contents;
  }
  else {
    echo "No Agent Set";
  }

}
