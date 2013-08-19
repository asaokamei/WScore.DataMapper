<?php
namespace WScore\DataMapper;

use \WScore\DiContainer\ContainerInterface;
use \WScore\DataMapper\Entity\EntityInterface;
use \WScore\DataMapper\Entity\EntityAbstract;

/**
 * Class ModelManager
 * @package WScore\DataMapper
 *
 * @singleton
 */
class ModelManager
{
    /**
     * @var \WScore\DataMapper\Model\Model[]
     */
    protected $models = array();

    /**
     * entities' default namespace to use.
     *
     * @var null|string
     */
    protected $entityNamespace = null;

    /**
     * @Inject
     * @var ContainerInterface
     */
    public $container;

    /**
     * @param string $namespace
     */
    public function setNamespace( $namespace )
    {
        if( substr( $namespace, -1 ) !== '\\' ) $namespace .= '\\';
        $this->entityNamespace = $namespace;
    }

    /**
     * @param Entity\EntityInterface|string $entity
     * @throws \RuntimeException
     * @return \WScore\DataMapper\Model\Model
     */
    public function getModel( $entity )
    {
        $modelName = $this->getModelName( $entity );
        $modelKey  = $modelName;
        if( substr( $modelKey, 0, 1 ) == '\\' ) $modelKey = substr( $modelKey, 1 );
        $modelKey = str_replace( '\\', '-', $modelKey );
        if( !array_key_exists( $modelKey, $this->models ) ) {
            $this->models[ $modelKey ] = $this->container->get( $modelName );
        }
        if( !$this->models[ $modelKey ] ) {
            throw new \RuntimeException( 'model not found: '.$modelName );
        }
        return $this->models[ $modelKey ];
    }

    /**
     * @param Entity\EntityInterface $entity
     * @throws \RuntimeException
     * @return string
     */
    private function getModelName( $entity )
    {
        if( is_string( $entity ) ) {
            if( $this->entityNamespace && substr( $entity, 0, 1 ) !== '\\' ) {
                $entity = $this->entityNamespace . $entity;
            }
            /** @var $entity EntityAbstract  */
            if( !method_exists( $entity, 'getStaticModelName' ) ) {
                throw new \RuntimeException( 'entity class not have getStaticModelName method: ' . $entity );
            }
            $modelName = $entity::getStaticModelName();
        } else {
            if( !$entity instanceof EntityAbstract ) {
                throw new \RuntimeException( 'entity object is not an EntityAbstract' );
            }
            $modelName = $entity->getModelName();
        }
        if( !$modelName ) {
            throw new \RuntimeException( 'cannot find model name for an entity' );
        }
        return $modelName;
    }

    /**
     * @param string $event
     * @param Filter\FilterInterface $filter
     */
    public function addFilter( $event, $filter )
    {
        if( empty( $this->models ) ) return;
        foreach( $this->models as $model ) {
            $model->filter()->addFilter( $event, $filter );
        }
    }

    /**
     * @param Filter\FilterInterface $rule
     */
    public function addRule( $rule )
    {
        if( empty( $this->models ) ) return;
        foreach( $this->models as $model ) {
            $model->filter()->addRule( $rule );
        }
    }
}