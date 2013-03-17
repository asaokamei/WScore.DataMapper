<?php
namespace WScore\DataMapper\Role;

use \WScore\DataMapper\Entity\EntityInterface;

interface RoleInterface
{
    /**
     * @param EntityInterface    $entity
     */
    public function register( $entity );
    public function retrieve();
    public function getId();
    public function getIdName();
}