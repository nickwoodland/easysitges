<div id="comments">
	<?php if ( have_comments() ) : ?>
	
		<div class='form-header'>
		  <h2 id='comments-title'><?php printf( _n( 'One Comment', '%1$s Comments', get_comments_number(), 'colabsthemes' ), number_format_i18n( get_comments_number() ) ); ?></h2>
		</div>

		<ol class="commentlist">
			<?php wp_list_comments( array(
				'type'			=> 'all',
				'callback' 	=> 'colabs_list_comments',
				'max_depth'	=> 4
			) ); ?>
		</ol>
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'colabsthemes' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'colabsthemes' ) ); ?></div>
			</div> <!-- .navigation -->
		<?php endif; // check for comment navigation ?>
	<?php
		/* If there are no comments and comments are closed, let's leave a little note, shall we?
		 * But we don't want the note on pages or post types that do not support comments.
		 */
		elseif ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="nocomments"><?php _e( 'Comments are closed.', 'colabsthemes' ); ?></p>
	<?php endif; ?>

	<?php
	// Custom comment form
	$commenter = wp_get_current_commenter();
	$aria_req = 10;
	$fields = array(
		'author'	=> '<p class="comment-form-author">
                  <label for="author">'. __('Your Name (required)', 'colabsthemes') .'</label>
									<input id="author" name="author" type="text" placeholder="'.__('Your Name','colabsthemes').'" value="' . esc_attr( $commenter['comment_author'] ) . '" size=""' . $aria_req . ' />' . ( $req ? '' : '' ).'</p>',
		'email'		=> '<p class="comment-form-email">
                  <label for="email">'. __('Your Email (required)', 'colabsthemes') .'</label>
									<input id="email" name="email" type="text" placeholder="'.__('Your Email','colabsthemes').'" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size=""' . $aria_req . ' />' . ( $req ? '' : '' ).'</p>',
		'url'			=> '<p class="comment-form-url">
                  <label for="url">'. __('Your Website', 'colabsthemes') .'</label>
									<input id="url" name="url" type="text" placeholder="'.__('Your Website','colabsthemes').'" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="" />
									</p>'
	);

	comment_form(array(
		'comment_field'        => '<p class="comment-form-comment"><label for="comment">'.__('Message', 'colabsthemes').'</label><textarea  id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
		'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
		'comment_notes_before' => '',
		'comment_notes_after' => '',
		'title_reply'          => __( 'Leave a comment','colabsthemes' ),
		'label_submit'         => __( 'Post Comment' ,'colabsthemes'),
		'cancel_reply_link'    => __( 'Cancel' ,'colabsthemes'),
	));
	?>
	
</div><!-- #comments -->
