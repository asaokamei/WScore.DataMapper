<?php
namespace WSTests\DataMapper\Model;

require( __DIR__ . '/../../../../autoloader.php' );

class PropertyCsv_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DataMapper\Model_Property */
    public $property;
    public $csv;
    // +----------------------------------------------------------------------+
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        class_exists( '\WScore\DataMapper\Model_Helper' );
        $this->property = $container->get( '\WScore\DataMapper\Model_Property' );
        $csv_file = __DIR__ . '/test.csv';
        $this->csv = file_get_contents( $csv_file );
    }

    function test_parse_csv()
    {
        /** @var $helper \WScore\DataMapper\Model_Helper */
        $helper = '\WScore\DataMapper\Model_Helper';
        $csv = $helper::analyzeCsv( $this->csv );
    }

}