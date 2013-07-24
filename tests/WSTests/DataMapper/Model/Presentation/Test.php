<?php
namespace WSTests\DataMapper\Model;

require( __DIR__ . '/../../../../autoloader.php' );

class Presentation_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DataMapper\Model\Property */
    public $property;
    
    /** @var \WScore\DataMapper\Model\Presentation */
    public $presentation;
    
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $this->property = $container->get( '\WScore\DataMapper\Model\PropertyCsv' );
        $this->property->prepare( file_get_contents( __DIR__.'/../Property/friends.csv' ) );
        $this->presentation = $container->get( '\WScore\DataMapper\Model\Presentation' );
        $this->presentation->setProperty( $this->property );
    }
    // +----------------------------------------------------------------------+
    function test_forgeSelector()
    {
        $sel = $this->presentation->forgeSelector( 'friend_name' );
        $this->assertEquals( 'WScore\Selector\Element_Text', get_class( $sel ) );
        $this->assertEquals( 'friend_name', $sel->name );
        $this->assertArrayHasKey( 'ime', $sel->attributes );
        $this->assertEquals( 'on', $sel->attributes['ime'] );

        $sel = $this->presentation->forgeSelector( 'gender' );
        $this->assertEquals( 'WScore\Selector\Element_Radio', get_class( $sel ) );
        $this->assertEquals( 'gender', $sel->name );
        $this->assertArrayNotHasKey( 'ime', $sel->attributes );
    }
    function test_getSelector()
    {
        $sel1 = $this->presentation->getSelector( 'friend_name' );
        $sel2 = $this->presentation->getSelector( 'friend_name' );
        $sel3 = $this->presentation->forgeSelector( 'friend_name' );
        $this->assertEquals(  $sel1, $sel2 );
        $this->assertEquals(  $sel2, $sel3 );
        $this->assertSame(    $sel1, $sel2 );
        $this->assertNotSame( $sel2, $sel3 );
    }
    // +----------------------------------------------------------------------+
}