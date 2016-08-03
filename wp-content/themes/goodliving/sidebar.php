<?php
/**
 * Sidebar Main Template
 */


$layout = get_option('colabs_layout');
if(get_post_meta($post->ID,'layout',true))$layout = get_post_meta($post->ID,'layout',true);
if( 'colabs-one-col' != $layout ):
?>
	<div class="property-sidebar sidebar column col3">
		<?php if(is_singular('property')){
		$id_property=$post->ID;
		$emailsent=get_permalink($id_property);
		$agent_id = get_post_meta($id_property, "property_agent", true);
		$current_user = wp_get_current_user();
		?>
		<aside class="widget widget_property_bookmark" data-property-id="<?php echo $id_property;?>" data-user-id="<?php echo $current_user->ID;?>">
      <ul class="property-bookmark">
        <li class="property-price">
          <strong>
						<?php 
						if('true'==get_post_meta($post->ID,'colabs_property_sold',true)){
							_e('Sold','colabsthemes');
						}else{
							if( $property_price = get_post_meta($id_property, 'property_price',true) ) {
								echo get_option('colabs_currency_symbol').' '.number_format( $property_price );
							}
						}
						?>
					</strong>
        </li>
        <li>
					<a class="bookmarkadded" href="javascript:void(0);" onClick="call_ajax_add_to_bookmark(bookmark_ajax_web_url);">+ <?php _e('Bookmark This Listing','colabsthemes');?></a>
					<div class="bookmarkaddedbrowse" style="display:none;">
							<div class="messagebookmark"></div><a href="<?php echo get_permalink(get_option('colabs_bookmark_property'));?>"><?php _e('Browse Bookmark','colabsthemes');?></a>
					</div>
				</li>
        <li><a href="#emailthis">+ <?php _e('Email To Friends','colabsthemes');?></a></li>
      </ul>
    </aside>
    
    <aside class="widget widget_property_author">
      <h4 class="widget-title"><?php _e('Listed By','colabsthemes');?></h4>
      <p><?php _e("To arrange a viewing or request more details about this property, contact","colabsthemes"); ?> :</p>
      <div class="property-author">
        <div class="property-author-image">
				<?php if($agent_id!='self'){?>
				<?php colabs_image('width=65&height=65&class=avatar&id='.$agent_id);?>
				<?php }else{?>
				<?php echo get_avatar( get_the_author_meta('user_email',$post->post_author), 65 ); ?>
				<?php }?>
				</div>
        <div class="property-author-name">
          <?php if($agent_id!='self'){?>
					<a href="<?php echo get_permalink($agent_id);?>"><?php echo get_the_title($agent_id);?></a>
					<span><?php echo get_post_meta($agent_id,'colabs_address_agent',true);?></span>
					<?php }else{?>
					<?php the_author_posts_link($post->post_author); ?>
					<span><?php echo get_the_author_meta('address',$post->post_author);?></span>
					<?php }?>        
        </div>
      </div>
      <ul class="property-author-info">
        <?php if( (get_post_meta($agent_id,'colabs_number_agent',true)) ) { ?>
					<li><i class="icon-phone"></i>
					<?php if($agent_id!='self'){?>
					<?php echo trim(get_post_meta($agent_id,'colabs_number_agent',true));?>
					<?php }else{?>
					<?php echo trim(get_the_author_meta('phone',$post->post_author));?>
					<?php }?>
					</li>
				<?php }?>
        <li><i class="icon-envelope"></i> <a href="#contactagent"><?php _e('Send Email','colabsthemes');?></a></li>
				<?php if($agent_id=='self'){?>
        <li><i class="icon-list"></i> <a href="<?php echo get_author_posts_url( $post->post_author ); ?>"><?php _e('More listing (s)','colabsthemes');?></a></li>
				<?php }else{?>
				<li><i class="icon-list"></i> <a href="<?php echo get_permalink(get_option('colabs_agent_page')).'?agent_id='.$agent_id; ?>"><?php _e('More listing (s)','colabsthemes');?></a></li>
				<?php }?>
      </ul>
    </aside>
		<?php }?>
		
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar') ) :  ?>
			<aside class="widget">
				<h4 class="widget-title"><?php _e('Categories','colabsthemes');?></h4>
				<ul>
					<?php wp_list_categories('title_li=&hierarchical=false');?>            
				</ul>
			</aside><!-- .widget -->
		<?php endif; ?>
	</div>
<!-- .sidebar -->
<?php endif;?>
<?php if(is_singular('property')){?>
<?php 

/* //If the form is submitted */

if(isset($_POST['submitted'])) {
	/* //Check to see if the honeypot captcha field was filled in */
	if(trim($_POST['checking']) !== '') {
		$captchaError = true;
	} else {	
		/* //Check to make sure that the name field is not empty */
		if(trim($_POST['contactName']) === '') {
			$nameError = 'You forgot to enter your name.';
			$hasError = true;
		} else {
			$name = trim($_POST['contactName']);
		}		
		/* //Check to make sure sure that a valid email address is submitted */
		if(trim($_POST['email']) === '')  {
			$emailError = 'You forgot to enter your email address.';
			$hasError = true;
		} else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email']))) {
			$emailError = 'You entered an invalid email address.';
			$hasError = true;
		} else {
			$email = trim($_POST['email']);
		}

		/* //Check to make sure that the subject field is not empty */
		if(trim($_POST['subjectmail']) === '') {
			$subjectError = 'You forgot to enter your subject.';
			$hasError = true;
		} else {
			$subjectmail = trim($_POST['subjectmail']);
			
		}

		/* //Check to make sure comments were entered	 */
		if(trim($_POST['comments']) === '') {
			$commentError = 'You forgot to enter your comments.';
			$hasError = true;
		} else {
			if(function_exists('stripslashes')) {
				$comments = stripslashes(trim($_POST['comments']));
			} else {
				$comments = trim($_POST['comments']);
			}
		}			

		/* //If there is no error, send the email */
		if(!isset($hasError)) {
			if($agent_id!='self'){
			$emailTo = get_post_meta($agent_id, 'colabs_email_agent',true);
			}else{
			$emailTo = get_the_author_meta('user_email',$post->post_author);
			}
			$message = "Hello,\n\n";
			if($subjectmail==0){
			$subject = "Request more information";
			$message .= "I want to request more information about the property ".get_the_title($id_property)."\n\n";
			}else{
			$subject = "Arrange a viewing";
			$message .= "I want to arrange a viewing on the property ".get_the_title($id_property)."\n\n";
			}
			$blogname=get_option('blogname');
			$message .= $comments;
			$sendcopy = trim($_POST['sendcopy']);
			$body = "Name: $name \n\nEmail: $email \n\nMessages: $message";
			$headers = 'From: '.$blogname.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;			
			mail($emailTo, $subject, $body, $headers);

			if($sendcopy == true) {	
				$headers2 = 'From: '.$blogname.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;	
				mail($email, $subject, $body, $headers2);
			}

			$emailSent = true;

		}
	}
} ?>

	<div class="popup-modal">
		<form class="popup-modal-inner" action="<?php echo $emailsent; ?>" id="contactagent" method="post">
					  <ul class="forms">
						<li>
						  <label for="contactName"><?php _e("Name","colabsthemes"); ?></label>
						  <input type="text" name="contactName" id="contactName" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" class="requiredField" />
						  <?php if($nameError != '') { ?>
						  <span class="error"><?php echo $nameError;?></span>
						  <?php } ?>
						</li>
						<li>
						  <label for="email"><?php _e("Email","colabsthemes"); ?></label>
						  <input type="text" name="email" id="email" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" class="requiredField email" />
						  <?php if($emailError != '') { ?>
						  <span class="error"><?php echo $emailError;?></span>
						  <?php } ?>
						</li>
						<li>
							<label for="subjectmail"><?php _e("I want to","colabsthemes"); ?></label>
							<select name="subjectmail" id="subjectmail">
							<option value="0"><?php _e("Request more information","colabsthemes"); ?></option>
							<option value="1"><?php _e("Arrange a viewing","colabsthemes"); ?></option>
							</select>
						</li>
						
						<li class="textarea">
						  <label for="commentsText"><?php _e("Message","colabsthemes"); ?></label>
						  <textarea name="comments" id="commentsText" class="requiredField"><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
						  <?php if($commentError != '') { ?>
						  <span class="error"><?php echo $commentError;?></span>
						  <?php } ?>
						</li>
						<li class="inline">
						  <input type="checkbox" name="sendcopy" id="sendcopy" value="true"<?php if(isset($_POST['sendcopy']) && $_POST['sendcopy'] == true) echo ' checked="checked"'; ?> />
						  <label for="sendcopy"><?php _e("Send a copy of this email to yourself","colabsthemes"); ?></label>
						</li>
						<li class="screenReader">
						  <label for="checking" class="screenReader"><?php _e("If you want to submit this form, do not enter anything in this field","colabsthemes"); ?></label>
						  <input type="text" name="checking" id="checking" class="screenReader" value="<?php if(isset($_POST['checking']))  echo $_POST['checking'];?>" />
						</li>
						<li class="buttons">
						  <input type="hidden" name="submitted" id="submitted" value="true" />
						  <button type="submit" class="button button-bold button-upper"><?php _e("Email Us","colabsthemes"); ?></button>
						</li>
					  </ul>
		</form>
	</div>

<?php
	$subjectproperty=get_the_title($id_property);
	$messageproperty=get_permalink($id_property);
	$emailthissent=get_permalink($id_property);
	?>
	<?php 

/* //If the form is submitted */

if(isset($_POST['emailthissubmitted'])) {
	/* //Check to see if the honeypot captcha field was filled in */
	if(trim($_POST['emailthischecking']) !== '') {
		$emailthiscaptchaError = true;
	} else {	
		/* //Check to make sure that the name field is not empty */
		if(trim($_POST['emailthiscontactName']) === '') {
			$emailthisnameError = 'You forgot to enter your name.';
			$emailthishasError = true;
		} else {
			$emailthisname = trim($_POST['emailthiscontactName']);
		}		
		/* //Check to make sure sure that a valid email address is submitted */
		if(trim($_POST['emailthisemail']) === '')  {
			$emailthisemailError = 'You forgot to enter your email address.';
			$emailthishasError = true;
		} else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['emailthisemail']))) {
			$emailthisemailError = 'You entered an invalid email address.';
			$emailthishasError = true;
		} else {
			$emailthisemail = trim($_POST['emailthisemail']);
		}
		/* //Check to make sure sure that a valid email address is submitted */
		if(trim($_POST['emailthisemailto']) === '')  {
			$emailthisemailtoError = 'You forgot to enter your email address.';
			$emailthishastoError = true;
		} else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['emailthisemailto']))) {
			$emailthisemailtoError = 'You entered an invalid email address.';
			$emailthishastoError = true;
		} else {
			$emailthisemailto = trim($_POST['emailthisemailto']);
		}
		/* //Check to make sure that the subject field is not empty */
		if(trim($_POST['emailthissubjectmail']) === '') {
			$emailthissubjectError = 'You forgot to enter your subject.';
			$emailthishasError = true;
		} else {
			$emailthissubjectmail = trim($_POST['emailthissubjectmail']);
		}

		/* //Check to make sure comments were entered	 */
		if(trim($_POST['emailthiscomments']) === '') {
			$emailthiscommentError = 'You forgot to enter your comments.';
			$emailthishasError = true;
		} else {
			if(function_exists('stripslashes')) {
				$emailthiscomments = stripslashes(trim($_POST['emailthiscomments']));
			} else {
				$emailthiscomments = trim($_POST['emailthiscomments']);
			}
		}			

		/* //If there is no error, send the email */
		if(!isset($emailthishasError)) {
			$emailthisemailto = $emailthisemailto;
			$emailthissubject = $emailthissubjectmail;
			$emailthissendCopy = trim($_POST['emailthissendCopy']);
			$emailthisbody = "Name: $emailthisname \n\nEmail: $emailthisemail \n\nMessages: $emailthiscomments";
			$emailthisheaders = 'From: My Site <'.$emailthisemailto.'>' . "\r\n" . 'Reply-To: ' . $emailthisemail;			
			mail($emailthisemailto, $emailthissubject, $emailthisbody, $emailthisheaders);

			if($emailthissendCopy == true) {
				$emailthissubject = $emailthissubjectmail;
				$emailthisheaders = 'From: <'.get_option('blogname').'>'. "\r\n" . 'Reply-To: ' . $emailthisemailto;	
				mail($emailthisemail, $emailthissubject, $emailthisbody, $emailthisheaders);
			}

			$emailthissent = true;

		}
	}
} ?>	
	<div class="popup-modal">
		<form class="popup-modal-inner" action="<?php echo $emailthissent; ?>" id="emailthis" method="post">
					  <ul class="forms">
						<li>
						  <label for="emailthisemailto"><?php _e("Email To","colabsthemes"); ?></label>
						  <input type="text" name="emailthisemailto" id="emailthisemailto" value="<?php if(isset($_POST['emailthisemailto']))  echo $_POST['emailthisemailto'];?>" class="requiredField email" />
						  <?php if($emailthisemailtoError != '') { ?>
						  <span class="error"><?php echo $emailthisemailtoError;?></span>
						  <?php } ?>
						</li>
						<li>
						  <label for="emailthiscontactName"><?php _e("Name","colabsthemes"); ?></label>
						  <input type="text" name="emailthiscontactName" id="emailthiscontactName" value="<?php if(isset($_POST['emailthiscontactName'])) echo $_POST['emailthiscontactName'];?>" class="requiredField" />
						  <?php if($emailthisnameError != '') { ?>
						  <span class="error"><?php echo $emailthisnameError;?></span>
						  <?php } ?>
						</li>
						<li>
						  <label for="emailthisemail"><?php _e("Email","colabsthemes"); ?></label>
						  <input type="text" name="emailthisemail" id="emailthisemail" value="<?php if(isset($_POST['emailthisemail']))  echo $_POST['emailthisemail'];?>" class="requiredField email" />
						  <?php if($emailthisemailError != '') { ?>
						  <span class="error"><?php echo $emailthisemailError;?></span>
						  <?php } ?>
						</li>
						<li>
						  <label for="emailthissubjectmail"><?php _e("Subject","colabsthemes"); ?></label>
						  <input type="text" name="emailthissubjectmail" id="emailthissubjectmail" value="<?php echo $subjectproperty;?>" class="requiredField" />
						  <?php if($emailthissubjectError != '') { ?>
						  <span class="error"><?php echo $emailthissubjectmail;?></span>
						  <?php } ?>
						</li>
						<li class="textarea">
						  <label for="emailthiscommentsText"><?php _e("Message","colabsthemes"); ?></label>
						  <textarea name="emailthiscomments" id="emailthiscommentsText" class="requiredField" rows="4"><?php  echo $messageproperty;?></textarea>
						  <?php if($emailthiscommentError != '') { ?>
						  <span class="error"><?php echo $emailthiscommentError;?></span>
						  <?php } ?>
						</li>
						<li class="inline">
						  <input type="checkbox" name="emailthissendCopy" id="emailthissendCopy" value="true"<?php if(isset($_POST['emailthissendCopy']) && $_POST['emailthissendCopy'] == true) echo ' checked="checked"'; ?> />
						  <label for="emailthissendCopy"><?php _e("Send a copy of this email to yourself","colabsthemes"); ?></label>
						</li>
						<li class="screenReader">
						  <label for="emailthischecking" class="screenReader"><?php _e("If you want to submit this form, do not enter anything in this field","colabsthemes"); ?></label>
						  <input type="text" name="emailthischecking" id="emailthischecking" class="screenReader" value="<?php if(isset($_POST['emailthischecking']))  echo $_POST['emailthischecking'];?>" />
						</li>
						<li class="buttons">
						  <input type="hidden" name="emailthissubmitted" id="emailthissubmitted" value="true" />
						  <button type="submit" class="button button-upper button-bold"><?php _e("Email Us","colabsthemes"); ?></button>
						</li>
					  </ul>
		</form>
	</div>	
<?php }?>
