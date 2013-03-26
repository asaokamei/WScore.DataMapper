<?php
namespace WSTests\DataMapper\Model;

require( __DIR__ . '/../../../../autoloader.php' );

class Property_CsvTest extends Property_AllTests
{
    /** @var \WScore\DataMapper\Model\Property */
    public $property;
    public $csv;
    // +----------------------------------------------------------------------+
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        class_exists( '\WScore\DataMapper\Model\Helper' );
        $this->property = $container->get( '\WScore\DataMapper\Model\PropertyCsv' );
        $csv_file = __DIR__ . '/friends.csv';
        $this->csv = file_get_contents( $csv_file );
        /** @var $helper \WScore\DataMapper\Model\Helper */
        $this->property->setTable( 'friend', 'friend_id' );
        $this->property->prepare( $this->csv );

        \WScore\DataMapper\Model\Helper::setCurrent();
    }

}