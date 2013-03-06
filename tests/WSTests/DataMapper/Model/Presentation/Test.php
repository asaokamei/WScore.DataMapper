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
    function test0()
    {
        $rule = $this->presentation->getValidationRule( 'friend_name' );
        $this->assertTrue( $rule );
    }
    // +----------------------------------------------------------------------+
}