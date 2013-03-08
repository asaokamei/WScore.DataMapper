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

    /** @var \WScore\DataMapper\Model */
    public $friend;
    // +----------------------------------------------------------------------+
    static function setUpBeforeClass()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
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
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $container->set( '\Pdo', self::$config );
        // set up persistence model
        $this->friend = $container->get( '\WSTests\DataMapper\Model\models\Friends' );
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
        $this->assertEquals( 'WSTests\DataMapper\Model\models\Friends', get_class( $this->friend ) );
        $data1 = $this->getFriendData(1);
        $this->friend->insert( $data1 );
        $this->friend->insert( $this->getFriendData(2) );
        $this->friend->insert( $this->getFriendData(3) );
        $this->friend->insert( $this->getFriendData(4) );
        $stmt = $this->friend->fetch( '1' );
        $fetched = $stmt->fetch();
        $list = array( 'friend_name', 'gender', 'friend_bday', 'friend_tel' );
        foreach( $list as $key ) {
            $this->assertEquals( $data1[$key], $fetched->$key );
        }

    }
}