<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Mivama_Media_Folders_Queries {

	public function filter_media_list_query( $query ) {
		global $pagenow;

		if ( ! is_admin() || 'upload.php' !== $pagenow ) {
			return;
		}

		$folder_value = $this->get_requested_folder_value( $query->get( self::TAXONOMY ) );
		if ( '' === $folder_value || '0' === $folder_value ) {
			return;
		}

		$post_type = $query->get( 'post_type' );
		if ( ! empty( $post_type ) && 'attachment' !== $post_type && ! ( is_array( $post_type ) && in_array( 'attachment', $post_type, true ) ) ) {
			return;
		}

		$query->set( self::TAXONOMY, '' );
		$this->apply_folder_query_var( $query, $folder_value );
	}

	public function filter_media_grid_query( $query ) {
		if ( ! current_user_can( 'upload_files' ) ) {
			return $query;
		}

		$folder_value = $this->get_requested_folder_value( isset( $query[ self::TAXONOMY ] ) ? $query[ self::TAXONOMY ] : '' );
		unset( $query[ self::TAXONOMY ] );
		unset( $query[ self::FILTER_QUERY_ARG ] );

		if ( '' === $folder_value || '0' === $folder_value ) {
			return $query;
		}

		$query['tax_query'] = $this->build_folder_tax_query( isset( $query['tax_query'] ) ? $query['tax_query'] : array(), $folder_value );
		return $query;
	}

	private function apply_folder_query_var( $query, $folder_value ) {
		if ( '' === $folder_value || '0' === $folder_value ) {
			return;
		}

		$query->set( 'tax_query', $this->build_folder_tax_query( $query->get( 'tax_query' ), $folder_value ) );
	}

	private function get_requested_folder_value( $primary = '' ) {
		$candidates = array( $primary );

		if ( isset( $_REQUEST[ self::FILTER_QUERY_ARG ] ) ) {
			$candidates[] = wp_unslash( $_REQUEST[ self::FILTER_QUERY_ARG ] );
		}

		if ( isset( $_REQUEST[ self::TAXONOMY ] ) ) {
			$candidates[] = wp_unslash( $_REQUEST[ self::TAXONOMY ] );
		}

		if ( isset( $_REQUEST['query'] ) && is_array( $_REQUEST['query'] ) ) {
			if ( isset( $_REQUEST['query'][ self::FILTER_QUERY_ARG ] ) ) {
				$candidates[] = wp_unslash( $_REQUEST['query'][ self::FILTER_QUERY_ARG ] );
			}

			if ( isset( $_REQUEST['query'][ self::TAXONOMY ] ) ) {
				$candidates[] = wp_unslash( $_REQUEST['query'][ self::TAXONOMY ] );
			}

			if ( isset( $_REQUEST['query']['query'] ) && is_array( $_REQUEST['query']['query'] ) && isset( $_REQUEST['query']['query'][ self::FILTER_QUERY_ARG ] ) ) {
				$candidates[] = wp_unslash( $_REQUEST['query']['query'][ self::FILTER_QUERY_ARG ] );
			}

			if ( isset( $_REQUEST['query']['query'] ) && is_array( $_REQUEST['query']['query'] ) && isset( $_REQUEST['query']['query'][ self::TAXONOMY ] ) ) {
				$candidates[] = wp_unslash( $_REQUEST['query']['query'][ self::TAXONOMY ] );
			}
		}

		foreach ( $candidates as $candidate ) {
			if ( is_array( $candidate ) || is_object( $candidate ) ) {
				continue;
			}

			$candidate = sanitize_text_field( (string) $candidate );
			if ( '' !== $candidate ) {
				return $candidate;
			}
		}

		return '';
	}

	private function build_folder_tax_query( $tax_query, $folder_value ) {
		$tax_query    = is_array( $tax_query ) ? $tax_query : array();
		$folder_value = sanitize_text_field( (string) $folder_value );

		foreach ( $tax_query as $key => $clause ) {
			if ( is_array( $clause ) && isset( $clause['taxonomy'] ) && self::TAXONOMY === $clause['taxonomy'] ) {
				unset( $tax_query[ $key ] );
			}
		}

		if ( '-1' === $folder_value || 'none' === $folder_value ) {
			$tax_query[] = array(
				'taxonomy' => self::TAXONOMY,
				'operator' => 'NOT EXISTS',
			);
			return $tax_query;
		}

		$folder_id = absint( $folder_value );
		if ( $folder_id <= 0 ) {
			return $tax_query;
		}

		$tax_query[] = array(
			'taxonomy'         => self::TAXONOMY,
			'field'            => 'term_id',
			'terms'            => array( $folder_id ),
			'include_children' => true,
		);

		return $tax_query;
	}
}
