<?php
require_once( "../../../../wp-load.php" );

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
if(isset($_POST['post_id'])){
	$ret_val = add_to_bookmark($_POST['post_id'],$_POST['user_id']);

	if($ret_val=="true") {
		echo __("Successfully added ","colabsthemes");
	}elseif($ret_val=="exists") {
		echo __("Already Exists ","colabsthemes");
	}
}
$success=false;
if(isset($_POST['bookmark_id'])){
	$bookmark_val = remove_bookmark($_POST['bookmark_id']);
	if($bookmark_val==true) {
		$success=true;
	}
}
?>