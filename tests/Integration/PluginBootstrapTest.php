<?php
/**
 * Plugin bootstrap integration tests.
 *
 * @package Mivama_Media_Folders
 */

/**
 * Verifies the plugin bootstraps and exposes its singleton controller.
 */
class Mivama_Media_Folders_Plugin_Bootstrap_Test extends WP_UnitTestCase {

	/**
	 * The plugin must load its controller and keep runtime/header versions aligned.
	 */
	public function test_plugin_bootstraps_and_exposes_singleton() {
		$this->assertTrue( class_exists( 'Mivama_Media_Folders' ) );
		$this->assertInstanceOf( Mivama_Media_Folders::class, Mivama_Media_Folders::instance() );

		$plugin_data = get_file_data(
			MIVAMA_MEDIA_FOLDERS_FILE,
			array( 'version' => 'Version' )
		);

		$this->assertSame( $plugin_data['version'], Mivama_Media_Folders::VERSION );
	}
}
