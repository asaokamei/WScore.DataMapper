<?php
namespace WSTests\DataMapper\Role;

require( __DIR__ . '/../../../autoloader.php' );

class RoleActive_BasicTests extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $config = 'dsn=mysql:dbname=test_WScore username=admin password=admin';

    /** @var \WScore\DataMapper\RoleManager */
    public $rm;

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
        $this->rm = $container->get( '\WScore\DataMapper\RoleManager' );
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

    // +----------------------------------------------------------------------+
    function test_basic()
    {
        $this->assertEquals( 'WScore\DataMapper\RoleManager', get_class( $this->rm ) );
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $role   = $this->rm->applyActive( $friend );
        $role->save();

        $friend2 = $this->em->fetch( $this->friendEntity, '1' );
        foreach( $data as $key => $val ) {
            $this->assertEquals( $val, $friend[$key] );
            $this->assertEquals( $val, $friend2[0][$key] );
        }

        $this->assertEquals( '1', $role->getId() );
        $this->assertEquals( 'friend_id', $role->getIdName() );
    }
    function test_basic_using_contact()
    {
        $this->assertEquals( 'WScore\DataMapper\RoleManager', get_class( $this->rm ) );
        $data   = $this->getContactData(1);
        $contact = $this->em->newEntity( $this->contactEntity, $data );
        $role   = $this->rm->applyActive( $contact );
        $role->save();

        $contact2 = $this->em->fetch( $this->contactEntity, '1' );
        foreach( $data as $key => $val ) {
            $this->assertEquals( $val, $contact[$key] );
            $this->assertEquals( $val, $contact2[0][$key] );
        }
    }
    function test_relation()
    {
        $friend = $this->em->fetch( $this->friendEntity, '1' );
        $friend = $friend[0];
        $contact = $this->em->fetch( $this->contactEntity, '1' );
        $contact = $contact[0];
        $this->assertEquals( '', $contact->friend_id );

        $role   = $this->rm->applyActive( $friend );
        $role->relation( 'contacts' )->set( $contact );
        $this->em->save();
        $this->assertEquals( '1', $contact->friend_id );
    }
    function test_relation2()
    {
        $friend = $this->em->fetch( $this->friendEntity, '1' );
        $friend = $friend[0];
        $contact = $this->em->relation( $friend, 'contacts' )->fetch();
        $contact = $contact[0];
        $this->assertEquals( 'WSTests\DataMapper\entities\contact', get_class( $contact ) );
    }
    function test_delete()
    {
        $contact = $this->em->fetch( $this->contactEntity, '1' );
        $this->assertEquals( 1, count( $contact ) );
        $contact = $contact[0];
        $role   = $this->rm->applyActive( $contact );
        $role->delete()->save();

        $contact = $this->em->fetch( $this->contactEntity, '1' );
        $this->assertEquals( 0, count( $contact ) );
    }
}