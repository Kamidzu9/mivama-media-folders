<?php
/**
 * Admin menu integration tests.
 *
 * @package Mivama_Media_Folders
 */

/**
 * Verifies Media > Folders is registered only for users with the structural capability.
 */
class Mivama_Media_Folders_Admin_Menu_Test extends WP_UnitTestCase {

	/**
	 * Prepare role capabilities before each test.
	 */
	public function set_up() {
		parent::set_up();
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		Mivama_Media_Folders::instance()->install_capabilities();
	}

	/**
	 * Reset global menu and plugin capability state.
	 */
	public function tear_down() {
		global $submenu;

		$submenu = array();

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
	 * Administrators should receive the Media > Folders submenu entry.
	 */
	public function test_administrator_receives_media_folders_menu() {
		$administrator = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $administrator );

		$this->assert_media_folders_menu_registered();
	}

	/**
	 * Editors should receive the Media > Folders submenu entry.
	 */
	public function test_editor_receives_media_folders_menu() {
		$editor = self::factory()->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $editor );

		$this->assert_media_folders_menu_registered();
	}

	/**
	 * Authors should not receive structural folder management navigation.
	 */
	public function test_author_does_not_receive_media_folders_menu() {
		$author = self::factory()->user->create( array( 'role' => 'author' ) );
		wp_set_current_user( $author );

		$this->assert_media_folders_menu_not_registered();
	}

	/**
	 * Subscribers should not receive structural folder management navigation.
	 */
	public function test_subscriber_does_not_receive_media_folders_menu() {
		$subscriber = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $subscriber );

		$this->assert_media_folders_menu_not_registered();
	}

	/**
	 * Assert the expected submenu metadata exists.
	 */
	private function assert_media_folders_menu_registered() {
		global $submenu;

		$submenu = array();
		Mivama_Media_Folders::instance()->register_admin_menu();

		$this->assertArrayHasKey( 'upload.php', $submenu );

		$matches = array_values(
			array_filter(
				$submenu['upload.php'],
				static function ( $item ) {
					return isset( $item[2] ) && 'mivama-media-folders' === $item[2];
				}
			)
		);

		$this->assertCount( 1, $matches );
		$this->assertSame( 'Folders', $matches[0][0] );
		$this->assertSame( Mivama_Media_Folders::MANAGE_CAPABILITY, $matches[0][1] );
	}

	/**
	 * Assert no Media Folders submenu entry is exposed.
	 */
	private function assert_media_folders_menu_not_registered() {
		global $submenu;

		$submenu = array();
		Mivama_Media_Folders::instance()->register_admin_menu();

		$items   = isset( $submenu['upload.php'] ) ? $submenu['upload.php'] : array();
		$matches = array_filter(
			$items,
			static function ( $item ) {
				return isset( $item[2] ) && 'mivama-media-folders' === $item[2];
			}
		);

		$this->assertCount( 0, $matches );
	}
}
