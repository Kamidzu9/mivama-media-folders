<?php
/**
 * Admin asset loading.
 *
 * @package Mivama_Media_Folders
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads the Media Folders admin assets.
 */
trait Mivama_Media_Folders_Assets {

	/**
	 * Enqueue styles and Media Library integration scripts.
	 *
	 * @param string $hook Current admin page hook suffix.
	 */
	public function enqueue_admin_assets( $hook = '' ) {
		if ( ! current_user_can( 'upload_files' ) ) {
			return;
		}

		$is_folder_screen = ( 'media_page_mivama-media-folders' === $hook );

		wp_enqueue_style(
			'mivama-media-folders-admin',
			MIVAMA_MEDIA_FOLDERS_URL . 'assets/admin.css',
			array(),
			self::VERSION
		);

		if ( $is_folder_screen ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script(
			'mivama-media-folders-admin',
			MIVAMA_MEDIA_FOLDERS_URL . 'assets/admin.js',
			array( 'jquery', 'media-views', 'underscore' ),
			self::VERSION,
			true
		);

		wp_localize_script(
			'mivama-media-folders-admin',
			'MivamaMediaFolders',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( self::NONCE_ACTION ),
				'taxonomy' => self::TAXONOMY,
				'fieldKey' => self::FIELD_KEY,
				'terms'    => $this->get_folder_options_for_js(),
				'labels'   => array(
					'allFolders'       => __( 'All folders', 'mivama-media-folders' ),
					'noFolder'         => __( 'No folder', 'mivama-media-folders' ),
					'unassigned'       => __( 'Unassigned', 'mivama-media-folders' ),
					'folder'           => __( 'Folder', 'mivama-media-folders' ),
					'newFolder'        => __( 'New folder', 'mivama-media-folders' ),
					'createFolder'     => __( 'Create folder', 'mivama-media-folders' ),
					'folderName'       => __( 'Folder name', 'mivama-media-folders' ),
					'parentFolder'     => __( 'Parent folder', 'mivama-media-folders' ),
					'noParent'         => __( 'No parent folder', 'mivama-media-folders' ),
					'cancel'           => __( 'Cancel', 'mivama-media-folders' ),
					'saving'           => __( 'Saving...', 'mivama-media-folders' ),
					'saved'            => __( 'Folder saved.', 'mivama-media-folders' ),
					'creating'         => __( 'Creating...', 'mivama-media-folders' ),
					'created'          => __( 'Folder created.', 'mivama-media-folders' ),
					'nameRequired'     => __( 'Please enter a folder name.', 'mivama-media-folders' ),
					'requestFailed'    => __( 'Request failed. Please try again.', 'mivama-media-folders' ),
					'bulkTargetFolder' => __( 'Bulk target folder', 'mivama-media-folders' ),
				),
			)
		);
	}
}
