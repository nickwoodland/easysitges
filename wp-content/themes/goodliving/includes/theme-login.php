<?php
/**
 *
 * This controls how the login, logout,
 * registration, and forgot your password pages look.
 * It overrides the default WP pages by intercepting the request.
 *
 */
class Colabs_Custom_Login {

  /**
   * Constructor
   */
  function __construct() {
    add_action( 'login_init', array( $this, 'init' ) );
  }


  /**
   * Initialization
   */
  function init() {
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'login';
    $errors = new WP_Error();

    if ( isset($_GET['key']) )
      $action = 'resetpass';

    // validate action so as to default to the login screen
    if ( !in_array( $action, array( 'postpass', 'logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'register', 'login' ), true ) && false === has_filter( 'login_form_' . $action ) )
      $action = 'login';

    // Only change login, register page
    if( in_array( $action , array( 'login', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'register' ) ) ) {
      add_action( 'colabs_title', array( $this, 'colabs_login_title' ) );
      add_action( 'wp_title', array( $this, 'colabs_login_title' ) );
      add_action( 'wp_head', array( $this, 'custom_login_header' ) );
      add_action( 'wp_head', '_custom_background_cb' );

      ob_start();

      get_header();
      $this->container_open( $action );

        // Check action
        switch ($action) {

          case 'lostpassword':
          case 'retrievepassword':
            $this->action_lostpassword();
          break;

          case 'resetpass':
          case 'rp':
            $this->action_reset_pass();
          break;

          case 'register':
            $this->action_register();
          break;

          case 'login' :
          default:
            $this->action_login();
          break;

        }
        

      $this->container_close();
      get_footer();

      $output = ob_get_contents();
      ob_end_clean();

      echo $output;
      exit;
    }    
  }


  /**
   * Hook into header
   */
  function custom_login_header() {
    /**
     * Enqueue scripts and styles for the login page.
     *
     * @since 3.1.0
     */
    do_action( 'login_enqueue_scripts' );

    /**
     * Fires in the login page header after scripts are enqueued.
     *
     * @since 2.1.0
     */
    do_action( 'login_head' );
  }


  /**
   * Page Title
   */
  function colabs_login_title( $title ) {
    if (isset($_GET['action'])) $action = $_GET['action']; else $action='';

    switch($action) {
      case 'lostpassword':
        $title = __('Retrieve your lost password? ','colabsthemes');
        break;

      case 'login':
      default:
        $title = __('Sign In/Register','colabsthemes');
        break;
    }

    return $title;
  }


  /**
   * Container Open
   * @param String $action WP login action
   */
  function container_open( $action ) {}


  /**
   * Container Close
   */
  function container_close() {}


  /**
   * Login hooks
   */
  function action_login() {
    $interim_login = isset($_REQUEST['interim-login']);
    $secure_cookie = '';
    $customize_login = isset( $_REQUEST['customize-login'] );
    if ( $customize_login )
      wp_enqueue_script( 'customize-base' );

    // If the user wants ssl but the session is not ssl, force a secure cookie.
    if ( !empty($_POST['log']) && !force_ssl_admin() ) {
      $user_name = sanitize_user($_POST['log']);
      if ( $user = get_user_by('login', $user_name) ) {
        if ( get_user_option('use_ssl', $user->ID) ) {
          $secure_cookie = true;
          force_ssl_admin(true);
        }
      }
    }

    if ( isset( $_REQUEST['redirect_to'] ) ) {
      $redirect_to = $_REQUEST['redirect_to'];
      // Redirect to https if user wants ssl
      if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
        $redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
    } else {
      $redirect_to = admin_url();
    }

    $reauth = empty($_REQUEST['reauth']) ? false : true;

    $user = wp_signon( '', $secure_cookie );

    if ( empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
      if ( headers_sent() ) {
        $user = new WP_Error( 'test_cookie', sprintf( __( '<strong>ERROR</strong>: Cookies are blocked due to unexpected output. For help, please see <a href="%1$s">this documentation</a> or try the <a href="%2$s">support forums</a>.', 'colabsthemes' ),
          'http://codex.wordpress.org/Cookies', 'https://wordpress.org/support/' ) );
      } elseif ( isset( $_POST['testcookie'] ) && empty( $_COOKIE[ TEST_COOKIE ] ) ) {
        // If cookies are disabled we can't log in even with a valid user+pass
        $user = new WP_Error( 'test_cookie', sprintf( __( '<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href="%s">enable cookies</a> to use WordPress.', 'colabsthemes' ),
          'http://codex.wordpress.org/Cookies' ) );
      }
    }

    $requested_redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';

    /**
     * Filter the login redirect URL.
     *
     * @since 3.0.0
     *
     * @param string           $redirect_to           The redirect destination URL.
     * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
     * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
     */
    $redirect_to = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );
    
    if ( !is_wp_error($user) && !$reauth ) {
      if( $interim_login ) {
        $message = '<div class="alert alert-success">' . __('You have logged in successfully.', 'colabsthemes') . '</div>';
        $interim_login = 'success';
        echo $message;
      }

      if ( ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) ) {
        // If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
        if ( is_multisite() && !get_active_blog_for_user($user->ID) && !is_super_admin( $user->ID ) )
          $redirect_to = user_admin_url();
        elseif ( is_multisite() && !$user->has_cap('read') )
          $redirect_to = get_dashboard_url( $user->ID );
        elseif ( !$user->has_cap('edit_posts') )
          $redirect_to = admin_url('profile.php');
      }

      wp_safe_redirect($redirect_to);
      exit();
    }

    $errors = $user;

    // Clear errors if loggedout is set.
    if ( !empty($_GET['loggedout']) || $reauth )
      $errors = new WP_Error();

    if ( $interim_login ) {
      if ( ! $errors->get_error_code() )
        $errors->add('expired', __('Session expired. Please log in again. You will not move away from this page.', 'colabsthemes'), 'message');
    } else {
      // Some parts of this script use the main login form to display a message
      if    ( isset($_GET['loggedout']) && true == $_GET['loggedout'] )
        $errors->add('loggedout', __('You are now logged out.', 'colabsthemes'), 'message');
      elseif  ( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )
        $errors->add('registerdisabled', __('User registration is currently not allowed.', 'colabsthemes'));
      elseif  ( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )
        $errors->add('confirm', __('Check your e-mail for the confirmation link.', 'colabsthemes'), 'message');
      elseif  ( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )
        $errors->add('newpass', __('Check your e-mail for your new password.', 'colabsthemes'), 'message');
      elseif  ( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )
        $errors->add('registered', __('Registration complete. Please check your e-mail.', 'colabsthemes'), 'message');
      elseif ( strpos( $redirect_to, 'about.php?updated' ) )
        $errors->add('updated', __( '<strong>You have successfully updated WordPress!</strong> Please log back in to see what&#8217;s new.', 'colabsthemes' ), 'message' );
    }

    /**
     * Filter the login page errors.
     *
     * @since 3.6.0
     *
     * @param object $errors      WP Error object.
     * @param string $redirect_to Redirect destination URL.
     */
    $errors = apply_filters( 'wp_login_errors', $errors, $redirect_to );

    // Clear any stale cookies.
    if ( $reauth )
      wp_clear_auth_cookie();
    
    // Error Messages
    $this->render_messages( $errors );

    $this->login_form( $interim_login, $redirect_to, $errors );
  }


  /**
   * Lost Password hooks
   */
  function action_lostpassword() {
    $errors = new WP_Error();
    $http_post = ('POST' == $_SERVER['REQUEST_METHOD']);

    if ( $http_post ) {
      $errors = retrieve_password();
      if ( !is_wp_error($errors) ) {
        $redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : 'wp-login.php?checkemail=confirm';
        wp_safe_redirect( $redirect_to );
        exit();
      }
    }

    if ( isset( $_GET['error'] ) ) {
      if ( 'invalidkey' == $_GET['error'] )
        $errors->add( 'invalidkey', __( 'Sorry, that key does not appear to be valid.', 'colabsthemes' ) );
      elseif ( 'expiredkey' == $_GET['error'] )
        $errors->add( 'expiredkey', __( 'Sorry, that key has expired. Please try again.', 'colabsthemes' ) );
    }

    $lostpassword_redirect = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';

    /**
     * Filter the URL redirected to after submitting the lostpassword/retrievepassword form.
     *
     * @since 3.0.0
     *
     * @param string $lostpassword_redirect The redirect destination URL.
     */
    $redirect_to = apply_filters( 'lostpassword_redirect', $lostpassword_redirect );

    /**
     * Fires before the lost password form.
     *
     * @since 1.5.1
     */
    do_action( 'lost_password' );

    $user_login = isset($_POST['user_login']) ? wp_unslash($_POST['user_login']) : '';

    // Error Messages
    $this->render_messages( $errors );

    $this->forgot_password_form( $redirect_to );
  }


  /**
   * Reset Password hooks
   */
  function action_reset_pass() {
    list( $rp_path ) = explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) );
    $rp_cookie = 'wp-resetpass-' . COOKIEHASH;
    if ( isset( $_GET['key'] ) ) {
      $value = sprintf( '%s:%s', wp_unslash( $_GET['login'] ), wp_unslash( $_GET['key'] ) );
      setcookie( $rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
      wp_safe_redirect( remove_query_arg( array( 'key', 'login' ) ) );
      exit;
    }

    if ( isset( $_COOKIE[ $rp_cookie ] ) && 0 < strpos( $_COOKIE[ $rp_cookie ], ':' ) ) {
      list( $rp_login, $rp_key ) = explode( ':', wp_unslash( $_COOKIE[ $rp_cookie ] ), 2 );
      $user = check_password_reset_key( $rp_key, $rp_login );
    } else {
      $user = false;
    }

    if ( ! $user || is_wp_error( $user ) ) {
      setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
      if ( $user && $user->get_error_code() === 'expired_key' )
        wp_redirect( site_url( 'wp-login.php?action=lostpassword&error=expiredkey' ) );
      else
        wp_redirect( site_url( 'wp-login.php?action=lostpassword&error=invalidkey' ) );
      exit;
    }

    $errors = new WP_Error();

    if ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] )
      $errors->add( 'password_reset_mismatch', __( 'The passwords do not match.', 'colabsthemes' ) );

    /**
     * Fires before the password reset procedure is validated.
     *
     * @since 3.5.0
     *
     * @param object           $errors WP Error object.
     * @param WP_User|WP_Error $user   WP_User object if the login and reset key match. WP_Error object otherwise.
     */
    do_action( 'validate_password_reset', $errors, $user );

    if ( ( ! $errors->get_error_code() ) && isset( $_POST['pass1'] ) && !empty( $_POST['pass1'] ) ) {
      reset_password($user, $_POST['pass1']);
      setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
      $message = __( 'Your password has been reset.', 'colabsthemes' ) . ' <a href="' . esc_url( wp_login_url() ) . '">' . __( 'Log in', 'colabsthemes' ) . '</a>';
    }

    wp_enqueue_script( 'password-strength-meter' );
    wp_enqueue_script( 'zxcvbn-async' );
    wp_enqueue_script( 'custom-strengthmeter', trailingslashit( get_template_directory_uri() ) . 'includes/js/custom-strengthmeter.js');
    
    if (isset($message) && !empty($message)) {
      $this->render_messages( $message );
    }

    if (isset($errors) && sizeof($errors)>0 && $errors->get_error_code()) {
      $this->render_messages( $errors );
    }

    $this->reset_pass_form( $rp_key );
  }


  /**
   * Register hooks
   */
  function action_register() {
    $http_post = ('POST' == $_SERVER['REQUEST_METHOD']);

    if ( !get_option('users_can_register') ) {
      wp_redirect( site_url('wp-login.php?registration=disabled') );
      exit();
    }

    $user_login = '';
    $user_email = '';
    $first_name = '';
    $last_name = '';

    if ( $http_post ) {
      $user_login = $_POST['user_login'];
      $user_email = $_POST['user_email'];
      $user_pass = $_POST['your_password'];
      $first_name = $_POST['first_name'];
      $last_name = $_POST['last_name'];

      $errors = $this->process_register(  );
    }

    $registration_redirect = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
    /**
     * Filter the registration redirect URL.
     *
     * @since 3.0.0
     *
     * @param string $registration_redirect The redirect destination URL.
     */
    $redirect_to = apply_filters( 'registration_redirect', $registration_redirect );
    
    if (isset($errors) && sizeof($errors)>0 && $errors->get_error_code()) {
      $this->render_messages( $errors );
    }

    $this->register_form( array(
      'user_login' => $user_login, 
      'user_email' => $user_email,
      'first_name' => $first_name,
      'last_name' => $last_name,
      'redirect_to' => $redirect_to
    ) );
  }


  /**
   * Render Messages
   *
   * @param Object $message_data WP_Error Object
   */
  function render_messages( $message_data ) {
    if( is_wp_error( $message_data ) ) {
      if ( $message_data->get_error_code() ) {
        $errors = '';
        $messages = '';
        
        foreach ( $message_data->get_error_codes() as $code ) {
          $severity = $message_data->get_error_data( $code );

          foreach ( $message_data->get_error_messages( $code ) as $error_message ) {
            if ( 'message' == $severity )
              $messages .= '  ' . $error_message . "<br />\n";
            else
              $errors .= '  ' . $error_message . "<br />\n";
          }
        }

        if ( ! empty( $errors ) ) {
          echo '<div class="alert alert-error">'. $errors .'</div>';
        }

        if ( ! empty( $messages ) ) {
          echo '<div class="alert alert-success">'. $messages .'</div>'; 
        }
        
      }
    }

    else {
      if( $message_data ) {
        echo '<div class="alert alert-success">'. $message_data .'</div>'; 
      }
    }
  }


  /**
   * Render Login Form
   */
  function login_form( $interim_login, $redirect_to, $errors ) {}


  /**
   * Forgot Password Form
   */
  function forgot_password_form( $redirect_to ) {}


  /**
   * Reset Password Form
   */
  function reset_pass_form( $rp_key ) {}


  /**
   * Register Form
   */
  function register_form( $options = array() ) {}


  /**
   * Register Form Process
   */
  function process_register() {}


  /**
   * email that gets sent out to new users once they register
   */
  function colabs_sent_email($user_id, $user_pass) {

    $user = new WP_User($user_id);

    $user_login = stripslashes($user->user_login);
    $user_email = stripslashes($user->user_email);

    // variables that can be used by admin to dynamically fill in email content
    // $find = array('/%username%/i', '/%password%/i', '/%blogname%/i', '/%siteurl%/i', '/%loginurl%/i', '/%useremail%/i');
    // $replace = array($user_login, $plaintext_pass, get_option('blogname'), get_option('siteurl'), get_option('siteurl').'/wp-login.php', $user_email);

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    // send the site admin an email everytime a new user registers
    $message  = sprintf(__('New user registration on your site %s:', 'colabsthemes'), $blogname) . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('Username: %s', 'colabsthemes'), $user_login) . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('E-mail: %s', 'colabsthemes'), $user_email) . PHP_EOL;

    @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration', 'colabsthemes'), $blogname), $message);

    // For user
    $message  = sprintf(__('Username: %s', 'colabsthemes'), $user_login) . PHP_EOL;
    
    if ( get_option('colabs_allow_registration_password') != 'true' ) {
      $message .= sprintf(__('Password: %s', 'colabsthemes'), $user_pass) . PHP_EOL;
    }
    $message .= wp_login_url() . PHP_EOL;

    wp_mail($user_email, sprintf(__('[%s] Your username and password', 'colabsthemes'), $blogname), $message);
  }
}



/**
 * Modify the login page by extending Colabs_Custom_Login class
 */
class Goodliving_Colabs_Custom_Login extends Colabs_Custom_Login {

  /**
   * Constructor
   */
  function __construct() {
    parent::__construct();
    add_filter( 'login_redirect', array( $this, 'custom_login_redirect'), 10, 3 );
  }


  /**
   * Filter login redirect
   */
  function custom_login_redirect( $redirect_to, $requested_redirect_to, $user ) {
    if( !is_wp_error( $user ) ) {
      if( empty( $redirect_to ) ) {
        $redirect_to = CL_DASHBOARD_URL;
      }
    }

    return $redirect_to;
  }

  /**
   * Container Open
   */
  function container_open( $action ) {

    switch ($action) {
      case 'lostpassword':
      case 'retrievepassword':
        $title = __('Forgot Your Password?', 'colabsthemes');
        break;

      case 'login':
        $title = __('Sign In or Create An Account', 'colabsthemes');
        break;

      case 'rp':
      case 'resetpass':
        $title = __('Enter your new password', 'colabsthemes');
        break;

      case 'register':
        $title = __('Create An Account', 'colabsthemes');
        break;
    }

    echo '
      <div class="main-content column col9">
        
        <article class="single-entry-post">
          <header class="entry-header">
            <h2 class="entry-title">'. $title .'</h2>
          </header>

          <div class="property-details">
            <div class="property-details-panel entry-content">
    ';
  }


  /**
   * Container close
   */
  function container_close() {
    echo '
          </div><!-- .entry-content -->
        </div><!-- .property-details -->
      </article><!-- .single-entry-post -->

    </div><!-- .main-content -->
    ';

    get_sidebar();
  }


  /**
   * Render Login Form
   */
  function login_form( $interim_login, $redirect_to, $errors ) {
    $user_login = '';
    if( is_wp_error( $errors ) ) {
      if ( isset($_POST['log']) ) {
        $user_login = ( 'incorrect_password' == $errors->get_error_code() || 'empty_password' == $errors->get_error_code() ) ? esc_attr(wp_unslash($_POST['log'])) : '';
      }
    }
    $rememberme = ! empty( $_POST['rememberme'] );
    ?>
    
    <form name="loginform" id="loginform" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post" class="loginform account_form">
      
      <p class="form-row field-username">
        <label id="user_login" for="user_login"><?php _e('Email Address/Username', 'colabsthemes'); ?>*</label>
        <input type="text" class="text" name="log" id="user_login" placeholder="<?php _e( 'Email Address/Username', 'woocommerce' ); ?>" value="<?php echo esc_attr($user_login); ?>" />
      </p>

      <p class="form-row field-password">
        <label id="login_password" for="login_password"><?php _e('Password', 'colabsthemes'); ?>*</label>
        <input type="password" class="text" name="pwd" id="login_password"  placeholder="<?php _e( 'Password', 'woocommerce' ); ?>" value="" />
      </p>

      <?php
      /**
       * Fires following the 'Password' field in the login form.
       *
       * @since 2.1.0
       */
      do_action( 'login_form' );
      ?>

      <p class="form-row forgetmenot rememberme">
        <label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever" <?php checked( $rememberme ); ?> /> <?php esc_attr_e('Remember Me', 'colabsthemes'); ?></label>
      </p>

      <p class="submit">
        <input type="submit" class="submit button button-bold" name="wp-submit" value="<?php esc_attr_e('Sign In', 'colabsthemes'); ?>" />
        <?php if ( $interim_login ) { ?>
          <input type="hidden" name="interim-login" value="1" />
        <?php } else { ?>
          <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
        <?php } ?>
        <input type="hidden" name="testcookie" value="1" />
      </p>

      <p class="lostpass">
        <?php wp_register('',' | '); ?>
        <a class="lostpass pull-right" href="<?php echo site_url('wp-login.php?action=lostpassword', 'login'); ?>" title="<?php esc_attr_e('Forgot your password?', 'colabsthemes'); ?>"><?php _e('Forgot your password?', 'colabsthemes'); ?></a>
      </p>

    </form>    
      

    <?php
  }


  /**
   * Forgot Password Form
   */
  function forgot_password_form( $redirect_to ) {
    ?>
    <p><?php _e('Please enter your email address below. You will receive a link to reset your password.', 'colabsthemes'); ?></p>
    <form name="lostpasswordform" id="lostpasswordform" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post'); ?>" method="post" class="loginform">

      <p class="form-row field-username">
        <label for="user_login"><?php _e('Username or E-mail:', 'colabsthemes'); ?>*</label>
        <input type="text" placeholder="<?php _e( 'Username or Email Address', 'woocommerce' ); ?>" class="text" name="user_login" id="user_login" value="<?php echo esc_attr($user_login); ?>" />
      </p>
    
      <?php
      /**
       * Fires inside the lostpassword <form> tags, before the hidden fields.
       *
       * @since 2.1.0
       */
      do_action( 'lostpassword_form' ); ?>
      <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
      
      <p class="submit form-row">
        <input type="submit" name="wp-submit" id="wp-submit" class="submit button button-bold"  value="<?php esc_attr_e('Get New Password','colabsthemes'); ?>" />
      </p>
    </form>
    <?php
  }


  /**
   * Reset Password Form
   */
  function reset_pass_form( $rp_key ) {
    ?>
      <form name="resetpassform" class="resetpassform loginform" id="resetpassform" action="<?php echo esc_url( site_url( 'wp-login.php?action=resetpass', 'login_post' ) ); ?>" method="post" autocomplete="off">
        <input type="hidden" id="user_login" value="<?php echo esc_attr( $rp_login ); ?>" autocomplete="off" />
        
        <p><?php _e('Please enter your new password below', 'colabsthemes'); ?></p>

        <p class="form-row">
          <label for="pass1"><?php _e('New password', 'colabsthemes') ?></label>
          <input type="password" name="pass1" id="pass1" class="input input-text" size="20" value="" autocomplete="off" placeholder="<?php _e('New password', 'colabsthemes') ?>" />
        </p>

        <p class="form-row">
          <label for="pass2"><?php _e('Confirm new password', 'colabsthemes') ?></label>
          <input type="password" name="pass2" id="pass2" class="input input-text" size="20" value="" autocomplete="off" placeholder="<?php _e('Confirm new password', 'colabsthemes') ?>" />
        </p>

        <div id="pass-strength-result" class="hide-if-no-js password-strength-result"><?php _e('Strength indicator', 'colabsthemes'); ?></div>
        <div class="clear"></div>

        <p class="description indicator-hint"><?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ &amp; ).'); ?></p>

        <br class="clear" />

        <?php
        /**
         * Fires following the 'Strength indicator' meter in the user password reset form.
         *
         * @since 3.9.0
         *
         * @param WP_User $user User object of the user whose password is being reset.
         */
        do_action( 'resetpass_form', $user );
        ?>
        <input type="hidden" name="rp_key" value="<?php echo esc_attr( $rp_key ); ?>" />
        <p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button button-bold" value="<?php esc_attr_e('Reset Password'); ?>" /></p>
      </form>
    <?php
  }


  /**
   * Register Form
   */
  function register_form( $options = array() ) {
    extract( $options );
    ?>
      
      <form name="registerform" id="registerform" action="<?php echo esc_url( site_url('wp-login.php?action=register', 'login_post') ); ?>" method="post" novalidate="novalidate" class="loginform">
        <p class="form-row field-username">
          <label id="user_login" for="user_login"><?php _e('Username', 'colabsthemes'); ?>*</label>
          <input type="text" class="text input-text" name="user_login" id="user_login"  value="<?php echo esc_attr(wp_unslash($user_login)); ?>" placeholder="<?php _e('Username', 'colabsthemes'); ?>" />
        </p>

        <p class="form-row field-email">
          <label id="user_email" for="user_email" ><?php _e('Email Address', 'colabsthemes'); ?>*</label>
          <input type="text" class="text input-text" name="user_email" id="user_email"  value="<?php echo esc_attr(wp_unslash($user_email)); ?>" placeholder="<?php _e('Email Address', 'colabsthemes'); ?>" />
        </p>
        
        <?php if (get_option('colabs_allow_registration_password')=='true') : ?>
          <p class="form-row field-password">
            <i class="icon-custom-locked"></i>
            <label id="your_password" for="your_password" ><?php _e('Password', 'colabsthemes'); ?>*</label>
            <input type="password" class="text input-text" name="your_password" id="your_password"  value="" placeholder="<?php _e('Password', 'colabsthemes'); ?>" />
          </p>

          <p class="form-row field-password">
            <i class="icon-custom-locked"></i>
            <label id="your_password_confirm" for="your_password_confirm" ><?php _e('Password', 'colabsthemes'); ?>*</label>
            <input type="password" class="text input-text" name="your_password_confirm" id="your_password_confirm"  value="" placeholder="<?php _e('Password', 'colabsthemes'); ?>" />
          </p>
        <?php endif; ?>

        <?php
        /**
         * Fires following the 'E-mail' field in the user registration form.
         *
         * @since 2.1.0
         */
        do_action( 'register_form' );
        ?>
        
        <?php if (get_option('colabs_captcha_enable') == 'true') : ?>
          <div class="form-row field-captcha">
            <?php colabsthemes_recaptcha(); ?>
          </div>
        <?php endif; ?>

        <p class="form-row">
          <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
          <input type="submit" class="button button-bold" tabindex="7" name="wp-submit" value="<?php esc_attr_e('Register', 'colabsthemes'); ?>" />
        </p>

      </form>

    <?php
  }


  /**
   * Register Process
   */
  function process_register() {
    $posted = array();
    $errors = new WP_Error();

    // Get (and clean) data
    $fields = array(
      'user_login',
      'user_email',
      'your_password',
      'your_password_confirm',
      'spam_check'
    );

    foreach ($fields as $field) {
      if (isset($_POST[$field])) {
        $posted[$field] = stripslashes(trim($_POST[$field])); 
      } else {
        $posted[$field] = '';
      }
    }
    extract( $posted );

    // Check the e-mail address
    if ('' == $user_email) {
      $errors->add('empty_email', __('<strong>ERROR</strong>: Please type your e-mail address.', 'colabsthemes'));
    } elseif ( !is_email( $user_email ) ) {
      $errors->add('invalid_email', __('<strong>ERROR</strong>: The email address isn&#8217;t correct.', 'colabsthemes'));
      $user_email = '';
    } elseif ( email_exists( $user_email ) )
      $errors->add('email_exists', __('<strong>ERROR</strong>: This email is already registered, please choose another one.', 'colabsthemes'));

    // Check the username
    if ( '' == $user_login )
      $errors->add('empty_username', __('<strong>ERROR</strong>: Please enter a username.', 'colabsthemes'));
    elseif ( !validate_username( $user_login ) ) {
      $errors->add('invalid_username', __('<strong>ERROR</strong>: This username is invalid.  Please enter a valid username.', 'colabsthemes'));
      $user_login = '';
    } elseif ( username_exists( $user_login ) )
      $errors->add('username_exists', __('<strong>ERROR</strong>: This username is already registered, please choose another one.', 'colabsthemes'));
    
    if ( '' == $user_login ) {
      $user_login = sanitize_user( $user_login );
    }

    // Check password
    if ( get_option('colabs_allow_registration_password') == 'true' ) {
      $user_pass = $your_password;
      if ('' == $your_password) {
        $errors->add('empty_password', __('<strong>ERROR</strong>: Please enter a password.', 'colabsthemes'));
      } elseif ('' == $your_password_confirm) {
        $errors->add('empty_password', __('<strong>ERROR</strong>: Please enter password twice.', 'colabsthemes'));
      } elseif ($posted['your_password'] !== $your_password_confirm) {
        $errors->add('wrong_password', __('<strong>ERROR</strong>: Passwords do not match.', 'colabsthemes'));
      }
    } else {
      $user_pass = wp_generate_password();
    }

    // process the reCaptcha request if it's been enabled
    if ('true' == get_option('colabs_captcha_enable')) {
      if( colabs_is_captcha_verified() ) {

      } else {
        $errors->add('invalid_captcha', __('<strong>ERROR</strong>: You are a bot', 'colabsthemes'));
      }
    }

    // Spam check
    if( $posted['spam_check'] != '' ) {
      $errors->add('spam_check', __('<strong>ERROR</strong>: You are spam, not human! Shoo!.', 'colabsthemes'));
    }

    do_action('register_post', $user_login, $user_email, $errors);
    $errors = apply_filters( 'registration_errors', $errors, $user_login, $user_email );

    // if there are no errors, let's create the user account
    if ( !$errors->get_error_code() ) {

      $user_id = wp_create_user( $user_login, $user_pass, $user_email );

      if ( !$user_id ) {
        $errors->add('registerfail', sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'colabsthemes'), get_option('admin_email')));
      } else {

        // Change role
        wp_update_user( array(
          'ID' => $user_id,
          'role' => 'member'
        ) );

        // send the user a confirmation and their login details
        $this->colabs_sent_email($user_id, $user_pass);

        // check to see if user set password option is enabled
        if ( get_option('colabs_allow_registration_password') == 'true' ) {
          
          // set the WP login cookie
          $secure_cookie = is_ssl() ? true : false;
          wp_set_auth_cookie($user_id, true, $secure_cookie);

          // redirect
          $redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : site_url();
          wp_redirect($redirect_to);
          exit;

        } else {

          //create own password option is turned off so show a message that it's been emailed instead
          $redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : 'wp-login.php?checkemail=newpass';
          wp_safe_redirect( $redirect_to );
          exit;

        }
      }

    }
    
    return $errors;   
  }

}

global $goodliving_custom_login;
$goodliving_custom_login = new Goodliving_Colabs_Custom_Login();