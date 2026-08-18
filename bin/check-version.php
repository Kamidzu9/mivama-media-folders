<?php

$root   = dirname( __DIR__ );
$plugin = file_get_contents( $root . '/mivama-media-folders.php' );
$class  = file_get_contents( $root . '/includes/class-mivama-media-folders.php' );
$readme = file_get_contents( $root . '/readme.txt' );

if ( ! preg_match( '/^ \* Version:\s*([^\s]+)$/m', $plugin, $plugin_match ) ) {
	fwrite( STDERR, "Could not read plugin header version.\n" );
	exit( 1 );
}

if ( ! preg_match( "/const\s+VERSION\s*=\s*'([^']+)'/", $class, $class_match ) ) {
	fwrite( STDERR, "Could not read class VERSION constant.\n" );
	exit( 1 );
}

if ( ! preg_match( '/^Stable tag:\s*([^\s]+)$/m', $readme, $readme_match ) ) {
	fwrite( STDERR, "Could not read readme stable tag.\n" );
	exit( 1 );
}

$versions = array(
	'plugin header'  => $plugin_match[1],
	'class constant' => $class_match[1],
	'stable tag'     => $readme_match[1],
);

if ( count( array_unique( array_values( $versions ) ) ) !== 1 ) {
	foreach ( $versions as $source => $version ) {
		fwrite( STDERR, sprintf( "%s: %s\n", $source, $version ) );
	}
	exit( 1 );
}

echo 'Version OK: ' . $plugin_match[1] . PHP_EOL;
