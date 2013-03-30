<?php
namespace WSTests\DataMapper\Relation;

use \WSTests\DataMapper\entities\friend;

require( __DIR__ . '/../../../autoloader.php' );

class Joined_BasicTests extends \PHPUnit_Framework_TestCase
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
        $em = $this->em;
        $friend  = $em->newEntity( $this->friendEntity,  $this->getFriendData(1) );
        $contact1 = $em->newEntity( $this->contactEntity, $this->getContactData(1) );
        $contact2 = $em->newEntity( $this->contactEntity, $this->getContactData(2) );
        $em->save();
        $relation = $em->relation( $friend, 'contacts' );

        // basic check.
        $this->assertEquals( 'WScore\DataMapper\Relation\Joined', get_class( $relation ) );

        $relation->set( $contact1 );
        $relation->set( $contact2 );
        $this->assertEquals( $friend[ 'friend_id' ], $contact1[ 'friend_id' ] );
        $this->assertEquals( $friend[ 'friend_id' ], $contact2[ 'friend_id' ] );

        $em->save();
    }
    function test_fetch_related_entities()
    {
        $em = $this->em;
        $friend   = $em->fetch( $this->friendEntity,  '1' );
        /** @var $friend \WSTests\DataMapper\entities\friend */
        $friend   = $friend[0];
        // get related contacts.
        $relation = $em->relation( $friend, 'contacts' );
        $contacts = $relation->fetch();
        // test contacts are array and have 2 contacts from previous test.
        $this->assertEquals( 2, count( $friend->contacts ) );
        $this->assertEquals( 2, count( $contacts ) );
        // and these contact's friend_id is the friend_id.
        $this->assertEquals( $friend[ 'friend_id' ], $contacts[0][ 'friend_id' ] );
        $this->assertEquals( $friend[ 'friend_id' ], $contacts[1][ 'friend_id' ] );

        // now test set.
        $contact3 = $em->newEntity( $this->contactEntity, $this->getContactData(3) );
        $relation->set( $contact3 );
        $this->assertEquals( 3, count( $friend->contacts ) );
        $em->save();
        $contacts = $em->relation( $friend, 'contacts' )->fetch();
        // test contacts are array and have 2 contacts from previous test.
        $this->assertEquals( 3, count( $contacts ) );
    }
    function test_link()
    {
        $em = $this->em;
        /** @var $friend \WSTests\DataMapper\entities\friend */
        /** @var $contact \WSTests\DataMapper\entities\contact */
        $friend   = $em->newEntity( $this->friendEntity,  $this->getFriendData(1) );
        $contact  = $em->newEntity( $this->contactEntity, $this->getContactData(1) );
        $relation = $em->relation( $friend, 'contacts' )->set( $contact );
        // check friend_id is not set yet, and relation is not linked. 
        $this->assertEquals( null, $contact->friend_id );
        $this->assertFalse( $relation->isLinked() );

        $em->save();
        // now all the entities and relations are saved.  
        $this->assertNotEquals( null, $contact->friend_id );
        $this->assertEquals( '2', $contact->friend_id );
        $this->assertTrue( $relation->isLinked() );
        $em->save();
    }
}
