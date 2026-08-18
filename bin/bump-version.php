<?php
/**
 * Prepare a release by updating all declared plugin versions and the changelog.
 *
 * Usage: php bin/bump-version.php 1.5.0
 *
 * @package Mivama_Media_Folders
 */

$root    = dirname( __DIR__ );
$version = $argv[1] ?? '';

if ( ! preg_match( '/^\d+\.\d+\.\d+(?:[-+][0-9A-Za-z.-]+)?$/', $version ) ) {
	fwrite( STDERR, "Usage: php bin/bump-version.php X.Y.Z\n" );
	exit( 1 );
}

$files = array(
	'plugin'    => $root . '/mivama-media-folders.php',
	'class'     => $root . '/includes/class-mivama-media-folders.php',
	'readme'    => $root . '/readme.txt',
	'changelog' => $root . '/CHANGELOG.md',
);

foreach ( $files as $name => $file ) {
	if ( ! is_readable( $file ) || ! is_writable( $file ) ) {
		fwrite( STDERR, sprintf( "Cannot update %s: %s\n", $name, $file ) );
		exit( 1 );
	}
}

$plugin = file_get_contents( $files['plugin'] );
$class  = file_get_contents( $files['class'] );
$readme = file_get_contents( $files['readme'] );
$log    = file_get_contents( $files['changelog'] );

$plugin = preg_replace( '/^ \* Version:\s*[^\s]+$/m', ' * Version:     ' . $version, $plugin, 1, $plugin_count );
$class  = preg_replace( "/const\s+VERSION\s*=\s*'[^']+';/", "const VERSION            = '" . $version . "';", $class, 1, $class_count );
$readme = preg_replace( '/^Stable tag:\s*[^\s]+$/m', 'Stable tag: ' . $version, $readme, 1, $readme_count );

if ( 1 !== $plugin_count || 1 !== $class_count || 1 !== $readme_count ) {
	fwrite( STDERR, "Could not update every version declaration.\n" );
	exit( 1 );
}

$release_heading = sprintf( "## [Unreleased]\n\n## [%s] - %s\n", $version, gmdate( 'Y-m-d' ) );
$log             = preg_replace( '/^## \[Unreleased\]\s*$/m', rtrim( $release_heading ), $log, 1, $log_count );

if ( 1 !== $log_count ) {
	fwrite( STDERR, "Could not update the Unreleased changelog heading.\n" );
	exit( 1 );
}

file_put_contents( $files['plugin'], $plugin );
file_put_contents( $files['class'], $class );
file_put_contents( $files['readme'], $readme );
file_put_contents( $files['changelog'], $log );

echo 'Prepared release ' . $version . PHP_EOL;
