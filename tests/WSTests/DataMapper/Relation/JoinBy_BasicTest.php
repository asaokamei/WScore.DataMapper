<?php
namespace WSTests\DataMapper\Relation;

use \WSTests\DataMapper\entities\friend;

require( __DIR__ . '/../../../autoloader.php' );

class JoinBy_BasicTests extends JoinBySetUp
{
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

        $relation->add( $group1 );
        $relation->add( $group2 );
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
        /** @var $group  \WSTests\DataMapper\entities\group */
        $friend   = $em->newEntity( $this->friendEntity, $this->getFriendData(2) );
        $group    = $em->newEntity( $this->groupEntity,  $this->getGroupData(2) );
        $relation = $em->relation( $friend, 'groups' )->set( $group );
        
        // check friend_id is not set yet, and relation is not linked. 
        $this->assertFalse( $relation->isLinked() );

        $em->save();
        // still, friend_id is not set yet, and relation is not linked. 
        $this->assertFalse( $relation->isLinked() );
        $relation->link();

        // check friend_id is not set yet, and relation is not linked. 
        $this->assertTrue( $relation->isLinked() );
        $em->save();
        
        // make sure that the friend and group are related. 
        $friend2 = $em->fetch( $this->friendEntity, '2' );
        $relation2 = $em->relation( $friend2[0], 'groups' );
        $fetched = $relation2->fetch();
        $group2 = $fetched[0];
        $list = array( 'group_code', 'name' );
        foreach( $list as $name ) {
            $this->assertEquals( $group[ $name ], $group2[ $name ] );
        }
    }
}
