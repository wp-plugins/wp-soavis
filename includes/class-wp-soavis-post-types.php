<?php
/**
 * Register all post_types for the plugin.
 *
 * @link       http://www.soavis.eu
 *
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/includes
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Register all post_types for the plugin.
 *
 * Defines the plugin name, version, and the hooks for
 * WP_SoaVis custom post_types.
 *
 * @package    WP_SoaVis
 * @subpackage WP_SoaVis/includes
 * @author     De B.A.A.T. <WP_SoaVis@de-baat.nl>
 */
class WP_SoaVis_Post_Types {

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
	 * Definitions of the "Custom Post Type" for SoaVis.
	 *
	 * @var string
	 */
	public $post_types = array();

	/**
	 * Definitions of the info_box_params for SoaVis.
	 *
	 * @var string
	 */
	public $info_box_params = array();

	/**
	 * Init the model by registering the Custom Post Type.
	 *
	 * @var      string    $main_plugin The object of this plugin.
	 */
	public function __construct( $main_plugin ) {

		$this->plugin      = $main_plugin;
		$this->plugin_name = $main_plugin->get_plugin_name();

		// we are on WP "init" hook already
		$this->wps_define_post_types();
		$this->wps_register_post_types();
		$this->wps_define_info_box_params();

		// Flush rewrite when necessary, e.g. when the definition of post_types changed
		if ( get_option( 'wp-soavis-rewrite-rules-version' ) != '1.0' ) {
			flush_rewrite_rules();
			update_option( 'wp-soavis-rewrite-rules-version', '1.0' );
		}
	}

	/**
	 * Define the Custom Post Type to be used by SoaVis.
	 *
	 */
	protected function wps_define_post_types() {
		$this->post_types = array(
			'soavis_service' => array(
				'slug'                => 'soavis_service',
				'name'                => __( 'Services', $this->plugin_name ),
				'singular_name'       => __( 'Service',  $this->plugin_name ),
				'menu_icon'           => 'dashicons-share-alt',
			),
			'soavis_component' => array(
				'slug'                => 'soavis_component',
				'name'                => __( 'Components', $this->plugin_name ),
				'singular_name'       => __( 'Component',  $this->plugin_name ),
				'menu_icon'           => 'dashicons-networking',
			),
			'soavis_system' => array(
				'slug'                => 'soavis_system',
				'name'                => __( 'Systems', $this->plugin_name ),
				'singular_name'       => __( 'System',  $this->plugin_name ),
				'menu_icon'           => 'dashicons-analytics',
			),
			'soavis_project' => array(
				'slug'                => 'soavis_project',
				'name'                => __( 'Projects', $this->plugin_name ),
				'singular_name'       => __( 'Project',  $this->plugin_name ),
				'menu_icon'           => 'dashicons-analytics',
			),
			'soavis_chains' => array(
				'slug'                => 'soavis_chains',
				'name'                => __( 'Chains', $this->plugin_name ),
				'singular_name'       => __( 'Chain',  $this->plugin_name ),
				'menu_icon'           => 'dashicons-admin-links',
			),
		);
		/**
		 * Filter the "Custom Post Type" definitions that SoaVis uses for storing info in the database.
		 *
		 * @param string $post_type The "Custom Post Type" that SoaVis uses.
		 */
		$this->post_types = apply_filters( 'wp_soavis_post_type', $this->post_types );
	}

	/**
	 * Define the info_box_params to be used by SoaVis.
	 *
	 */
	protected function wps_define_info_box_params() {
		$this->info_box_params = array(
			'wp_soavis_version' => array(
				'slug'                => 'wp_soavis_version',
				'label'               => __( 'Version', $this->plugin_name ),
				'post_meta'           => '_soavis_version',
				'show_column'         => true,
			),
			'wp_soavis_last_updated' => array(
				'slug'                => 'wp_soavis_last_updated',
				'label'               => __( 'Last Updated', $this->plugin_name ),
				'post_meta'           => '_soavis_last_updated',
				'show_column'         => true,
			),
			'wp_soavis_visibility' => array(
				'slug'                => 'wp_soavis_visibility',
				'label'               => __( 'Visibility', $this->plugin_name ),
				'post_meta'           => '_soavis_visibility',
				'show_column'         => true,
			),
		);

	}

	/**
	 * Register the Custom Post Types to be used by SoaVis.
	 *
	 */
	protected function wps_register_post_types() {

		// Register each post_type defined
		foreach ($this->post_types as $cur_post_type) {

			// Define the post_type_args
			$post_type_args = array(
				'labels' => array(
					'name'                => $cur_post_type['name'],
					'singular_name'       => $cur_post_type['singular_name'],
					'menu_name'           => $cur_post_type['name'],
					'name_admin_bar'      => $cur_post_type['name'],
					'add_new'             => __( 'Add New', $this->plugin_name ),
					'add_new_item'        => sprintf(__( 'Add New %s', $this->plugin_name ), $cur_post_type['singular_name']),
					'edit_item'           => sprintf(__( 'Edit %s', $this->plugin_name ), $cur_post_type['singular_name']),
					'new_item'            => sprintf(__( 'New %s', $this->plugin_name ), $cur_post_type['singular_name']),
					'view_item'           => sprintf(__( 'View %s', $this->plugin_name ), $cur_post_type['singular_name']),
					'search_item'         => sprintf(__( 'Search %s', $this->plugin_name ), $cur_post_type['name']),
					'not_found'           => sprintf(__( 'No %s found', $this->plugin_name ), $cur_post_type['name']),
					'not_found_in_trash'  => sprintf(__( 'No %s found in trash', $this->plugin_name ), $cur_post_type['name']),
					'all_items'           => sprintf(__( 'SoaVis %s', $this->plugin_name ), $cur_post_type['name']),
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_menu'      => $this->plugin_name,
				'show_in_nav_menus' => true,
				'query_var'         => true,
				'can_export'        => true,
				'has_archive'       => true,
				'supports'          => array( 'title', 'editor', 'excerpt', 'author', 'custom-fields', 'revisions', 'thumbnail' ),
				'rewrite'           => array( 'slug' => $cur_post_type['slug'] ),
				'menu_icon'         => $cur_post_type['menu_icon'],
			);

			/**
			 * Filter the arguments for the registration of the "Custom Post Type" that SoaVis uses.
			 *
			 * @param array $post_type_args Arguments for the registration of the SoaVis "Custom Post Type".
			 */
			$post_type_args = apply_filters( 'wp_soavis_post_type_args', $post_type_args );
			register_post_type( $cur_post_type['slug'], $post_type_args );
		}

	}

	/**
	 * Insert a post with the correct Custom Post Type and default values in the the wp_posts table in the database.
	 *
	 * @param array $post Post to insert.
	 * @return int|WP_Error Post ID of the inserted post on success, WP_Error on error.
	 */
	public function insert( array $post ) {
		$default_post = array(
			'ID' => false, // false on new insert, but existing post ID on update
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_category' => false,
			'post_content' => '',
			'post_excerpt' => '',
			'post_parent' => 0,
			'post_password' => '',
			'post_status' => 'publish',
			'post_title' => '',
			'post_type' => $this->post_type,
			'tags_input' => '',
			'to_ping' => '',
		);
		$post = array_merge( $default_post, $post );
		// WP expects everything to be slashed.
		$post = wp_slash( $post );

		// Remove balanceTags() from sanitize_post(), as it can destroy the JSON when messing with HTML.
		remove_filter( 'content_save_pre', 'balanceTags', 50 );
		remove_filter( 'excerpt_save_pre', 'balanceTags', 50 );
		/*
		 * Remove possible KSES filtering here, as it can destroy the JSON when messing with HTML.
		 * KSES filtering is done to table cells individually, when saving.
		 */
		remove_filter( 'content_save_pre', 'wp_filter_post_kses' );

		$post_id = wp_insert_post( $post, true );

		// Re-add balanceTags() to sanitize_post().
		add_filter( 'content_save_pre', 'balanceTags', 50 );
		add_filter( 'excerpt_save_pre', 'balanceTags', 50 );
		// Re-add KSES filtering, if necessary.
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			add_filter( 'content_save_pre', 'wp_filter_post_kses' );
		}

		return $post_id;
	}

	/**
	 * Update an existing post with the correct Custom Post Type and default values in the the wp_posts table in the database.
	 *
	 * @param array $post Post.
	 * @return int|WP_Error Post ID of the updated post on success, WP_Error on error.
	 */
	public function update( array $post ) {
		$default_post = array(
			'ID' => false, // false on new insert, but existing post ID on update
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_category' => false,
			'post_content' => '',
			'post_excerpt' => '',
			'post_parent' => 0,
			'post_password' => '',
			'post_status' => 'publish',
			'post_title' => '',
			'post_type' => $this->post_type,
			'tags_input' => '',
			'to_ping' => '',
		);
		$post = array_merge( $default_post, $post );
		// WP expects everything to be slashed.
		$post = wp_slash( $post );

		// Remove balanceTags() from sanitize_post(), as it can destroy the JSON when messing with HTML
		remove_filter( 'content_save_pre', 'balanceTags', 50 );
		remove_filter( 'excerpt_save_pre', 'balanceTags', 50 );
		/*
		 * Remove possible KSES filtering here, as it can destroy the JSON when messing with HTML
		 * Saving is done to table cells individually, when saving
		 */
		remove_filter( 'content_save_pre', 'wp_filter_post_kses' );

		$post_id = wp_update_post( $post, true );

		// Re-add balanceTags() to sanitize_post().
		add_filter( 'content_save_pre', 'balanceTags', 50 );
		add_filter( 'excerpt_save_pre', 'balanceTags', 50 );
		// Re-add KSES filtering, if necessary.
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			add_filter( 'content_save_pre', 'wp_filter_post_kses' );
		}

		return $post_id;
	}

	/**
	 * Get a post from the wp_posts table in the database.
	 *
	 * @param int $post_id Post ID.
	 * @return WP_Post|bool Post on success, false on error.
	 */
	public function get( $post_id ) {
		$post = get_post( $post_id );
		if ( is_null( $post ) ) {
			return false;
		}
		return $post;
	}

	/**
	 * Delete a post (and all revisions) from the wp_posts table in the database.
	 *
	 * @param int $post_id Post ID.
	 * @return mixed|bool Post on success, false on error.
	 */
	public function delete( $post_id ) {
		return wp_delete_post( $post_id, true ); // true means force delete, although for CPTs this is automatic in this function
	}

	/**
	 * Move a post to the trash (if trash is globally enabled), instead of directly deleting the post.
	 * (yet unused)
	 *
	 * @param int $post_id Post ID.
	 * @return mixed|bool Post on success, false on error.
	 */
	public function trash( $post_id ) {
		return wp_trash_post( $post_id );
	}

	/**
	 * Restore a post from the trash.
	 * (yet unused)
	 *
	 * @param int $post_id Post ID.
	 * @return array|bool Post on success, false on error.
	 */
	public function untrash( $post_id ) {
		return wp_untrash_post( $post_id );
	}

	/**
	 * Load all posts with one query, to prime the cache.
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 * @see get_post()
	 *
	 * @param array $all_post_ids      List of Post IDs.
	 * @param bool  $update_meta_cache Optional. Whether to update the Post Meta Cache (for table options and visibility).
	 */
	public function load_posts( array $all_post_ids, $update_meta_cache = true ) {
		global $wpdb;

		// Split post loading, to save memory.
		$offset = 0;
		$length = 100; // 100 posts at a time
		$number_of_posts = count( $all_post_ids );
		while ( $offset < $number_of_posts ) {
			$post_ids = array_slice( $all_post_ids, $offset, $length );
			// Don't load posts that are in the cache already.
			$post_ids = _get_non_cached_ids( $post_ids, 'posts' );
			if ( ! empty( $post_ids ) ) {
				$post_ids_list = implode( ',', $post_ids );
				$posts = $wpdb->get_results( "SELECT {$wpdb->posts}.* FROM {$wpdb->posts} WHERE ID IN ({$post_ids_list})" );
				update_post_cache( $posts );
				if ( $update_meta_cache ) {
					// Get all post meta data for all table posts, @see get_post_meta().
					update_meta_cache( 'post', $post_ids );
				}
			}
			$offset += $length; // next array_slice() $offset
		}
	}

	/**
	 * Count the number of posts with the model's CPT in the wp_posts table in the database.
	 * (currently for debug only)
	 *
	 * @return int Number of posts.
	 */
	public function count_posts() {
		return array_sum( (array) wp_count_posts( $this->post_type ) ); // original return value is object with the counts for each post_status
	}

	/**
	 * Add a post meta field to a post.
	 *
	 * @param int    $post_id ID of the post for which the field shall be added.
	 * @param string $field   Name of the post meta field.
	 * @param string $value   Value of the post meta field (not slashed).
	 * @return bool True on success, false on error.
	 */
	public function add_meta_field( $post_id, $field, $value ) {
		// WP expects a slashed value.
		$value = wp_slash( $value );
		$success = add_post_meta( $post_id, $field, $value, true ); // true means unique
		// Make sure that $success is a boolean, as add_post_meta() returns an ID or false.
		$success = ( false === $success ) ? false : true;
		return $success;
	}

	/**
	 * Update the value of a post meta field of a post.
	 *
	 * If the field does not yet exist, it is added.
	 *
	 * @param int    $post_id ID of the post for which the field shall be updated.
	 * @param string $field   Name of the post meta field.
	 * @param string $value   Value of the post meta field (not slashed).
	 * @return bool True on success, false on error.
	 */
	public function update_meta_field( $post_id, $field, $value ) {
		$prev_value = (string) get_post_meta( $post_id, $field, true );
		// No need to update, if values are equal (also, update_post_meta() would return false for this).
		if ( $prev_value === $value ) {
			return true;
		}

		// WP expects a slashed value.
		$value = wp_slash( $value );
		return update_post_meta( $post_id, $field, $value, $prev_value );
	}

	/**
	 * Get the value of a post meta field of a post.
	 *
	 * @param int    $post_id ID of the post for which the field shall be retrieved.
	 * @param string $field   Name of the post meta field.
	 * @return string Value of the meta field.
	 */
	public function get_meta_field( $post_id, $field ) {
		return get_post_meta( $post_id, $field, true ); // true means single value
	}

	/**
	 * Delete a post meta field of a post.
	 * (yet unused)
	 *
	 * @param int    $post_id ID of the post of which the field shall be deleted.
	 * @param string $field   Name of the post meta field.
	 * @return bool True on success, false on error.
	 */
	public function delete_meta_field( $post_id, $field ) {
		return delete_post_meta( $post_id, $field, true ); // true means single value
	}

	/**
	 * Return the Custom Post Type that SoaVis uses.
	 *
	 * @return string The used Custom Post Type.
	 */
	public function get_post_types() {
		return $this->post_types;
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
		if ($hdr != '') { $hdr = ' PT: ' . $hdr; }
		$this->plugin->debugMP($type,$hdr,$msg,NULL,NULL,true);
	}

} // class WP_SoaVis_Post_Types
