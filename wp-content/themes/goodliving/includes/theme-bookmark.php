<?php
function colabs_bookmark_head() {
	wp_enqueue_script('bookmark-js',get_template_directory_uri().'/includes/js/bookmark.js','jquery','','in_footer');
	echo '<script>	
	var bookmark_ajax_web_url = "'. get_template_directory_uri().'/includes/theme-bookmark-ajax.php";
	</script>';		
}
add_action("wp_head", "colabs_bookmark_head");

function bookmark_is_exist($post_id,$user_id) {
	
		global $wpdb;
		$cnt = 0;
		//if user loggedin
		if(is_user_logged_in()) {
			//prepare sql query to get count		
			$sql ="SELECT COUNT(*) as cnt FROM ". $wpdb->prefix . "colabs_bookmarks WHERE post_id=".$post_id." AND user_id=".$user_id;
			$results = $wpdb->get_results($sql);
			$cnt = $results[0]->cnt;
		}	
		//execute and return the boolean based on its existence
		if($cnt>0)
			return true;
		return false;
		
}

function add_to_bookmark($post_id,$user_id) {
	
	global $wpdb;
			
	// check for existence,  product ID, variation ID, variation data, and other cart item data
	if(bookmark_is_exist( $post_id,$user_id )) {
				return "exists";
	}
			
	//if user logged in add into db, else add to session
	if(is_user_logged_in()) {
				//insert into db
				$sql ="INSERT INTO ". $wpdb->prefix . "colabs_bookmarks VALUES('',".$post_id.",".$user_id.",now())";
				$ret_val = $wpdb->query($sql);
	} 
	if($ret_val) :
		return "true";
	else :
		return "error";
	endif;
}

function remove_bookmark($id) {
	
		global $wpdb;

		//if user logged in add into db, else add to session
		if(is_user_logged_in()) {
			//delete and return boolean based on the operation
			$sql ="DELETE FROM ". $wpdb->prefix . "colabs_bookmarks WHERE ID=".$id;
			if($wpdb->query($sql)) {
				
				return true;
			} else{
				
				return false;
			}
		} else {

			return true;
		}
}
?>