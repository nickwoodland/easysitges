<?php

class Colabs_Theme_install {

  public function __construct() {
    add_action( 'after_setup_theme', array( $this, 'colabs_theme_activate' ));
    add_action( 'admin_notices', array( $this, 'colabs_admin_install_notices' ));
    add_action( 'admin_init', array( $this, 'colabs_install_pages' ) );
  }


  /**
   * Check if pages has been installed
   */
  public function colabs_theme_activate() {
    if( get_option('colabs_dashboard_url') == '' ) {
      update_option( '_goodliving_needs_pages', 1 );
    }
  }


  /**
   * Notice user to install pages
   */
  public function colabs_admin_install_notices() {
    if( get_option('_goodliving_needs_pages') == 1 ) : ?>

      <div class="updated">
        <h4><?php _e( '<strong>Thank you for using GoodLiving</strong> &#8211; You\'re almost ready :)', 'colabsthemes' ); ?></h4>
        <p class="submit">
          <a href="<?php echo add_query_arg('install_goodliving_pages', 'true', admin_url('admin.php?page=colabsthemes') ); ?>" class="button-primary"><?php _e( 'Install goodliving Pages', 'colabsthemes' ); ?></a>
          <a href="<?php echo add_query_arg('skip_install_goodliving_pages', 'true', admin_url('admin.php?page=colabsthemes') ); ?>" class="skip button"><?php _e( 'Skip', 'colabsthemes' ); ?></a>
        </p>
      </div>

    <?php endif;
  }


  /**
   * Install pages
   */
  public function colabs_install_pages() {

    // Install pages
    if ( ! empty( $_GET['install_goodliving_pages'] ) ) {
      $this->create_pages();
      $this->create_terms();

      // We no longer need to install pages
      delete_option( '_goodliving_needs_pages' );

      wp_redirect( admin_url( 'index.php?page=colabsthemes' ) );
      exit;
    }

    // Skip installation
    elseif ( ! empty( $_GET['skip_install_goodliving_pages'] ) ) {
      // We no longer need to install pages
      delete_option( '_goodliving_needs_pages' );

      wp_redirect( admin_url( 'index.php?page=colabsthemes' ) );
      exit;
    }

  }


  /**
   * Create required pages
   */
  public function create_pages() {

    // Dashboard
    $this->colabs_create_page(
      'colabs_dashboard_url',
      __('Dashboard', 'colabsthemes'),
      '',
      'template-dashboard.php'
    );

    // Profile
    $this->colabs_create_page(
      'colabs_profile_url',
      __('Profile', 'colabsthemes'),
      '',
      'template-profile.php'
    );

    // Edit Property
    $this->colabs_create_page(
      'colabs_edit_url',
      __('Edit Property', 'colabsthemes'),
      '',
      'template-edit-property.php'
    );

    // Submit Property
    $this->colabs_create_page(
      'colabs_submit_url',
      __('Submit Property', 'colabsthemes'),
      '',
      'template-submit-property.php'
    );

    // Bookmark Property
    $this->colabs_create_page(
      'colabs_bookmark_property',
      __('Bookmark', 'colabsthemes'),
      '',
      'template-bookmark.php'
    );

    // Agent
    $this->colabs_create_page(
      'colabs_agent_page',
      __('Agent', 'colabsthemes'),
      '',
      'template-agent.php'
    );

  }


  /**
   * Install Default Terms
   */
  public function create_terms() {

    // Property status
    wp_insert_term( 'Rent', 'property_status' );
    wp_insert_term( 'Sell', 'property_status' );
    wp_insert_term( 'Sold', 'property_status' );
  }


  /**
   * Function Helper for creating page
   *
   * @param mixed $slug Slug for the new page
   * @param mixed $option Option name to store the page's ID
   * @param string $page_title (default: '') Title for the new page
   * @param string $page_template (default: 'default') Page template for the new page
   * @param string $page_content (default: '') Content for the new page
   * @param int $post_parent (default: 0) Parent for the new page
   */
  public function colabs_create_page( $option, $page_title = '', $page_content = '', $page_template = 'default', $option_identifier = 'id' ) {

    $page_data = array(
      'post_status'     => 'publish',
      'post_type'       => 'page',
      'post_author'     => 1,
      'post_title'      => $page_title,
      'post_content'    => $page_content,
      'post_parent'     => 0,
      'comment_status'  => 'closed'
    );

    $page_id = wp_insert_post( $page_data );

    // Set page template
    update_post_meta( $page_id, '_wp_page_template', $page_template );

    if( $option != '' ) {
      if( $option_identifier == 'id' ) {
        update_option( $option, $page_id );
      } elseif( $option_identifier == 'slug' ) {
        $page_data = get_post( $page_id );
        update_option( $option, $page_data->post_name );
      }
    }
  }




}

new Colabs_Theme_install();
