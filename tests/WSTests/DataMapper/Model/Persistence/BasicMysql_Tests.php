<?php
namespace wsTests\DbAccess;

require( __DIR__ . '/../../../../autoloader.php' );

class Persistence_BasicMySql_Tests extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $config = 'dsn=mysql:dbname=test_WScore username=admin password=admin';

    public static $table = 'friend';

    public static $id_name = 'friend_id';

    /** @var \WScore\DbAccess\Query */
    public $query;

    /** @var \WScore\DataMapper\Model_Persistence */
    public $friend;
    // +----------------------------------------------------------------------+
    static function setUpBeforeClass()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $container->set( '\Pdo', self::$config );
        $query = $container->get( '\WScore\DbAccess\Query' );
        self::setupFriend( $query );
    }
    /**
     * @param \WScore\DbAccess\Query $query
     */
    static function setupFriend( $query )
    {
        $table = self::$table;
        $sql = "DROP TABLE IF EXISTS {$table}";
        $query->dbAccess()->execSQL( $sql );
        $sql = "
            CREATE TABLE {$table} (
              friend_id    SERIAL,
              friend_name  text    NOT NULL,
              gender       char(1) NOT NULL,
              friend_bday  date,
              friend_tel   text    NOT NULL,
              new_dt_friend   datetime,
              mod_dt_friend   datetime,
              constraint friend_pkey PRIMARY KEY (
                friend_id
              )
            )
        ";
        $query->dbAccess()->execSQL( $sql );
    }

    /**
     *
     */
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $container->set( '\Pdo', self::$config );
        // set up persistence model
        $csv = __DIR__ . '/../Property/test.csv';
        $property = new \WScore\DataMapper\Model_PropertyCsv();
        $property->prepare( file_get_contents( $csv ) );
        $this->friend = $container->get( '\WScore\DataMapper\Model_Persistence' );
        $this->friend->setProperty( $property );
        $this->friend->setTable( self::$table, self::$id_name );
    }

    /**
     * @param int $idx
     * @return array
     */
    function getFriendData( $idx=1 )
    {
        $gender = array( 'M', 'F' );
        $gender = $gender[ $idx % 2 ];
        $day    = 10 + $idx;
        $data = array(
            'friend_name' => 'friend #' . $idx,
            'gender'      => $gender,
            'friend_bday' => '1989-02-' . $day,
            'friend_tel'  => '03-123456-' . $idx,
        );
        return $data;
    }
    // +----------------------------------------------------------------------+
    function test_insert_and_fetch()
    {
        $this->assertEquals( 'WScore\DataMapper\Model_Persistence', get_class( $this->friend ) );
        $data1 = $this->getFriendData(1);
        $this->friend->insertId( $data1 );
        $this->friend->insertId( $this->getFriendData(2) );
        $this->friend->insertId( $this->getFriendData(3) );
        $this->friend->insertId( $this->getFriendData(4) );
        $stmt = $this->friend->fetch( '1' );
        $fetched = $stmt->fetch();
        $list = array( 'friend_name', 'gender', 'friend_bday', 'friend_tel' );
        foreach( $list as $key ) {
            $this->assertEquals( $data1[$key], $fetched[$key] );
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
}
