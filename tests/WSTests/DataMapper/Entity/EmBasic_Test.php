<?php
namespace WSTests\DataMapper;

use \WSTests\DataMapper\entities\friend;

require( __DIR__ . '/../../../autoloader.php' );

class EmBasic_Tests extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $config = 'dsn=mysql:dbname=test_WScore username=admin password=admin';

    public static $table = 'friend';

    /** @var \WScore\DataMapper\EntityManager */
    public $em;
    
    public $friendEntity = '\WSTests\DataMapper\entities\friend';
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
        class_exists( '\WScore\DataMapper\Entity\EntityAbstract' );
        class_exists( '\WSTests\DataMapper\models\Friends' );
        class_exists( '\WSTests\DataMapper\entities\friend' );
    }

    /**
     * @param int $idx
     * @return array
     */
    function getFriendData( $idx=1 )
    {
        /** @var $model \WSTests\DataMapper\models\Friends */
        $model = $this->em->getModel( $this->friendEntity );
        return $model->getFriendData( $idx );
    }

    /**
     *
     */
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );
        $container->set( '\Pdo', self::$config );
        // set up persistence model
        $this->em = $container->get( '\WScore\DataMapper\EntityManager' );
    }

    // +----------------------------------------------------------------------+
    function test_new_entity_and_insert()
    {
        // basic check. 
        $this->assertEquals( 'WScore\DataMapper\EntityManager', get_class( $this->em ) );
        
        // create entity from array of data using newEntity. 
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        
        // check entity has the same data. 
        $this->assertEquals( 'WSTests\DataMapper\entities\friend', get_class( $friend ) );
        $this->assertFalse( $friend->isIdPermanent() );
        $list = array( 'friend_name', 'gender', 'friend_bday', 'friend_tel' );
        foreach( $list as $key ) {
            $this->assertEquals( $data[$key], $friend->$key );
        }
        // save the entity to database, i.e. insert. 
        $this->em->save();
        $this->assertTrue( $friend->isIdPermanent() );
        $this->assertEquals( '1', $friend->getId() );
    }
    function test_fetch_entity()
    {
        // save second data to database. 
        $data   = $this->getFriendData(2);
        $this->em->newEntity( $this->friendEntity, $data );
        $this->em->save();
        
        // fetch the inserted data. 
        $fetch = $this->em->fetch( $this->friendEntity, '2' );
        $friend = $fetch[0];
        
        // now compare it with the original data. 
        $this->assertTrue( $friend->isIdPermanent() );
        $data   = $this->getFriendData(2);
        $this->assertEquals( 'WSTests\DataMapper\entities\friend', get_class( $friend ) );
        $list = array( 'friend_name', 'gender', 'friend_bday', 'friend_tel' );
        foreach( $list as $key ) {
            $this->assertEquals( $data[$key], $friend->$key );
        }
        $this->assertEquals( '2', $friend->getId() );
    }
    function test_fetch_and_modify()
    {
        // save third data to database. 
        $data   = $this->getFriendData(3);
        $this->em->newEntity( $this->friendEntity, $data );
        $this->em->save();
        $this->em->clear();

        // fetch the inserted data. 
        $fetch = $this->em->fetch( $this->friendEntity, '3' );
        $friend = $fetch[0];
        $this->assertEquals( '3', $friend->getId() );
        
        $friend[ 'friend_name' ] = 'save test';
        $this->em->save();
        $this->em->clear();

        $fetch = $this->em->fetch( $this->friendEntity, '3' );
        $friend = $fetch[0];
        $this->assertEquals( 'save test', $friend[ 'friend_name' ] );
        $list = array( 'gender', 'friend_bday', 'friend_tel' );
        foreach( $list as $key ) {
            $this->assertEquals( $data[$key], $friend->$key );
        }
    }
    function test_delete()
    {
        // fetch the inserted data. 
        $fetch = $this->em->fetch( $this->friendEntity, '2' );
        $friend = $fetch[0];
        $friend->toDelete( true );
        $this->em->save();
        $this->em->clear();
        
        $fetch = $this->em->fetch( $this->friendEntity, '2' );
        $this->assertEquals( '0', count( $fetch ) );
    }
}
