<?php
/**
 * Media Library query filtering.
 *
 * @package Mivama_Media_Folders
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Applies folder taxonomy filters to Media Library list and grid queries.
 */
trait Mivama_Media_Folders_Queries {

	/**
	 * Apply the selected folder to the Media Library list query.
	 *
	 * @param WP_Query $query Current admin query.
	 */
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

	/**
	 * Apply the selected folder to the Media Library grid query.
	 *
	 * @param array $query Attachment query arguments.
	 * @return array Filtered query arguments.
	 */
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

		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Attachment taxonomy is the plugin's intentional folder data model.
		$query['tax_query'] = $this->build_folder_tax_query( isset( $query['tax_query'] ) ? $query['tax_query'] : array(), $folder_value );
		return $query;
	}

	/**
	 * Apply a folder taxonomy clause to a WP_Query instance.
	 *
	 * @param WP_Query $query        Query instance.
	 * @param string   $folder_value Folder filter value.
	 */
	private function apply_folder_query_var( $query, $folder_value ) {
		if ( '' === $folder_value || '0' === $folder_value ) {
			return;
		}

		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Attachment taxonomy is the plugin's intentional folder data model.
		$query->set( 'tax_query', $this->build_folder_tax_query( $query->get( 'tax_query' ), $folder_value ) );
	}

	/**
	 * Resolve the requested folder filter from supported Media Library shapes.
	 *
	 * @param mixed $primary Preferred filter value supplied by the current query.
	 * @return string Sanitized folder filter value.
	 */
	private function get_requested_folder_value( $primary = '' ) {
		$candidates = array( $primary );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- This is read-only filter state; all request scalars are sanitized immediately.
		$request = map_deep( wp_unslash( $_REQUEST ), 'sanitize_text_field' );

		if ( isset( $request[ self::FILTER_QUERY_ARG ] ) ) {
			$candidates[] = $request[ self::FILTER_QUERY_ARG ];
		}

		if ( isset( $request[ self::TAXONOMY ] ) ) {
			$candidates[] = $request[ self::TAXONOMY ];
		}

		if ( isset( $request['query'] ) && is_array( $request['query'] ) ) {
			if ( isset( $request['query'][ self::FILTER_QUERY_ARG ] ) ) {
				$candidates[] = $request['query'][ self::FILTER_QUERY_ARG ];
			}

			if ( isset( $request['query'][ self::TAXONOMY ] ) ) {
				$candidates[] = $request['query'][ self::TAXONOMY ];
			}

			if ( isset( $request['query']['query'] ) && is_array( $request['query']['query'] ) && isset( $request['query']['query'][ self::FILTER_QUERY_ARG ] ) ) {
				$candidates[] = $request['query']['query'][ self::FILTER_QUERY_ARG ];
			}

			if ( isset( $request['query']['query'] ) && is_array( $request['query']['query'] ) && isset( $request['query']['query'][ self::TAXONOMY ] ) ) {
				$candidates[] = $request['query']['query'][ self::TAXONOMY ];
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

	/**
	 * Build the taxonomy query for an assigned or unassigned folder filter.
	 *
	 * @param array  $tax_query    Existing taxonomy clauses.
	 * @param string $folder_value Requested folder value.
	 * @return array Updated taxonomy clauses.
	 */
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
