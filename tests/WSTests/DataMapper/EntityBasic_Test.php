<?php
namespace WSTests\DataMapper;

use \WScore\DataMapper\Entity\EntityInterface;
use \WSTests\DataMapper\entities\friend;

require( __DIR__ . '/../../autoloader.php' );

class EntityBasic_Tests extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $config = 'dsn=mysql:dbname=test_WScore username=admin password=admin';

    /** @var \WScore\DataMapper\Model */
    public $friend;

    /**
     *
     */
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $container->set( '\Pdo', self::$config );
        // set up persistence model
        $this->friend = $container->get( '\WSTests\DataMapper\models\Friends' );
        class_exists( '\WScore\DataMapper\Entity\EntityAbstract' );
        class_exists( '\WSTests\DataMapper\entities\friend' );
    }
    // +----------------------------------------------------------------------+
    function test_new_friend()
    {
        $entity = new friend( $this->friend, EntityInterface::_ID_TYPE_VIRTUAL );
        $this->assertEquals( friend::getStaticModelName(), $entity->getModelName() );
        $this->assertFalse( $entity->isIdPermanent() );
        $this->assertEquals( $this->friend->getModelName(), $entity->getModelName() );
        $this->assertEquals( $this->friend->getModelName(true), $entity->getModelName(true) );
        $this->assertEquals( 'Friends.0.1', $entity->getCenaId() );
    }
    function test_to_delete()
    {
        $entity = new friend( $this->friend, EntityInterface::_ID_TYPE_VIRTUAL );
        $this->assertFalse( $entity->toDelete() );
        
        $entity->toDelete( false );
        $this->assertFalse( $entity->toDelete() );

        $entity->toDelete( '1' );
        $this->assertFalse( $entity->toDelete() );

        $entity->toDelete( true );
        $this->assertTrue( $entity->toDelete() );
    }
    function test_system_id()
    {
        $entity = new friend( $this->friend, EntityInterface::_ID_TYPE_VIRTUAL );
        $this->assertFalse( $entity->isIdPermanent() );
        $this->assertNull( $entity->getId() );
        
        $entity->setSystemId( '10' );
        $this->assertTrue( $entity->isIdPermanent() );
        $this->assertEquals( '10', $entity->getId() );
    }
}
