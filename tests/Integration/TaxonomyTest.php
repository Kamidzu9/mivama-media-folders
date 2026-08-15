<?php
/**
 * Media folder taxonomy integration tests.
 *
 * @package Mivama_Media_Folders
 */

/**
 * Verifies taxonomy registration and hierarchical folder behavior.
 */
class Mivama_Media_Folders_Taxonomy_Test extends WP_UnitTestCase {

	/**
	 * Register the plugin taxonomy before each test.
	 */
	public function set_up() {
		parent::set_up();
		Mivama_Media_Folders::instance()->register_taxonomy();
	}

	/**
	 * The taxonomy must be private, hierarchical, REST-enabled, and attachment-only.
	 */
	public function test_taxonomy_is_registered_for_attachments() {
		$taxonomy = get_taxonomy( Mivama_Media_Folders::TAXONOMY );

		$this->assertNotFalse( $taxonomy );
		$this->assertTrue( $taxonomy->hierarchical );
		$this->assertFalse( $taxonomy->public );
		$this->assertTrue( in_array( 'attachment', $taxonomy->object_type, true ) );
		$this->assertTrue( $taxonomy->show_in_rest );
		$this->assertFalse( $taxonomy->rewrite );
	}

	/**
	 * Nested folder terms must retain their parent-child relationship.
	 */
	public function test_nested_folder_terms_can_be_created() {
		$parent = wp_insert_term( 'Marketing', Mivama_Media_Folders::TAXONOMY );
		$this->assertNotWPError( $parent );

		$child = wp_insert_term(
			'Social',
			Mivama_Media_Folders::TAXONOMY,
			array(
				'parent' => (int) $parent['term_id'],
			)
		);
		$this->assertNotWPError( $child );

		$term = get_term( (int) $child['term_id'], Mivama_Media_Folders::TAXONOMY );
		$this->assertSame( (int) $parent['term_id'], (int) $term->parent );
	}
}
