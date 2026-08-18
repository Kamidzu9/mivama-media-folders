<?php
/**
 * Main plugin controller.
 *
 * @package Mivama_Media_Folders
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coordinates Media Folders hooks and shared configuration.
 */
final class Mivama_Media_Folders {

	use Mivama_Media_Folders_Taxonomy;
	use Mivama_Media_Folders_Assets;
	use Mivama_Media_Folders_Admin_Page;
	use Mivama_Media_Folders_Ajax;
	use Mivama_Media_Folders_Attachment_Fields;
	use Mivama_Media_Folders_Media_List;
	use Mivama_Media_Folders_Queries;
	use Mivama_Media_Folders_Helpers;

	const VERSION            = '1.5.0';
	const TAXONOMY           = 'mivama_media_folder';
	const FIELD_KEY          = 'mivama_media_folder_term_id';
	const NONCE_ACTION       = 'mivama_media_folders_nonce';
	const FILTER_QUERY_ARG   = 'mivama_media_folder_filter';
	const MANAGE_CAPABILITY  = 'manage_media_folders';
	const CAPABILITY_VERSION = 1;
	const CAPABILITY_OPTION  = 'mivama_media_folders_capability_version';

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return self Plugin instance.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register WordPress hooks.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'admin_init', array( $this, 'maybe_install_capabilities' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		add_action( 'admin_post_mivama_mf_create_folder', array( $this, 'handle_folder_page_create' ) );
		add_action( 'admin_post_mivama_mf_update_folder', array( $this, 'handle_folder_page_update' ) );
		add_action( 'admin_post_mivama_mf_delete_folder', array( $this, 'handle_folder_page_delete' ) );

		add_action( 'wp_ajax_mivama_create_media_folder', array( $this, 'ajax_create_media_folder' ) );
		add_action( 'wp_ajax_mivama_set_attachment_folder', array( $this, 'ajax_set_attachment_folder' ) );

		add_filter( 'attachment_fields_to_edit', array( $this, 'add_attachment_folder_field' ), 10, 2 );
		add_filter( 'attachment_fields_to_save', array( $this, 'save_attachment_folder_field' ), 10, 2 );

		add_filter( 'manage_media_columns', array( $this, 'add_media_column' ) );
		add_action( 'manage_media_custom_column', array( $this, 'render_media_column' ), 10, 2 );

		add_action( 'restrict_manage_posts', array( $this, 'render_list_filters' ) );
		add_action( 'pre_get_posts', array( $this, 'filter_media_list_query' ) );
		add_filter( 'ajax_query_attachments_args', array( $this, 'filter_media_grid_query' ) );

		add_filter( 'bulk_actions-upload', array( $this, 'register_bulk_actions' ) );
		add_filter( 'handle_bulk_actions-upload', array( $this, 'handle_bulk_actions' ), 10, 3 );
		add_action( 'admin_notices', array( $this, 'render_admin_notices' ) );
	}

	/**
	 * Install the structural folder-management capability on trusted roles.
	 *
	 * Roles that can manage WordPress categories retain equivalent structural
	 * permissions for media folders. Attachment assignment remains governed by
	 * the normal media and per-attachment capabilities.
	 */
	public function install_capabilities() {
		$roles = wp_roles();
		if ( ! $roles ) {
			return;
		}

		foreach ( $roles->roles as $role_name => $role_data ) {
			if ( empty( $role_data['capabilities']['manage_categories'] ) ) {
				continue;
			}

			$role = get_role( $role_name );
			if ( $role ) {
				$role->add_cap( self::MANAGE_CAPABILITY );
			}
		}

		update_option( self::CAPABILITY_OPTION, self::CAPABILITY_VERSION, false );
	}

	/**
	 * Migrate capabilities for existing installations when the model changes.
	 */
	public function maybe_install_capabilities() {
		if ( (int) get_option( self::CAPABILITY_OPTION, 0 ) < self::CAPABILITY_VERSION ) {
			$this->install_capabilities();
		}
	}
}
