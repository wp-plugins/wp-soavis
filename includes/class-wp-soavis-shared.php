<?php

/**
 * The admin and public-facing functionality of the plugin.
 *
 * @link       http://www.soavis.eu
 *
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/shared
 */

/**
 * The admin and public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and the hooks for
 * both admin and public.
 *
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/shared
 * @author     De B.A.A.T. <WP_SoaVis@de-baat.nl>
 */
class WP_SoaVis_Shared {

	/**
	 * The main plugin object.
	 *
	 * @access   private
	 * @var      WP_SoaVis    $plugin    The main plugin object.
	 */
	private $plugin;

	/**
	 * The ID of this plugin.
	 *
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @var      string    $main_plugin The object of this plugin.
	 */
	public function __construct( $main_plugin ) {

		$this->plugin      = $main_plugin;
		$this->plugin_name = $main_plugin->get_plugin_name();
		$this->version     = $main_plugin->get_version();

	}

	/**
	 * Register the SoaVis objects.
	 *
	 */
	public function create_soavis_objects() {

		$this->plugin->wps_post_types = new WP_SoaVis_Post_Types( $this->plugin );
		$this->plugin->wps_shortcodes = new WP_SoaVis_Shortcodes($this->plugin);
		$this->plugin->wps_graphviz   = new WP_SoaVis_GraphViz($this->plugin);

	}

}
