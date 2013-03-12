<?php

    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );

class All_SuiteTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( 'all tests for WScore\'s DataMapper' );
        $folder = __DIR__ . '/';
        $suite->addTestFile( $folder . 'DataMapper/All_DataMapper_SuiteTests.php' );
        $suite->addTestFile( $folder . 'Selector/Selector_Test.php' );
        return $suite;
    }

}
