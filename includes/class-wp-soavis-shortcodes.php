<?php

/**
 * Register all shortcode functionality for the plugin.
 *
 * @link       http://www.soavis.eu
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/shared
 */

/**
 * Register all shortcode functionality for the plugin.
 *
 * Defines the plugin name, version, and the hooks for
 * WP_SoaVis shortcodes.
 *
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/shared
 * @author     De B.A.A.T. <WP_SoaVis@de-baat.nl>
 */
class WP_SoaVis_Shortcodes {

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
	 * Definitions of the shortcodes for SoaVis.
	 *
	 * @var array
	 */
	public $shortcodes = array();

	/**
	 * Definitions of the fields to show for the list of SoaVis post_types.
	 *
	 * @var array
	 */
	public $post_type_fields = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @var      string    $main_plugin The object of this plugin.
	 */
	public function __construct( $main_plugin ) {

		$this->plugin      = $main_plugin;
		$this->plugin_name = $main_plugin->get_plugin_name();
		$this->version     = $main_plugin->get_version();

		// Set some variables
		$this->menu_title = __('WP SoaVis Shortcodes', $this->plugin_name);
		$this->capability = 'edit_theme_options';
		$this->menu_slug = 'wp-soavis-shortcodes';
		$this->count = 1;

		// we are on WP "init" hook already
		$this->wps_define_shortcodes();
		$this->wps_register_shortcodes();
		$this->post_type_fields = array(
				'ID'            => 'ID',
				'post_name'     => 'Name',
				'post_title'    => 'Title',
				'post_date'     => 'Date',
			);

		$this->shortcode_options = get_option('wp_soavis_shortcode_options');
		if (!isset($this->shortcode_options) || !is_array($this->shortcode_options)) {
			$this->shortcode_options = array();
		}
		$this->shortcode_options = array_merge(
			array(
				'adhoc_wareas' => 5,
				'adhoc_column_counts' => array(
					1 => 1,
					2 => 1,
					3 => 1,
					4 => 1,
					5 => 1,
				)
			),
			$this->shortcode_options
		);

		// Add a menu entry to the WP_SoaVis plugin menu
//        add_filter('add_wp_soavis_menu_items',array($this,'add_menu_items'),90);

	}

	/**
	 * Get all shortcodes defined for WP SoaVis
	 *
	 * @return $shortcodes[]
	 */
	function wps_define_shortcodes() {
		$this->shortcodes = array(
			'soavis_graph' => array(
				'label'       => 'SoaVis_Graph',
				'description' => __('The shortcode to render a graph of service dependencies specified in the DOT language. ', $this->plugin_name) .
									__('By default, this shortcode shows the dependencies of the current post. ', $this->plugin_name) .
									__('If the current post is a service, this shortcode shows the dependencies of this current service. ', $this->plugin_name) .
									__('For any other post type, this shortcode shows the dependencies of this current post. ', $this->plugin_name),
				'class'       => $this,
				'function'    => 'wp_soavis_graph_shortcode',
				'parameters'  => '<ul>' .
									'<li>' .
									__('<strong>title</strong>="Title text"', $this->plugin_name) .
									'<br/>' .
									__('A text string that is used as title for this list. ', $this->plugin_name) .
									__('Default value is Dependencies Graph.', $this->plugin_name) .
									'</li>' .
									'<li>' .
									__('<strong>alignment</strong>="&lt;slug&gt;"', $this->plugin_name) .
									'<br/>' .
									__('Determines the location of the box, similar to the alignment of media. ', $this->plugin_name) .
									__('Valid values are: <code>none</code>, <code>left</code>, <code>center</code>, <code>right</code>. ', $this->plugin_name) .
									__('Default value is: <code>left</code>.', $this->plugin_name) .
									'</li>' .
									'<li>' .
									__('<strong>start_nodes</strong>="&lt;slug&gt;"', $this->plugin_name) .
									'<br/>' .
									__('With this value, a list can be defined to be used as starting point for the graph tree. ', $this->plugin_name) .
									__('The value should be a comma separated list of post IDs or titles. ', $this->plugin_name) .
									__('Default value is empty, using the current post as starting node. ', $this->plugin_name) .
									'</li>' .
									'<li>' .
									__('<strong>show_list</strong>="&lt;slug&gt;"', $this->plugin_name) .
									'<br/>' .
									__('Determines whether to show a list of dependencies. ', $this->plugin_name) .
									__('Default value is: <code>NO</code>, showing NO list. ', $this->plugin_name) .
									__('Any other value will show a list. ', $this->plugin_name) .
									'</li>' .
								 '</ul>',
			),
			'soavis_list' => array(
				'label'       => 'SoaVis_List',
				'description' => __('The shortcode to render a list of SoaVis entities, optionally filtered.', $this->plugin_name),
				'class'       => $this,
				'function'    => 'wp_soavis_list_shortcode',
				'parameters'  => '<ul>' .
									'<li>' .
									__('<strong>type</strong>="&lt;slug&gt;"', $this->plugin_name) .
									'<br/>' .
									__('The <code>slug</code> of the SoaVis type to show. ', $this->plugin_name) .
									__('Valid values are: ', $this->plugin_name) .
									__('<code>Service</code>, <code>Project</code>, <code>System</code>, <code>Chain</code>, <code>Component</code>. ', $this->plugin_name) .
									__('Default value is: <code>Service</code>.', $this->plugin_name) .
									'</li>' .
									'<li>' .
									__('<strong>title</strong>="Title text"', $this->plugin_name) .
									'<br/>' .
									__('A text string that is used as title for this list. ', $this->plugin_name) .
									__('Default value is empty, thus showing no title.', $this->plugin_name) .
									'</li>' .
									'<li>' .
									__('<strong>filter</strong>="&lt;slug&gt;"', $this->plugin_name) .
									'<br/>' .
									__('A set of parameters that can be used to filter the list.', $this->plugin_name) .
									'</li>' .
//									'<li>' .
//									__('<strong>table_class</strong>="&lt;slug&gt;"', $this->plugin_name) .
//									'<br/>' .
//									__('The <code>class</code> to format the looks of the table to show.', $this->plugin_name) .
//									'</li>' .
								 '</ul>',
			),
			'soavis_dependencies' => array(
				'label'       => 'SoaVis_Dependencies',
				'description' => __('Renders a list of services this entity dependends on.', $this->plugin_name),
				'class'       => $this,
				'function'    => 'wp_soavis_dependencies_shortcode',
				'parameters'  => '<ul>' .
									'<li>' .
									__('<strong>title</strong>="Title text"', $this->plugin_name) .
									'<br/>' .
									__('A text string that is used as title for this list. ', $this->plugin_name) .
									__('Default value is Dependencies.', $this->plugin_name) .
									'</li>' .
									'<li>' .
									__('<strong>alignment</strong>="&lt;slug&gt;"', $this->plugin_name) .
									'<br/>' .
									__('Determines the location of the box, similar to the alignment of media. ', $this->plugin_name) .
									__('Valid values are: <code>none</code>, <code>left</code>, <code>center</code>, <code>right</code>. ', $this->plugin_name) .
									__('Default value is: <code>left</code>.', $this->plugin_name) .
									'</li>' .
								 '</ul>',
			),
			'soavis_dependencies_up' => array(
				'label'       => 'SoaVis_Dependencies_Up',
				'description' => __('Renders a list of services that depend on this service.', $this->plugin_name),
				'class'       => $this,
				'function'    => 'wp_soavis_dependencies_up_shortcode',
				'parameters'  => '<ul>' .
									'<li>' .
									__('<strong>title</strong>="Title text"', $this->plugin_name) .
									'<br/>' .
									__('A text string that is used as title for this list. ', $this->plugin_name) .
									__('Default value is Dependencies.', $this->plugin_name) .
									'</li>' .
									'<li>' .
									__('<strong>alignment</strong>="&lt;slug&gt;"', $this->plugin_name) .
									'<br/>' .
									__('Determines the location of the box, similar to the alignment of media. ', $this->plugin_name) .
									__('Valid values are: <code>none</code>, <code>left</code>, <code>center</code>, <code>right</code>. ', $this->plugin_name) .
									__('Default value is: <code>left</code>.', $this->plugin_name) .
									'</li>' .
								 '</ul>',
			),
			'soavis_info_box' => array(
				'label'       => 'SoaVis_Info_Box',
				'description' => __('Renders a box with information.', $this->plugin_name),
				'class'       => $this,
				'function'    => 'wp_soavis_info_box_shortcode',
				'parameters'  => '<ul>' .
									'<li>' .
									__('<strong>title</strong>="Title text"', $this->plugin_name) .
									'<br/>' .
									__('A text string that is used as title for this list. ', $this->plugin_name) .
									__('Default value is Info Box.', $this->plugin_name) .
									'</li>' .
									'<li>' .
									__('<strong>alignment</strong>="&lt;slug&gt;"', $this->plugin_name) .
									'<br/>' .
									__('Determines the location of the box, similar to the alignment of media. ', $this->plugin_name) .
									__('Valid values are: <code>none</code>, <code>left</code>, <code>center</code>, <code>right</code>. ', $this->plugin_name) .
									__('Default value is: <code>right</code>.', $this->plugin_name) .
									'</li>' .
								 '</ul>',
			),
			'soavis_references' => array(
				'label'       => 'SoaVis_References',
				'description' => __('Renders a list of references.', $this->plugin_name),
				'class'       => $this,
				'function'    => 'wp_soavis_references_shortcode',
				'parameters'  => '<ul>' .
									'<li>' .
									__('<strong>title</strong>="Title text"', $this->plugin_name) .
									'<br/>' .
									__('A text string that is used as title for this list. ', $this->plugin_name) .
									__('Default value is References.', $this->plugin_name) .
									'</li>' .
									'<li>' .
									__('<strong>alignment</strong>="&lt;slug&gt;"', $this->plugin_name) .
									'<br/>' .
									__('Determines the location of the box, similar to the alignment of media. ', $this->plugin_name) .
									__('Valid values are: <code>none</code>, <code>left</code>, <code>center</code>, <code>right</code>. ', $this->plugin_name) .
									__('Default value is: <code>right</code>.', $this->plugin_name) .
									'</li>' .
								 '</ul>',
			),
		);

	}

	/**
	 * Register the shortcodes to be used by SoaVis.
	 *
	 * @return $shortcodes[]
	 */
	function wps_register_shortcodes() {

		//add_shortcode('wp-soavis-categories', array(&$this, 'list_categories'));
		//add_shortcode('wp-soavis-the-post', array(&$this, 'the_post'));
		$wp_soavis_shortcodes = $this->get_shortcodes();
		foreach ($wp_soavis_shortcodes as $shortcode) {

			// Also define shortcode with all lower case and all upper case letters
			$shortcode_lc = strtolower($shortcode['label']);
			$shortcode_uc = strtoupper($shortcode['label']);

			add_shortcode($shortcode['label'], array($this, $shortcode['function']));
			add_shortcode($shortcode_lc,       array($this, $shortcode['function']));
			add_shortcode($shortcode_uc,       array($this, $shortcode['function']));
		}

	}

	/**
	 * The shortcode to render a box of SoaVis dependencies.
	 *
	 * @param  $attr
	 * @param  $content
	 * @return string
	 */
	function wp_soavis_graph_shortcode($attr, $content) {
		$this->debugMP('msg',__FUNCTION__);

		$wps_shortcode_output = '';
		$wps_shortcode_output_text = '';
		$wps_shortcode_output_list = '';

		// Get the shortcode_attributes
		$wps_atts = shortcode_atts(array(
			'title'       => 'Dependencies Graph',
			'alignment'   => 'left',
			'show_list'   => 'NO',
			'show_text'   => 'NO',
		), $attr);

		// Check value of alignment
		$alignment = $this->check_alignment($wps_atts['alignment']);
		$this->debugMP('msg',__FUNCTION__ . ' alignment = ' . $alignment);

		// Check whether the current post is defined
		$wps_post = get_post();

		$wps_shortcode_output_list .= '<div id="wp_soavis_info_box" class="' . $alignment . '">';

		// Generate the optional title
		if ($wps_atts['title']) {
			$wps_shortcode_output_list .= '<h2>' . __('Graph Dependencies List', $this->plugin_name) . '</h2>';
		}

		// Generate the info box itself
		if (!$wps_post) {
			$wps_shortcode_output_list .= __('No dependencies found.', $this->plugin_name);
		} else {
			$wps_shortcode_output_text = $this->plugin->wps_graphviz->get_graphviz_output($wps_post->ID);
			$wps_nodes_list = $this->plugin->wps_graphviz->node_list;

//			$this->debugMP('pr',__FUNCTION__ . ' wps_post', $wps_post);
//			$this->debugMP('pr',__FUNCTION__ . ' ServiceDependencies', $wps_dependencies);

			$wps_shortcode_output_list .= '<table id="wp_soavis_shortcodes_table" class="wp-soavis wp-list-table widefat fixed posts" cellspacing="0">';
			//$wps_shortcode_output .= $this->wps_generate_soavis_box_header(__('Service', $this->plugin_name), __('Visibility', $this->plugin_name));
			$wps_shortcode_output_list .= '<tbody>';

//			if (count($wps_dependencies) == 0) {
			if ( $wps_nodes_list == false) {
				$wps_shortcode_output_list .= '<tr><td>';
				$wps_shortcode_output_list .= __('No dependencies found.', $this->plugin_name);
				$wps_shortcode_output_list .= '</td></tr>';
			} else {

				// Generate the table showing the info box items
					foreach ($wps_nodes_list as $ref_title => $ref_node) {
//						$ref_post = get_page_by_title($ref_title, 'OBJECT', 'soavis_service');
						if ($ref_node) {
							$ref_link = $ref_node['type'] . ': <a href="' . $ref_node['url'] . '">' . $ref_node['post_title'] . ' (' . $ref_node['ID'] . ')</a>';
							$ref_direction = $ref_node['direction'] . ' (' . $ref_node['level'] . ')';
							$wps_shortcode_output_list .= $this->wps_generate_soavis_box_row($ref_link, $ref_direction);
						} else {
							$wps_shortcode_output_list .= $this->wps_generate_soavis_box_row($ref_title, 'Not found');
						}
					}
			}
			$wps_shortcode_output_list .= '</tbody>';
			$wps_shortcode_output_list .= '</table>';
		}
		$wps_shortcode_output_list .= '</div>';

		// Generate the optional title
		if ($wps_atts['title']) {
			$wps_shortcode_output .= '<h2>' . $wps_atts['title'] . '</h2>';
		}

		// Generate a WP_GraphViz shortcode command
		$wps_graphviz_input   = '';
		$wps_graphviz_input  .= '[WP_GraphViz type="digraph"]';
		$wps_graphviz_input  .= $wps_shortcode_output_text;
		$wps_graphviz_input  .= '[/WP_GraphViz]';
		$wps_graphviz_output  = do_shortcode($wps_graphviz_input);

		$wps_shortcode_output .= '<div id="wp_soavis_graph_box" class="' . $alignment . '">';
		$wps_shortcode_output .= '<div id="wp_soavis_graph_box_inner" class="alignleft">';
		$wps_shortcode_output .= $wps_graphviz_output;
		$wps_shortcode_output .= '</div>';
//		$wps_shortcode_output .= '<br style="clear: both;" />';
		$wps_shortcode_output .= '<div class="clearfix">';
		$wps_shortcode_output .= '</div>';
		$wps_shortcode_output .= '</div>';

		// Generate the optional text if requested
		if ($wps_atts['show_text'] != 'NO') {
			$wps_shortcode_output .= '<br/>';
			$wps_shortcode_output .= '<h3>' . __('Graph Dependencies Text', $this->plugin_name) . '</h3>';
			$wps_shortcode_output .= $wps_shortcode_output_text;
		}

		// Generate the optional list if requested
		if ($wps_atts['show_list'] != 'NO') {
			$wps_shortcode_output .= '<br/>';
			$wps_shortcode_output .= $wps_shortcode_output_list;
		}

		return $wps_shortcode_output;

	}

	/**
	 * The shortcode to render a box of SoaVis dependencies.
	 *
	 * @param  $attr
	 * @param  $content
	 * @return string
	 */
	function wp_soavis_dependencies_shortcode($attr, $content) {
		$this->debugMP('msg',__FUNCTION__);

		$wps_shortcode_output = '';

		// Get the shortcode_attributes
		$wps_atts = shortcode_atts(array(
			'title'       => 'Dependencies',
			'alignment'   => 'left',
		), $attr);

		// Check value of alignment
		$alignment = $this->check_alignment($wps_atts['alignment']);
		$this->debugMP('msg',__FUNCTION__ . ' alignment = ' . $alignment);

		// Check whether the current post is defined
		$wps_post = get_post();

		$wps_shortcode_output .= '<div class="clearfix">';
		$wps_shortcode_output .= '</div>';
		$wps_shortcode_output .= '<div id="wp_soavis_info_box" class="' . $alignment . '">';

		// Generate the optional title
		if ($wps_atts['title']) {
			$wps_shortcode_output .= '<h2>' . $wps_atts['title'] . '</h2>';
		}

		// Generate the info box itself
		if (!$wps_post) {
			$wps_shortcode_output .= __('No dependencies found.', $this->plugin_name);
		} else {
			$wps_dependencies = $this->get_service_dependencies_down($wps_post->ID);
			$this->debugMP('pr',__FUNCTION__ . ' wps_post', $wps_post);
			$this->debugMP('pr',__FUNCTION__ . ' ServiceDependencies', $wps_dependencies);

			$wps_shortcode_output .= '<table id="wp_soavis_shortcodes_table" class="wp-soavis wp-list-table widefat fixed posts" cellspacing="0">';
			//$wps_shortcode_output .= $this->wps_generate_soavis_box_header(__('Service', $this->plugin_name), __('Visibility', $this->plugin_name));
			$wps_shortcode_output .= '<tbody>';

//			if (count($wps_dependencies) == 0) {
			if ( $wps_dependencies == false) {
				$wps_shortcode_output .= '<tr><td>';
				$wps_shortcode_output .= __('No dependencies found.', $this->plugin_name);
				$wps_shortcode_output .= '</td></tr>';
			} else {

				// Generate the table showing the info box items
					foreach ($wps_dependencies as $ref_title => $ref_post) {
//						$ref_post = get_page_by_title($ref_title, 'OBJECT', 'soavis_service');
						if ($ref_post) {
							$ref_link = '<a href="' . get_page_link($ref_post->ID) . '">' . $ref_title . '</a>';
							$ref_status = get_post_meta( $ref_post->ID, '_soavis_visibility', true );
							$wps_shortcode_output .= $this->wps_generate_soavis_box_row($ref_link, $ref_status);
						} else {
							$wps_shortcode_output .= $this->wps_generate_soavis_box_row($ref_title, 'Not found');
						}
					}
			}
			$wps_shortcode_output .= '</tbody>';
			$wps_shortcode_output .= '</table>';
		}
		$wps_shortcode_output .= '</div>';

		return $wps_shortcode_output;

	}

	/**
	 * The shortcode to render a box of SoaVis dependencies.
	 *
	 * @param  $attr
	 * @param  $content
	 * @return string
	 */
	function wp_soavis_dependencies_up_shortcode($attr, $content) {
		$this->debugMP('msg',__FUNCTION__);

		$wps_shortcode_output = '';

		// Get the shortcode_attributes
		$wps_atts = shortcode_atts(array(
			'title'       => 'Dependencies Up',
			'alignment'   => 'left',
		), $attr);

		// Check value of alignment
		$alignment = $this->check_alignment($wps_atts['alignment']);
		$this->debugMP('msg',__FUNCTION__ . ' alignment = ' . $alignment);

		// Check whether the current post is defined
		$wps_post = get_post();

		$wps_shortcode_output .= '<div class="clearfix">';
		$wps_shortcode_output .= '</div>';
		$wps_shortcode_output .= '<div id="wp_soavis_info_box" width="100%" class="' . $alignment . '">';

		// Generate the optional title
		if ($wps_atts['title']) {
			$wps_shortcode_output .= '<h2>' . $wps_atts['title'] . '</h2>';
		}

		// Generate the info box itself
		if (!$wps_post) {
			$wps_shortcode_output .= __('No dependencies found.', $this->plugin_name);
		} else {
			$wps_dependencies = $this->get_service_dependencies_up($wps_post->post_title);
			$this->debugMP('pr',__FUNCTION__ . ' wps_post', $wps_post);
			$this->debugMP('pr',__FUNCTION__ . ' ServiceDependencies', $wps_dependencies);

			$wps_shortcode_output .= '<table id="wp_soavis_shortcodes_table" class="wp-soavis wp-list-table widefat fixed posts" cellspacing="0">';
			//$wps_shortcode_output .= $this->wps_generate_soavis_box_header(__('Service', $this->plugin_name), __('Visibility', $this->plugin_name));
			$wps_shortcode_output .= '<tbody>';

//			if (count($wps_dependencies) == 0) {
			if ( $wps_dependencies == false) {
				$wps_shortcode_output .= '<tr><td>';
				$wps_shortcode_output .= __('No dependencies found.', $this->plugin_name);
				$wps_shortcode_output .= '</td></tr>';
			} else {

				// Generate the table showing the info box items
					foreach ($wps_dependencies as $ref_title => $ref_post) {
//						$ref_post = get_page_by_title($ref_title, 'OBJECT', 'soavis_service');
						if ($ref_post) {
							$ref_link = '<a href="' . get_page_link($ref_post->ID) . '">' . $ref_title . '</a>';
							$ref_status = get_post_meta( $ref_post->ID, '_soavis_visibility', true );
							$wps_shortcode_output .= $this->wps_generate_soavis_box_row($ref_link, $ref_post->post_type);
						} else {
							$wps_shortcode_output .= $this->wps_generate_soavis_box_row($ref_title, 'Not found');
						}
					}
			}
			$wps_shortcode_output .= '</tbody>';
			$wps_shortcode_output .= '</table>';
		}
		$wps_shortcode_output .= '</div>';

		return $wps_shortcode_output;

	}

	/**
	 * The shortcode to render a box of SoaVis references.
	 *
	 * @param  $attr
	 * @param  $content
	 * @return string
	 */
	function wp_soavis_references_shortcode($attr, $content) {
		$this->debugMP('msg',__FUNCTION__);

		$wps_shortcode_output = '';

		// Get the shortcode_attributes
		$wps_atts = shortcode_atts(array(
			'title'       => 'References',
			'alignment'   => 'right',
		), $attr);

		// Check value of alignment
		$alignment = $this->check_alignment($wps_atts['alignment']);
		$this->debugMP('msg',__FUNCTION__ . ' alignment = ' . $alignment);

		// Check whether the current post is defined
		$wps_post = get_post();

		$wps_shortcode_output .= '<div class="clearfix">';
		$wps_shortcode_output .= '</div>';
		$wps_shortcode_output .= '<div id="wp_soavis_info_box" class="' . $alignment . '">';

		// Generate the optional title
		if ($wps_atts['title']) {
			$wps_shortcode_output .= '<h2>' . $wps_atts['title'] . '</h2>';
		}

		// Generate the info box itself
		if (!$wps_post) {
			$wps_shortcode_output .= __('No references found.', $this->plugin_name);
		} else {
			$wps_references = get_post_meta($wps_post->ID, 'RelatedDocument', false);
			$this->debugMP('pr',__FUNCTION__ . ' wps_post', $wps_post);
			$this->debugMP('pr',__FUNCTION__ . ' RelatedDocuments', $wps_references);

			// Generate the table showing the info box items
			$wps_shortcode_output .= '<table id="wp_soavis_shortcodes_table" class="wp-soavis wp-list-table widefat fixed posts" cellspacing="0">';
			//$wps_shortcode_output .= $this->wps_generate_soavis_box_header(__('Reference', $this->plugin_name), __('Link', $this->plugin_name));
			$wps_shortcode_output .= '<tbody>';

			if (count($wps_references) == 0) {
				$wps_shortcode_output .= '<tr><td>';
				$wps_shortcode_output .= __('No references found.', $this->plugin_name);
				$wps_shortcode_output .= '</td></tr>';
			} else {
				foreach ($wps_references as $ref_title) {
					$ref_post = get_page_by_title($ref_title, 'OBJECT', 'attachment');
					if ($ref_post) {
						$wps_shortcode_output .= $this->wps_generate_soavis_box_row(wp_get_attachment_link($ref_post->ID), wp_get_attachment_link($ref_post->ID, 'thumbnail', false, true));
					} else {
						$wps_shortcode_output .= $this->wps_generate_soavis_box_row($ref_title, 'Not found');
					}
				}
			}
			$wps_shortcode_output .= '</tbody>';
			$wps_shortcode_output .= '</table>';
		}
		$wps_shortcode_output .= '</div>';

		return $wps_shortcode_output;

	}

	/**
	 * The shortcode to render a box of SoaVis info.
	 *
	 * @param  $attr
	 * @param  $content
	 * @return string
	 */
	function wp_soavis_info_box_shortcode($attr, $content) {
		$this->debugMP('msg',__FUNCTION__);

		$wps_shortcode_output = '';

		// Get the shortcode_attributes
		$wps_atts = shortcode_atts(array(
			'title'       => 'Info Box',
			'alignment'   => 'right',
		), $attr);

		// Check value of alignment
		$alignment = $this->check_alignment($wps_atts['alignment']);
		$this->debugMP('msg',__FUNCTION__ . ' alignment = ' . $alignment);

		// Check whether the current post is defined
		$wps_post = get_post();

		$wps_shortcode_output .= '<div class="clearfix">';
		$wps_shortcode_output .= '</div>';
		$wps_shortcode_output .= '<div id="wp_soavis_info_box" class="' . $alignment . '">';

		// Generate the optional title
		if ($wps_atts['title']) {
			$wps_shortcode_output .= '<h2>' . $wps_atts['title'] . '</h2>';
		}

		// Generate the info box itself
		if (!$wps_post) {
			$wps_shortcode_output .= __('No post found.', $this->plugin_name);
		} else {
			$this->debugMP('pr',__FUNCTION__ . ' wps_post', $wps_post);

			// Generate the table showing the info box items
			$wps_shortcode_output .= '<table id="wp_soavis_shortcodes_table" class="wp-soavis wp-list-table widefat fixed posts" cellspacing="0">';
			//$wps_shortcode_output .= $this->wps_generate_soavis_box_header(__('Item', $this->plugin_name), __('Value', $this->plugin_name));
			$wps_shortcode_output .= '<tbody>';
				foreach ($this->plugin->wps_post_types->info_box_params as $key => $value) {
					$post_meta_value = get_post_meta( $wps_post->ID, $value['post_meta'], true );
					$wps_shortcode_output .= $this->wps_generate_soavis_box_row($value['label'], $post_meta_value);
				}
			$wps_shortcode_output .= '</tbody>';
			$wps_shortcode_output .= '</table>';
		}
		$wps_shortcode_output .= '</div>';

		return $wps_shortcode_output;

	}

	/**
	 * The shortcode to render a list of SoaVis entities, optionally filtered.
	 *
	 * @param  $attr
	 * @param  $content
	 * @return string
	 */
	function wp_soavis_list_shortcode($attr, $content) {
		$this->debugMP('msg',__FUNCTION__);

		$wps_shortcode_output = '';

		// Get the shortcode_attributes
		$wps_atts = shortcode_atts(array(
			'type'        => 'soavis_service',
			'filter'      => '',
			'title'       => '',
			'table_class' => '',
		), $attr);

		// Check value of type to start with soavis_
		$wps_atts['type'] = strtolower($wps_atts['type']);
		$this->debugMP('msg',__FUNCTION__ . ' strpos = ' . strpos($wps_atts['type'], 'soavis_'));
		if (isset($wps_atts['type']) && (strpos($wps_atts['type'], 'soavis_') !== 0)) {
			$wps_atts['type'] = 'soavis_' . $wps_atts['type'];
		}

		// Build the output
		if ($wps_atts['title']) {
			$wps_shortcode_output .= '<h2>' . $wps_atts['title'] . '</h2>';
		}

		$this->debugMP('pr',__FUNCTION__ . ' attributes', $wps_atts);
		$this->debugMP('msg',__FUNCTION__ . ' content', esc_html($content));

		// Get all posts for the requested type
		$wps_posts = get_posts(
			array(
				'orderby' 		   => 'post_title',
				'order'            => 'ASC',
				'post_type'        => $wps_atts['type'],
				'post_status'      => 'publish',
				'nopaging'         => true,
				'suppress_filters' => true
			)
		);
		$this->debugMP('pr',__FUNCTION__ . ' # wps_posts = ' . count($wps_posts), $wps_posts);

		// Generate the output for the posts found
		$wps_shortcode_output .= ' # wps_posts = ' . count($wps_posts) . '<br/>';
		$wps_shortcode_output .= $this->wps_generate_soavis_list($wps_posts, $wps_atts['type']);

		return $wps_shortcode_output;

	}

	/**
	 * Generate the list for the SoaVis posts found.
	 *
	 * @param array WP_Post $wps_posts
	 * @param string $type
	 * @return string The generated output
	 */
	public function wps_generate_soavis_list( $wps_posts, $type = '' ) {

		$wps_list_output = '';

		// Check number of posts found and generate display accordingly
		if (!$wps_posts || (count($wps_posts) == 0)) {
			$wps_list_output .= sprintf(__('No entities found for type %s.', $this->plugin_name), $type);
		} else {
			$wps_list_output .= '<div id="wp_soavis_table_wrapper">';
			$wps_list_output .= '<table id="wp_soavis_shortcodes_table" class="wp-soavis wp-list-table widefat fixed posts" cellspacing="0">';
			$wps_list_output .= '<thead>';
				$wps_list_output .= $this->wps_generate_soavis_list_header();
			$wps_list_output .= '</thead>';
			$wps_list_output .= '<tbody>';
				foreach ($wps_posts as $cur_post) {
					$wps_list_output .= $this->wps_generate_soavis_list_row( $cur_post );
				}
			$wps_list_output .= '</tbody>';
			$wps_list_output .= '</table>';
			$wps_list_output .= '</div>';
		}

		return $wps_list_output;
	}

	/**
	 * Generate the list header for the SoaVis posts found.
	 *
	 * @param string $label the label to show for the row
	 * @param string $value the value to show for the row
	 * @return string The generated output
	 */
	public function wps_generate_soavis_box_header( $label, $value ) {

		$wps_box_output = '';

		$wps_box_output .= '<thead>';
			$wps_box_output .= '<tr>';
				$wps_box_output .= '<th class="wp_soavis_shortcodes_cell">';
				$wps_box_output .= $label;
				$wps_box_output .= '</th>';
				$wps_box_output .= '<th class="wp_soavis_shortcodes_cell">';
				$wps_box_output .= $value;
				$wps_box_output .= '</th>';
			$wps_box_output .= '</tr>';
		$wps_box_output .= '</thead>';

		return $wps_box_output;
	}

	/**
	 * Generate the list row for the SoaVis posts found.
	 *
	 * @param string $label the label to show for the row
	 * @param string $value the value to show for the row
	 * @return string The generated output
	 */
	public function wps_generate_soavis_box_row( $label, $value ) {

		$wps_box_output = '';

		$wps_box_output .= '<tr>';
			$wps_box_output .= '<td>' . $label . '</td>';
			$wps_box_output .= '<td>' . $value . '</td>';
		$wps_box_output .= '</tr>';

		return $wps_box_output;
	}

	/**
	 * Generate the list header for the SoaVis posts found.
	 *
	 * @return string The generated output
	 */
	public function wps_generate_soavis_list_header() {

		$wps_list_output = '';

		// Check number of posts found and generate display accordingly
		if ((count($this->post_type_fields) == 0)) {
			$wps_list_output .= __('No header information to display.', $this->plugin_name);
		} else {
			$wps_list_output .= '<tr class="wp_soavis_shortcodes_row">';
			foreach ($this->post_type_fields as $key => $title) {
				$wps_list_output .= '<th class="wp_soavis_shortcodes_cell">';
				$wps_list_output .= $title;
				$wps_list_output .= '</th>';
			}
			$wps_list_output .= '</tr>';
		}

		return $wps_list_output;
	}

	/**
	 * Generate the list row for the SoaVis posts found.
	 *
	 * @param WP_Post $cur_post
	 * @return string The generated output
	 */
	public function wps_generate_soavis_list_row( $cur_post ) {

		$wps_list_output = '';
		$row_style       = 'even';

		// Check number of posts found and generate display accordingly
		if ((count($this->post_type_fields) == 0)) {
			$wps_list_output .= sprintf(__('No information to display for post %s.', $this->plugin_name), $cur_post->ID);
		} else {
			$wps_list_output .= '<tr class="wp_soavis_shortcodes_row ' . $row_style . '">';
			foreach ($this->post_type_fields as $key => $title) {
				$wps_list_output .= '<td class="wp_soavis_shortcodes_cell">';
				switch ($key) {
					case 'post_name':
						$wps_list_output .= '<a href="';
						$wps_list_output .= get_post_permalink($cur_post->ID);
						$wps_list_output .= '">';
						$wps_list_output .= $cur_post->post_name;
						$wps_list_output .= '</a>';
						break;
					default:
						$wps_list_output .= $cur_post->$key;
						break;
				}
				$wps_list_output .= '</td>';
			}
			$wps_list_output .= '</tr>';
		}

		return $wps_list_output;
	}

	/**
	 * Return the shortcodes that SoaVis uses.
	 *
	 * @return string $alignment The alignment found, based on the input
	 */
	public function check_alignment( $value_in ) {

		// Check value of alignment
		$value = strtolower($value_in);
		switch ($value) {
			case 'none':
				$alignment = 'none';
				break;
			case 'alignleft':
			case 'left':
			case 'leftalign':
				$alignment = 'alignleft';
				break;
			case 'aligncenter':
			case 'center':
			case 'centeralign':
				$alignment = 'aligncenter';
				break;
			default:
				$alignment = 'alignright';
				break;
		}

		return $alignment;
	}

	/**
	 * Return the services that this service references.
	 *
	 * @param string $post_id The ID of the service referencing
	 * @return mixed $wps_dependencies All dependencies for the post_id given.
	 */
	public function get_service_dependencies_down( $post_id = 0 ) {

		// Check post_id
		if ( $post_id == 0 ) {
			return false;
		}

		// Get the dependencies from the post_meta
		$wps_dependency_titles = get_post_meta($post_id, 'ServiceDependency', false);
		$this->debugMP('pr',__FUNCTION__ . ' ServiceDependency found wps_dependency_titles', $wps_dependency_titles);

		// Check dependency titles found
		if (count($wps_dependency_titles) == 0) {
			return false;
		}

		$wps_dependencies = array();

		// Get the post objects for each dependency found
		foreach ($wps_dependency_titles as $ref_title) {
			$ref_post = get_page_by_title($ref_title, 'OBJECT', 'soavis_service');
			if ($ref_post) {
				$wps_dependencies[$ref_title] = $ref_post;
			} else {
				$wps_dependencies[$ref_title] = false;
			}
		}

		return $wps_dependencies;

	}

	/**
	 * Return the services that reference this service.
	 *
	 * @param string $post_title The title of the service referenced
	 * @return mixed $wps_dependencies All dependencies for the post_id given.
	 */
	public function get_service_dependencies_up( $post_title = '' ) {

		// Check post_title
		if ( $post_title == '' ) {
			return false;
		}

		// Get the dependencies from the post_meta
		//$wps_dependency_ids = get_posts( 'meta_key=ServiceDependency&meta_value=' . $post_title . '&fields=ids' );
		global $wpdb;
		$wps_dependency_ids = $wpdb->get_results( "select post_id, meta_key from " . $wpdb->postmeta . " where meta_value = '" . $post_title . "'", OBJECT );

		$this->debugMP('pr',__FUNCTION__ . ' ServiceDependency ' . $post_title . ' found wps_dependency_ids', $wps_dependency_ids);

		// Check dependency titles found
		if (count($wps_dependency_ids) == 0) {
			return false;
		}

		$wps_dependencies = array();

		// Get the post objects for each dependency found
		foreach ($wps_dependency_ids as $ref_id) {
			$ref_post = get_post($ref_id->post_id, 'OBJECT', 'soavis_service');
			if ($ref_post) {
				$wps_dependencies[$ref_post->post_title] = $ref_post;
			} else {
				$wps_dependencies[$ref_id->post_id] = false;
			}
		}

		return $wps_dependencies;

	}

	/**
	 * Return the shortcodes that SoaVis uses.
	 *
	 * @return string The used Custom Post Type.
	 */
	public function get_shortcodes() {
		return $this->shortcodes;
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
		if ($hdr != '') { $hdr = ' SC: ' . $hdr; }
		$this->plugin->debugMP($type,$hdr,$msg,NULL,NULL,true);
	}

}
