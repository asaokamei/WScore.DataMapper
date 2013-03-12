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
        $friend  = $this->em->newEntity( $this->friendEntity,  $this->getFriendData(1) );
        $contact = $this->em->newEntity( $this->contactEntity, $this->getContactData(1) );
        $relation = $this->em->relation( $contact, 'friend' );

        // basic check. 
        $this->assertEquals( 'WScore\DataMapper\Relation\HasOne', get_class( $relation ) );
    }
}
