<?php
/**
 * PHPUnit bootstrap for WordPress integration tests.
 *
 * @package Mivama_Media_Folders
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- WordPress is not loaded yet, so WP_Filesystem is unavailable.
	fwrite( STDERR, "WordPress test library not found in {$_tests_dir}. Run bin/install-wp-tests.sh first.\n" );
	exit( 1 );
}

if ( ! defined( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' ) ) {
	$polyfills_path = dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills';

	if ( is_dir( $polyfills_path ) ) {
		define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $polyfills_path );
	}
}

if ( ! defined( 'WP_TESTS_DOMAIN' ) ) {
	define( 'WP_TESTS_DOMAIN', 'example.org' );
}

if ( ! defined( 'WP_TESTS_EMAIL' ) ) {
	define( 'WP_TESTS_EMAIL', 'admin@example.org' );
}

if ( ! defined( 'WP_TESTS_TITLE' ) ) {
	define( 'WP_TESTS_TITLE', 'mivama Media Folders Tests' );
}

if ( ! defined( 'WP_PHP_BINARY' ) ) {
	define( 'WP_PHP_BINARY', PHP_BINARY );
}

require_once $_tests_dir . '/includes/functions.php';

tests_add_filter(
	'muplugins_loaded',
	function () {
		require dirname( __DIR__ ) . '/mivama-media-folders.php';
	}
);

require $_tests_dir . '/includes/bootstrap.php';
