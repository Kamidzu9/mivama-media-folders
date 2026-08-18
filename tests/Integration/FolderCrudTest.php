<?php
/**
 * Folder CRUD integration tests.
 *
 * @package Mivama_Media_Folders
 */

/**
 * Verifies folder creation, hierarchy validation, duplicate handling, and deletion behavior.
 */
class Mivama_Media_Folders_Folder_CRUD_Test extends WP_UnitTestCase {

	/**
	 * Register the taxonomy before each test.
	 */
	public function set_up() {
		parent::set_up();
		Mivama_Media_Folders::instance()->register_taxonomy();
	}

	/**
	 * Invoke a private plugin helper for integration-level behavior testing.
	 *
	 * @param string $method Method name.
	 * @param array  $args   Arguments.
	 * @return mixed Method result.
	 */
	private function invoke_helper( $method, $args = array() ) {
		$reflection = new ReflectionMethod( Mivama_Media_Folders::class, $method );
		$reflection->setAccessible( true );
		return $reflection->invokeArgs( Mivama_Media_Folders::instance(), $args );
	}

	/**
	 * Root and nested folders must be created with the expected hierarchy.
	 */
	public function test_root_and_nested_folders_can_be_created() {
		$parent = $this->invoke_helper( 'create_or_get_folder', array( 'Marketing', 0 ) );
		$this->assertIsArray( $parent );
		$this->assertFalse( $parent['existed'] );

		$child = $this->invoke_helper( 'create_or_get_folder', array( 'Social', $parent['term_id'] ) );
		$this->assertIsArray( $child );
		$this->assertFalse( $child['existed'] );

		$term = get_term( $child['term_id'], Mivama_Media_Folders::TAXONOMY );
		$this->assertNotWPError( $term );
		$this->assertSame( (int) $parent['term_id'], (int) $term->parent );
	}

	/**
	 * Creating the same folder under the same parent must reuse the existing term.
	 */
	public function test_duplicate_folder_is_reused() {
		$first  = $this->invoke_helper( 'create_or_get_folder', array( 'Products', 0 ) );
		$second = $this->invoke_helper( 'create_or_get_folder', array( 'Products', 0 ) );

		$this->assertFalse( $first['existed'] );
		$this->assertTrue( $second['existed'] );
		$this->assertSame( (int) $first['term_id'], (int) $second['term_id'] );
	}

	/**
	 * A missing parent must be rejected before WordPress inserts the term.
	 */
	public function test_invalid_parent_is_rejected() {
		$result = $this->invoke_helper( 'create_or_get_folder', array( 'Orphan', 999999 ) );

		$this->assertWPError( $result );
		$this->assertSame( 'mivama_invalid_parent', $result->get_error_code() );
	}

	/**
	 * Deleting a folder must not delete the attachment assigned to it.
	 */
	public function test_deleting_folder_keeps_attachment() {
		$attachment_id = self::factory()->post->create(
			array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
			)
		);
		$folder        = $this->invoke_helper( 'create_or_get_folder', array( 'Archive', 0 ) );

		$result = $this->invoke_helper( 'assign_attachment_folder', array( $attachment_id, $folder['term_id'] ) );
		$this->assertTrue( $result );
		$this->assertNotWPError( wp_delete_term( $folder['term_id'], Mivama_Media_Folders::TAXONOMY ) );
		$this->assertNotNull( get_post( $attachment_id ) );

		$terms = wp_get_object_terms( $attachment_id, Mivama_Media_Folders::TAXONOMY, array( 'fields' => 'ids' ) );
		$this->assertSame( array(), $terms );
	}
}
