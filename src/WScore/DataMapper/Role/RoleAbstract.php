<?php
namespace WScore\DataMapper\Role;

use \WScore\DataMapper\Entity\EntityInterface;

abstract class RoleAbstract implements RoleInterface
{
    /** @var EntityInterface */
    public $entity;

    // +----------------------------------------------------------------------+
    /**
     * @param EntityInterface    $entity
     */
    public function register( $entity )
    {
        $this->entity = $entity;
    }

    /**
     * @return EntityInterface
     */
    public function retrieve() {
        return $this->entity;
    }

    /**
     * @return null|string
     */
    public function getId() {
        return $this->entity->getId();
    }

    /**
     * @return string
     */
    public function getIdName() {
        return $this->entity->getIdName();
    }
    // +----------------------------------------------------------------------+
}
