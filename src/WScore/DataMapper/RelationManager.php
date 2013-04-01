<?php
namespace WScore\DataMapper;

/**
 * Class RelationManager
 * @package WScore\DataMapper
 *
 * @singleton
 */
class RelationManager
{
    /**
     * @var Relation\RelationInterface[][]
     */
    protected $relations = array();

    /**
     * @param \WScore\DataMapper\EntityManager $em
     * @param string                 $name
     * @param Entity\EntityInterface $source
     * @param array                  $info
     * @throws \RuntimeException
     * @return Relation\RelationInterface
     */
    public function relation( $em, $name, $source, $info )
    {
        $cenaId = $source->getCenaId();
        if( !isset( $this->relations[ $cenaId ][ $name ] ) )
        {
            $type = $info[ 'type' ];
            $class = '\WScore\DataMapper\Relation\\' . ucwords( $type );
            if( !class_exists( $class ) ) {
                throw new \RuntimeException( "no relation class for $class" );
            }
            $this->relations[ $cenaId ][ $name ] = new $class( $em, $name, $source, $info );
        }
        return $this->relations[ $cenaId ][ $name ];
    }

    /**
     * links unlinked relations.
     * returns number of unlinked relations.
     *
     * @param null|string $saveId
     * @return int
     */
    public function link( $saveId=null )
    {
        $countUnLinked = 0;
        foreach( $this->relations as $cenaId => $list ) {
            if( $saveId && $cenaId !== $saveId ) continue;
            foreach( $list as $name => $relation ) {
                /** @var $relation Relation\RelationAbstract */
                if( !$relation->linked ) {
                    $relation->link();
                }
                if( !$relation->linked ) {
                    $countUnLinked++;
                }
            }
        }
        return $countUnLinked;
    }
}