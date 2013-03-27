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

}