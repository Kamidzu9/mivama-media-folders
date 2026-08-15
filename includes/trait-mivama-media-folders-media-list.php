<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Mivama_Media_Folders_Media_List {

	public function add_media_column( $columns ) {
		$new_columns = array();

		foreach ( $columns as $key => $label ) {
			$new_columns[ $key ] = $label;
			if ( 'title' === $key ) {
				$new_columns[ self::TAXONOMY ] = __( 'Folder', 'mivama-media-folders' );
			}
		}

		if ( ! isset( $new_columns[ self::TAXONOMY ] ) ) {
			$new_columns[ self::TAXONOMY ] = __( 'Folder', 'mivama-media-folders' );
		}

		return $new_columns;
	}

	public function render_media_column( $column_name, $post_id ) {
		if ( self::TAXONOMY !== $column_name ) {
			return;
		}

		$terms = wp_get_object_terms( $post_id, self::TAXONOMY );
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			echo '<span class="mivama-folder-empty">' . esc_html__( 'No folder', 'mivama-media-folders' ) . '</span>';
			return;
		}

		$links = array();
		foreach ( $terms as $term ) {
			$url     = add_query_arg(
				array(
					'mode'                 => 'list',
					'post_type'            => 'attachment',
					self::FILTER_QUERY_ARG => absint( $term->term_id ),
				),
				admin_url( 'upload.php' )
			);
			$links[] = sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( $term->name ) );
		}

		echo wp_kses_post( implode( ' ', $links ) );
	}

	public function render_list_filters( $post_type ) {
		global $pagenow;

		if ( 'upload.php' !== $pagenow || ! current_user_can( 'upload_files' ) ) {
			return;
		}

		if ( ! empty( $post_type ) && 'attachment' !== $post_type ) {
			return;
		}

		$selected_filter = $this->get_requested_folder_value();

		echo '<label class="screen-reader-text" for="mivama-media-folder-filter">' . esc_html__( 'Filter by folder', 'mivama-media-folders' ) . '</label>';
		echo '<select id="mivama-media-folder-filter" name="' . esc_attr( self::FILTER_QUERY_ARG ) . '">';
		echo $this->render_folder_filter_options( $selected_filter );
		echo '</select>';
		echo '<button type="button" class="button mivama-new-folder-trigger">+ ' . esc_html__( 'New folder', 'mivama-media-folders' ) . '</button>';

		$selected_bulk = isset( $_REQUEST['mivama_bulk_folder'] ) ? absint( wp_unslash( $_REQUEST['mivama_bulk_folder'] ) ) : 0;
		echo '<label class="screen-reader-text" for="mivama-bulk-folder-target">' . esc_html__( 'Bulk target folder', 'mivama-media-folders' ) . '</label>';
		echo '<select id="mivama-bulk-folder-target" class="mivama-bulk-folder-target" name="mivama_bulk_folder">';
		echo $this->render_folder_select_options( $selected_bulk, __( 'Bulk target folder', 'mivama-media-folders' ) );
		echo '</select>';
	}

	public function register_bulk_actions( $actions ) {
		$actions['mivama_assign_to_folder'] = __( 'Move to selected folder', 'mivama-media-folders' );
		$actions['mivama_clear_folder']     = __( 'Remove from folder', 'mivama-media-folders' );
		return $actions;
	}

	public function handle_bulk_actions( $redirect_to, $action, $post_ids ) {
		if ( ! current_user_can( 'upload_files' ) ) {
			return $redirect_to;
		}

		if ( ! in_array( $action, array( 'mivama_assign_to_folder', 'mivama_clear_folder' ), true ) ) {
			return $redirect_to;
		}

		$changed = 0;

		if ( 'mivama_assign_to_folder' === $action ) {
			$folder_id = isset( $_REQUEST['mivama_bulk_folder'] ) ? absint( wp_unslash( $_REQUEST['mivama_bulk_folder'] ) ) : 0;
			if ( $folder_id <= 0 || ! $this->folder_exists_by_id( $folder_id ) ) {
				return add_query_arg( 'mivama_folder_error', 'missing_target', $redirect_to );
			}

			foreach ( $post_ids as $post_id ) {
				$post_id = absint( $post_id );
				if ( 'attachment' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
					continue;
				}
				$this->assign_attachment_folder( $post_id, $folder_id );
				++$changed;
			}

			return add_query_arg( 'mivama_folder_moved', $changed, $redirect_to );
		}

		foreach ( $post_ids as $post_id ) {
			$post_id = absint( $post_id );
			if ( 'attachment' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
				continue;
			}
			$this->assign_attachment_folder( $post_id, 0 );
			++$changed;
		}

		return add_query_arg( 'mivama_folder_removed', $changed, $redirect_to );
	}

	public function render_admin_notices() {
		if ( ! current_user_can( 'upload_files' ) ) {
			return;
		}

		if ( isset( $_GET['mivama_folder_error'] ) && 'missing_target' === sanitize_key( wp_unslash( $_GET['mivama_folder_error'] ) ) ) {
			echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Please choose a target folder before using "Move to selected folder".', 'mivama-media-folders' ) . '</p></div>';
		}

		if ( isset( $_GET['mivama_folder_moved'] ) ) {
			$count = absint( wp_unslash( $_GET['mivama_folder_moved'] ) );
			printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( sprintf( _n( '%d media file moved to the selected folder.', '%d media files moved to the selected folder.', $count, 'mivama-media-folders' ), $count ) ) );
		}

		if ( isset( $_GET['mivama_folder_removed'] ) ) {
			$count = absint( wp_unslash( $_GET['mivama_folder_removed'] ) );
			printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( sprintf( _n( '%d media file removed from folders.', '%d media files removed from folders.', $count, 'mivama-media-folders' ), $count ) ) );
		}
	}
}
