<?php
if( defined( 'VENDOR_DIRECTORY' ) ) { return; }
elseif( file_exists( __DIR__ . '/../vendor/' ) ) {
    define( 'VENDOR_DIRECTORY', __DIR__ . '/../vendor/' );
}
elseif( file_exists( __DIR__ . '/../../../../vendor/' ) ) {
    define( 'VENDOR_DIRECTORY', __DIR__ . '/../../../../vendor/' );
}
else {
    die( 'vendor directory not found' );
}
require_once( VENDOR_DIRECTORY . 'autoload.php' );
$loader = new \Composer\Autoload\ClassLoader();

$loader->add( 'WScore\DataMapper', __DIR__.'/../src' );
$loader->add( 'WScore\Selector',   __DIR__.'/../src' );
$loader->add( 'WSTests',   __DIR__ );
$loader->register();
