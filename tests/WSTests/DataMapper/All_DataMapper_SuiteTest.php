<?php

ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

class All_DataMapper_SuiteTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( 'all tests for WScore\'s DbAccess' );
        $folder = dirname( __FILE__ ) . '/';
        $suite->addTestFile( $folder . 'Model/Property/OrigTest.php' );
        $suite->addTestFile( $folder . 'Model/Property/CsvTests.php' );
        $suite->addTestFile( $folder . 'Model/Presentation/Test.php' );
        $suite->addTestFile( $folder . 'Model/Persistence/BasicMysql_Tests.php' );
        $suite->addTestFile( $folder . 'Model/Basic_Test.php' );
        $suite->addTestFile( $folder . 'EntityBasic_Test.php' );
        $suite->addTestFile( $folder . 'EmBasic_Test.php' );
        return $suite;
    }
}

