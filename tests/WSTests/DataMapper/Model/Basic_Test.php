<?php
namespace WSTests\DataMapper\Model;

require( __DIR__ . '/../../../autoloader.php' );

class Basic_Tests extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $config = 'dsn=mysql:dbname=test_WScore username=admin password=admin';

    public static $table = 'friend';

    public static $id_name = 'friend_id';

    /** @var \WScore\DbAccess\Query */
    public $query;

    /** @var \WSTests\DataMapper\models\Friends */
    public $friend;

    /** @var \WSTests\DataMapper\models\Contacts */
    public $contact;
    // +----------------------------------------------------------------------+
    static function setUpBeforeClass()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $container->set( '\Pdo', self::$config );
        /** @var $friend \WSTests\DataMapper\models\Friends */
        $friend = $container->get( '\WSTests\DataMapper\models\Friends' );
        $friend->setupTable();
        /** @var $friend \WSTests\DataMapper\models\Contacts */
        $contact = $container->get( '\WSTests\DataMapper\models\Contacts' );
        $contact->setupTable();
    }

    /**
     *
     */
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $container->set( '\Pdo', self::$config );
        // set up persistence model
        $this->friend = $container->get( '\WSTests\DataMapper\models\Friends' );
        $this->contact = $container->get( '\WSTests\DataMapper\models\Contacts' );
    }

    /**
     * @param int $idx
     * @return array
     */
    function getFriendData( $idx=1 )
    {
        return $this->friend->getFriendData( $idx );
    }

    /**
     * @param int $idx
     * @return array
     */
    function getContactData( $idx=1 )
    {
        return $this->contact->makeContact( $idx );
    }
    // +----------------------------------------------------------------------+
    //  test for persistence
    // +----------------------------------------------------------------------+
    function test_insert_and_fetch()
    {
        $this->assertEquals( 'WSTests\DataMapper\models\Friends', get_class( $this->friend ) );
        $data1 = $this->getFriendData(1);
        $this->friend->insert( $data1 );
        $this->friend->insert( $this->getFriendData(2) );
        $this->friend->insert( $this->getFriendData(3) );
        $this->friend->insert( $this->getFriendData(4) );
        $stmt = $this->friend->fetch( '1' );
        $fetched = $stmt->fetch();
        $list = array( 'friend_name', 'gender', 'friend_bday', 'friend_tel' );
        foreach( $list as $key ) {
            $this->assertEquals( $data1[$key], $fetched[$key] );
        }
    }
    function test_insert_and_fetch_using_contact()
    {
        $this->assertEquals( 'WSTests\DataMapper\models\Contacts', get_class( $this->contact ) );
        $data = $this->getContactData(1);
        $this->contact->insert( $data );
        $stmt = $this->contact->fetch( '1' );
        $fetched = $stmt->fetch();
        foreach( $data as $key => $val ) {
            $this->assertEquals( $val, $fetched[$key] );
        }
    }
    function test_fetch_with_condition()
    {
        $stmt = $this->friend->fetch( 'F', 'gender' );
        $fetched = $stmt->fetchAll();
        $this->assertEquals( 2, count( $fetched ) );
        $this->assertNotEquals( $fetched[0]['friend_name'], $fetched[1]['friend_name'] );
        $this->assertEquals( 'F', $fetched[0][ 'gender' ] );
        $this->assertEquals( 'F', $fetched[1][ 'gender' ] );
    }
    function test_fetch_with_condition_as_array()
    {
        $stmt = $this->friend->fetch( 'F', 'gender' );
        $fetched = $stmt->fetchAll();
        $this->assertEquals( 2, count( $fetched ) );
        $this->assertNotEquals( $fetched[0]['friend_name'], $fetched[1]['friend_name'] );
        $this->assertEquals( 'F', $fetched[0][ 'gender' ] );
        $this->assertEquals( 'F', $fetched[1][ 'gender' ] );
    }
    function test_update()
    {
        $stmt = $this->friend->fetch( '2' );
        $fetched = $stmt->fetch();
        $fetched[ 'friend_tel' ] = '999-9999-9999';
        $this->friend->update( $fetched );

        $stmt = $this->friend->fetch( '2' );
        $fetched = $stmt->fetch();
        $this->assertEquals( '999-9999-9999', $fetched[ 'friend_tel' ] );
    }
    function test_update_with_id()
    {
        $stmt = $this->friend->fetch( '1' );
        $original = $stmt->fetch();

        $vals = array( 'friend_tel' => '12345678' );
        $this->friend->update( '1', $vals );

        $stmt = $this->friend->fetch( '1' );
        $updated = $stmt->fetch();

        $list = array( 'friend_name', 'gender', 'friend_bday' );
        foreach( $list as $key ) {
            $this->assertEquals( $original[$key], $updated[$key] );
        }
        $this->assertEquals( '12345678', $updated[ 'friend_tel' ] );
    }
    function test_fetch_mode_to_get_object()
    {
        $data = $this->getFriendData(3);
        $stmt = $this->friend->fetch( '3' );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, 'stdClass' );
        $fetched = $stmt->fetch();
        $this->assertTrue( is_object( $fetched ) );
        $list = array( 'friend_name', 'gender', 'friend_bday', 'friend_tel' );
        foreach( $list as $key ) {
            $this->assertEquals( $data[$key], $fetched->$key );
        }
    }
    function test_delete()
    {
        $stmt = $this->friend->fetch( 'M', 'gender' );
        $fetched = $stmt->fetchAll();
        $this->assertEquals( 2, count( $fetched ) );

        $id_to_delete = $fetched[0];
        $this->friend->delete( $id_to_delete );

        $stmt = $this->friend->fetch( 'M', 'gender' );
        $fetched = $stmt->fetchAll();
        $this->assertEquals( 1, count( $fetched ) );
        $this->assertNotEquals( $id_to_delete, $fetched[0]['friend_id'] );
    }
    // +----------------------------------------------------------------------+
    //  test for rules
    // +----------------------------------------------------------------------+
    function test_forgeRule()
    {
        $rule = $this->friend->getRule( 'friend_name' );
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $rule ) );
        $this->assertEquals( 'text', $rule->getType() );
        $this->assertTrue( $rule[ 'required' ] );

        $rule = $this->friend->getRule( 'friend_tel' );
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $rule ) );
        $this->assertEquals( 'tel', $rule->getType() );
        $this->assertFalse( $rule[ 'required' ] );
        $this->assertEquals( '[-0-9]*', $rule[ 'pattern' ] );
    }
    function test_getRule()
    {
        $rule1 = $this->friend->getRule( 'friend_name' );
        $rule2 = $this->friend->getRule( 'friend_name' );
        $this->assertEquals(  $rule1, $rule2 );
        $this->assertSame(    $rule1, $rule2 );
    }
    // +----------------------------------------------------------------------+
    //  test for selector
    // +----------------------------------------------------------------------+
    function test_forgeSelector()
    {
        $sel = $this->friend->getSelector( 'friend_name' );
        $this->assertEquals( 'WScore\Selector\Element_Text', get_class( $sel ) );
        $this->assertEquals( 'friend_name', $sel->name );
        $this->assertArrayHasKey( 'ime', $sel->attributes );
        $this->assertEquals( 'on', $sel->attributes['ime'] );

        $sel = $this->friend->getSelector( 'gender' );
        $this->assertEquals( 'WScore\Selector\Element_Radio', get_class( $sel ) );
        $this->assertEquals( 'gender', $sel->name );
        $this->assertArrayNotHasKey( 'ime', $sel->attributes );
    }
    function test_getSelector()
    {
        $sel1 = $this->friend->getSelector( 'friend_name' );
        $sel2 = $this->friend->getSelector( 'friend_name' );
        $this->assertEquals(  $sel1, $sel2 );
        $this->assertSame(    $sel1, $sel2 );
    }
    function test_selector_choice()
    {
        $sel = $this->friend->getSelector( 'gender' );
        $this->assertTrue( is_array( $sel->item_data ) );
        $this->assertEquals( 2, count( $sel->item_data ) );

        $this->friend->setGenderChoice( true );
        $sel = $this->friend->getSelector( 'gender', true );
        $this->assertTrue( is_array( $sel->item_data ) );
        $this->assertEquals( 3, count( $sel->item_data ) );
    }
    // +----------------------------------------------------------------------+
    //  test for serialization
    // +----------------------------------------------------------------------+
    function test_serialize()
    {
        $ser = serialize( $this->friend );
        /** @var $friend \WSTests\DataMapper\models\Friends */
        $friend = unserialize( $ser );

        $data = $this->getFriendData(5);
        $friend->insert( $data );
        $stmt = $friend->fetch( '5' );
        $fetched = $stmt->fetch();
        $list = array( 'friend_name', 'gender', 'friend_bday', 'friend_tel' );
        foreach( $list as $key ) {
            $this->assertEquals( $data[$key], $fetched[$key] );
        }
    }
    // +----------------------------------------------------------------------+
}