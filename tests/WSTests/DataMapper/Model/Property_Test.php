<?php
namespace WSTests\DataMapper\Model;

require( __DIR__ . '/../../../autoloader.php' );

class Property_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DataMapper\Model_Property */
    public $property;
    public $define;
    // +----------------------------------------------------------------------+
    function setUp()
    {
        $this->property = new \WScore\DataMapper\Model_Property();
        $this->define = array(
            'friend_id'     => array( 'friend code', 'number', ),
            'friend_name'   => array( 'name',        'string', ),
            'friend_bday'   => array( 'birthday',    'string', ),
            'new_dt_friend' => array( 'created at',  'string', 'created_at'),
            'mod_dt_friend' => array( 'updated at',  'string', 'updated_at'),
        );
        $this->property->setTable( 'friend', 'friend_id' );
        $this->property->prepare( $this->define, array() );
    }

    // +----------------------------------------------------------------------+
    function test_exists_method()
    {
        $this->assertTrue(  $this->property->exists( 'friend_id' ) );
        $this->assertFalse( $this->property->exists( 'not_exist' ) );
    }
    function test_isProtected()
    {
        $this->assertTrue(  $this->property->isProtected( 'friend_id' ) );
        $this->assertFalse( $this->property->isProtected( 'birthday' ) );
    }
}