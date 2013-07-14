<?php
namespace WSTests\DataMapper\Model;

class Property_AllTests extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DataMapper\Model\Property */
    public $property;
    // +----------------------------------------------------------------------+
    function test_exists_method()
    {
        $this->assertTrue(  $this->property->exists( 'friend_id' ) );
        $this->assertTrue(  $this->property->exists( 'friend_name' ) );
        $this->assertTrue(  $this->property->exists( 'tag_id' ) );
        $this->assertFalse( $this->property->exists( 'not_exist' ) );
    }
    function test_getLabel()
    {
        $label = $this->property->getLabel( 'friend_bday' );
        $this->assertEquals( 'birthday', $label );
        $label = $this->property->getLabel( 'not_exists' );
        $this->assertEquals( null, $label );
    }
    function test_isProtected()
    {
        $this->assertTrue(  $this->property->isProtected( 'friend_id' ) );
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
        $data = $this->property->protect( $data );
        $this->assertArrayNotHasKey( 'friend_id', $data );
        $this->assertArrayHasKey(    'friend_name', $data );
        $this->assertArrayHasKey(    'friend_not', $data );
        $this->assertEquals( 2, count( $data ) );
    }
    // +----------------------------------------------------------------------+
    function test_selector_info()
    {
        $rule = $this->property->getSelectInfo( 'friend_bday' );
        $this->assertEquals( 'Selector', $rule[0] );
        $this->assertEquals( 'DateYMD',  $rule[ 'type' ] );
    }
    function test_validation_info()
    {
        $valid = $this->property->getValidateInfo( 'friend_bday' );
        $this->assertEquals( 'date', $valid[0] );

        $valid = $this->property->getValidateInfo( 'friend_name' );
        $this->assertEquals( 'text', $valid[0] );
    }
    function test_required()
    {
        $this->assertTrue(  $this->property->isRequired( 'friend_name' ) );
        $this->assertFalse( $this->property->isRequired( 'friend_tel' ) );
        $this->assertFalse( $this->property->isRequired( 'no_such_info' ) );
    }
    // +----------------------------------------------------------------------+
}