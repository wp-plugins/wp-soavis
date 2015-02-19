<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.soavis.eu
 *
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/public
 * @author     De B.A.A.T. <WP_SoaVis@de-baat.nl>
 */
class WP_SoaVis_Public {

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
	 *
	 * @var      string    $main_plugin The object of this plugin.
	 */
	public function __construct( $main_plugin ) {

		$this->plugin      = $main_plugin;
		$this->plugin_name = $main_plugin->get_plugin_name();
		$this->version     = $main_plugin->get_version();

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Soavis_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Soavis_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-soavis-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Soavis_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Soavis_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-soavis-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Use a custom post type if defined by a field in the form
	 *
	 */
	public function wps_create_demo_post($entry, $form) {

		$debug_out  = '';
		$debug_out .= __FUNCTION__ . ' entry : ' . print_r($entry, true);
		$debug_out .= __FUNCTION__ . ' form : ' . print_r($form, true);

		// Get the post_id if it exists
		if (isset($entry['post_id'])) {
			$post_id = $entry['post_id'];
		} else {
			return;
		}

//		update_post_meta($post_id, 'debug_out', $debug_out);
		// Add the demo attachment to the post
		$ref_attachment = get_page_by_title('Demo', 'OBJECT', 'attachment');
		set_post_thumbnail($post_id, $ref_attachment->ID);

		// Check the values of the form fields
		if (isset($form['fields'])) {
			foreach ($form['fields'] as $field) {
				// If isset, then get the value entered for this field
				if (isset($entry[$field['id']])) {
					$entry_value = $entry[$field['id']];

					// If an existing post_type is found, then use it for this post
					if (post_type_exists($entry_value)) {
						set_post_type($post_id, $entry_value);
						return;
					}
				}
			}
		}

	}

	/**
	 * Simplify the parent debugMP interface.
	 *
	 * @param string $type
	 * @param string $hdr
	 * @param string $msg
	 */
	function debugMP($type,$hdr,$msg='') {
		if (($type === 'msg') && ($msg!=='')) {
			$msg = esc_html($msg);
		}
		if ($hdr != '') { $hdr = ' Public: ' . $hdr; }
		$this->plugin->debugMP($type,$hdr,$msg,NULL,NULL,true);
	}

}
