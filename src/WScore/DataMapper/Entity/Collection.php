<?php
namespace WScore\DataMapper\Entity;

use Traversable;

class Collection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /** @var EntityInterface[]  */
    public $entities = array();

    /** @var int[] */
    public $cenaIds = array();
    
    /** @var array  */
    protected $idxId = array();

    // +----------------------------------------------------------------------+
    public function __construct()
    {
    }

    /**
     * @param EntityInterface[] $entities
     * @return Collection
     */
    public function collection( $entities = array() )
    {
        /** @var $collection Collection */
        $collection = new static();
        foreach( $entities as $entity ) {
            $collection->add( $entity );
        }
        return $collection;
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function exists( $entity ) {
        return array_key_exists( $entity->getCenaId(), $this->cenaIds );
    }

    /**
     * @param \WScore\DataMapper\Entity\EntityInterface $entity
     */
    public function add( $entity )
    {
        if( !$entity ) return;
        if( !$this->exists( $entity ) ) {
            $this->_add( $entity );
        }
    }

    /**
     * @param \WScore\DataMapper\Entity\EntityInterface $entity
     */
    protected function _add( $entity )
    {
        $this->entities[] = $entity;
        $this->cenaIds[ $entity->getCenaId() ] = $entity;
        // create index on id. 
        $entityName = $entity->getEntityName();
        $idName = $entity->getIdName();
        $idVal  = $entity->getId();
        $this->_idx( $entityName, $idName, $idVal, $entity );
    }

    /**
     * @param $model
     * @param $idName
     * @param $idVal
     * @param $entity
     */
    protected function _idx( $model, $idName, $idVal, $entity=null ) 
    {
        if( !isset( $this->idxId[ $model ] ) ) {
            $this->idxId[ $model ] = array();
        }
        if( !isset( $this->idxId[ $model ][ $idName ] ) ) {
            $this->idxId[ $model ][ $idName ] = array();
        }
        if( !isset( $this->idxId[ $model ][ $idName ][ $idVal ] ) ) {
            $this->idxId[ $model ][ $idName ][ $idVal ] = array();
        }
        if( $entity ) {
            $this->idxId[ $model ][ $idName ][ $idVal ][] = $entity;
        }
    }

    /**
     * @param string $entityName
     * @param string $column
     */
    public function makeIndexOn( $entityName, $column )
    {
        if( substr( $entityName, 0, 1 ) === '\\' ) $entityName = substr( $entityName, 1 );
        $this->_idx( $entityName, $column, null );
        foreach( $this->entities as $entity )
        {
            if( $entityName != $entity->getEntityName() ) continue;
            $this->_idx( $entityName, $column, $entity->$column, $entity );
        }
    }
    
    /**
     * @param string $model
     * @param string $column
     * @return bool
     */
    protected function isIndexed( $model, $column ) {
        if( $model && $column ) {
            return isset( $this->idxId[ $model ][ $column ] );
        }
        return false;
    }

    /**
     * @param string $model
     * @param string $column
     * @param string $value
     * @return bool|EntityInterface[]
     */
    protected function getIndexed( $model, $column, $value ) {
        return isset( $this->idxId[ $model ][ $column ][ $value ] ) ? 
            $this->idxId[ $model ][ $column ][ $value ] : false;
    }

    /**
     * @param \WScore\DataMapper\Entity\EntityInterface $entity
     */
    public function remove( $entity )
    {
        $cenaId = $entity->getCenaId();
        if( !isset( $this->cenaIds[ $cenaId ] ) ) return;
        foreach( $this->entities as $key => $maybe ) {
            if( $cenaId == $maybe->getCenaId() ) {
                unset( $this->entities[ $key ] );
                unset( $this->cenaIds[ $cenaId ] );
            }
        }
    }

    /**
     * clears the collection.
     */
    public function clear() {
        $this->entities = array();
        $this->cenaIds  = array();
    }
    // +----------------------------------------------------------------------+
    /**
     * @param string $name
     * @param string $value
     */
    public function set( $name, $value )
    {
        if( empty( $this->entities ) ) return;
        foreach( $this->entities as $entity ) {
            $entity[ $name ] = $value;
        }
    }

    /**
     * @param string $cenaId
     * @return bool|EntityInterface
     */
    public function getByCenaId( $cenaId )
    {
        if( array_key_exists( $cenaId, $this->cenaIds ) ) {
            return $this->cenaIds[ $cenaId ];
        }
        return false;
    }
    
    /**
     * @param array|string $values
     * @param string|null  $column
     * @param string|null  $entityName
     * @return Collection
     */
    public function get( $values, $column=null, $entityName=null )
    {
        if( !is_null( $values ) && !is_array( $values ) ) $values = array( $values );
        if( strpos( $entityName, '\\' ) !== false ) {
            $entityName = substr( $entityName, strrpos( $entityName, '\\' ) + 1 );
        }
        if( $this->isIndexed( $entityName, $column ) ) {
            return $this->getByIndex( $entityName, $column, $values );
        }
        $result = $this->collection();
        foreach( $this->entities as $entity )
        {
            if( $entityName && $entityName !== $entity->getEntityName() ) continue;
            if( is_null( $values ) || empty( $values ) ) {
                $result->_add( $entity );
            }
            elseif( !$column && in_array( $entity->getId(), $values ) ) {
                $result->_add( $entity );
            }
            elseif( in_array( $entity[ $column ], $values ) ) {
                $result->_add( $entity );
            }
        }
        return $result;
    }

    /**
     * @param string  $model
     * @param string  $column
     * @param array|string $values
     * @return Collection
     */
    public function getByIndex( $model, $column, $values )
    {
        $result = $this->collection();
        if( !is_array( $values ) ) {
            $values = array( $values );
        }
        foreach( $values as $val ) {
            if( $list = $this->getIndexed( $model, $column, $val ) ) {
                foreach( $list as $entity ) {
                    $result->add( $entity );
                }
            }
        }
        return $result;
    }
    
    /**
     * @param string     $entityName
     * @param array|string $values
     * @param string|null $column
     * @return Collection
     */
    public function fetch( $entityName, $values=null, $column=null )
    {
        return $this->get( $values, $column, $entityName );
    }

    /**
     * extracts values for selected column(s) and packs into an array.
     *
     * @param string|array  $select
     * @return array
     */
    public function pack( $select )
    {
        $result = array();
        if( empty( $this->entities ) ) return $result;
        foreach( $this->entities as $rec ) {
            if( !is_array( $select ) ) {
                $result[] = $this->arrGet( $rec, $select );
            }
            else {
                $pack = array();
                foreach( $select as $item ) {
                    $pack[ $item ] = $this->arrGet( $rec, $item );
                }
                $result[] = $pack;
            }
        }
        if( !is_array( $select ) ) {
            $result = array_unique( $result );
        }
        return $result;
    }

    /**
     * @param bool $delete
     */
    public function toDelete( $delete=true ) {
        foreach( $this->entities as $entity ) {
            $entity->toDelete( $delete );
        }
    }
    // +----------------------------------------------------------------------+
    /**
     * @param array|EntityInterface $arr
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function arrGet( $arr, $key, $default=null ) {
        if( is_array( $arr ) && array_key_exists( $key, $arr ) ) {
            return $arr[ $key ];
        }
        elseif( is_object( $arr ) && isset( $arr->$key ) ) {
            return $arr->$key;
        }
        return $default;
    }

    // +----------------------------------------------------------------------+
    //  for ArrayAccess and Iterator. 
    // +----------------------------------------------------------------------+
    /**
     * Whether a offset exists
     *
     * @param mixed $offset
     * @return boolean true on success or false on failure.
     */
    public function offsetExists( $offset ) {
        return array_key_exists( $offset, $this->entities );
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset
     * @return mixed Can return all value types.
     */
    public function offsetGet( $offset ) {
        return $this->offsetExists( $offset ) ? $this->entities[ $offset ]: null;
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param EntityInterface $value
     * @return void
     */
    public function offsetSet( $offset, $value ) {
        if( !$offset ) {
            $this->add( $value );
            return;
        }
        $this->entities[ $offset ] = $value;
        $this->cenaIds[ $value->getCenaId() ] = $offset;
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset( $offset ) {
        if( !$this->offsetExists( $offset ) ) return;
        $this->remove( $this->entities[ $offset ] );
    }

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     */
    public function count() {
        return count( $this->entities );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new \ArrayIterator( $this->entities );
    }
}