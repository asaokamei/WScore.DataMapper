<?php
namespace WSTests\DataMapper;

use \WScore\DataMapper\Entity\EntityInterface;
use \WSTests\DataMapper\entities\friend;
use \WSTests\DataMapper\entities\contact;

require( __DIR__ . '/../../../autoloader.php' );

class EntityBasic_Tests extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $config = 'dsn=mysql:dbname=test_WScore username=admin password=admin';

    /** @var \WSTests\DataMapper\models\Friends */
    public $friend;

    /** @var \WSTests\DataMapper\models\Contacts */
    public $contact;
    /**
     *
     */
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( '\Pdo', self::$config );
        // set up persistence model
        $this->friend  = $container->get( '\WSTests\DataMapper\models\Friends' );
        $this->contact = $container->get( '\WSTests\DataMapper\models\Contacts' );
        class_exists( '\WScore\DataMapper\Entity\EntityAbstract' );
        class_exists( '\WSTests\DataMapper\entities\friend' );
        class_exists( '\WSTests\DataMapper\entities\contact' );
    }
    // +----------------------------------------------------------------------+
    function test_new_friend()
    {
        $entity = new friend( $this->friend, EntityInterface::_ID_TYPE_VIRTUAL );
        $this->assertEquals( friend::getStaticModelName(), $entity->getModelName() );
        $this->assertFalse( $entity->isIdPermanent() );
        $this->assertEquals( $this->friend->getModelName(), $entity->getModelName() );
        $this->assertEquals( $this->friend->getModelName(true), $entity->getModelName(true) );
        $this->assertEquals( 'friend.0.1', $entity->getCenaId() );
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
    function test_entity_attribute()
    {
        $entity = new friend( $this->friend, EntityInterface::_ID_TYPE_VIRTUAL );
        $entity->setEntityAttribute( 'test', 'some value' );
        $this->assertEquals( 'some value', $entity->getEntityAttribute( 'test' ) );
    }
    function test_property_attribute()
    {
        $entity = new friend( $this->friend, EntityInterface::_ID_TYPE_VIRTUAL );
        $entity->setPropertyAttribute( 'property', 'attributes', 'more value' );
        $this->assertEquals( 'more value', $entity->getPropertyAttribute( 'property', 'attributes' ) );
        
        $attributes = $entity->getPropertyAttribute( 'property' );
        $this->assertArrayHasKey( 'attributes', $attributes );
        $this->assertEquals( 'more value', $attributes[ 'attributes' ] );
    }
    function test_array_access()
    {
        $entity = new friend( $this->friend, EntityInterface::_ID_TYPE_VIRTUAL );
        $entity->friend_name = 'test value';
        $this->assertEquals( 'test value', $entity[ 'friend_name' ] );
        
        $entity[ 'friend_name' ] = 'more value';
        $this->assertEquals( 'more value', $entity->friend_name );
    }
    // +----------------------------------------------------------------------+
    function test_new_contact()
    {
        $entity = new contact( $this->contact, EntityInterface::_ID_TYPE_VIRTUAL );
        $this->assertEquals( contact::getStaticModelName(), $entity->getModelName() );
        $this->assertFalse( $entity->isIdPermanent() );
        $this->assertEquals( $this->contact->getModelName(), $entity->getModelName() );
        $this->assertEquals( $this->contact->getModelName(true), $entity->getModelName(true) );
        $this->assertEquals( 'contact.0.7', $entity->getCenaId() );
    }
}
