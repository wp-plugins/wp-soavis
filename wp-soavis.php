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
 * Version:           0.3.0
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
	define( 'WP_SOAVIS_VERSION',				'0.3.0' );
	define( 'WP_SOAVIS_VERSION_NAME',			'wp_soavis_version' );
	define( 'WP_SOAVIS_LINK',					'http://www.soavis.eu' );
	define( 'WP_SOAVIS_OPTIONS_NAME',			'wp-soavis-options' ); // Option name for save settings
	define( 'WP_SOAVIS_DISPLAY_NAME',			'WP SoaVis' );

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
require plugin_dir_path( __FILE__ ) . 'includes/wp-soavis-functions.php';

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
