<?php
namespace WSTests\Selector;

require( __DIR__ . '/../../autoloader.php' );

/** @var $container \WScore\DiContainer\Container */
$container = include( __DIR__ . '/../../../vendor/wscore/dicontainer/scripts/instance.php' );

/** @var $selector \WScore\Selector\Selector */
$selector = $container->get( '\WScore\Selector\Selector' );

/**
 * todo: make PHPUnit tests
 */

// test Selector_Text

$sel = $selector->getInstance( 'text', 'test' );

echo 'class: ' . get_class( $sel ) . "\n";
echo 'html: ' . $sel->popHtml( 'name', 'text <strong>bold</strong> output' ) . "\n";
echo 'form: ' . $sel->popHtml( 'form', 'text <strong>bold</strong> output' ) . "\n";

// test Selector_Textarea

$sel = $selector->getInstance( 'textarea', 'test' );

echo 'class: ' . get_class( $sel ) . "\n";
echo 'html: ' . $sel->popHtml( 'name', "text <strong>bold</strong>\n output" ) . "\n";
echo 'form: ' . $sel->popHtml( 'form', "text <strong>bold</strong>\n output" ) . "\n";

// test Selector_Hidden

$sel = $selector->getInstance( 'hidden', 'test' );

echo 'class: ' . get_class( $sel ) . "\n";
echo 'html: ' . $sel->popHtml( 'name', "text <strong>bold</strong>\n output" ) . "\n";
echo 'form: ' . $sel->popHtml( 'form', "text <strong>bold</strong>\n output" ) . "\n";

// test Selector_Mail

$sel = $selector->getInstance( 'mail', 'test' );

echo 'class: ' . get_class( $sel ) . "\n";
echo 'html: ' . $sel->popHtml( 'name', "text <strong>bold</strong>\n output" ) . "\n";
echo 'form: ' . $sel->popHtml( 'form', "text <strong>bold</strong>\n output" ) . "\n";

// test Selector_SelYMD

$sel = $selector->getInstance( 'dateYMD', 'test', 'start_y:1980' );

echo 'class: ' . get_class( $sel ) . "\n";
echo 'html: ' . $sel->popHtml( 'name', "1984-03-31" ) . "\n";
echo 'form: ' . $sel->popHtml( 'form', "1984-03-31" ) . "\n";

