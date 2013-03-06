<?php
require_once( __DIR__ . '/../vendor/autoload.php' );
require_once( __DIR__ . '/../vendor/composer/ClassLoader.php' );
$loader = new \Composer\Autoload\ClassLoader();

$loader->add( 'WScore\DataMapper', __DIR__.'/../src' );
$loader->add( 'WScore\Selector',   __DIR__.'/../src' );
$loader->add( 'WSTests',   __DIR__ );
$loader->register();
