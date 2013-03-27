<?php
namespace WSTests\DataMapper;

use \WSTests\DataMapper\entities\friend;
use WScore\DataMapper\Entity\EntityAbstract;

require( __DIR__ . '/../../../autoloader.php' );

class EmBasic_Tests extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $config = 'dsn=mysql:dbname=test_WScore username=admin password=admin';

    public static $table = 'friend';

    /** @var \WScore\DataMapper\EntityManager */
    public $em;
    
    public $friendEntity = '\WSTests\DataMapper\entities\friend';

    public $contactEntity = '\WSTests\DataMapper\entities\contact';

    // +----------------------------------------------------------------------+
    static function setUpBeforeClass()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
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

    function getContactData( $idx=1 )
    {
        /** @var $model \WSTests\DataMapper\models\Contacts */
        $model = $this->em->getModel( $this->contactEntity );
        return $model->makeContact( $idx );
    }

    /**
     *
     */
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
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
    // +----------------------------------------------------------------------+
    function test_new_entity_and_insert_with_contact()
    {
        // create entity from array of data using newEntity.
        $data1   = $this->getFriendData(1);
        $friend  = $this->em->newEntity( $this->friendEntity, $data1 );
        $data2   = $this->getContactData(1);
        $contact = $this->em->newEntity( $this->contactEntity, $data2 );

        // check entity has the same data.
        $this->assertEquals( 'WSTests\DataMapper\entities\friend',  get_class( $friend ) );
        $this->assertEquals( 'WSTests\DataMapper\entities\contact', get_class( $contact ) );
        $this->assertFalse( $friend->isIdPermanent() );
        $this->assertFalse( $contact->isIdPermanent() );
        foreach( $data1 as $key => $val ) {
            $this->assertEquals( $val, $friend->$key );
        }
        foreach( $data2 as $key => $val ) {
            $this->assertEquals( $val, $contact->$key );
        }
        // save the entity to database, i.e. insert.
        $this->em->save();
        $this->assertTrue( $friend->isIdPermanent() );
        $this->assertTrue( $contact->isIdPermanent() );
        $this->assertEquals( '4', $friend->getId() );
        $this->assertEquals( '1', $contact->getId() );
    }
    
    function test_getByCenaId()
    {
        $data1   = $this->getFriendData(1);
        $friend1 = $this->em->newEntity( $this->friendEntity, $data1 );
        $this->em->register( $friend1 );
        
        $id = EntityAbstract::$_id_for_new-1;
        $this->assertEquals( $friend1, $this->em->getByCenaId( $friend1->getCenaId() ) );
        $this->assertEquals( $friend1, $this->em->getByCenaId( 'Friends.0.'.$id ) );
        $this->assertFalse( $this->em->getByCenaId( 'Friends.0.2' ) );
    }
}
