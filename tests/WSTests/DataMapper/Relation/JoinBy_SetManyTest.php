<?php
namespace WSTests\DataMapper\Relation;

use \WSTests\DataMapper\entities\friend;

require( __DIR__ . '/../../../autoloader.php' );

class JoinBy_SetManyTest extends JoinBySetUp
{
    function test_insert_data()
    {
        $em = $this->em;
        $friend = $em->newEntity( $this->friendEntity, $this->getFriendData(1) );
        $group1 = $em->newEntity( $this->groupEntity,  $this->getGroupData(0) );
        $group2 = $em->newEntity( $this->groupEntity,  $this->getGroupData(1) );
        $group3 = $em->newEntity( $this->groupEntity,  $this->getGroupData(2) );
        $group4 = $em->newEntity( $this->groupEntity,  $this->getGroupData(3) );
        $relation = $em->relation( $friend, 'groups' );
        $relation->set( $group1 );
        $relation->set( $group3 );
        $em->save();
    }
    function test_fetch_data()
    {
        $em = $this->em;
        $friend   = $em->fetch( $this->friendEntity,  '1' );
        /** @var $friend \WSTests\DataMapper\entities\friend */
        $friend   = $friend[0];
        $relation = $em->relation( $friend, 'groups' );
        $targets  = $relation->fetch();
        $this->assertEquals( 1, count( $targets ) );
    }
}
