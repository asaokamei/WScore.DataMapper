<?php
namespace WScore\DataMapper;

use \WScore\DataMapper\Entity\EntityInterface;

class EntityManager
{
    /**
     * @param Model        $model
     * @param string       $class
     * @param string       $value
     * @param null|string  $column
     * @param bool|string  $packed
     * @return \PdoStatement
     */
    public function fetchModel( $model, $class, $value, $column=null, $packed=false )
    {
        $stmt  = $model->fetch( $value, $column, $packed );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, $class, array( $this ) );
        $class = null;
        return $stmt;
    }


    /**
     * @param string $class
     * @param array  $data
     * @return \WScore\DataMapper\Entity\EntityInterface
     */
    public function newEntity( $class, $data=array() )
    {
        /** @var $record \WScore\DataMapper\Entity\EntityInterface */
        $record = new $class( $this, EntityInterface::_ID_TYPE_VIRTUAL );
        if( !empty( $data ) ) {
            foreach( $data as $key => $val ) {
                $record->$key = $val;
            }
        }
        return $record;
    }
}