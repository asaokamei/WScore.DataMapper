<?php
namespace WScore\DataMapper\Entity;

use Traversable;

class Collection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /** @var EntityInterface[]  */
    public $entities = array();

    /** @var int[] */
    public $cenaIds = array();

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
        $cenaId = $entity->getCenaId();
        if( !$this->exists( $entity ) ) {
            $this->entities[] = $entity;
            end( $this->entities );
            $this->cenaIds[ $cenaId ] = key( $this->entities );
        }
    }

    /**
     * @param \WScore\DataMapper\Entity\EntityInterface $entity
     */
    public function remove( $entity )
    {
        $cenaId = $entity->getCenaId();
        if( !isset( $this->cenaIds[ $cenaId ] ) ) return;
        $offset = $this->cenaIds[ $cenaId ];
        unset( $this->entities[ $offset ] );
        unset( $this->cenaIds[ $cenaId ] );
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
            return $this->entities[ $this->cenaIds[ $cenaId ] ];
        }
        return false;
    }
    
    /**
     * @param array|string $values
     * @param string|null  $column
     * @param string|null  $model
     * @return Collection
     */
    public function get( $values, $column=null, $model=null )
    {
        if( !is_array( $values ) ) $values = array( $values );
        $result = array();
        if( isset( $model ) && substr( $model, 0, 1 ) === '\\' ) $model = substr( $model, 1 );
        foreach( $this->entities as $entity )
        {
            if( $model && $model !== $entity->getModelName() ) continue;
            if( empty( $values ) ) {
                $result[] = $entity;
                continue;
            }
            if( !$column ) {
                $prop = $entity->getId();
            }
            else {
                $prop = $entity[ $column ];
            }
            if( in_array( $prop, $values ) ) $result[] = $entity;
        }
        $result = $this->collection( $result );
        return $result;
    }
    
    /**
     * @param string     $model
     * @param array|string $values
     * @param string|null $column
     * @return Collection
     */
    public function fetch( $model, $values=null, $column=null )
    {
        return $this->get( $values, $column, $model );
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