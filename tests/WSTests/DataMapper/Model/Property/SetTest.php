<?php
namespace WSTests\DataMapper\Model\Property;

use WScore\DataMapper\Model\PropertySet;

require( __DIR__ . '/../../../../autoloader.php' );

class SetTest extends \PHPUnit_Framework_TestCase
{
    /** @var PropertySet */
    public $property;

    /** @var  array */
    public $definitions;
    
    function setup()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        /** @var  $this->property PropertySet */
        $this->property = $container->get( '\WScore\DataMapper\Model\PropertySet' );
        $this->definitions = array(
            'friend_id'     => array( 
                'title'=>'friend code', 'type'=>'number', 
            ),
            'friend_name'   => array( 
                'title'=>'name',        'type'=>'string',   'required' => true , 
            ),
            'gender'        => array( 
                'title'=>'gender',      'type'=>'string',   'required' => true, 'choice' => '[m:male,f:female]' , 
            ),
            'friend_bday'   => array( 
                'title'=>'birthday',    'type'=>'datetime', 
                'presentAs' => 'DateYMD', 
            ),
            'friend_tel'    => array( 
                'title'=>'telephone',   'type'=>'string',   
                'evaluateAs'=>'tel',  'pattern'=>'[-0-9]*', 
            ),
            'tag_id'        => array( 
                'title'=>'tag ID',      'type'=>'number', 
            ),
            'new_dt_friend' => array( 'title'=>'created at',  'type'=>'created_at'),
            'mod_dt_friend' => array( 'title'=>'updated at',  'type'=>'updated_at'),
            'tags' => array(
                'type' => 'relation', 'relation' => 'HasOne', 'source' => 'tag_id', 'entity' => 'WSTests\DbAccess\Tags',
            ),
        );
        $this->property->setupProperty( $this->definitions );
    }
    
    function test0()
    {
        $this->assertequals( 'WScore\DataMapper\Model\PropertySet', get_class( $this->property ) );
    }

    function test_exists_method()
    {
        $this->assertTrue(  $this->property->exists( 'friend_id' ) );
        $this->assertTrue(  $this->property->exists( 'friend_name' ) );
        $this->assertTrue(  $this->property->exists( 'tag_id' ) );
        $this->assertFalse( $this->property->exists( 'not_exist' ) );
    }
    
    function test_isProtected()
    {
        $this->assertTrue(  $this->property->isProtected( 'new_dt_friend' ) );
        $this->assertTrue(  $this->property->isProtected( 'mod_dt_friend' ) );
        $this->assertFalse( $this->property->isProtected( 'birthday' ) );
    }
    
    function test_isProtected_from_HasOne_relation()
    {
        $this->assertTrue(  $this->property->isProtected( 'tag_id' ) );
    }
    // +----------------------------------------------------------------------+
    function test_restrict()
    {
        $data = array(
            'friend_id'     => 'friend code',
            'friend_name'   => 'name',
            'friend_not'    => 'birthday',
        );
        $data = $this->property->restrict( $data );
        $this->assertArrayHasKey(    'friend_id', $data );
        $this->assertArrayHasKey(    'friend_name', $data );
        $this->assertArrayNotHasKey( 'friend_not', $data );
        $this->assertEquals( 2, count( $data ) );
    }
    function test_protect()
    {
        $data = array(
            'friend_id'     => 'friend code',
            'friend_name'   => 'name',
            'friend_not'    => 'birthday',
        );
        $this->property->setProtected( 'friend_id' );
        $data = $this->property->protect( $data );
        $this->assertArrayNotHasKey( 'friend_id', $data );
        $this->assertArrayHasKey(    'friend_name', $data );
        $this->assertArrayHasKey(    'friend_not', $data );
        $this->assertEquals( 2, count( $data ) );
    }
    // +----------------------------------------------------------------------+
    function test_property_returns_all_data()
    {
        $data = $this->property->getProperty();
        $this->assertTrue( is_array( $data ) );
        $this->assertArrayHasKey(    'friend_id', $data );
        $this->assertArrayHasKey(    'friend_name', $data );
    }
    
    function test_property_returns_about_column()
    {
        $info = $this->property->getProperty( 'friend_name' );
        $this->assertEquals( 'string', $info[ 'type' ] );
        $this->assertEquals( 'name', $info[ 'title' ] );
        $this->assertEquals( true, $info[ 'required' ] );
    }
}

    