<?php
/* SLiMS Setting page */

/**
 * custom option and settings
 */
function slims_settings_init() {
	// Register a new setting for "slims" page.
	register_setting( 'slims', 'slims_options' );

	// Register a new section in the "slims" page.
	add_settings_section( 'slims_main_config_section',
		__( 'SLiMS Plugin Main Setting/Configuration', 'slims' ), 'slims_main_config_section_callback',
		'slims'
	);

	// Register slims URL config field.
	add_settings_field(
		'slims_base_url', __( 'SLiMS Base URL', 'slims' ),
		'slims_base_url_cb',
		'slims',
		'slims_main_config_section',
		array(
			'label_for'         => 'slims_base_url',
			'class'             => 'slims_row',
			'slims_default_base_url'    => 'http://localhost/slims',
		)
	);

	// Register biblio detail config field.
	add_settings_field(
		'slims_open_biblio_detail', __( 'SLiMS Open Biblio/Record Detail', 'slims' ),
		'slims_open_biblio_detail_cb',
		'slims',
		'slims_main_config_section',
		array(
			'label_for'         => 'slims_open_biblio_detail',
			'class'             => 'slims_row',
			'slims_default_open_biblio'    => 'slims',
		)
	);

	// Register catalog fetch method config field
	add_settings_field(
		'slims_field_fetch_method', __( 'Catalog Fetch Method', 'slims' ),
		'slims_field_fetch_method_cb',
		'slims',
		'slims_main_config_section',
		array(
			'label_for'             => 'slims_field_fetch_method',
			'class'                 => 'slims_row',
			'slims_default_fetch_method'    => 'json',
		)
	);
}

/**
 * Register our slims_settings_init to the admin_init action hook.
 */
add_action( 'admin_init', 'slims_settings_init' );


/**
 * Custom option and settings:
 *  - callback functions
 */


/**
 * Developers section callback function.
 *
 * @param array $args  The settings array, defining title, id, callback.
 */
function slims_main_config_section_callback( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Below are the main configuration settings you need to set before integrating SLiMS.', 'slims' ); ?></p>
	<?php
}

/**
 * SLiMS base URL setting callback function.
 *
 * @param array $args
 */
function slims_base_url_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'slims_options' );
	?>
    <input type="text" class="regular-text" id="slims_options[<?php echo esc_attr( $args['label_for'] ); ?>]" name="slims_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : $args['slims_default_base_url']; ?>" />  
	<p class="description">
		<?php esc_html_e( 'Do not write/put any trailing slash at the end of URL', 'slims' ); ?>
	</p>
	<?php
}

/**
 * SLiMS open biblio detail setting callback function.
 *
 * @param array $args
 */
function slims_open_biblio_detail_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'slims_options' );
	?>
	<select id="<?php echo esc_attr( $args['label_for'] ); ?>" data-custom="<?php echo esc_attr( $args['slims_default_open_biblio'] ); ?>"
		name="slims_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
		<option value="slims" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'json', false ) ) : $args['slims_default_open_biblio']; ?>>
			<?php esc_html_e( 'Open Biblio/Record Detail in SLiMS site', 'slims' ); ?>
		</option>
 		<option value="wp" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'xml', false ) ) : $args['slims_default_open_biblio']; ?>>
			<?php esc_html_e( 'Open Biblio/Record Detail in WordPress page', 'slims' ); ?>
		</option>
	</select>
	<p class="description">
		<?php esc_html_e( 'This option define how the catalog record detail open when a user clicked the detail link.', 'slims' ); ?>
	</p>
	<?php
}

/**
 * SLiMS Fetch method setting callback function.
 *
 * @param array $args
 */
function slims_field_fetch_method_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'slims_options' );
	?>
	<select id="<?php echo esc_attr( $args['label_for'] ); ?>" data-custom="<?php echo esc_attr( $args['slims_default_fetch_method'] ); ?>"
		name="slims_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
		<option value="json" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'json', false ) ) : $args['slims_default_fetch_method']; ?>>
			<?php esc_html_e( 'JSON', 'slims' ); ?>
		</option>
 		<option value="xml" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'xml', false ) ) : $args['slims_default_fetch_method']; ?>>
			<?php esc_html_e( 'XML', 'slims' ); ?>
		</option>
	</select>
	<p class="description">
		<?php esc_html_e( 'JSON is the recommended way to fetch SLiMS catalog data', 'slims' ); ?>
	</p>
	<?php
}

/**
 * Add the top level menu page.
 */
function slims_options_page() {
	add_menu_page(
		'SLiMS Plugin Settings',
		'SLiMS',
		'manage_options',
		'slims',
		'slims_options_page_html'
	);
}

/**
 * Register to the admin_menu action hook.
 */
add_action( 'admin_menu', 'slims_options_page' );


/**
 * Top level menu callback function
 */
function slims_options_page_html() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// add error/update messages

	// check if the user have submitted the settings
	// WordPress will add the "settings-updated" $_GET parameter to the url
	if ( isset( $_GET['settings-updated'] ) ) {
		// add settings saved message with the class of "updated"
		add_settings_error( 'slims_messages', 'slims_message', __( 'Settings Saved', 'slims' ), 'updated' );
	}

	// show error/update messages
	settings_errors( 'slims_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "slims"
			settings_fields( 'slims' );
			// output setting sections and their fields
			// (sections are registered for "slims", each field is registered to a specific section)
			do_settings_sections( 'slims' );
			// output save settings button
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
}

/** Add SLiMS setting page link to plugin description */
add_filter( 'plugin_action_links_'. SLIMS_PLUGIN_BASE, 'slims_settings_link' );
function slims_settings_link( $links ) {
	// Build and escape the URL.
	$url = esc_url( add_query_arg(
		'page',
		'slims',
		get_admin_url() . 'admin.php'
	) );
	// Create the link.
	$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
	// Adds the link to the end of the array.
	array_push(
		$links,
		$settings_link
	);
	return $links;
}