<?php
/**
 * Sidebar Main Template
 */
$layout = get_option('colabs_layout');
if(get_post_meta($post->ID,'layout',true))$layout = get_post_meta($post->ID,'layout',true);
if( 'colabs-one-col' != $layout ): ?>

	<?php if(is_singular('property')) : ?>

		<?php $property_ref = get_post_meta($post->ID, "property_unique_key", true); ?>
		<?php $prop_term_object = false; ?>
		<?php $prop_term_slug = false; ?>
		<?php $prop_term_array = wp_get_post_terms($post->ID, 'property_type'); ?>
		<?php if(!is_wp_error($prop_term_array)): ?>
			<?php $prop_term_object = $prop_term_array[0]; ?>
			<?php $prop_term_slug = $prop_term_object->slug; ?>
			<?php $prop_term_name = $prop_term_object->name; ?>
		<?php endif; ?>

		<?php $enquiry_page_id = get_page_by_path('enquiry')->ID; ?>
		<?php $enquiry_page_link = get_permalink($enquiry_page_id); ?>
		<?php $enquiry_page_q_vars = add_query_arg(
		    array(
		        'prop_ref' => $property_ref,
		        'prop_type' => $prop_term_name,
		    ), $enquiry_page_link
		); ?>

		<?php
		$id_property=$post->ID;
		$emailsent=get_permalink($id_property);
		$agent_id = get_post_meta($id_property, "property_agent", true);
		$current_user = wp_get_current_user();
		?>

		<div class="property-sidebar sidebar column col3">
			<aside class="widget widget_property_bookmark" data-property-id="<?php echo $id_property;?>" data-user-id="<?php echo $current_user->ID;?>">
		      	<ul class="property-bookmark">
					<li class="property-id">
						Property Reference: <?php echo get_post_meta($post->ID, "property_unique_key", true); ?>
					</li>
			        <li class="property-price <?php echo($prop_term_slug == 'holiday-rental' ? 'property-price--holiday' : ''); ?>">
			          <strong>
						  	<?php if($prop_term_slug == 'holiday-rental'): ?>
								Daily Cost
								<?php if( $property_price_day_low = get_post_meta($post->ID, 'property_price_day_low',true)): ?>
									<p>Low: <?php echo get_option('colabs_currency_symbol').' '.number_format( $property_price_day_low ); ?></p>
								<?php endif; ?>
								<?php if( $property_price_day_med = get_post_meta($post->ID, 'property_price_day_med',true)): ?>
									<p>Mid: <?php echo get_option('colabs_currency_symbol').' '.number_format( $property_price_day_med ); ?></p>
								<?php endif; ?>
								<?php if( $property_price_day_high = get_post_meta($post->ID, 'property_price_day_high',true)): ?>
									<p>High <?php echo get_option('colabs_currency_symbol').' '.number_format( $property_price_day_high ); ?></p>
								<?php endif; ?>
							<?php else: ?>
								<?php if($prop_term_slug == 'long-term-rental'): ?>
									<strong>Monthly Rental:</strong>
								<?php endif; ?>
								<?php
								if('true'==get_post_meta($post->ID,'colabs_property_sold',true)){
									_e('Sold','colabsthemes');
								}else{
									if( $property_price = get_post_meta($id_property, 'property_price',true) ) {
										echo get_option('colabs_currency_symbol').' '.number_format( $property_price );

									}
								}
								?>
							<?php endif; ?>
						</strong>
			        </li>
			        <li>
						<a class="bookmarkadded" href="javascript:void(0);" onClick="call_ajax_add_to_bookmark(bookmark_ajax_web_url);">+ <?php _e('Bookmark This Listing','colabsthemes');?></a>
						<div class="bookmarkaddedbrowse" style="display:none;">
								<div class="messagebookmark"></div><a href="<?php echo get_permalink(get_option('colabs_bookmark_property'));?>"><?php _e('Browse Bookmark','colabsthemes');?></a>
						</div>
					</li>
			        <li>
						<a href="#emailthis">+ <?php _e('Email To Friends','colabsthemes');?></a>
					</li>
				</ul>
		  </aside>
		  <aside class="widget widget_property_author">
		      <p><?php _e("To arrange a viewing or request more details about this property, contact","colabsthemes"); ?> :</p>
		      <ul class="property-author-info">
				  <?php $phone = of_get_option('contact_telephone'); ?>
				  <?php if($phone && "" != $phone): ?>
						<li>
							<i class="icon-phone"></i>
						    <?php /*echo trim(get_the_author_meta('phone',$post->post_author));?> <?php //replace with theme options phone number */ ?>
							<a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a>
						</li>
					<?php endif; ?>
		        <li>
					<i class="icon-envelope"></i>
					<a href="<?php echo $enquiry_page_q_vars; ?>"><?php _e('Send Enquiry','colabsthemes');?></a>
				</li>
		      </ul>
		  </aside>
	   </div>

   <?php else: /* singular_property() */ ?>
	   <div class="sidebar column col3">
		<?php  if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar') ) :  ?>
			<div class="sidebar column col3">
				<aside class="widget">
					<h4 class="widget-title"><?php _e('Categories','colabsthemes');?></h4>
					<ul>
						<?php wp_list_categories('title_li=&hierarchical=false');?>
					</ul>
				</aside><!-- .widget -->
			</div>
		<?php endif; ?>

	<?php endif; ?>
<!-- .sidebar -->

<?php endif; /*1col layout */?>
<?php if(is_singular('property')){?>
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
