<?php
namespace WSTests\DataMapper\Model;

require( __DIR__ . '/../../../autoloader.php' );

class BasicCode_Tests extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $config = 'dsn=mysql:dbname=test_WScore username=admin password=admin';

    public static $table = 'friend';

    public static $id_name = 'friend_id';

    /** @var \WScore\DbAccess\Query */
    public $query;

    /** @var \WSTests\DataMapper\models\Groups */
    public $group;
    // +----------------------------------------------------------------------+
    static function setUpBeforeClass()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $container->set( '\Pdo', self::$config );
        /** @var $friend \WSTests\DataMapper\models\Groups */
        $group = $container->get( '\WSTests\DataMapper\models\Groups' );
        $group->setupTable();
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
        $this->group = $container->get( '\WSTests\DataMapper\models\Groups' );
    }

    /**
     * @param int $idx
     * @return array
     */
    function getGroupData( $idx=1 )
    {
        return $this->group->makeGroup( $idx );
    }
    // +----------------------------------------------------------------------+
    //  test for persistence
    // +----------------------------------------------------------------------+
    function test_insert_and_fetch()
    {
        $this->assertEquals( 'WSTests\DataMapper\models\Groups', get_class( $this->group ) );
        $data1 = $this->getGroupData(0);
        $this->group->insert( $data1 );
        $this->group->insert( $this->getGroupData(1) );
        $stmt = $this->group->fetch( 'demo' );
        $fetched = $stmt->fetch();
        foreach( $data1 as $key => $val ) {
            $this->assertEquals( $val, $fetched[$key] );
        }
    }
    function test_fetch_with_condition()
    {
        $stmt = $this->group->fetch( 'testing', 'name' );
        $fetched = $stmt->fetchAll();
        $this->assertEquals( 1, count( $fetched ) );
        $this->assertEquals( 'test', $fetched[0][ 'group_code' ] );
    }
    function test_fetch_with_condition_as_array()
    {
        $this->group->insert( $this->getGroupData(2) );
        $stmt = $this->group->persistence->query()->select();
        $fetched = $stmt->fetchAll();
        $this->assertEquals( 3, count( $fetched ) );
        $this->assertNotEquals( $fetched[0]['group_code'], $fetched[1]['group_code'] );
        $this->assertNotEquals( $fetched[1]['group_code'], $fetched[2]['group_code'] );
    }
    function test_update()
    {
        $stmt = $this->group->fetch( 'more' );
        $fetched = $stmt->fetch();
        $this->assertEquals( 'more', $fetched[ 'group_code' ] );
        $this->assertEquals( 'more more more', $fetched[ 'name' ] );
        $fetched[ 'name' ] = 'more update test';
        $this->group->update( $fetched );

        $stmt = $this->group->fetch( 'more' );
        $fetched = $stmt->fetch();
        $this->assertEquals( 'more update test', $fetched[ 'name' ] );
    }
    function test_delete()
    {
        $stmt = $this->group->fetch( 'test' );
        $fetched = $stmt->fetchAll();
        $this->assertEquals( 1, count( $fetched ) );

        $id_to_delete = $fetched[0];
        $this->group->delete( $id_to_delete );

        $stmt = $this->group->fetch( 'test' );
        $fetched = $stmt->fetchAll();
        $this->assertEquals( 0, count( $fetched ) );
    }
    // +----------------------------------------------------------------------+
}