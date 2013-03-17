<?php
namespace WSTests\DataMapper;

use \WScore\DataMapper\Entity\EntityAbstract;

require( __DIR__ . '/../../../autoloader.php' );

class Collection_BasicTest extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $config = 'dsn=sqlite::memory:';

    /** @var \WScore\DataMapper\EntityManager */
    public $em;

    public $friendEntity = '\WSTests\DataMapper\entities\friend';

    /** @var \WScore\DataMapper\Entity\Collection */
    public $c;

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

    function getFriendDataSet( $count=1 ) 
    {
        $set = array();
        for( $i = 0; $i < $count; $i++ ) {
            $set[] = $this->em->newEntity( $this->friendEntity, $this->getFriendData($i+1) );
        }
        return $set;
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
        $this->c  = $container->get( '\WScore\DataMapper\Entity\Collection' );
        EntityAbstract::$_id_for_new = 1;
    }
    // +----------------------------------------------------------------------+
    function test_basic_function()
    {
        $this->assertEquals( 'WScore\DataMapper\Entity\Collection', get_class( $this->c ) );
        $set = $this->getFriendDataSet(4);
        $col = $this->c->collection( $set );
        $this->assertEquals( 4, $col->count() );
        $this->assertEquals( $set[0], $col[0] );
        $this->assertSame(   $set[0], $col[0] );
    }
    function test_iteration()
    {
        $set = $this->getFriendDataSet(4);
        $col = $this->c->collection( $set );
        foreach( $col as $idx => $entity ) {
            $this->assertSame( $set[$idx], $entity );
        }
    }
    function test_iteration2()
    {
        $set = $this->getFriendDataSet(4);
        $col = $this->c->collection();
        foreach( $set as $entity ) {
            $col->add( $entity );
        }
        $this->assertEquals( 4, $col->count() );
        foreach( $col as $idx => $entity ) {
            $this->assertSame( $set[$idx], $entity );
        }
    }
    function test_exists()
    {
        $set = $this->getFriendDataSet(3);
        $col = $this->c->collection();
        $col->add( $set[0] );
        $col->add( $set[2] );
        $this->assertTrue(  $col->exists( $set[0] ) );
        $this->assertFalse( $col->exists( $set[1] ) );
        $this->assertTrue(  $col->exists( $set[2] ) );
    }
    function test_unique_entity()
    {
        $set = $this->getFriendDataSet(2);
        $col = $this->c->collection( $set );
        $this->assertEquals( 2, $col->count() );
        $this->assertEquals( $set[0], $col[0] );
        $this->assertSame(   $set[0], $col[0] );
        // create identical entity but different instance. 
        $new1 = clone( $set[0] );
        $col->add( $new1 );
        $this->assertEquals(  2, $col->count() );
        $this->assertEquals(  $new1, $col[0] );
        $this->assertNotSame( $new1, $col[0] );
    }
    function test_remove()
    {
        // set up with 4 data. 
        $set = $this->getFriendDataSet(4);
        $col = $this->c->collection( $set );
        $this->assertEquals( 4, $col->count() );
        foreach( $col as $idx => $entity ) {
            $this->assertSame( $set[$idx], $entity );
        }
        // removed #3, which is the last entity. 
        $col->remove( $set[3] );
        $this->assertEquals( 3, $col->count() );
        foreach( $col as $idx => $entity ) {
            $this->assertSame( $set[$idx], $entity );
        }
        // removed #1, which is the second entity. 
        $col->remove( $set[1] );
        $this->assertEquals( 2, $col->count() );
        foreach( $col as $idx => $entity ) {
            $this->assertSame( $set[$idx], $entity );
        }
        // removing #3 again won't have no effect. 
        $col->remove( $set[3] );
        $this->assertEquals( 2, $col->count() );
        foreach( $col as $idx => $entity ) {
            $this->assertSame( $set[$idx], $entity );
        }
    }
}
