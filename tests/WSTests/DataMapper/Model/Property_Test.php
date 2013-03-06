<?php
namespace WSTests\DataMapper\Model;

require( __DIR__ . '/../../../autoloader.php' );

class Property_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DataMapper\Model_Property */
    public $property;
    public $define;
    public $relation;
    // +----------------------------------------------------------------------+
    function setUp()
    {
        $this->property = new \WScore\DataMapper\Model_Property();
        $this->define = array(
            'friend_id'     => array( 'friend code', 'number', ),
            'friend_name'   => array( 'name',        'string', ),
            'friend_bday'   => array( 'birthday',    'string', ),
            'tag_id'        => array( 'tag ID',      'number' ),
            'new_dt_friend' => array( 'created at',  'string', 'created_at'),
            'mod_dt_friend' => array( 'updated at',  'string', 'updated_at'),
        );
        $this->relation = array(
            'tag' => array(
                'relation_type' => 'HasOne',
                'target_model'  => 'WSTests\DbAccess\Tags',
                'source_column' => 'tag_id',
            ),
        );
        $this->property->setTable( 'friend', 'friend_id' );
        $this->property->prepare( $this->define, $this->relation );
    }

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
}