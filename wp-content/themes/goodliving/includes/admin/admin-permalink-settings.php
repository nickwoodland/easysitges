<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Colabs_Admin_Permalink_Settings' ) ) :

/**
 * Colabs_Admin_Permalink_Settings Class
 */
class Colabs_Admin_Permalink_Settings {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_init', array( $this, 'settings_save' ) );
	}

	/**
	 * Init our settings
	 */
	public function settings_init() {
		// Add a section to the permalinks page
		add_settings_section( 'foxestate-permalink', __( 'Property permalink base', 'colabsthemes' ), array( $this, 'settings' ), 'permalink' );

		// Add our settings
		add_settings_field(
			'property_type_slug',      		// id
			__( 'Property type base', 'colabsthemes' ), 	// setting title
			array( $this, 'property_type_slug_input' ),  // display callback
			'permalink',                 				// settings page
			'optional'                  				// settings section
		);
		add_settings_field(
			'property_features_slug',      	// id
			__( 'Property feature base', 'colabsthemes' ), 	// setting title
			array( $this, 'property_features_slug_input' ),  // display callback
			'permalink',                 				// settings page
			'optional'                  				// settings section
		);
    add_settings_field(
			'property_status_slug',      	// id
			__( 'Property status base', 'colabsthemes' ), 	// setting title
			array( $this, 'property_status_slug_input' ),  // display callback
			'permalink',                 				// settings page
			'optional'                  				// settings section
		);
	}

	/**
	 * Show a slug input box.
	 */
	public function property_type_slug_input() {
		$permalinks = get_option( 'foxestate_permalinks' );
		?>
		<input name="property_type_slug_input" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['type_base'] ) ) echo esc_attr( $permalinks['type_base'] ); ?>" placeholder="<?php echo _x('property-type', 'slug', 'colabsthemes') ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function property_features_slug_input() {
		$permalinks = get_option( 'foxestate_permalinks' );
		?>
		<input name="property_features_slug_input" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['feature_base'] ) ) echo esc_attr( $permalinks['feature_base'] ); ?>" placeholder="<?php echo _x('property-feature', 'slug', 'colabsthemes') ?>"/>
		<?php
	}
  
  /**
	 * Show a slug input box.
	 */
	public function property_status_slug_input() {
		$permalinks = get_option( 'foxestate_permalinks' );
		?>
		<input name="property_status_slug_input" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['status_base'] ) ) echo esc_attr( $permalinks['status_base'] ); ?>" placeholder="<?php echo _x('property-status', 'slug', 'colabsthemes') ?>"/>
		<?php
	}

	/**
	 * Show the settings
	 */
	public function settings() {
		echo wpautop( __( 'These settings control the permalinks used for property. These settings only apply when <strong>not using "default" permalinks above</strong>.', 'colabsthemes' ) );

		$permalinks = get_option( 'foxestate_permalinks' );
		$property_permalink = $permalinks['property_base'];

		// Get property page
    $page = get_page_by_path('properties');
		$property_page_id 	= $page->ID;
		$base_slug 		= ( $property_page_id > 0 && get_page( $property_page_id ) ) ? get_page_uri( $property_page_id ) : _x( 'properties', 'default-slug', 'colabsthemes' );
		$property_base 	= _x( 'property', 'default-slug', 'colabsthemes' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $property_base ),
			2 => '/' . trailingslashit( $base_slug ),
			3 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%property_type%' )
		);
		?>
		<table class="form-table">
			<tbody>
				<tr>
					<th><label><input name="property_permalink" type="radio" value="<?php echo $structures[0]; ?>" class="fetog" <?php checked( $structures[0], $property_permalink ); ?> /> <?php _e( 'Default', 'colabsthemes' ); ?></label></th>
					<td><code><?php echo home_url(); ?>/?property=sample-property</code></td>
				</tr>
				<tr>
					<th><label><input name="property_permalink" type="radio" value="<?php echo $structures[1]; ?>" class="fetog" <?php checked( $structures[1], $property_permalink ); ?> /> <?php _e( 'Property', 'colabsthemes' ); ?></label></th>
					<td><code><?php echo home_url(); ?>/<?php echo $property_base; ?>/sample-property/</code></td>
				</tr>
				<?php if ( $property_page_id ) : ?>
					<tr>
						<th><label><input name="property_permalink" type="radio" value="<?php echo $structures[2]; ?>" class="fetog" <?php checked( $structures[2], $property_permalink ); ?> /> <?php _e( 'Properties base', 'colabsthemes' ); ?></label></th>
						<td><code><?php echo home_url(); ?>/<?php echo $base_slug; ?>/sample-property/</code></td>
					</tr>
					<tr>
						<th><label><input name="property_permalink" type="radio" value="<?php echo $structures[3]; ?>" class="fetog" <?php checked( $structures[3], $property_permalink ); ?> /> <?php _e( 'Properties base with type', 'colabsthemes' ); ?></label></th>
						<td><code><?php echo home_url(); ?>/<?php echo $base_slug; ?>/property-type/sample-property/</code></td>
					</tr>
				<?php endif; ?>
				<tr>
					<th><label><input name="property_permalink" id="foxestate_custom_selection" type="radio" value="custom" class="tog" <?php checked( in_array( $property_permalink, $structures ), false ); ?> />
						<?php _e( 'Custom Base', 'colabsthemes' ); ?></label></th>
					<td>
						<input name="property_permalink_structure" id="foxestate_permalink_structure" type="text" value="<?php echo esc_attr( $property_permalink ); ?>" class="regular-text code"> <span class="description"><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'colabsthemes' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<script type="text/javascript">
			jQuery(function(){
				jQuery('input.fetog').change(function() {
					jQuery('#foxestate_permalink_structure').val( jQuery(this).val() );
				});

				jQuery('#foxestate_permalink_structure').focus(function(){
					jQuery('#foxestate_custom_selection').click();
				});
			});
		</script>
		<?php
	}

	/**
	 * Save the settings
	 */
	public function settings_save() {
		if ( ! is_admin() )
			return;

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page
		if ( isset( $_POST['permalink_structure'] ) || isset( $_POST['type_base'] ) && isset( $_POST['property_permalink'] ) ) {
			
			$property_type_slug_input = sanitize_text_field( $_POST['property_type_slug_input'] );
			$property_features_slug_input = sanitize_text_field( $_POST['property_features_slug_input'] );
      $property_status_slug_input = sanitize_text_field( $_POST['property_status_slug_input'] );

			$permalinks = get_option( 'foxestate_permalinks' );
			if ( ! $permalinks )
				$permalinks = array();

      $permalinks['type_base'] 	= untrailingslashit( $property_type_slug_input );
			$permalinks['feature_base'] 		= untrailingslashit( $property_features_slug_input );
			$permalinks['status_base'] 	= untrailingslashit( $property_status_slug_input );

			// Property base
			$property_permalink = sanitize_text_field( $_POST['property_permalink'] );

			if ( $property_permalink == 'custom' ) {
				// Get permalink without slashes
				$property_permalink = trim( sanitize_text_field( $_POST['property_permalink_structure'] ), '/' );

				// This is an invalid base structure and breaks pages
				if ( '%property_type%' == $property_permalink ) {
					$property_permalink = _x( 'property', 'slug', 'colabsthemes' ) . '/' . $property_permalink;
				}

				// Prepending slash
				$property_permalink = '/' . $property_permalink;
			} elseif ( empty( $property_permalink ) ) {
				$property_permalink = false;
			}

			$permalinks['property_base'] = untrailingslashit( $property_permalink );

			update_option( 'foxestate_permalinks', $permalinks );
		}
	}
}

endif;

return new Colabs_Admin_Permalink_Settings();