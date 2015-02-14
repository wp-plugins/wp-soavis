<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://www.soavis.eu
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/admin
 * @author     De B.A.A.T. <WP_SoaVis@de-baat.nl>
 */
class WP_SoaVis_Admin {

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
	 * Register the stylesheets for the Dashboard.
	 *
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_SoaVis_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_SoaVis_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-soavis-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_SoaVis_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_SoaVis_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-soavis-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Creates the settings menu and sub menus for SoaVis admin pages.
	 *
	 */
	public function add_soavis_menu_page() {
		add_menu_page(
			__( 'WP SoaVis', $this->plugin_name ),
			__( 'WP SoaVis', $this->plugin_name ),
			'edit_posts',
			$this->plugin_name,
			array( $this, 'render_soavis_shortcode_page' ),
			'dashicons-networking'
//			,27 // position
		);

		add_submenu_page(
			$this->plugin_name,
			__( 'SoaVis Shortcodes', $this->plugin_name ),
			__( 'SoaVis Shortcodes', $this->plugin_name ),
			'edit_posts',
			$this->plugin_name . '_shortcodes',
			array( $this, 'render_soavis_shortcode_page' )
		);

		add_submenu_page(
			$this->plugin_name,
			__( 'SoaVis Settings', $this->plugin_name ),
			__( 'SoaVis Settings', $this->plugin_name ),
			'edit_posts',
			$this->plugin_name . '_settings',
			array( $this, 'render_soavis_settings_page' )
		);
	}


	/**
	 * Creates the settings fields for the plugin options page.
	 */
	public function add_soavis_settings_fields() {
		$settings_group_id = WP_SOAVIS_OPTIONS_NAME . '-group';
		$soavis_settings_section_id = 'wp-soavis-settings-section-general';

		// Check whether to update options or not
		if (wps_get_option(WP_SOAVIS_VERSION_NAME) != WP_SOAVIS_VERSION) {
			wps_init_option_defaults();
		}

		register_setting( $settings_group_id, WP_SOAVIS_OPTIONS_NAME, array( $this, 'check_wp_soavis_option' ) );

		add_settings_section(
			$soavis_settings_section_id,
			__( 'General Settings', $this->plugin_name ),
			array( $this, 'render_soavis_settings_section' ),
			$settings_group_id
		);

		add_settings_field(
			'wp_soavis_max_graph_level',
			__( 'Max Graph Level', $this->plugin_name ),
			array( $this, 'render_wp_soavis_max_graph_level_field' ),
			$settings_group_id,
			$soavis_settings_section_id
		);

	}

	function check_wp_soavis_option($input) {

		$newinput = array();

		// Always set the current version
		$newinput[WP_SOAVIS_VERSION_NAME] = WP_SOAVIS_VERSION;

		// Check value of wp_soavis_max_graph_level which should be a positive integer
		if (filter_var($input['wp_soavis_max_graph_level'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
			$newinput['wp_soavis_max_graph_level'] = (int) trim($input['wp_soavis_max_graph_level']);
		} else {
			$newinput['wp_soavis_max_graph_level'] = WP_SOAVIS_MAX_GRAPH_LEVEL_DEFAULT;
		}

		return $newinput;
	}

	/**
	 * Renders the plugin's options page.
	 */
	public function render_soavis_settings_page() {
		$settings_group_id = WP_SOAVIS_OPTIONS_NAME . '-group';
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wp-soavis-admin-display.php';
	}

	/**
	 * Renders the description for the AWS settings section.
	 */
	public function render_soavis_settings_section() {
		echo _e( 'Manage your SoaVis settings below.', $this->plugin_name );
	}

	/**
	 * Renders the settings field for the Max Graph Level.
	 */
	public function render_wp_soavis_max_graph_level_field() {
		$wp_soavis_max_graph_level = wps_get_option('wp_soavis_max_graph_level');
		$wp_soavis_max_graph_level_name = WP_SOAVIS_OPTIONS_NAME . '[wp_soavis_max_graph_level]';
		?><input type="text" id="input_wp_soavis_max_graph_level" name="<?php echo $wp_soavis_max_graph_level_name; ?>" value="<?php echo $wp_soavis_max_graph_level;?>" />
		<?php
		echo ' ' . __('The maximum level to traverse the service network. ', $this->plugin_name);
		echo sprintf(__('Must be a positive integer, any invalid value will render to the default value of %d. ', $this->plugin_name), WP_SOAVIS_MAX_GRAPH_LEVEL_DEFAULT);
	}

	/**
	 * Renders the page showing the SoaVis shortcodes.
	 *
	 */
	function render_soavis_shortcode_page() {
		$this->debugMP('msg',__FUNCTION__);

		$render_shortcode_output = '';

		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h3><?php echo __('Shortcodes', $this->plugin_name); ?></h3>
			<?php
			if (isset($_REQUEST['settings-updated'])) {
				?>
				<div id="sip-return-message" class="updated"><?php echo __('Your Settings have been saved.', $this->plugin_name); ?></div>
				<?php
			}
			?>
			<p>
				<?php echo __('This page shows the shortcodes supported by the WP SoaVis plugin:', $this->plugin_name); ?>
			</p>
			<div id='wp_soavis_table_wrapper'>
			<table id='wp_soavis_shortcodes_table' class='wp-soavis wp-list-table widefat fixed posts' cellspacing="0">

			<thead>
				<tr class="wp_soavis_shortcodes_row">
					<th class="wp_soavis_shortcodes_cell" width="20%"><code>[SHORTCODE]</code>&nbsp;</th>
					<th class="wp_soavis_shortcodes_cell" width="25%"><?php echo __('Description', $this->plugin_name); ?>&nbsp;</th>
					<th class="wp_soavis_shortcodes_cell"><?php echo __('Parameters', $this->plugin_name); ?>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$row_style = 'even';
				$wp_soavis_shortcodes = $this->plugin->wps_shortcodes->get_shortcodes();
				foreach ($wp_soavis_shortcodes as $shortcode) {
					$row_style = ($row_style == 'odd') ? 'even' : 'odd';
					$render_shortcode_output = '';
					$render_shortcode_output .= '<tr class="wp_soavis_shortcodes_row ' . $row_style . '">';
					$render_shortcode_output .= '<td class="wp_soavis_shortcodes_cell"><code>[' . $shortcode['label'] . ']</code></td>';
					$render_shortcode_output .= '<td class="wp_soavis_shortcodes_cell">' . $shortcode['description'] . '</td>';
					$render_shortcode_output .= '<td class="wp_soavis_shortcodes_cell">' . $shortcode['parameters'] . '</td>';
					$render_shortcode_output .= '</tr>';
					echo $render_shortcode_output;
				}
				?>
			</tbody>
			</table>
		</div>
		<p>
			<?php
				echo sprintf (__('<br/>This plugin supports the shortcodes as shown above as well as in all upper and lower case.', $this->plugin_name));
				echo sprintf (__('<br/>More information can be found <a href="%s">here</a>.', $this->plugin_name), WP_SOAVIS_LINK);
				echo sprintf (__('<br/>If you have suggestions to improve this plugin, please leave a comment on this same <a href="%s">page</a>.', $this->plugin_name), WP_SOAVIS_LINK);
			?>
		</p>

	</div>
	<?php
	}

	/**
	 * Register the actions for the dashboard.
	 *
	 */
	public function add_admin_post_actions() {
		// Register the callbacks for processing action requests.
		$post_actions = array( 'list', 'add', 'edit', 'options', 'export', 'import' );
		$get_actions  = array( 'hide_message', 'delete_table', 'copy_table', 'preview_table', 'editor_button_thickbox', 'uninstall_tablepress' );
		foreach ( $post_actions as $action ) {
			add_action( "admin_post_soavis_{$action}", array( $this, "handle_post_soavis_action_{$action}" ) );
		}
		foreach ( $get_actions as $action ) {
			add_action( "admin_post_soavis_{$action}", array( $this, "handle_get_soavis_action_{$action}" ) );
		}

//		// Register callbacks to trigger load behavior for admin pages.
//		foreach ( $this->page_hooks as $page_hook ) {
//			add_action( "load-{$page_hook}", array( $this, 'load_admin_page' ) );
//		}
//
//		$pages_with_editor_button = array( 'post.php', 'post-new.php' );
//		foreach ( $pages_with_editor_button as $editor_page ) {
//			add_action( "load-{$editor_page}", array( $this, 'add_editor_buttons' ) );
//		}
//
//		if ( ! is_network_admin() && ! is_user_admin() ) {
//			add_action( 'admin_bar_menu', array( $this, 'add_wp_admin_bar_new_content_menu_entry' ), 71 );
//		}
//
//		add_action( 'load-plugins.php', array( $this, 'plugins_page' ) );
//		add_action( 'admin_print_styles-media-upload-popup', array( $this, 'add_media_upload_thickbox_css' ) );
//
//		// Add filters and actions for the integration into the WP WXR exporter and importer.
//		add_action( 'wp_import_insert_post', array( TablePress::$model_table, 'add_table_id_on_wp_import' ), 10, 4 );
//		add_filter( 'wp_import_post_meta', array( TablePress::$model_table, 'prevent_table_id_post_meta_import_on_wp_import' ), 10, 3 );
//		add_filter( 'wxr_export_skip_postmeta', array( TablePress::$model_table, 'add_table_id_to_wp_export' ), 10, 3 );

	}

	/**
	 * Register the meta_boxes for the soavis post_types.
	 *
	 */
	public function add_admin_post_type_actions() {
		// Register the callbacks for processing action requests.
		$post_types = $this->plugin->wps_post_types->get_post_types();
		foreach ( $post_types as $slug => $type ) {
			// Add a meta_box for each post_type
			add_action( "add_meta_boxes_{$slug}", array( $this, "add_soavis_information_meta_box" ) );

			$screen_id = 'edit-' . $slug;

			// Add manageble columns for each post_type
			add_filter( 'manage_' . $slug . '_posts_columns',         array( $this, 'manage_soavis_posts_columns'         ), 10, 1 );
			add_action( 'manage_' . $slug . '_posts_custom_column',   array( $this, 'manage_soavis_posts_custom_column'   ), 10, 2 );
			add_action( 'manage_' . $screen_id . '_sortable_columns', array( $this, 'manage_soavis_post_sortable_columns' ), 10    );
		}
	}

	/**
	 * Handle the LIST action for the dashboard.
	 *
	 */
	public function handle_post_soavis_action_list() {
		$this->debugMP('pr', __FUNCTION__ . ' REQUEST:', $_REQUEST);
	}

	/**
	 * Handle the ADD action for the dashboard.
	 *
	 */
	public function handle_post_soavis_action_add() {
		$this->debugMP('pr', __FUNCTION__ . ' REQUEST:', $_REQUEST);
	}

	/**
	 * Handle the EDIT action for the dashboard.
	 *
	 */
	public function handle_post_soavis_action_edit() {
		$this->debugMP('pr', __FUNCTION__ . ' REQUEST:', $_REQUEST);
	}

	/**
	 * Handle the OPTIONS action for the dashboard.
	 *
	 */
	public function handle_post_soavis_action_options() {
		$this->debugMP('pr', __FUNCTION__ . ' REQUEST:', $_REQUEST);
	}

	/**
	 * Handle the EXPORT action for the dashboard.
	 *
	 */
	public function handle_post_soavis_action_export() {
		$this->debugMP('pr', __FUNCTION__ . ' REQUEST:', $_REQUEST);
	}

	/**
	 * Handle the IMPORT action for the dashboard.
	 *
	 */
	public function handle_post_soavis_action_import() {
		$this->debugMP('pr', __FUNCTION__ . ' REQUEST:', $_REQUEST);
	}

	/**
	 * Manage edit columns for soavis post_types
	 *
	 * @param array $columns
	 */
	public function manage_soavis_posts_columns( $columns ) {

		$new_columns = array();

		// Add the new columns before the Author column
		foreach ( $columns as $name => $label ) {
			if ( 'author' == $name ) {
				foreach ($this->plugin->wps_post_types->info_box_params as $key => $value) {
					if ($value['show_column']) {
						$new_columns[$key] = $value['label'];
					}
				}
			}

			$new_columns[ $name ] = $label;
		}

		$columns = $new_columns;
		$this->debugMP('pr', __FUNCTION__ . ' columns:', $columns);

		return $columns;
	}

	/**
	 * Manage edit sortable columns
	 *
	 * @param array $columns
	 */
	public function manage_soavis_post_sortable_columns( $columns ) {
		foreach ($this->plugin->wps_post_types->info_box_params as $key => $value) {
			if ($value['show_column']) {
				$columns[$key] = $key;
			}
		}

		return $columns;
	}

	/**
	 * Manage posts custom column
	 *
	 * @param string $column_name
	 * @param string $post_id
	 */
	public function manage_soavis_posts_custom_column( $column_name, $post_id ) {
//		$post = get_post( $post_id );
//		$this->debugMP('pr', __FUNCTION__ . ' column_name: [' . $column_name . '] , info_box_params', $this->plugin->wps_post_types->info_box_params);
//		$this->debugMP('pr', __FUNCTION__ . ' in_array: [' . in_array($column_name, $this->plugin->wps_post_types->info_box_params) . '] , info_box_params', $this->plugin->wps_post_types->info_box_params[$column_name]);

		// Check whether this value is present in the list of info_box_params
		if (isset($this->plugin->wps_post_types->info_box_params[$column_name])) {
			$value = get_post_meta( $post_id, $this->plugin->wps_post_types->info_box_params[$column_name]['post_meta'], true );
			echo '<span>' . $value . '</span>';
		}
	}

	/**
	 * Registers a meta box for entering soavis information. The meta box is
	 * shown in the post editor for the "soavis" post types.
	 *
	 * @param   $post   WP_Post The post object to apply the meta box to
	 */
	public function add_soavis_information_meta_box( $post ) {
		$this->debugMP('pr', __FUNCTION__ . ' REQUEST:', $_REQUEST);
		add_meta_box(
			'soavis-information-meta-box',
			__( 'SoaVis Information', $this->plugin_name ),
			array ( $this, 'render_soavis_information_meta_box' ),
			$_REQUEST['post_type'],
			'side',
			'default'
		);
	}

	/**
	 * Renders the soavis information meta box for the given post (soavis).
	 *
	 * @param $post     WP_Post     The WordPress post object being rendered.
	 */
	public function render_soavis_information_meta_box( $post ) {
		$this->debugMP('pr', __FUNCTION__ . ' REQUEST:', $_REQUEST);

		// The data for the meta box fields (rendered in the partial)
		$soavis_meta_box_output = '';
		foreach ($this->plugin->wps_post_types->info_box_params as $key => $value) {
			$post_meta_value = get_post_meta( $post->ID, $value['post_meta'], true );
			$soavis_meta_box_output .= '<p>';
			$soavis_meta_box_output .= '<label for="' . $value['label'] . '">';
			$soavis_meta_box_output .= $value['label'];
			$soavis_meta_box_output .= '</label>';
			$soavis_meta_box_output .= '<input type="text" id="' . $key . '" name="' . $key . '" value="' . $post_meta_value . '" size="25" >';
			$soavis_meta_box_output .= '</p>';
		}

		$this->render_nonce_field( 'soavis_meta_box' );

		// Display the form
		echo $soavis_meta_box_output;
	}

	/**
	 * Saves the soavis information meta box contents.
	 *
	 * @param $post_id  int     The id of the post being saved.
	 */
	public function save_soavis_information_meta_box( $post_id ) {
		$this->debugMP('pr', __FUNCTION__ . ' REQUEST:', $_REQUEST);

		// Check nonce
		if ( !$this->is_nonce_ok( 'soavis_meta_box' ) ) {
			return $post_id;
		}

		// Ignore auto saves
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions
		if ( !current_user_can( 'edit_posts', $post_id ) ) {
			return $post_id;
		}

		// Sanitize user input
		foreach ($this->plugin->wps_post_types->info_box_params as $key => $value) {
			$meta_value = sanitize_text_field( $_POST[$key] );
			update_post_meta( $post_id, $value['post_meta'], $meta_value );
		}
	}

	/**
	 * A helper function for creating and rendering a nonce field.
	 *
	 * @param   $nonce_label  string  An internal (shorter) nonce name
	 */
	private function render_nonce_field( $nonce_label ) {
		$nonce_field_name = $this->plugin_name . '_' . $nonce_label . '_nonce';
		$nonce_name = $this->plugin_name . '_' . $nonce_label;

		wp_nonce_field( $nonce_name, $nonce_field_name );
	}

	/**
	 * A helper function for checking the soavis meta box nonce.
	 *
	 * @param   $nonce_label string  An internal (shorter) nonce name
	 * @return  mixed   False if nonce is not OK. 1 or 2 if nonce is OK (@see wp_verify_nonce)
	 */
	private function is_nonce_ok( $nonce_label ) {
		$nonce_field_name = $this->plugin_name . '_' . $nonce_label . '_nonce';
		$nonce_name = $this->plugin_name . '_' . $nonce_label;

		if ( !isset( $_POST[ $nonce_field_name ] ) ) {
			return false;
		}

		$nonce = $_POST[ $nonce_field_name ];

		return wp_verify_nonce( $nonce, $nonce_name );
	}

	/**
	 * An action function that adds a dropdown to insert a shortcode in the editor
	 *
	 */
	public function wps_action_media_buttons(){

		$shortcodes_list = '';

		$shortcodes_list .= '&nbsp;<select id="sc_select"><option>' . __('SoaVis Shortcode', $this->plugin_name) . '</option>';

		$wp_soavis_shortcodes = $this->plugin->wps_shortcodes->get_shortcodes();
		foreach ($wp_soavis_shortcodes as $shortcode) {
			$shortcodes_list .= '<option value="[' . $shortcode['label'] . ']">' . $shortcode['label'] . '</option>';
		}

		$shortcodes_list .= '</select>';

		echo $shortcodes_list;
	}

	/**
	 * An action function that adds a dropdown to insert a shortcode in the editor
	 *
	 */
	function wps_action_admin_head() {
			echo '<script type="text/javascript">
			jQuery(document).ready(function(){
			   jQuery("#sc_select").change(function() {
							  send_to_editor(jQuery("#sc_select :selected").val());
							  return false;
					});
			});
			</script>';
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
		if ($hdr != '') { $hdr = ' Admin: ' . $hdr; }
		$this->plugin->debugMP($type,$hdr,$msg,NULL,NULL,true);
	}

}
