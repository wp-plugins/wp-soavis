<?php
/**
 * Handle all graph functionality for the plugin.
 *
 * @link       http://www.soavis.eu
 *
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/includes
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Define some constans to help generate the GraphViz output
 */
define ('SOAVIS_GRAPHVIZ_IMAGE_RENDER_GGRAPH_START',	'strict digraph %s { size ="%s;" ');
define ('SOAVIS_GRAPHVIZ_IMAGE_RENDER_GRAPH_END',		' }');
define ('SOAVIS_GRAPHVIZ_IMAGE_RENDER_EDGE',			' -> ');
define ('SOAVIS_GRAPHVIZ_IMAGE_RENDER_EDGE_END',		'; ');
define ('SOAVIS_GRAPHVIZ_IMAGE_RENDER_LABEL',			'label="%s",');
define ('SOAVIS_GRAPHVIZ_IMAGE_RENDER_URL',				'URL="%s", target="_parent",');

define ('SOAVIS_NODE_FORMAT_DEFAULT_LAST',				'style=filled');

define ('SOAVIS_NODE_FORMAT_FILL_COLOR_START',			'fillcolor=orange,');
define ('SOAVIS_NODE_FORMAT_FILL_COLOR_DOWN',			'fillcolor=yellow,');
define ('SOAVIS_NODE_FORMAT_FILL_COLOR_UP',				'fillcolor=springgreen,');
define ('SOAVIS_NODE_FORMAT_FILL_COLOR_DEFAULT',		'fillcolor=grey,fontcolor=white,');

define ('SOAVIS_NODE_FORMAT_SHAPE_SERVICE',				'shape=box,');
define ('SOAVIS_NODE_FORMAT_SHAPE_SYSTEM_DOWN',			'shape=invtriangle,');
define ('SOAVIS_NODE_FORMAT_SHAPE_SYSTEM_UP',			'shape=triangle,');

define ('SOAVIS_NODE_FORMAT_START',						' [width="3" ');
define ('SOAVIS_NODE_FORMAT_END',						']; ');

/**
 * Handle all graph functionality for the plugin.
 *
 * Defines the plugin name, version, and the hooks for
 * WP_SoaVis Graph.
 *
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/includes
 * @author     De B.A.A.T. <WP_SoaVis@de-baat.nl>
 */
class WP_SoaVis_GraphViz {

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
	 * Definitions of the Node_Shapes for SoaVis.
	 *
	 * @var array() $node_shapes
	 */
	public $node_shapes = array();

	/**
	 * The list of nodes to show.
	 *
	 * @var array() $node_list
	 */
	public $node_list = array();

	/**
	 * The list of edges to show.
	 *
	 * @var array() $edge_list
	 */
	public $edge_list = array();

	/**
	 * Init the model.
	 *
	 * @var      string    $main_plugin The object of this plugin.
	 */
	public function __construct( $main_plugin ) {

		$this->plugin      = $main_plugin;
		$this->plugin_name = $main_plugin->get_plugin_name();

		// we are on WP "init" hook already
		$this->wps_define_node_shapes();

	}

	/**
	 * Define the Custom Post Type to be used by SoaVis.
	 *
	 */
	protected function wps_define_node_shapes() {
		$this->node_shapes = array(
			'soavis_service' => array(
				'slug'                => 'service',
				'name'                => __( 'Service', $this->plugin_name ),
				'shape'               => 'box',
				'fillcolor'           => 'orange',
				'fillcolordown'       => 'yellow',
				'fillcolorup'         => 'springgreen',
			),
		);
		/**
		 * Filter the "Custom Post Type" definitions that SoaVis uses for storing info in the database.
		 *
		 * @param string $post_type The "Custom Post Type" that SoaVis uses.
		 */
		$this->node_shapes = apply_filters( 'wp_soavis_node_shapes', $this->node_shapes );
	}

	/**
	 * Return the Custom Post Type that SoaVis uses.
	 *
	 * @return string The used Custom Post Type.
	 */
	public function get_node_shapes() {
		return $this->node_shapes;
	}

	/**
	 * Generate the list of nodes to generate the GraphViz image from
	 *
	 * @param mixed $post_ids An ID or array of IDs of the post(s) starting the tree
	 */
	public function get_graphviz_output( $post_ids ) {

		// Build the node_list_data first
		$post_list = $this->get_post_list_base($post_ids);
		$this->get_node_list_data($post_list);

		// Process the node_list to GraphViz output
		$graphviz_output  = '';
		$graphviz_output .= $this->get_graphviz_tree_text();

		return $graphviz_output;
	}

	/**
	 * Generate the list of start nodes to generate the GraphViz image from
	 *
	 * @param WP_Post $post_in The post object to start travel down the tree
	 */
	protected function get_post_list_base( $post_in ) {

		$post_list      = array();
		$post_list_base = array();

		// Get a valid list of posts
		if (is_array($post_ids)) {
			$args = array( 'posts_per_page' => -1, 'post__in ' => $post_ids, 'post_type=any' );
			$post_list = get_posts($args);
		} else {
			$post_list[] = get_post($post_ids);
		}
		$this->debugMP('pr',__FUNCTION__ . ' post_ids:', $post_ids);
		$this->debugMP('pr',__FUNCTION__ . ' post_list:', $post_list);

		// Create the service tree in both directions up and down
		foreach ($post_list as $cur_post) {

			if ($cur_post->post_type == 'soavis_service') {
				// Use the service as base node
				$post_list_base[$cur_post->ID] = $cur_post;
			} else {
				// Find the ServiceDependency references as base nodes
				$post_list_base = $this->get_post_dependencies_down($cur_post->ID,         $post_list_base);
				$post_list_base = $this->get_post_dependencies_up  ($cur_post->post_title, $post_list_base);
			}
		}
		$this->debugMP('pr',__FUNCTION__ . ' post_list_base generated:', $post_list_base);

		return $post_list_base;
	}

	/**
	 * Find the list of posts that are referenced by this post_id
	 *
	 * @param id $post_id The post id to find dependencies down for
	 * @param array $post_list The list of posts found thus far, needed to prevent doubles
	 */
	protected function get_post_dependencies_down( $post_id = '', $post_list ) {

		if ( $post_id == '' ) {
			return false;
		}

		// Find the ServiceDependency references as base nodes
		$wps_dependency_titles = get_post_meta($post_id, 'ServiceDependency', false);
		//$this->debugMP('pr',__FUNCTION__ . ' wps_dependency_titles:', $wps_dependency_titles);

		// Check dependency titles found
		if ($wps_dependency_titles != false) {

			// Get the post objects for each dependency found
			foreach ($wps_dependency_titles as $ref_title) {
				// Get the cur_post from this ref_title
				$new_post = get_page_by_title($ref_title, 'OBJECT', 'soavis_service');
				if (!isset($new_post)) {
					// If no service found, check for system
					$new_post = get_page_by_title($ref_title, 'OBJECT', 'soavis_system');
				}
				//$this->debugMP('pr',__FUNCTION__ . ' new_post found for ' . $ref_title . ' :', $new_post);
				if (isset($new_post)) {
					// Only process new_posts that are not processed yet
					if (!isset($post_list[$new_post->ID])){
						$post_list[$new_post->ID] = $new_post;
					}
				}
			}
		}
		$this->debugMP('pr',__FUNCTION__ . ' post_list generated:', $post_list);

		return $post_list;
	}

	/**
	 * Find the list of posts that reference this post_title
	 *
	 * @param string $post_title The title to find dependencies up for
	 * @param array $post_list The list of posts found thus far, needed to prevent doubles
	 */
	protected function get_post_dependencies_up( $post_title = '', $post_list ) {

		if ( $post_title == '' ) {
			return false;
		}

		// Find the ServiceDependency references as base nodes
		global $wpdb;
		$wps_dependency_ids = $wpdb->get_results( "select post_id, meta_key from " . $wpdb->postmeta . " where meta_value = '" . $post_title . "'", OBJECT );
		//$this->debugMP('pr',__FUNCTION__ . ' wps_dependency_titles:', $wps_dependency_titles);

		// Check dependency_ids found
		if ($wps_dependency_ids != false) {

			// Get the post objects for each dependency found
			foreach ($wps_dependency_ids as $ref_id) {
				// Only process new_posts that are not processed yet
				if (!isset($post_list[$ref_id->post_id])){
					$new_post = get_post($ref_id->post_id);
					if (($new_post) && (($new_post->post_type == 'soavis_service') || ($new_post->post_type == 'soavis_system'))) {
						$post_list[$new_post->ID] = $new_post;
					}
				}
			}
		}
		$this->debugMP('pr',__FUNCTION__ . ' post_list generated:', $post_list);

		return $post_list;

	}

	/**
	 * Generate the list of nodes to generate the GraphViz image from
	 *
	 * @param array $post_list_in An array of the post(s) starting the tree
	 */
	public function get_node_list_data( $post_list_in ) {

		// Check the list of posts
		$post_list = array();
		if (is_array($post_list_in)) {
			$post_list = $post_list_in;
		} else {
			$post_list[] = get_post($post_list_in);
		}
//		$this->debugMP('pr',__FUNCTION__ . ' post_ids:', $post_ids);
		$this->debugMP('pr',__FUNCTION__ . ' post_list:', $post_list);

		// Refresh the node_list
		$this->node_list = array();

		// Create the service tree in both directions up and down
		foreach ($post_list as $cur_post) {
			// Get the node_data of the cur_post
			$cur_node = $this->get_node_data( $cur_post );
			$this->node_list[$cur_post->ID] = $cur_node;

			// Get the node data of the posts up and down the tree
			$this->get_node_list_data_down($cur_post);
			$this->get_node_list_data_up  ($cur_post);
		}
		$this->debugMP('pr',__FUNCTION__ . ' node_list generated:', $this->node_list);

		return $this->node_list;
	}

	/**
	 * Generate the list of nodes to generate the GraphViz image from
	 *
	 * @param WP_Post $post_in The post object to travel down the tree
	 */
	protected function get_node_list_data_down( $post_in, $level = 0 ) {

		// Get the dependencies from the post_meta
		$wps_dependency_titles = get_post_meta($post_in->ID, 'ServiceDependency', false);
		//$this->debugMP('pr',__FUNCTION__ . ' wps_dependency_titles:', $wps_dependency_titles);

		// Check dependency titles found
		if (($wps_dependency_titles == false) || (count($wps_dependency_titles) == 0)) {
			return false;
		}

		// Handle the level check
		$level++;
		if ($level > 3) {
			$this->debugMP('msg',__FUNCTION__ . ' returned for LEVEL ' . $level . ', post_title = !' . $post_title . '!');
			return;
		}

		// Get the post objects for each dependency found
		$child_nodes = array();
		foreach ($wps_dependency_titles as $ref_title) {
			// Get the cur_post from this ref_title
			$cur_post = get_page_by_title($ref_title, 'OBJECT', 'soavis_service');
			if (!isset($cur_post)) {
				// If no service found, check for system
				$cur_post = get_page_by_title($ref_title, 'OBJECT', 'soavis_system');
			}
			//$this->debugMP('pr',__FUNCTION__ . ' cur_post found for ' . $ref_title . ' :', $cur_post);
			if (isset($cur_post)) {
				// Only process nodes that are not processed yet
				if (!isset($this->node_list[$cur_post->ID])){
					// Get the node_data of the cur_post
					$cur_node = $this->get_node_data( $cur_post, $level, 'down' );
					$this->node_list[$cur_post->ID] = $cur_node;

					// Get the node data of the posts further down the tree
					$this->get_node_list_data_down($cur_post, $level);
				}
				$child_nodes[] = $cur_post->ID;
			}
		}

		// Set the child list for this node
		$this->node_list[$post_in->ID]['children'] = $child_nodes;
	}

	/**
	 * Generate the list of nodes to generate the GraphViz image from
	 *
	 * @param WP_Post $post_in The post object to travel up the tree
	 */
	protected function get_node_list_data_up( $post_in, $level = 0 ) {

		// Check post_in
		if ( !$post_in ) {
			return;
		}
		$post_title = $post_in->post_title;

		// Get the dependencies from the post_meta
		global $wpdb;
		$wps_dependency_ids = $wpdb->get_results( "select post_id, meta_key from " . $wpdb->postmeta . " where meta_value = '" . $post_title . "'", OBJECT );

		// Check dependency titles found
		if (($wps_dependency_ids == false) || (count($wps_dependency_ids) == 0)) {
			return;
		}

		// Handle the level check
		$level++;
		if ($level > 3) {
			$this->debugMP('msg',__FUNCTION__ . ' returned for LEVEL ' . $level . ', post_title = !' . $post_title . '!');
			return;
		}

		// Get the post objects for each dependency found
		$parent_nodes = array();
		foreach ($wps_dependency_ids as $ref_id) {
			// Only process nodes that are not processed yet
			if (!isset($this->node_list[$ref_id->post_id])){
				$cur_post = get_post($ref_id->post_id);
	//			$this->debugMP('pr',__FUNCTION__ . ' cur_post found for ' . $ref_id->post_id . ' :', $cur_post);
				if ($cur_post) {
					// Check the post_type
					if (($cur_post->post_type == 'soavis_service') || (($cur_post->post_type == 'soavis_system'))) {
						// Get the node_data of the cur_post
						$cur_node = $this->get_node_data( $cur_post, $level, 'up' );
						$this->node_list[$cur_post->ID] = $cur_node;

						// Get the node data of the posts further up the tree
						$this->get_node_list_data_up($cur_post, $level);

						$parent_nodes[] = $ref_id->post_id;
					}
				}
			} else {
				$parent_nodes[] = $ref_id->post_id;
			}
		}

		// Set the parent list for this node
		$this->node_list[$post_in->ID]['parents'] = $parent_nodes;

	}

	/**
	 * Generate the data for the node to generate the GraphViz image from
	 *
	 * @param WP_Post $post The post object to travel up the tree
	 */
	protected function get_node_data( $post_in, $level = 0, $direction = 'base' ) {
		$node_data = array();

		$node_data['ID']         = $post_in->ID;
		$node_data['guid']       = $post_in->guid;
		$node_data['post_name']  = $post_in->post_name;
		$node_data['post_title'] = $post_in->post_title;
		$node_data['post_type']  = $post_in->post_type;
		$node_data['type']       = ucwords(str_replace('soavis_', '', $post_in->post_type));
		$node_data['level']      = $level;
		$node_data['direction']  = $direction;
//		$this->debugMP('pr',__FUNCTION__ . ' node_data:', $node_data);

		return $node_data;
	}

	/**
	 * Generate the text to generate the GraphViz image from
	 *
	 * @return string $str_node_tree_output
	 */
	protected function get_graphviz_tree_text() {
		$str_node_tree_output = '';

		// Format start node
//		$str_node_tree_output .= $this->SetNodeFormatting($nodeID, $this->mSOAVisNodeDirectionStart);

		// Refresh the edge_list
		$this->edge_list = array();

		// Create the service tree based on nodes and edges
		foreach ($this->node_list as $node_id => $node_data) {
			// Generate the configuration for this node
			$str_node_tree_output .= $this->get_graphviz_tree_text_node($node_data);

			// Generate the configuration for edges from this node to its children
			if (isset($node_data['children'])) {
				foreach ($node_data['children'] as $child_node_id) {
					if (isset($this->node_list[$child_node_id])) {
						$str_node_tree_output .= $this->get_graphviz_tree_text_edge($node_data, $this->node_list[$child_node_id]);
					}
				}
			}

			// Generate the configuration for edges from its parents to this node
			if (isset($node_data['parents'])) {
				foreach ($node_data['parents'] as $parent_node_id) {
					if (isset($this->node_list[$parent_node_id])) {
						$str_node_tree_output .= $this->get_graphviz_tree_text_edge($this->node_list[$parent_node_id], $node_data);
					}
				}
			}
		}
		$this->debugMP('pr',__FUNCTION__ . ' this->edge_list generated :', $this->edge_list);

		return $str_node_tree_output;
	}

	/**
	 * Generate the text for a node to generate the GraphViz image from
	 *
	 * @param array $node_data
	 * @return string
	 */
	protected function get_graphviz_tree_text_node( $node_data ) {
		$str_graphviz_text_node  = '';
//		$this->debugMP('pr',__FUNCTION__ . ' node_data found :', $node_data);

		$str_graphviz_text_node .= $node_data['ID'];
//		$str_graphviz_text_node .= ' [style="rounded,filled", shape=box];';
		$str_graphviz_text_node .= SOAVIS_NODE_FORMAT_START;
		$str_graphviz_text_node .= sprintf(SOAVIS_GRAPHVIZ_IMAGE_RENDER_LABEL, $node_data['post_title']);

		// Add the url to the details for this node
		$str_graphviz_text_node .= sprintf(SOAVIS_GRAPHVIZ_IMAGE_RENDER_URL, $node_data['guid']);

		// Get some formatting parameters
		$str_graphviz_text_node .= $this->get_node_format_color($node_data);
		$str_graphviz_text_node .= $this->get_node_format_fill_color($node_data);
//		$str_graphviz_text_node .= $this->GetNodeFormatFontColor($node_data);
		$str_graphviz_text_node .= $this->get_node_format_shape($node_data);

		// Terminate formatting string
		$str_graphviz_text_node .= SOAVIS_NODE_FORMAT_DEFAULT_LAST;
		$str_graphviz_text_node .= SOAVIS_NODE_FORMAT_END;

		return $str_graphviz_text_node;
	}

	/**
	 * Generate the text for an edge to generate the Graphviz image from
	 *
	 * @param array $upper_node
	 * @param array $lower_node
	 * @return string
	 */
	protected function get_graphviz_tree_text_edge( $upper_node, $lower_node ) {
		$str_graphviz_text_edge  = '';
//		$this->debugMP('pr',__FUNCTION__ . ' upper_node found :', $upper_node);
//		$this->debugMP('pr',__FUNCTION__ . ' lower_node found :', $lower_node);

		// Get an edge_id to check
		$edge_id = $upper_node['ID'] . '+' . $lower_node['ID'];

		// Only generate edge if it has not been defined before
		if (!isset($this->edge_list[$edge_id])) {

			$str_graphviz_text_edge .= $upper_node['ID'];
			$str_graphviz_text_edge .= ' -> ';
			$str_graphviz_text_edge .= $lower_node['ID'];
			$str_graphviz_text_edge .= ';';

			$this->edge_list[$edge_id] = $edge_id;
		}

		return $str_graphviz_text_edge;
	}

	/**
	 * Generate the text for a SoaVis Node
	 *
	 * @param array $startNodes
	 * @return string
	 */
	protected function get_node_formatting( $node_in ) {

		$str_node_format = '';

		// Get the node for this serviceNode
		if (is_array($node_in)) {
			$soavis_node = $node_in;
		} else {
			$soavis_node = $this->node_list[$node_in];
		}
		if (!$soavis_node) {
			return 'ERROR: ' . __FUNCTION__ . ': No node provided for ' . $node_in . '!<br/>';
		}
		$serviceNodeID = $soavis_node[$this->mSOAVisNodeNodeID];
		$serviceNodeName = $soavis_node[$this->mSOAVisNodeNodeName];
		$serviceUrlID = $soavis_node[$this->mSOAVisNodeUrl];

		// Start formatting string
		$str_node_format .= $soavis_node['name'];
		$str_node_format .= SOAVIS_NODE_FORMAT_START;

		// Add the url to the details for this node
		$str_node_format .= sprintf(SOAVIS_GRAPHVIZ_IMAGE_RENDER_URL, $soavis_node['guid']);

		// Get some formatting parameters
		$str_node_format .= $this->get_node_format_color($soavis_node);
		$str_node_format .= $this->get_node_format_fill_color($soavis_node);
		$str_node_format .= $this->GetNodeFormatFontColor($soavis_node);
		$str_node_format .= $this->get_node_format_shape($soavis_node);

		// Terminate formatting string
		$str_node_format .= SOAVIS_NODE_FORMAT_DEFAULT_LAST;
		$str_node_format .= SOAVIS_NODE_FORMAT_END;

		return $str_node_format;
	}

	/**
	 * Generate the color for a SOAVisNode
	 *
	 * @param $soavis_node
	 * @return string
	 */
	protected function get_node_format_color( $soavis_node ) {

		$strColorFormat = '';

		return $strColorFormat;

	}

	/**
	 * Generate the FillColor for a SOAVisNode
	 *
	 * @param $soavis_node
	 * @return string
	 */
	protected function get_node_format_fill_color( $soavis_node ) {

		$str_fill_color_format = '';

		// Set different color for service or other node type
		$node_type = isset($soavis_node['type']) ? $soavis_node['type'] : 'Service';
		if ($node_type == 'Service') {

			// Set fill color depending on direction for services only
			$node_direction = isset($soavis_node['direction']) ? $soavis_node['direction'] : '';
			switch ($node_direction) {
				case 'base':
					$str_fill_color_format .= SOAVIS_NODE_FORMAT_FILL_COLOR_START;
					break;
				case 'down':
					$str_fill_color_format .= SOAVIS_NODE_FORMAT_FILL_COLOR_DOWN;
					break;
				case 'up':
					$str_fill_color_format .= SOAVIS_NODE_FORMAT_FILL_COLOR_UP;
					break;
				default:
					$str_fill_color_format .= SOAVIS_NODE_FORMAT_FILL_COLOR_DEFAULT;
					break;
			}
		} else {

			// Set fill color depending on direction for services only
			$str_fill_color_format .= SOAVIS_NODE_FORMAT_FILL_COLOR_DEFAULT;
		}

		return $str_fill_color_format;

	}

	/**
	 * Generate the FontColor for a SOAVisNode
	 *
	 * @since 1.8
	 *
	 * @param $SOAVisNode
	 *
	 * @return string
	 */
	protected function GetNodeFormatFontColor( $SOAVisNode ) {

		$strFontColorFormat = '';

		// Get fontColor format depending on parameters provided
		$strFontColorFormat = $this->getParamMapValue($this->mSOAVisNodeFontColorColumn, $this->mFontColorMap, $SOAVisNode);
		if ($strFontColorFormat) {
			return $strFontColorFormat;
		}

		return $strFontColorFormat;

	}

	/**
	 * Generate the shape for a SOAVisNode
	 *
	 * @param $soavis_node
	 * @return string
	 */
	protected function get_node_format_shape( $soavis_node ) {

		$str_shape_format = '';

		// Set different color for service or other node type
		if (isset($soavis_node['type']) && ($soavis_node['type'] == 'Service')) {
			$str_shape_format .= SOAVIS_NODE_FORMAT_SHAPE_SERVICE;
		} else {

			// Set node shape for system depending on direction
			if ($soavis_node['direction'] == 'up') {
				$str_shape_format .= SOAVIS_NODE_FORMAT_SHAPE_SYSTEM_UP;
			} else {
				$str_shape_format .= SOAVIS_NODE_FORMAT_SHAPE_SYSTEM_DOWN;
			}
		}

		return $str_shape_format;

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
		if ($hdr != '') { $hdr = ' GV: ' . $hdr; }
		$this->plugin->debugMP($type,$hdr,$msg,NULL,NULL,true);
	}

} // class WP_SoaVis_GraphViz
