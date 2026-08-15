<?php
/**
 * Media folder taxonomy registration.
 *
 * @package Mivama_Media_Folders
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the hierarchical attachment taxonomy used for folders.
 */
trait Mivama_Media_Folders_Taxonomy {

	/**
	 * Register the media folder taxonomy for attachments.
	 */
	public function register_taxonomy() {
		$labels = array(
			'name'              => __( 'Media Folders', 'mivama-media-folders' ),
			'singular_name'     => __( 'Media Folder', 'mivama-media-folders' ),
			'search_items'      => __( 'Search Media Folders', 'mivama-media-folders' ),
			'all_items'         => __( 'All Media Folders', 'mivama-media-folders' ),
			'parent_item'       => __( 'Parent Folder', 'mivama-media-folders' ),
			'parent_item_colon' => __( 'Parent Folder:', 'mivama-media-folders' ),
			'edit_item'         => __( 'Edit Folder', 'mivama-media-folders' ),
			'update_item'       => __( 'Update Folder', 'mivama-media-folders' ),
			'add_new_item'      => __( 'Add New Folder', 'mivama-media-folders' ),
			'new_item_name'     => __( 'New Folder Name', 'mivama-media-folders' ),
			'menu_name'         => __( 'Folders', 'mivama-media-folders' ),
		);

		register_taxonomy(
			self::TAXONOMY,
			array( 'attachment' ),
			array(
				'labels'                => $labels,
				'public'                => false,
				'show_ui'               => false,
				'show_admin_column'     => false,
				'show_in_quick_edit'    => false,
				'show_in_rest'          => true,
				'hierarchical'          => true,
				'query_var'             => true,
				'rewrite'               => false,
				'capabilities'          => array(
					'manage_terms' => 'manage_categories',
					'edit_terms'   => 'manage_categories',
					'delete_terms' => 'manage_categories',
					'assign_terms' => 'upload_files',
				),
				'update_count_callback' => '_update_generic_term_count',
			)
		);
	}
}
