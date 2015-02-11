<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * WP-SoaVis Plugin.
 *
 * @package   WP_SoaVis
 * @author    Jan de Baat <WP_SoaVis@de-baat.nl>
 * @license   GPL-2.0+
 * @link      http://www.de-baat.nl/WP_SoaVis
 * @copyright 2013 De B.A.A.T.
 */

// If uninstall, not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// TODO: Define uninstall functionality here