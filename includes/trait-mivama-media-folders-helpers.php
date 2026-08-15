<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Mivama_Media_Folders_Helpers {

	private function create_or_get_folder( $name, $parent_id = 0 ) {
		$name      = trim( wp_strip_all_tags( (string) $name ) );
		$parent_id = absint( $parent_id );

		if ( '' === $name ) {
			return new WP_Error( 'mivama_empty_folder_name', __( 'Please enter a folder name.', 'mivama-media-folders' ) );
		}

		if ( $parent_id > 0 && ! $this->folder_exists_by_id( $parent_id ) ) {
			return new WP_Error( 'mivama_invalid_parent', __( 'The selected parent folder does not exist.', 'mivama-media-folders' ) );
		}

		$existing = term_exists( $name, self::TAXONOMY, $parent_id );
		if ( $existing ) {
			$term_id = is_array( $existing ) ? absint( $existing['term_id'] ) : absint( $existing );
			return array(
				'term_id' => $term_id,
				'existed' => true,
			);
		}

		$result = wp_insert_term( $name, self::TAXONOMY, array( 'parent' => $parent_id ) );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return array(
			'term_id' => absint( $result['term_id'] ),
			'existed' => false,
		);
	}

	private function assign_attachment_folder( $attachment_id, $folder_id ) {
		$attachment_id = absint( $attachment_id );
		$folder_id     = absint( $folder_id );

		if ( ! $attachment_id || 'attachment' !== get_post_type( $attachment_id ) ) {
			return new WP_Error( 'mivama_invalid_attachment', __( 'Invalid media file.', 'mivama-media-folders' ) );
		}

		if ( $folder_id > 0 && ! $this->folder_exists_by_id( $folder_id ) ) {
			return new WP_Error( 'mivama_invalid_folder', __( 'The selected folder does not exist.', 'mivama-media-folders' ) );
		}

		$result = $folder_id > 0
			? wp_set_object_terms( $attachment_id, (int) $folder_id, self::TAXONOMY, false )
			: wp_set_object_terms( $attachment_id, array(), self::TAXONOMY, false );

		return is_wp_error( $result ) ? $result : true;
	}

	private function folder_exists_by_id( $folder_id ) {
		$term = get_term( absint( $folder_id ), self::TAXONOMY );
		return $term && ! is_wp_error( $term );
	}

	private function get_attachment_folder_id( $attachment_id ) {
		$terms = wp_get_object_terms( $attachment_id, self::TAXONOMY, array( 'fields' => 'ids' ) );
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return 0;
		}

		return absint( $terms[0] );
	}

	private function render_folder_filter_options( $selected = '' ) {
		$selected = (string) $selected;
		$html     = '<option value=""' . selected( $selected, '', false ) . '>' . esc_html__( 'All folders', 'mivama-media-folders' ) . '</option>';
		$html    .= '<option value="-1"' . selected( $selected, '-1', false ) . '>' . esc_html__( 'Unassigned', 'mivama-media-folders' ) . '</option>';

		foreach ( $this->get_folder_tree() as $folder ) {
			$id    = (string) absint( $folder['id'] );
			$html .= sprintf(
				'<option value="%1$s"%2$s>%3$s%4$s</option>',
				esc_attr( $id ),
				selected( $selected, $id, false ),
				esc_html( str_repeat( '-- ', absint( $folder['depth'] ) ) ),
				esc_html( $folder['name'] )
			);
		}

		return $html;
	}

	private function render_folder_select_options( $selected = 0, $placeholder = '', $exclude_term_id = 0 ) {
		$selected = (string) absint( $selected );
		$html     = sprintf( '<option value="0"%s>%s</option>', selected( $selected, '0', false ), esc_html( $placeholder ) );

		foreach ( $this->get_folder_tree() as $folder ) {
			if ( $exclude_term_id && ( (int) $folder['id'] === (int) $exclude_term_id || $this->is_descendant_term( (int) $folder['id'], (int) $exclude_term_id ) ) ) {
				continue;
			}

			$id    = (string) absint( $folder['id'] );
			$html .= sprintf(
				'<option value="%1$s"%2$s>%3$s%4$s</option>',
				esc_attr( $id ),
				selected( $selected, $id, false ),
				esc_html( str_repeat( '-- ', absint( $folder['depth'] ) ) ),
				esc_html( $folder['name'] )
			);
		}

		return $html;
	}

	private function get_folder_options_for_js() {
		$folders = array();
		foreach ( $this->get_folder_tree() as $folder ) {
			$folders[] = array(
				'id'    => absint( $folder['id'] ),
				'name'  => str_repeat( '-- ', absint( $folder['depth'] ) ) . $folder['name'],
				'raw'   => $folder['name'],
				'slug'  => $folder['slug'],
				'depth' => absint( $folder['depth'] ),
			);
		}

		return $folders;
	}

	private function get_folder_tree( $parent = 0, $depth = 0 ) {
		$terms = get_terms(
			array(
				'taxonomy'   => self::TAXONOMY,
				'hide_empty' => false,
				'parent'     => absint( $parent ),
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		$tree = array();
		foreach ( $terms as $term ) {
			$parent_name = '';
			if ( (int) $term->parent > 0 ) {
				$parent_term = get_term( (int) $term->parent, self::TAXONOMY );
				if ( $parent_term && ! is_wp_error( $parent_term ) ) {
					$parent_name = $parent_term->name;
				}
			}

			$tree[] = array(
				'id'          => absint( $term->term_id ),
				'name'        => $term->name,
				'slug'        => $term->slug,
				'parent'      => absint( $term->parent ),
				'parent_name' => $parent_name,
				'count'       => (int) $term->count,
				'depth'       => absint( $depth ),
			);

			$children = $this->get_folder_tree( (int) $term->term_id, $depth + 1 );
			if ( ! empty( $children ) ) {
				$tree = array_merge( $tree, $children );
			}
		}

		return $tree;
	}

	private function format_term_for_js( $term ) {
		$depth = $this->get_term_depth( (int) $term->term_id );
		return array(
			'id'    => absint( $term->term_id ),
			'name'  => str_repeat( '-- ', absint( $depth ) ) . $term->name,
			'raw'   => $term->name,
			'slug'  => $term->slug,
			'depth' => absint( $depth ),
		);
	}

	private function get_term_depth( $term_id ) {
		$depth = 0;
		$term  = get_term( $term_id, self::TAXONOMY );

		while ( $term && ! is_wp_error( $term ) && (int) $term->parent > 0 ) {
			++$depth;
			$term = get_term( (int) $term->parent, self::TAXONOMY );
		}

		return $depth;
	}

	private function is_descendant_term( $term_id, $ancestor_id ) {
		$term_id     = absint( $term_id );
		$ancestor_id = absint( $ancestor_id );

		if ( ! $term_id || ! $ancestor_id ) {
			return false;
		}

		$term = get_term( $term_id, self::TAXONOMY );
		while ( $term && ! is_wp_error( $term ) && (int) $term->parent > 0 ) {
			if ( (int) $term->parent === $ancestor_id ) {
				return true;
			}
			$term = get_term( (int) $term->parent, self::TAXONOMY );
		}

		return false;
	}
}
