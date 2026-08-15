<?php
/**
 * Permission integration tests.
 *
 * @package Mivama_Media_Folders
 */

/**
 * Verifies Media Folders UI respects WordPress user capabilities.
 */
class Mivama_Media_Folders_Permissions_Test extends WP_UnitTestCase {

	/**
	 * Register the plugin taxonomy before each test.
	 */
	public function set_up() {
		parent::set_up();
		Mivama_Media_Folders::instance()->register_taxonomy();
	}

	/**
	 * Remove the plugin-specific capability so role state cannot leak between tests.
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
	 * Subscribers must not receive the attachment folder field.
	 */
	public function test_subscriber_cannot_receive_attachment_folder_field() {
		$subscriber = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $subscriber );

		$attachment_id = self::factory()->post->create(
			array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
			)
		);

		$fields = Mivama_Media_Folders::instance()->add_attachment_folder_field( array(), get_post( $attachment_id ) );
		$this->assertArrayNotHasKey( Mivama_Media_Folders::FIELD_KEY, $fields );
	}

	/**
	 * Administrators must receive the attachment folder field.
	 */
	public function test_administrator_can_receive_attachment_folder_field() {
		$administrator = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $administrator );

		$attachment_id = self::factory()->post->create(
			array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
			)
		);

		$fields = Mivama_Media_Folders::instance()->add_attachment_folder_field( array(), get_post( $attachment_id ) );
		$this->assertArrayHasKey( Mivama_Media_Folders::FIELD_KEY, $fields );
	}

	/**
	 * Folder structure management must be granted only to category-manager roles.
	 */
	public function test_folder_management_capability_is_installed_on_trusted_roles_only() {
		Mivama_Media_Folders::instance()->install_capabilities();

		$this->assertTrue( get_role( 'administrator' )->has_cap( Mivama_Media_Folders::MANAGE_CAPABILITY ) );
		$this->assertTrue( get_role( 'editor' )->has_cap( Mivama_Media_Folders::MANAGE_CAPABILITY ) );
		$this->assertFalse( get_role( 'author' )->has_cap( Mivama_Media_Folders::MANAGE_CAPABILITY ) );
		$this->assertFalse( get_role( 'subscriber' )->has_cap( Mivama_Media_Folders::MANAGE_CAPABILITY ) );
		$this->assertSame( Mivama_Media_Folders::CAPABILITY_VERSION, (int) get_option( Mivama_Media_Folders::CAPABILITY_OPTION ) );
	}

	/**
	 * Capability migration must restore the structural permission on existing sites.
	 */
	public function test_capability_migration_runs_when_version_is_missing() {
		delete_option( Mivama_Media_Folders::CAPABILITY_OPTION );
		Mivama_Media_Folders::instance()->maybe_install_capabilities();

		$this->assertTrue( get_role( 'administrator' )->has_cap( Mivama_Media_Folders::MANAGE_CAPABILITY ) );
		$this->assertTrue( get_role( 'editor' )->has_cap( Mivama_Media_Folders::MANAGE_CAPABILITY ) );
		$this->assertFalse( get_role( 'author' )->has_cap( Mivama_Media_Folders::MANAGE_CAPABILITY ) );
	}
}
