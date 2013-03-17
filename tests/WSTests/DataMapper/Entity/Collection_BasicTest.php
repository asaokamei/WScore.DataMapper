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
    function test_unset()
    {
        // set up with 4 data. 
        $set = $this->getFriendDataSet(4);
        $col = $this->c->collection( $set );
        $this->assertEquals( 4, $col->count() );
        foreach( $col as $idx => $entity ) {
            $this->assertSame( $set[$idx], $entity );
        }
        // removed #3, which is the last entity. 
        unset( $col[3] );
        $this->assertEquals( 3, $col->count() );
        foreach( $col as $idx => $entity ) {
            $this->assertSame( $set[$idx], $entity );
        }
        // removed #1, which is the second entity. 
        unset( $col[1] );
        $this->assertEquals( 2, $col->count() );
        foreach( $col as $idx => $entity ) {
            $this->assertSame( $set[$idx], $entity );
        }
        // removing #3 again won't have no effect. 
        unset( $col[3] );
        $this->assertEquals( 2, $col->count() );
        foreach( $col as $idx => $entity ) {
            $this->assertSame( $set[$idx], $entity );
        }
    }
    function test_clear()
    {
        $set = $this->getFriendDataSet(4);
        $col = $this->c->collection( $set );
        $this->assertEquals( 4, $col->count() );
        $this->assertEquals( $set[0], $col[0] );
        $this->assertSame(   $set[0], $col[0] );
        
        $col->clear();
        $col = $this->c->collection( $set );
        $this->assertEquals( 4, $col->count() );
        $this->assertEquals( $set[0], $col[0] );
        $this->assertSame(   $set[0], $col[0] );
    }
    function test_offsetSet()
    {
        $set = $this->getFriendDataSet(3);
        $col = $this->c->collection();
        $col[] = $set[0];
        $col[] = $set[1];
        $col[4]= $set[2];
        $this->assertEquals( 3, $col->count() );
        foreach( $col as $idx => $entity ) {
            if( $idx === 4 ) $idx = 2;
            $this->assertSame( $set[$idx], $entity );
        }
    }
    // +----------------------------------------------------------------------+
    function test_set()
    {
        $set = $this->getFriendDataSet(2);
        $col = $this->c->collection( $set );
        $this->assertEquals( 2, $col->count() );
        $col->set( 'friend_name', 'set all' );
        foreach( $col as $entity ) {
            $this->assertEquals( 'set all', $entity->friend_name );
        }
    }
    function test_fetch()
    {
        $set = $this->getFriendDataSet(4);
        $col = $this->c->collection( $set );
        $this->assertEquals( 4, $col->count() );

        $model = $this->em->getModel( $this->friendEntity );
        $female = $col->fetch( $model->getModelName(), 'F', 'gender' );
        $this->assertTrue( is_array( $female ) );
        $this->assertEquals( 2, count( $female ) );
        foreach( $female as $entity ) {
            $this->assertEquals( 'F', $entity[ 'gender' ] );
        }
    }
    function test_pack()
    {
        $set = $this->getFriendDataSet(4);
        $col = $this->c->collection( $set );
        $this->assertEquals( 4, $col->count() );
        // pack friend's name. 
        $packed = $col->pack( 'friend_name' );
        $this->assertEquals( 4, count( $packed ) );
        foreach( $set as $friend ) {
            $this->assertTrue( in_array( $friend->friend_name, $packed ) );
        }
        // pack gender. 
        $packed = $col->pack( 'gender' );
        $this->assertEquals( 2, count( $packed ) );
        foreach( $set as $friend ) {
            $this->assertTrue( in_array( $friend->gender, $packed ) );
        }
    }
    function test_pack_array()
    {
        $set = $this->getFriendDataSet(4);
        $col = $this->c->collection( $set );
        $this->assertEquals( 4, $col->count() );
        // pack friend's name. 
        $packed = $col->pack( array( 'friend_name', 'gender' ) );
        $this->assertEquals( 4, count( $packed ) );
        $names = array();
        $gender = array();
        foreach( $packed as $data ) {
            $names[] = $data[ 'friend_name' ];
            $gender[] = $data[ 'gender' ];
        }
        foreach( $set as $friend ) {
            $this->assertTrue( in_array( $friend->friend_name, $names ) );
            $this->assertTrue( in_array( $friend->gender,      $gender ) );
        }
    }
    function test_toDelete()
    {
        $set = $this->getFriendDataSet(2);
        $col = $this->c->collection( $set );
        $this->assertEquals( 2, $col->count() );
        /** @var $entity EntityAbstract */
        // check entities are not to be deleted. 
        foreach( $col as $entity ) {
            $this->assertFalse( $entity->toDelete() );
        }
        // mark them as delete.
        $col->toDelete( true );
        foreach( $col as $entity ) {
            $this->assertTrue( $entity->toDelete() );
        }
        // mark them as un-delete.
        $col->toDelete( false );
        foreach( $col as $entity ) {
            $this->assertFalse( $entity->toDelete() );
        }
    }
}
