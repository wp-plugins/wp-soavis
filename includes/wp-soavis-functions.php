<?php
/**
 * WP-SoaVis Plugin.
 *
 * @package   WP_SoaVis
 * @author    Jan de Baat <WP_SoaVis@de-baat.nl>
 * @license   GPL-2.0+
 * @link      http://www.de-baat.nl/WP_SoaVis
 * @copyright 2013 De B.A.A.T.
 */


/**
 * Checks if WP_GraphViz is supported
 *
 * @return true if WP_GraphViz is supported and active, false otherwise
 */
function wps_init_option_defaults() {
	$wp_soavis_options = get_option(WP_SOAVIS_OPTIONS_NAME);

	// Only set defaults when the options are not set yet
	if ( wps_get_option(WP_SOAVIS_VERSION_NAME) === false ) {
		$wp_soavis_options['wp_soavis_max_graph_level']				= '5';
	}

	// Always set the current version
	$wp_soavis_options[WP_SOAVIS_VERSION_NAME] = WP_SOAVIS_VERSION;

	return update_option(WP_SOAVIS_OPTIONS_NAME, $wp_soavis_options);
}

/**
 * Checks if WP_GraphViz is supported
 *
 * @return true if WP_GraphViz is supported and active, false otherwise
 */
function wps_get_option($option_key = '') {
	$wp_soavis_options = get_option(WP_SOAVIS_OPTIONS_NAME);
	return isset( $wp_soavis_options[$option_key] ) ? $wp_soavis_options[$option_key] : false;
}

/**
 * Checks if WP_GraphViz is supported
 *
 * @return true if WP_GraphViz is supported and active, false otherwise
 */
function wps_update_option($option_key = '', $option_value = '') {
	$wp_soavis_options = get_option(WP_SOAVIS_OPTIONS_NAME);
	if ( isset( $wp_soavis_options[$option_key] ) ) {
		$wp_soavis_options[$option_key] = $option_value;
	}
	return update_option('wp_soavis_options', $wp_soavis_options);
}

/**
 * Checks if WP_GraphViz is supported
 *
 * @return true if WP_GraphViz is supported and active, false otherwise
 */
function wps_get_option_bool($option_key = '') {
	$wp_soavis_options = get_option(WP_SOAVIS_OPTIONS_NAME);
	if ( isset( $wp_soavis_options[$option_key] ) ) {
		return ( wps_string_to_bool( $wp_soavis_options[$option_key] ) );
	} else {
		return false;
	}
}

/**
 * Checks if WP_GraphViz is supported
 *
 * @return true if WP_GraphViz is supported and active, false otherwise
 */
function wps_string_to_bool($value) {
	if ($value == true || $value == 'true' || $value == 'TRUE' || $value == '1') {
		return true;
	}
	else if ($value == false || $value == 'false' || $value == 'FALSE' || $value == '0') {
		return false;
	}
	else {
		return $value;
	}
}

/**
 * Checks if WP_GraphViz is supported
 *
 * @return true if WP_GraphViz is supported and active, false otherwise
 */
function wps_is_wp_graphviz_supported() {

	if ( defined( 'WP_GRAPHVIZ_VERSION' ) ) {
		return version_compare( WP_GRAPHVIZ_VERSION, WPS_WP_GRAPHVIZ_MINIMUM_VERSION, '>=' );
	} else {
		return false;
	}
}

/**
 * Manually deactivate this plugin when WP_GraphViz is not supported
 */
function wps_manually_deactivate() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Generate a notice message when WP_GraphViz is not supported
 *
 * @return    string    The version number of the plugin.
 */
function wps_check_wp_graphviz_notice() {

	// IF WP_GraphViz is NOT installed and active then display notice and deactivate this plugin
	if (! wps_is_wp_graphviz_supported()) {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-soavis-activator.php';

		$notice  = '';
		$notice .= '<div class="error">';
		$notice .= '<p>';
		$notice .= '<strong>';
		$notice .= sprintf(__('%s has been deactivated.', WPS_PLUGIN), WP_SOAVIS_DISPLAY_NAME);
		$notice .= '</strong>';
		$notice .= '<br/> ';
		$notice .= sprintf(__('This plugin requires %s to be installed and active.', WPS_PLUGIN), WPS_WP_GRAPHVIZ_PLUGIN_NAME);
		$notice .= '<br/> ';

		$notice .= sprintf(__('Please <a href="%s">download</a> and install at least version %s of %s and try again.', WPS_PLUGIN),
								WPS_WP_GRAPHVIZ_DOWNLOAD_URL, WPS_WP_GRAPHVIZ_MINIMUM_VERSION, WPS_WP_GRAPHVIZ_PLUGIN_NAME);
		$notice .= '<br/>';
		$notice .= '</p>';
		$notice .= '</div>';

		echo $notice;

		// Deactivate ths plugin
		wps_manually_deactivate();
	}
}
add_action( 'admin_notices', 'wps_check_wp_graphviz_notice');
