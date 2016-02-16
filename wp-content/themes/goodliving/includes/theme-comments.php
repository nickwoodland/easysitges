<?php
/*-----------------------------------------------------------------------------------*/
/* CoLabs - List Comment */
/*-----------------------------------------------------------------------------------*/
function colabs_list_comments($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	$GLOBALS['comment_depth'] = $depth;
?>

	<li <?php comment_class(); ?>>
		<div id="comment-<?php comment_ID(); ?>" class="comment-entry">
			<?php if ( '0' == $comment->comment_approved ) : ?>
				<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'colabsthemes' ); ?></em>
			<?php endif; ?>

			<div class="comment-meta">
				<div class="comment-author-avatar">
				<?php commenter_link(); ?>
				</div>
				<div class="comment-author-meta">
					<div class="comment-author-name"><?php echo get_comment_author_link(); ?></div>
					<div class="comment-date"><strong><?php _e('on','colabsthemes');?></strong> <?php printf( __( '%1$s', 'colabsthemes' ), get_comment_date() ); ?></div>
				</div>
			</div>
			<div class="comment-content">
				<?php comment_text(); ?>
			</div>
			<?php comment_reply_link( array_merge( $args, array(
					'reply_text' => __( 'Reply', 'colabsthemes' ),
					'depth' => $depth,
					'max_depth' => $args['max_depth']
				) ) ); ?><br />
		</div>
  
<?php }


// Produces an avatar image with the hCard-compliant photo class
function commenter_link() {
 
 $avatar_email = get_comment_author_email();
 $avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( $avatar_email, 47 ) );
 echo $avatar;
} // end commenter_link

// Custom callback to list pings
function custom_pings($comment, $args, $depth) {
       $GLOBALS['comment'] = $comment;
        ?>
      <li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
       <div class="comment-author"><?php printf(__('By %1$s on %2$s at %3$s', 'colabsthemes'),
         get_comment_author_link(),
         get_comment_date(),
         get_comment_time() );
         edit_comment_link(__('Edit', 'colabsthemes'), ' <span class="meta-sep">|</span> <span class="edit-link">', '</span>'); ?></div>
    <?php if ('0' == $comment->comment_approved) _e('\t\t\t\t\t<span class="unapproved">Your trackback is awaiting moderation.</span>\n', 'colabsthemes'); ?>
            <div class="comment-content">
       <?php comment_text(); ?>
   </div>
<?php } // end custom_pings

?>