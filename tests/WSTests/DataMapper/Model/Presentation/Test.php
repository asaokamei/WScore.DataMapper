<?php
namespace WSTests\DataMapper\Model;

require( __DIR__ . '/../../../../autoloader.php' );

class Presentation_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DataMapper\Model_Property */
    public $property;
    
    /** @var \WScore\DataMapper\Model_Presentation */
    public $presentation;
    
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $this->property = $container->get( '\WScore\DataMapper\Model_PropertyCsv' );
        $this->property->prepare( file_get_contents( __DIR__.'/../Property/test.csv' ) );
        $this->presentation = $container->get( '\WScore\DataMapper\Model_Presentation' );
        $this->presentation->setProperty( $this->property );
    }
    // +----------------------------------------------------------------------+
    function test_forgeRule()
    {
        $rule = $this->presentation->forgeRule( 'friend_name' );
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $rule ) );
        $this->assertEquals( 'text', $rule->getType() );
        $this->assertTrue( $rule[ 'required' ] );

        $rule = $this->presentation->forgeRule( 'friend_tel' );
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $rule ) );
        $this->assertEquals( 'tel', $rule->getType() );
        $this->assertFalse( $rule[ 'required' ] );
        $this->assertEquals( '[-0-9]*', $rule[ 'pattern' ] );
    }
    function test_getRule()
    {
        $rule1 = $this->presentation->getRule( 'friend_name' );
        $rule2 = $this->presentation->getRule( 'friend_name' );
        $rule3 = $this->presentation->forgeRule( 'friend_name' );
        $this->assertEquals(  $rule1, $rule2 );
        $this->assertEquals(  $rule2, $rule3 );
        $this->assertSame(    $rule1, $rule2 );
        $this->assertNotSame( $rule2, $rule3 );
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
        $this->assertEquals( 'WScore\Selector\Selector', get_class( $sel ) );
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