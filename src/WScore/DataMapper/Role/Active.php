<?php
namespace WScore\DataMapper\Role;

use \WScore\DataMapper\EntityManager;
use \WScore\DataMapper\Relation\RelationInterface;

class Active extends RoleAbstract
{
    /** 
     * @Inject
     * @var \WScore\DataMapper\EntityManager
     */
    public $em;
    // +----------------------------------------------------------------------+
    /**
     * @param $name
     * @return RelationInterface
     */
    public function relation( $name )
    {
        return $this->em->relation( $this->entity, $name );
    }

    /**
     * @param bool $delete
     * @return Active
     */
    public function delete( $delete=true )
    {
        $this->entity->toDelete( $delete );
        return $this;
    }

    /**
     * TODO: maybe add $all option to save all related entities as well.
     *
     * @return Active
     */
    public function save()
    {
        $this->em->saveEntity( $this->entity );
        return $this;
    }
    // +----------------------------------------------------------------------+
}
