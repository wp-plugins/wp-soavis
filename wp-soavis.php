<?php
/**
 * The WordPress SoaVis Plugin.
 *
 * A plugin to provide SoaVis functionality for WordPress sites.
 *
 * @link              http://www.soavis.eu
 * @package           WP_SoaVis
 * @copyright         2015 De B.A.A.T.
 *
 * @wordpress-plugin
 * Plugin Name:       WP SoaVis
 * Plugin URI:        http://www.soavis.eu
 * Description:       A plugin to provide SoaVis functionality for WordPress sites.
 * Version:           0.2.0
 * Author:            De B.A.A.T.
 * Author URI:        https://www.de-baat.nl/WP_SoaVis
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       wp-soavis
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//	WP_SoaVis definitions
if ( ! defined( 'WP_SOAVIS_VERSION' ) ) {
	define( 'WP_SOAVIS_VERSION',				'0.2.0' );
	define( 'WP_SOAVIS_LINK',					'http://www.soavis.eu' );
	define( 'WP_SOAVIS_OPTIONS_NAME',			'wp-soavis-options' ); // Option name for save settings
	define( 'WP_SOAVIS_DISPLAY_NAME',			'WP SoaVis' ); // Option name for save settings

	define( 'WP_SOAVIS_URL',					plugins_url('', __FILE__) );
	define( 'WP_SOAVIS_DIR',					rtrim(plugin_dir_path(__FILE__), '/') );
	define( 'WP_SOAVIS_BASENAME',				dirname(plugin_basename(__FILE__)) );
	define( 'WP_SOAVIS_PLUGIN_BASENAME',		plugin_basename(__FILE__) );

	define( 'WPS_PLUGIN',						'wp-soavis' );
}

//	WP_GraphViz minimum required version
if ( ! defined( 'WPS_WP_GRAPHVIZ_MINIMUM_VERSION' ) ) {
	define( 'WPS_WP_GRAPHVIZ_MINIMUM_VERSION',	'1.1.0' );
	define( 'WPS_WP_GRAPHVIZ_PLUGIN_NAME',		'WP GraphViz' );
	define( 'WPS_WP_GRAPHVIZ_PLUGIN_SLUG',		'wp-graphviz' );
	define( 'WPS_WP_GRAPHVIZ_PLUGIN_URI',		'https://wordpress.org/extend/plugins/wp-graphviz/' );
	define( 'WPS_WP_GRAPHVIZ_DOWNLOAD_URL',		'https://wordpress.org/plugins/wp-graphviz/' );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-soavis-activator.php
 */
function activate_wp_soavis() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-soavis-activator.php';
	WP_SoaVis_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-soavis-deactivator.php
 */
function deactivate_wp_soavis() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-soavis-deactivator.php';
	WP_SoaVis_Deactivator::deactivate();
}

register_activation_hook(   __FILE__, 'activate_wp_soavis'   );
register_deactivation_hook( __FILE__, 'deactivate_wp_soavis' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-soavis.php';

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

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 */
function run_wp_soavis() {

	$plugin = new WP_SoaVis();
	$plugin->run();

}
run_wp_soavis();
