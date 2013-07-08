<?php
namespace WScore\DataMapper\Entity;

class Utils
{
    /**
     * saves or delete an entity to/from database.
     *
     * @param \WScore\DataMapper\Model $model
     * @param EntityInterface $entity
     */
    public function saveEntity( $model, $entity )
    {
        if( $entity->toDelete() ) {
            if( $entity->isIdPermanent() ) { // i.e. entity is from db.
                $model->delete( $entity->getId() );
            }
            // ignore if it is not permanent data; do not have to save. 
        }
        elseif( !$entity->isIdPermanent() ) { // i.e. entity is new. insert this.
            $data = $this->entityToArray( $entity );
            $id   = $model->insert( $data );
            $entity->setSystemId( $id );
            $this->preserveOriginalValue( $entity, $model );
        }
        else {
            $id   = $entity->getId();
            $data = $this->entityToArray( $entity );
            $model->update( $id, $data );
        }
    }

    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function entityToArray( $entity ) 
    {
        $data = get_object_vars( $entity );
        foreach( $data as $key => $val ) {
            if( substr( $key, 0, 1 ) === '_' ) {
                unset( $data[ $key ] );
            }
        }
        return $data;
    }

    /**
     * checks if entity is modified or not for save. 
     * 
     * @param EntityInterface            $entity
     * @param \WScore\DataMapper\Model   $model
     * @return bool
     */
    public function isModified( $entity, $model )
    {
        // new entity must be saved. 
        if( !$entity->isIdPermanent() ) return true;
        if( $entity->toDelete() ) return true;
        $original = $entity->getEntityAttribute( 'originalValue' );
        foreach( $original as $key => $oVal ) {
            $val = isset( $entity->$key ) ? $entity->$key : null;
            if( $val !== $oVal ) return true;
        }
        return false;
    }

    /**
     * @param EntityInterface|EntityInterface[] $entity
     * @param \WScore\DataMapper\Model          $model
     */
    public function preserveOriginalValue( $entity, $model )
    {
        if( is_array( $entity ) ) {
            foreach( $entity as $e ) {
                $this->preserveOriginalValue( $e, $model );
            }
            return;
        }
        $original = clone( $entity );
        $entity->setEntityAttribute( 'originalValue', $original );
    }
    
    /**
     * @param \WScore\DataMapper\Model     $model
     * @param string    $class
     * @param array    $data
     * @param null|string $id
     * @return EntityAbstract
     */
    public function forge( $model, $class, $data=array(), $id=null )
    {
        /** @var $entity \WScore\DataMapper\Entity\EntityAbstract */
        $entity = new $class( $model, EntityInterface::_ID_TYPE_VIRTUAL, $id );
        if( !empty( $data ) ) {
            foreach( $data as $key => $val ) {
                $entity->$key = $val;
            }
        }
        return $entity;
    }
}