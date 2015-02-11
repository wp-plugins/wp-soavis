<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://www.soavis.eu
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/includes
 * @author     De B.A.A.T. <WP_SoaVis@de-baat.nl>
 */
class WP_SoaVis_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 */
	public static function deactivate() {
		// Flush rewrite on deactivation
		flush_rewrite_rules();
	}

}
