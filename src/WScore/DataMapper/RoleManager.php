<?php
namespace WScore\DataMapper;

use \WScore\DiContainer\ContainerInterface;
use \WScore\DataMapper\Entity\EntityInterface;
use \WScore\DataMapper\Role\RoleInterface;

class RoleManager
{
    /** 
     * @Inject
     * @var ContainerInterface 
     */
    protected $container;

    // +----------------------------------------------------------------------+
    public function __construct() {}

    /**
     * @param EntityInterface|RoleInterface $entity
     * @param string                        $roleName
     * @throws \InvalidArgumentException
     * @return RoleInterface
     */
    public function applyRole( $entity, $roleName )
    {
        if( $entity instanceof RoleInterface ) {
            $entity = $entity->retrieve();
        }
        if( !is_object( $entity ) || !$entity instanceof EntityInterface ) {
            throw new \InvalidArgumentException( 'entity no an object or not EntityInterface. ' );
        }
        /** @var $role RoleInterface */
        $role = $this->forgeRole( $roleName );
        $role->register( $entity );
        return $role;
    }

    /**
     * @param string $roleName
     * @return RoleInterface
     */
    private function forgeRole( $roleName )
    {
        if( strpos( $roleName, '\\' ) !== false ) {
            $class = $roleName;
        }
        else {
            $class = '\WScore\DataMapper\Role\\' . ucwords( $roleName );
        }
        return $this->container->get( $class );
    }
    // +----------------------------------------------------------------------+
    /**
     * @param EntityInterface|RoleInterface $entity
     * @return \WScore\DataMapper\Role\Active
     */
    public function applyActive( $entity ) {
        return $this->applyRole( $entity, 'active' );
    }

    /**
     * @param EntityInterface|RoleInterface $entity
     * @return \WScore\DataMapper\Role\DataIO
     */
    public function applyDataIO( $entity ) {
        return $this->applyRole( $entity, 'dataIO' );
    }
    // +----------------------------------------------------------------------+
}