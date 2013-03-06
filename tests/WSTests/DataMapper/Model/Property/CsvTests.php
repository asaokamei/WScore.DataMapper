<?php
namespace WSTests\DataMapper\Model;

require( __DIR__ . '/../../../../autoloader.php' );

class Property_CsvTest extends Property_AllTests
{
    /** @var \WScore\DataMapper\Model_Property */
    public $property;
    public $csv;
    // +----------------------------------------------------------------------+
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        class_exists( '\WScore\DataMapper\Model_Helper' );
        $this->property = $container->get( '\WScore\DataMapper\Model_PropertyCsv' );
        $csv_file = __DIR__ . '/test.csv';
        $this->csv = file_get_contents( $csv_file );
        /** @var $helper \WScore\DataMapper\Model_Helper */
        $this->property->setTable( 'friend', 'friend_id' );
        $this->property->prepare( $this->csv );

        \WScore\DataMapper\Model_Helper::setCurrent();
    }

}