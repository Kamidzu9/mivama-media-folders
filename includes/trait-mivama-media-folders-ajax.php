<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Mivama_Media_Folders_Ajax {

	public function ajax_create_media_folder() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to create media folders.', 'mivama-media-folders' ) ), 403 );
		}

		$name      = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$parent_id = isset( $_POST['parent'] ) ? absint( wp_unslash( $_POST['parent'] ) ) : 0;
		$result    = $this->create_or_get_folder( $name, $parent_id );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ), 400 );
		}

		$term = get_term( absint( $result['term_id'] ), self::TAXONOMY );
		if ( ! $term || is_wp_error( $term ) ) {
			wp_send_json_error( array( 'message' => __( 'Folder could not be loaded.', 'mivama-media-folders' ) ), 500 );
		}

		wp_send_json_success(
			array(
				'message' => ! empty( $result['existed'] ) ? __( 'Folder already exists. Existing folder selected.', 'mivama-media-folders' ) : __( 'Folder created.', 'mivama-media-folders' ),
				'term'    => $this->format_term_for_js( $term ),
				'terms'   => $this->get_folder_options_for_js(),
				'existed' => ! empty( $result['existed'] ),
			)
		);
	}

	public function ajax_set_attachment_folder() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		$attachment_id = isset( $_POST['attachmentId'] ) ? absint( wp_unslash( $_POST['attachmentId'] ) ) : 0;
		$folder_id     = isset( $_POST['folderId'] ) ? absint( wp_unslash( $_POST['folderId'] ) ) : 0;

		if ( ! $attachment_id || ! current_user_can( 'edit_post', $attachment_id ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to edit this media file.', 'mivama-media-folders' ) ), 403 );
		}

		$result = $this->assign_attachment_folder( $attachment_id, $folder_id );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ), 400 );
		}

		$term_label = __( 'No folder', 'mivama-media-folders' );
		if ( $folder_id > 0 ) {
			$term = get_term( $folder_id, self::TAXONOMY );
			if ( $term && ! is_wp_error( $term ) ) {
				$term_label = $term->name;
			}
		}

		wp_send_json_success(
			array(
				'message'      => __( 'Folder saved.', 'mivama-media-folders' ),
				'attachmentId' => $attachment_id,
				'folderId'     => $folder_id,
				'folderLabel'  => $term_label,
			)
		);
	}
}
