<?php
namespace WSTests\DataMapper\Model;

use WScore\DataMapper\Model\PropertySet;

require( __DIR__ . '/../../../../autoloader.php' );

class Property_CsvTest extends Property\SetTest
{
    /** @var PropertySet */
    public $property;
    public $csv;
    // +----------------------------------------------------------------------+
    function setUp()
    {
        parent::setup();
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $this->property = $container->get( '\WScore\DataMapper\Model\PropertySet' );
        $csv_file = __DIR__ . '/friends.csv';
        $this->csv = file_get_contents( $csv_file );
        /** @var $helper \WScore\DataMapper\Model\Helper */
        $this->property->setupCsv( $this->csv );
    }

    function test_property_returns_about_column()
    {
        $info = $this->property->getProperty( 'friend_name' );
        $this->assertEquals( 'friend_name', $info[ 'column' ] );
        $this->assertEquals( 'string', $info[ 'type' ] );
        $this->assertEquals( 'name', $info[ 'title' ] );
        $this->assertEquals( true, $info[ 'required' ] );
    }

}