<?php
namespace WSTests\DataMapper\Relation;

use \WSTests\DataMapper\entities\friend;

require( __DIR__ . '/../../../autoloader.php' );

class HasOneBasic_Tests extends \PHPUnit_Framework_TestCase
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
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
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
        $contact = $em->newEntity( $this->contactEntity, $this->getContactData(1) );
        $em->save();
        $relation = $em->relation( $contact, 'friend' );

        // basic check. 
        $this->assertEquals( 'WScore\DataMapper\Relation\HasOne', get_class( $relation ) );
        
        $relation->set( $friend );
        $this->assertEquals( $friend[ 'friend_id' ], $contact[ 'friend_id' ] );

        $em->save();
    }
    function test_new_entity_and_insert2()
    {
        $em = $this->em;
        $friend  = $em->newEntity( $this->friendEntity,  $this->getFriendData(2) );
        $contact = $em->newEntity( $this->contactEntity, $this->getContactData(2) );
        $em->save();
        $relation = $em->relation( $contact, 'friend' );

        $relation->set( $friend );
        $this->assertEquals( $friend[ 'friend_id' ], $contact[ 'friend_id' ] );

        $em->save();
    }
    function test_fetch_related_entity()
    {
        $em = $this->em;
        $contacts = $em->fetch( $this->contactEntity, array( '1', '2' ) );
        /** @var $contact1 \WSTests\DataMapper\entities\contact */
        /** @var $contact2 \WSTests\DataMapper\entities\contact */
        $contact1 = $contacts[0];
        $contact2 = $contacts[1];

        // assert that the related entity (friend) is not set yet.
        $this->assertEquals( null, $contact1[ 'friend' ] );
        $this->assertEquals( null, $contact2[ 'friend' ] );

        // relate with friends!
        $friend1 = $em->relation( $contact1, 'friend' )->fetch();
        $friend2 = $em->relation( $contact2, 'friend' )->fetch();
        $this->assertEquals( $this->friendEntity, '\\'.get_class( $contact1[ 'friend' ] ) );

        // assert that the friend is really my friend (same id in this test).
        $this->assertEquals( '1', $contact1->friend->friend_id );
        $this->assertEquals( '2', $contact2->friend->friend_id );

        // assert that related and fetched are truely the same friend.
        $this->assertSame( $friend1[0], $contact1->friend );
        $this->assertSame( $friend2[0], $contact2->friend );
    }
    function test_get_related_entity()
    {
        $em = $this->em;
        /** @var $friends \WSTests\DataMapper\entities\friend[] */
        $friends  = $em->fetch( $this->friendEntity,  array( '1', '2' ) );
        $contacts = $em->fetch( $this->contactEntity, array( '1', '2' ) );
        foreach( $friends as $i => $f ) {
            $f->friend_name = 'updated Name#'.$i;
        }

        $friend1 = $em->relation( $contacts[0], 'friend' )->get();
        $this->assertSame( $friends[0], $friend1[0] );
        $friend2 = $em->relation( $contacts[1], 'friend' )->get();
        $this->assertSame( $friends[1], $friend2[0] );
    }
    function test_link()
    {
        $em = $this->em;
        $friend   = $em->newEntity( $this->friendEntity,  $this->getFriendData(1) );
        /** @var $contact \WSTests\DataMapper\entities\contact */
        $contact  = $em->newEntity( $this->contactEntity, $this->getContactData(1) );
        $relation = $em->relation( $contact, 'friend' )->set( $friend );
        // check friend_id is not set yet, and relation is not linked. 
        $this->assertEquals( null, $contact->friend_id );
        $this->assertFalse( $relation->isLinked() );
        
        $em->save();
        // still, friend_id is not set yet, and relation is not linked. 
        $this->assertEquals( null, $contact->friend_id );
        $this->assertFalse( $relation->isLinked() );
        $relation->link();
        
        // check friend_id is not set yet, and relation is not linked. 
        $this->assertEquals( '3', $contact->friend_id );
        $this->assertTrue( $relation->isLinked() );
        $em->save();
    }
}
