</div>
</div>
<!-- .main-content-wrapper -->

<footer class="footer-section container">
<div class="row">
    <?php /*if('true'==get_option('colabs_subscribe_form')):?>
<div class="newsletter-subscribe">
      <h4><?php echo get_option('colabs_subscribe_title');?></h4>
      <form action="<?php echo get_option('colabs_subscribe_action');?>" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" novalidate>
        <input type="email" name="EMAIL" id="mce-EMAIL" placeholder="<?php _e('Sign up newsletter','colabsthemes');?>" required>
        <input class="button button-bold" name="subscribe" type="submit" value="<?php echo get_option('colabs_subscribe_button');?>">
      </form>
      <p><?php echo get_option('colabs_subscribe_desc');?></p>
</div><!-- .newsletter-subscribe -->
<?php endif; */?>
<?php gravity_form( 4, $display_title = true, $display_description = true, $display_inactive = false, $field_values = null, $ajax = false, $tabindex, $echo = true ); ?>
<?php get_sidebar( 'footer' );?><!-- .footer-widgets -->
</div>
</footer>

<div class="copyrights container">
        <div class="row">
        <?php // colabs_credit();?>
        <?php include(locate_template('parts/footer-contact.php')); ?>
        </div>
    </div>
</div>

<div id="top-slide-menu">
<div class="mm-inner">
<div class="my-account-wrapper clearfix">
  <?php if( is_user_logged_in() ) : ?>
    <div class="mobile-user-photo">
      <?php $current_user_id = '';
      global $current_user;
      if( is_user_logged_in() ) {
        $current_user_id = $current_user->ID;
      }
      echo get_avatar($current_user_id, 70); ?>
    </div>
  <?php endif; ?>

  <div class="mobile-user-data">
    <?php if( !is_user_logged_in() ) : ?>
      <a href="<?php echo wp_login_url(); ?>" class="btn btn-red btn-full-color btn-uppercase btn-bold"><?php _e('Login', 'colabsthemes'); ?></a>
      &nbsp;&nbsp;&nbsp;&nbsp;
      <a href="<?php echo wp_registration_url(); ?>" class="btn btn-red btn-full-color btn-uppercase btn-bold"><?php _e('Register', 'colabsthemes'); ?></a>
    <?php else : ?>
      <strong><?php _e('Hello', 'colabsthemes'); ?>,</strong><br>
      <span class="mobile-user-name">
        <?php $user_meta = get_user_meta($current_user->ID);

        if( $user_meta['first_name'][0] ) {
          echo "{$user_meta['first_name'][0]} {$user_meta['last_name'][0]}";
        } else {
          echo $user_meta['nickname'][0];
        } ?>
      </span><br>
      <a class="btn btn-red btn-full-color btn-uppercase btn-bold" href="<?php echo  wp_logout_url(); ?>"><?php _e('Sign Out', 'colabsthemes'); ?></a>
    <?php endif; ?>
  </div>

</div>
<div>
<?php wp_nav_menu( array( 'theme_location' => 'top-menu', 'container' => '', 'menu_class' => 'slide-menu', 'container_class' => 'mobile-nav') ); ?>
</div>
</div>
</div>

<?php wp_footer(); ?>
</body>

</html>
