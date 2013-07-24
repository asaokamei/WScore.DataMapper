<?php
namespace WSTests\DataMapper\Model;

require( __DIR__ . '/../../../autoloader.php' );

class Validation_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DataMapper\Model\Property */
    public $property;
    
    /** @var \WScore\DataMapper\Model\Validation */
    public $validation;
    
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $this->property = $container->get( '\WScore\DataMapper\Model\PropertyCsv' );
        $this->property->prepare( file_get_contents( __DIR__.'/Property/friends.csv' ) );
        $this->validation = $container->get( '\WScore\DataMapper\Model\Validation' );
        $this->validation->setProperty( $this->property );
    }
    // +----------------------------------------------------------------------+
    function test_forgeRule()
    {
        $rule = $this->validation->forgeRule( 'friend_name' );
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $rule ) );
        $this->assertEquals( 'text', $rule->getType() );
        $this->assertTrue( $rule[ 'required' ] );

        $rule = $this->validation->forgeRule( 'friend_tel' );
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $rule ) );
        $this->assertEquals( 'tel', $rule->getType() );
        $this->assertFalse( $rule[ 'required' ] );
        $this->assertEquals( '[-0-9]*', $rule[ 'pattern' ] );
    }
    function test_getRule()
    {
        $rule1 = $this->validation->getRule( 'friend_name' );
        $rule2 = $this->validation->getRule( 'friend_name' );
        $rule3 = $this->validation->forgeRule( 'friend_name' );
        $this->assertEquals(  $rule1, $rule2 );
        $this->assertEquals(  $rule2, $rule3 );
        $this->assertSame(    $rule1, $rule2 );
        $this->assertNotSame( $rule2, $rule3 );
    }
    // +----------------------------------------------------------------------+
}