<?php
namespace WSTests\DataMapper\Model;

class Property_AllTests extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DataMapper\Model_Property */
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
    function test_updatedAt()
    {
        $data = array( 'test' => 'fine' );
        $data = $this->property->updatedAt( $data );
        // make sure existing properties are not affected.
        $this->assertArrayHasKey( 'test', $data );
        $this->assertEquals( 'fine', $data[ 'test' ] );
        // test updated at.
        $this->assertArrayHasKey( 'mod_dt_friend', $data );
        $date = new \DateTime( $data[ 'mod_dt_friend' ] );
        $now  = new \DateTime();
        $this->assertEquals( $now->diff( $date )->s, 0 );
    }
    function test_createdAt()
    {
        $data = array( 'test' => 'fine' );
        $data = $this->property->createdAt( $data );
        // make sure existing properties are not affected.
        $this->assertArrayHasKey( 'test', $data );
        $this->assertEquals( 'fine', $data[ 'test' ] );
        // test updated at.
        $this->assertArrayHasKey( 'new_dt_friend', $data );
        $date = new \DateTime( $data[ 'new_dt_friend' ] );
        $now  = new \DateTime();
        $this->assertEquals( $now->diff( $date )->s, 0 );
    }
    function test_setCurrent_in_helper()
    {
        /** @var $helper \WScore\DataMapper\Model_Helper */
        $helper = '\WScore\DataMapper\Model_Helper';
        $now    = new \DateTime( '2001-03-04 05:06:07' );
        $helper::setCurrent( $now );
        $data = array( 'test' => 'fine' );
        $data = $this->property->updatedAt( $data );
        $data = $this->property->createdAt( $data );

        $date = new \DateTime( $data[ 'new_dt_friend' ] );
        $this->assertEquals( $now->diff( $date )->s, 0 );

        $date = new \DateTime( $data[ 'mod_dt_friend' ] );
        $this->assertEquals( $now->diff( $date )->s, 0 );
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