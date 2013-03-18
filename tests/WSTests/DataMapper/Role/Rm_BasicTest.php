<?php
namespace WSTests\DataMapper\Role;

require( __DIR__ . '/../../../autoloader.php' );

class Rm_BasicTests extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DataMapper\RoleManager */
    public $rm;

    /** @var \WScore\DataMapper\EntityManager */
    public $em;

    public $friendEntity = '\WSTests\DataMapper\entities\friend';

    /**
     *
     */
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );
        $container->set( '\Pdo', 'dsn=sqlite::memory:' );
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

    // +----------------------------------------------------------------------+
    function test_basic()
    {
        $this->assertEquals( 'WScore\DataMapper\RoleManager', get_class( $this->rm ) );
    }
    function test_apply()
    {
        $roleName = '\WSTests\DataMapper\Role\MockRole';
        $entity   = $this->em->newEntity( $this->friendEntity, $this->getFriendData(1) );
        $role     = $this->rm->applyRole( $entity, $roleName );
        $this->assertEquals( 'WSTests\DataMapper\Role\MockRole', get_class( $role ) );
        $retrieved= $role->retrieve();
        $this->assertEquals( 'WSTests\DataMapper\entities\friend', get_class( $retrieved ) );
    }
    function test_active()
    {
        $entity   = $this->em->newEntity( $this->friendEntity, $this->getFriendData(1) );
        $role     = $this->rm->applyActive( $entity );
        $this->assertEquals( 'WScore\DataMapper\Role\Active', get_class( $role ) );
    }
    function test_register_role()
    {
        $roleName = '\WSTests\DataMapper\Role\MockRole';
        $entity   = $this->em->newEntity( $this->friendEntity, $this->getFriendData(1) );
        $role     = $this->rm->applyRole( $entity, $roleName );
        $this->assertEquals( 'WSTests\DataMapper\Role\MockRole', get_class( $role ) );

        $role     = $this->rm->applyActive( $role );
        $this->assertEquals( 'WScore\DataMapper\Role\Active', get_class( $role ) );

        $retrieved= $role->retrieve();
        $this->assertEquals( 'WSTests\DataMapper\entities\friend', get_class( $retrieved ) );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    function test_give_bad_argument()
    {
        $roleName = '\WSTests\DataMapper\Role\MockRole';
        $entity   = array( 'bad' => 'not an entity' );
        $role     = $this->rm->applyRole( $entity, $roleName );
    }
}
