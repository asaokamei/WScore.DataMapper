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
     * @var array
     */
    protected $modelNames = array();

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
        if( !array_key_exists( $modelName, $this->models ) ) {
            $this->models[ $modelName ] = $this->container->load( $modelName );
        }
        if( !$this->models[ $modelName ] ) {
            throw new \RuntimeException( 'model not found: '.$modelName );
        }
        return $this->models[ $modelName ];
    }

    /**
     * @param Entity\EntityInterface|string $entity
     * @return string
     */
    public function getModelName( $entity )
    {
        if( is_string( $entity ) ) {
            if( array_key_exists( $entity, $this->modelNames ) ) {
                return $this->modelNames[ $entity ];
            }
            $modelName = $this->extractModelName( $entity );
            $this->modelNames[ $entity ] = $modelName;
        }
        else {
            $class = get_class( $entity );
            if( array_key_exists( $class, $this->modelNames ) ) {
                return $this->modelNames[ $class ];
            }
            $modelName = $this->extractModelName( $entity );
            $this->modelNames[ $class ] = $modelName;
        }
        return $modelName;
    }

    /**
     * @param Entity\EntityInterface|string $entity
     * @throws \RuntimeException
     * @return string
     */
    private function extractModelName( $entity )
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
            $modelName = $entity->getModelName();
        }
        if( !$modelName ) {
            throw new \RuntimeException( 'cannot find model name for an entity' );
        }
        if( substr( $modelName, 0, 1 ) == '\\' ) $modelName = substr( $modelName, 1 );
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