<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       https://www.soavis.eu
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/includes
 * @author     De B.A.A.T. <WP_SoaVis@de-baat.nl>
 */
class WP_SoaVis {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @access   protected
	 * @var      WP_SoaVis_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Object for the "Custom Post Type" for SoaVis.
	 *
	 * @var WP_SoaVis_Post_Types
	 */
	public $wps_post_types;

	/**
	 * Object for the shortcodes for SoaVis.
	 *
	 * @var WP_SoaVis_Shortcodes
	 */
	public $wps_shortcodes;

	/**
	 * Object for the GraphViz functionality for SoaVis.
	 *
	 * @var WP_SoaVis_GraphViz
	 */
	public $wps_graphviz;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 */
	public function __construct() {

		$this->plugin_name = 'wp-soavis';
		$this->version     = WP_SOAVIS_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_shared_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_SoaVis_Loader. Orchestrates the hooks of the plugin.
	 * - WP_SoaVis_i18n.   Defines internationalization functionality.
	 * - WP_SoaVis_Admin.  Defines all hooks for the dashboard.
	 * - WP_SoaVis_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-soavis-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-soavis-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-soavis-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-soavis-public.php';

		/**
		 * The class responsible for defining all actions that occur in the admin and public-facing
		 * sides of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-soavis-shared.php';

		/**
		 * The class responsible for handling the SoaVis post_types.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-soavis-post-types.php';

		/**
		 * The class responsible for handling the SoaVis shortcodes.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-soavis-shortcodes.php';

		/**
		 * The class responsible for handling the SoaVis GraphViz functionality.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-soavis-graphviz.php';

		$this->loader = new WP_SoaVis_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Soavis_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WP_SoaVis_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WP_SoaVis_Admin( $this );

		$this->loader->add_action( 'admin_enqueue_scripts',		$plugin_admin, 'enqueue_styles'  );
		$this->loader->add_action( 'admin_enqueue_scripts',		$plugin_admin, 'enqueue_scripts' );

		// Note: we're only adding the meta box for product post type
		$this->loader->add_action( 'add_meta_boxes_product',	$plugin_admin, 'add_soavis_information_meta_box' );
		$this->loader->add_action( 'save_post',					$plugin_admin, 'save_soavis_information_meta_box' );

		// Plugin menu
		$this->loader->add_action( 'admin_init',				$plugin_admin, 'add_soavis_settings_fields');
		$this->loader->add_action( 'admin_menu',				$plugin_admin, 'add_soavis_menu_page');

		$this->loader->add_action( 'admin_init',				$plugin_admin, 'add_admin_post_actions');
		$this->loader->add_action( 'admin_init',				$plugin_admin, 'add_admin_post_type_actions');

		// Add actions to enable inserting shortcodes in the editor
		$this->loader->add_action( 'media_buttons',				$plugin_admin, 'wps_action_media_buttons', 11);
		$this->loader->add_action( 'admin_head',				$plugin_admin, 'wps_action_admin_head');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WP_SoaVis_Public( $this );

		$this->loader->add_action( 'wp_enqueue_scripts',		$plugin_public, 'enqueue_styles'             );
		$this->loader->add_action( 'wp_enqueue_scripts',		$plugin_public, 'enqueue_scripts'            );
		$this->loader->add_action( 'gform_after_submission',	$plugin_public, 'wps_create_demo_post', 10, 2);

	}

	/**
	 * Register all of the hooks related to both admin and public-facing functionality
	 * of the plugin.
	 *
	 * @access   private
	 */
	private function define_shared_hooks() {

		$plugin_shared = new WP_SoaVis_Shared( $this );

		$this->loader->add_action( 'init',             $plugin_shared, 'create_soavis_objects' );
		$this->loader->add_action( 'dmp_addpanel',     $this,          'create_DMPPanels'      );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Soavis_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Create a Map Settings Debug My Plugin panel.
	 *
	 * @return null
	 */
	function create_DMPPanels() {
		if (!isset($GLOBALS['DebugMyPlugin'])) { return; }
		if (class_exists('DMPPanelWPSoaVisMain') == false) {
			require_once(dirname( __FILE__ ) . '/class.dmppanels.php');
		}
		$GLOBALS['DebugMyPlugin']->panels['wp-soavis'] = new DMPPanelWPSoaVisMain();
	}

	/**
	 * Add DebugMyPlugin messages.
	 *
	 * @param string $panel - panel name
	 * @param string $type - what type of debugging (msg = simple string, pr = print_r of variable)
	 * @param string $header - the header
	 * @param string $message - what you want to say
	 * @param string $file - file of the call (__FILE__)
	 * @param int $line - line number of the call (__LINE__)
	 * @param boolean $notime - show time? default true = yes.
	 * @return null
	 */
	function debugMP($type='msg', $header='Debug WP SoaVis',$message='',$file=null,$line=null,$notime=false) {

		$panel='wp-soavis';

		// Panel not setup yet?  Return and do nothing.
		//
		if (
			!isset($GLOBALS['DebugMyPlugin']) ||
			!isset($GLOBALS['DebugMyPlugin']->panels[$panel])
		   ) {
			return;
		}

		// Do normal real-time message output.
		//
		switch (strtolower($type)):
			case 'pr':
				$GLOBALS['DebugMyPlugin']->panels[$panel]->addPR($header,$message,$file,$line,$notime);
				break;
			default:
				$GLOBALS['DebugMyPlugin']->panels[$panel]->addMessage($header,$message,$file,$line,$notime);
		endswitch;
	}

}
