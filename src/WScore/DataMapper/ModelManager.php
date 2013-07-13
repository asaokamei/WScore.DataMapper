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
    /** @var \WScore\DataMapper\Model\Model[] */
    protected $models = array();

    /**
     * @Inject
     * @var ContainerInterface
     */
    public $container;

    /**
     * @param Entity\EntityInterface|string $entity
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

}