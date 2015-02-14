<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.soavis.eu
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/includes
 * @author     De B.A.A.T. <WP_SoaVis@de-baat.nl>
 */
class WP_SoaVis_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 */
	public static function activate() {

		// Validate availability of WP_GraphViz
		if ( ! wps_is_wp_graphviz_supported() ) {
			deactivate_plugins(WP_SOAVIS_PLUGIN_BASENAME);
		}
	}

	/**
	 * Compare current plugin version with minimum required.
	 *
	 * Set a notification message.
	 * Disable this add-on pack if requirement is not met.
	 */
//	public static function wps_graphviz_version_check() {
//
//		$this->debugMP('msg',__FUNCTION__ . ' Validate for ' . $this->name );
//		$slp_validated_ok = true;
//
//		// Validate availability of WP_GraphViz
//		if ( ! wps_is_wp_graphviz_supported() ) {
//			deactivate_plugins(WP_SOAVIS_PLUGIN_BASENAME);
//		}
//
//		return $slp_validated_ok;
//	}

	// return true if installed, false if not
	public function wps_check_plugin_installed( $plugin_uri = '') {

		if ($plugin_uri == '') {
			return false;
		}

		// Get all plugins and check whether the requested value is present in the result
		$all_plugins = get_plugins();
		$plugin_uris = wp_list_pluck( $all_plugins, 'PluginURI' );
		return in_array( $plugin_uri, $plugin_uris );

	}

	// gives a link to activate the plugin
	public static function wps_activate_link( $plugin_file = '' ) {

		if ( $plugin_file != '' ) {
			return wp_nonce_url( self_admin_url('plugins.php?action=activate&plugin=' . $plugin_file ), 'activate-plugin_' . $plugin_file );
		} else {
			return false;
		}
	}

	// return a nonced installation link for the plugin. checks wordpress.org to make sure it's there first.
	public static function wps_install_link( $plugin_slug = '' ) {
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		$info = plugins_api('plugin_information', array('slug' => $plugin_slug ));

		if ( is_wp_error( $info ) ) {
			return false; // plugin not available from wordpress.org
		} else {
			return wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ), 'install-plugin_' . $plugin_slug );
		}
	}

}
