<?php
/**
 * PHPUnit bootstrap for WordPress integration tests.
 */

$_tests_dir = getenv('WP_TESTS_DIR');
if (! $_tests_dir) {
    $_tests_dir = rtrim(sys_get_temp_dir(), '/\\') . '/wordpress-tests-lib';
}

if (! file_exists($_tests_dir . '/includes/functions.php')) {
    fwrite(STDERR, "WordPress test library not found in {$_tests_dir}. Run bin/install-wp-tests.sh first.\n");
    exit(1);
}

require_once $_tests_dir . '/includes/functions.php';

tests_add_filter('muplugins_loaded', function () {
    require dirname(__DIR__) . '/mivama-media-folders.php';
});

require $_tests_dir . '/includes/bootstrap.php';
