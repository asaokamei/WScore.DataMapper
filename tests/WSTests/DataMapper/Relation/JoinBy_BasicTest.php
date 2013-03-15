<?php
namespace WSTests\DataMapper\Relation;

use \WSTests\DataMapper\entities\friend;

require( __DIR__ . '/../../../autoloader.php' );

class JoinBy_BasicTests extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $config = 'dsn=mysql:dbname=test_WScore username=admin password=admin';

    public static $table = 'friend';

    /** @var \WScore\DataMapper\EntityManager */
    public $em;

    public $friendEntity = '\WSTests\DataMapper\entities\friend';

    public $groupEntity = '\WSTests\DataMapper\entities\group';

    public $fr2grEntity = '\WSTests\DataMapper\entities\fr2gr';

    // +----------------------------------------------------------------------+
    static function setUpBeforeClass()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $container->set( '\Pdo', self::$config );
        
        /** @var $friend \WSTests\DataMapper\models\Friends */
        $friend = $container->get( '\WSTests\DataMapper\models\Friends' );
        $friend->setupTable();
        
        /** @var $friend \WSTests\DataMapper\models\Groups */
        $group = $container->get( '\WSTests\DataMapper\models\Groups' );
        $group->setupTable();

        /** @var $friend \WSTests\DataMapper\models\Fr2gr */
        $fr2gr = $container->get( '\WSTests\DataMapper\models\Fr2gr' );
        $fr2gr->setupTable();
        
        class_exists( '\WScore\DataMapper\Entity\EntityAbstract' );
        class_exists( '\WSTests\DataMapper\entities\friend' );
        class_exists( '\WSTests\DataMapper\entities\group' );
        class_exists( '\WSTests\DataMapper\entities\fr2gr' );
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

    function getGroupData( $idx=1 )
    {
        /** @var $model \WSTests\DataMapper\models\Groups */
        $model = $this->em->getModel( $this->groupEntity );
        return $model->makeGroup( $idx );
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
        $friend = $em->newEntity( $this->friendEntity, $this->getFriendData(1) );
        $group1 = $em->newEntity( $this->groupEntity,  $this->getGroupData(0) );
        $group2 = $em->newEntity( $this->groupEntity,  $this->getGroupData(1) );
        $em->save();
        $relation = $em->relation( $friend, 'groups' );
        $relation->fetch();

        // basic check.
        $this->assertEquals( 'WScore\DataMapper\Relation\JoinBy', get_class( $relation ) );

        $relation->set( $group1 );
        $relation->set( $group2 );
        $joiners = $this->em->get( $this->fr2grEntity, $friend->getId(), 'friend_id' );
        // there are 2 join entities, for group1 and group2. 
        $this->assertEquals( 2, count( $joiners ) );
        // each join entity has friend_id of the $friend. 
        $this->assertEquals( $friend[ 'friend_id' ], $joiners[0][ 'friend_id' ] );
        $this->assertEquals( $friend[ 'friend_id' ], $joiners[1][ 'friend_id' ] );
        // each join entity has group_code that are.. 
        $this->assertNotEquals( $joiners[0][ 'group_code' ], $joiners[1][ 'group_code' ] );
        $this->assertTrue( in_array( $joiners[0][ 'group_code' ], array( 'test', 'demo' ) ) );
        $this->assertTrue( in_array( $joiners[1][ 'group_code' ], array( 'test', 'demo' ) ) );

        $em->save();
    }
    function test_fetch_joinBy_entities()
    {
        $em = $this->em;
        $friend   = $em->fetch( $this->friendEntity,  '1' );
        /** @var $friend \WSTests\DataMapper\entities\friend */
        $friend   = $friend[0];
        // get related contacts.
        $relation = $em->relation( $friend, 'groups' );
        $groups   = $relation->fetch();
        
        // there are 2 join entities, for group1 and group2. 
        $this->assertEquals( 2, count( $groups ) );
        // each join entity has group_code that are.. 
        $this->assertNotEquals( $groups[0][ 'group_code' ], $groups[1][ 'group_code' ] );
        $this->assertTrue( in_array( $groups[0][ 'group_code' ], array( 'test', 'demo' ) ) );
        $this->assertTrue( in_array( $groups[1][ 'group_code' ], array( 'test', 'demo' ) ) );
    }
    function test_link()
    {
        $em = $this->em;
        /** @var $friend \WSTests\DataMapper\entities\friend */
        /** @var $contact \WSTests\DataMapper\entities\groups */
        $friend   = $em->newEntity( $this->friendEntity, $this->getFriendData(2) );
        $group    = $em->newEntity( $this->groupEntity,  $this->getGroupData(2) );
        $relation = $em->relation( $friend, 'groups' )->set( $group );
        
        // check friend_id is not set yet, and relation is not linked. 
        $this->assertEquals( null, $contact->friend_id );
        $this->assertFalse( $relation->isLinked() );

        $em->save();
        // still, friend_id is not set yet, and relation is not linked. 
        $this->assertEquals( null, $contact->friend_id );
        $this->assertFalse( $relation->isLinked() );
        $relation->link();

        // check friend_id is not set yet, and relation is not linked. 
        $this->assertEquals( '2', $contact->friend_id );
        $this->assertTrue( $relation->isLinked() );
        $em->save();
    }
}
