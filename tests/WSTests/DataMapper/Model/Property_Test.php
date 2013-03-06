<?php
namespace WSTests\DataMapper\Model;

require( __DIR__ . '/../../../autoloader.php' );

class Property_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DataMapper\Model_Property */
    public $property;

    public $define;
    public $relation;
    public $validation;
    public $selector;
    // +----------------------------------------------------------------------+
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $this->property = $container->get( '\WScore\DataMapper\Model_Property' );
        $this->define = array(
            'friend_id'     => array( 'friend code', 'number', ),
            'friend_name'   => array( 'name',        'string', ),
            'friend_bday'   => array( 'birthday',    'string', ),
            'friend_tel'    => array( 'telephone',   'string', ),
            'tag_id'        => array( 'tag ID',      'number', ),
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
        $this->validation = array(
            'friend_id'   => array( 'number' ),
            'friend_name' => array( 'text', 'required' => true ),
            'friend_bday' => array( 'date', 'required' => false ),
            'friend_tel'  => array( 'tel',  'pattern' => '[-0-9]*' ),
        );
        $this->selector = array(
            'friend_id'   => array( 'Selector', 'text' ),
            'friend_name' => array( 'Selector', 'text',    'width:43 | ime:on' ),
            'friend_bday' => array( 'Selector', 'DateYMD', 'ime:off' ),
            'friend_tel'  => array( 'Selector', 'text',    'ime:off' ),
        );
        $this->property->setTable( 'friend', 'friend_id' );
        $this->property->prepare( $this->define, $this->relation );
        $this->property->present( $this->validation, $this->selector );
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
        $this->assertEquals( 'DateYMD',  $rule[1] );
    }
    function test_validation_info()
    {
        $valid = $this->property->getValidateInfo( 'friend_bday' );
        $this->assertEquals( 'date', $valid[0] );
        $this->assertEquals( false,  $valid['required'] );

        $valid = $this->property->getValidateInfo( 'friend_name' );
        $this->assertEquals( 'text', $valid[0] );
        $this->assertEquals( true,   $valid['required'] );
    }
    function test_required()
    {
        $this->assertTrue(  $this->property->isRequired( 'friend_name' ) );
        $this->assertFalse( $this->property->isRequired( 'friend_tel' ) );
        $this->assertFalse( $this->property->isRequired( 'no_such_info' ) );
    }
    // +----------------------------------------------------------------------+
}