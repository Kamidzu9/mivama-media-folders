<?php
/**
 * AJAX security integration tests.
 *
 * @package Mivama_Media_Folders
 */

if ( ! class_exists( 'WP_Ajax_UnitTestCase' ) ) {
	$tests_dir = getenv( 'WP_TESTS_DIR' );
	if ( ! $tests_dir ) {
		$tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
	}
	require_once $tests_dir . '/includes/testcase-ajax.php';
}

/**
 * Verifies AJAX mutation endpoints enforce nonces and capability boundaries.
 */
class Mivama_Media_Folders_Ajax_Security_Test extends WP_Ajax_UnitTestCase {

	/**
	 * Register plugin state before each AJAX test.
	 */
	public function set_up() {
		parent::set_up();
		Mivama_Media_Folders::instance()->install_capabilities();
		Mivama_Media_Folders::instance()->register_taxonomy();
	}

	/**
	 * Remove plugin-specific role state after each test.
	 */
	public function tear_down() {
		foreach ( array( 'administrator', 'editor', 'author', 'subscriber' ) as $role_name ) {
			$role = get_role( $role_name );
			if ( $role ) {
				$role->remove_cap( Mivama_Media_Folders::MANAGE_CAPABILITY );
			}
		}
		delete_option( Mivama_Media_Folders::CAPABILITY_OPTION );
		parent::tear_down();
	}

	/**
	 * Execute an AJAX action and decode the JSON response.
	 *
	 * @param string $action AJAX action name.
	 * @return array Decoded JSON response.
	 */
	private function run_json_ajax( $action ) {
		$terminated = false;

		try {
			$this->_handleAjax( $action );
		} catch ( WPAjaxDieContinueException $exception ) {
			$terminated = true;
		}

		$this->assertTrue( $terminated, 'The AJAX handler should terminate after writing its JSON response.' );
		$response = json_decode( $this->_last_response, true );
		$this->assertIsArray( $response );
		return $response;
	}

	/**
	 * A missing nonce must stop folder creation before authorization or mutation.
	 */
	public function test_create_folder_rejects_missing_nonce() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		$_POST = array(
			'name'   => 'Secure',
			'parent' => 0,
		);

		$this->expectException( WPAjaxDieStopException::class );
		$this->expectExceptionMessage( '-1' );
		$this->_handleAjax( 'mivama_create_media_folder' );
	}

	/**
	 * Authors may upload media but must not mutate the global folder structure.
	 */
	public function test_author_cannot_create_folder() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'author' ) ) );
		$_POST = array(
			'nonce'  => wp_create_nonce( Mivama_Media_Folders::NONCE_ACTION ),
			'name'   => 'Forbidden',
			'parent' => 0,
		);

		$response = $this->run_json_ajax( 'mivama_create_media_folder' );
		$this->assertFalse( $response['success'] );
		$this->assertFalse( term_exists( 'Forbidden', Mivama_Media_Folders::TAXONOMY ) );
	}

	/**
	 * Administrators with the structural capability may create folders.
	 */
	public function test_administrator_can_create_folder() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		$_POST = array(
			'nonce'  => wp_create_nonce( Mivama_Media_Folders::NONCE_ACTION ),
			'name'   => 'Allowed',
			'parent' => 0,
		);

		$response = $this->run_json_ajax( 'mivama_create_media_folder' );
		$this->assertTrue( $response['success'] );
		$this->assertNotFalse( term_exists( 'Allowed', Mivama_Media_Folders::TAXONOMY ) );
	}

	/**
	 * An author may organize an attachment they are allowed to edit.
	 */
	public function test_author_can_assign_own_attachment_without_structural_capability() {
		$author_id = self::factory()->user->create( array( 'role' => 'author' ) );
		$folder    = wp_insert_term( 'Existing', Mivama_Media_Folders::TAXONOMY );
		$this->assertNotWPError( $folder );
		$attachment_id = self::factory()->post->create(
			array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
				'post_author' => $author_id,
			)
		);

		wp_set_current_user( $author_id );
		$this->assertFalse( current_user_can( Mivama_Media_Folders::MANAGE_CAPABILITY ) );
		$this->assertTrue( current_user_can( 'edit_post', $attachment_id ) );

		$_POST = array(
			'nonce'        => wp_create_nonce( Mivama_Media_Folders::NONCE_ACTION ),
			'attachmentId' => $attachment_id,
			'folderId'     => $folder['term_id'],
		);

		$response = $this->run_json_ajax( 'mivama_set_attachment_folder' );
		$this->assertTrue( $response['success'] );

		$terms = wp_get_object_terms( $attachment_id, Mivama_Media_Folders::TAXONOMY, array( 'fields' => 'ids' ) );
		$this->assertSame( array( (int) $folder['term_id'] ), array_map( 'intval', $terms ) );
	}

	/**
	 * Users who cannot edit an attachment must not change its folder assignment.
	 */
	public function test_subscriber_cannot_assign_attachment() {
		$attachment_id = self::factory()->post->create(
			array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
			)
		);
		$folder        = wp_insert_term( 'Private', Mivama_Media_Folders::TAXONOMY );
		$this->assertNotWPError( $folder );

		wp_set_current_user( self::factory()->user->create( array( 'role' => 'subscriber' ) ) );
		$_POST = array(
			'nonce'        => wp_create_nonce( Mivama_Media_Folders::NONCE_ACTION ),
			'attachmentId' => $attachment_id,
			'folderId'     => $folder['term_id'],
		);

		$response = $this->run_json_ajax( 'mivama_set_attachment_folder' );
		$this->assertFalse( $response['success'] );
	}

	/**
	 * Invalid folder identifiers must fail without changing the attachment.
	 */
	public function test_invalid_folder_assignment_returns_error() {
		$administrator = self::factory()->user->create( array( 'role' => 'administrator' ) );
		$attachment_id = self::factory()->post->create(
			array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
			)
		);
		wp_set_current_user( $administrator );

		$_POST = array(
			'nonce'        => wp_create_nonce( Mivama_Media_Folders::NONCE_ACTION ),
			'attachmentId' => $attachment_id,
			'folderId'     => 999999,
		);

		$response = $this->run_json_ajax( 'mivama_set_attachment_folder' );
		$this->assertFalse( $response['success'] );
	}
}
