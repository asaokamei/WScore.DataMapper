<?php
namespace WScore\DataMapper;

/**
 * base class for dao's for database tables.
 * a Table Data Gateway pattern.
 *
 */
class Model_Persistence
{
    /** @var string                          name of database table          */
    protected $table;

    /** @var string                          name of primary key             */
    protected $id_name;

    /**
     * define property and data type. from this data,
     * properties, extraTypes and dataTypes are generated.
     * definition = array(
     *   column => [ name, data_type, extra_info ],
     * )
     *
     * @var array
     */
    protected $definition = array();

    /** @var array                           relations settings              */
    protected $relations  = array();

    /** 
     * @Inject
     * @var \WScore\DbAccess\Query  
     */
    protected $query;

    /** @var \WScore\DataMapper\Entity_Interface    return class from Pdo            */
    public $recordClassName = 'WScore\DataMapper\Entity_Generic';

    /** @var null|string    entity class name for quick methods (find/fetch). */
    protected $entityClass = null;

    /**
     * @var \WScore\DataMapper\Model_Property
     */
    protected $property;
    
    // +----------------------------------------------------------------------+
    //  Managing Object and Instances. 
    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
    }

    /**
     * @param \WScore\DataMapper\Model_Property $property
     */
    public function setProperty( $property )
    {
        $this->property = $property;
    }

    /**
     * restrict keys in the property list.
     *
     * @param array $values
     * @return array
     */
    public function restrict( $values )
    {
        if( empty( $values ) ) return $values;
        foreach( $values as $key => $val ) {
            if( !$this->property->exists( $key ) ) {
                unset( $values[ $key ] );
            }
        }
        return $values;
    }

    /**
     * @param null|string $class   entity class name.
     * @return \WScore\DbAccess\Query
     */
    public function query( $class=null ) {
        if( !$class ) $class = $this->recordClassName;
        // set fetch mode after query is cloned in table() method.
        return $this->query
            ->table( $this->table, $this->id_name )
            ->setFetchMode( \PDO::FETCH_CLASS, $class, array( $this, 'get' ) );
    }

    /**
     * set entity class for quick methods (find/fetch).
     *
     * @param $class
     */
    public function setEntityClass( $class ) {
        $this->entityClass = $class;
    }
    /**
     * @param array $data
     * @return \WScore\DataMapper\Entity_Interface
     */
    public function getRecord( $data=array() )
    {
        /** @var $record \WScore\DataMapper\Entity_Interface */
        $class  = ( $this->entityClass ) ?: $this->recordClassName;
        $record = new $class( $this, Entity_Interface::_ENTITY_TYPE_NEW_ );
        $this->entityClass = null;
        if( !empty( $data ) ) {
            foreach( $data as $key => $val ) {
                $record->$key = $val;
            }
        }
        return $record;
    }

    // +----------------------------------------------------------------------+
    //  Basic DataBase Access.
    // +----------------------------------------------------------------------+
    /**
     * @param string $id
     * @return Entity_Interface
     */
    public function find( $id ) {
        if( !$id ) return null;
        $limit = count( $id );
        $record = $this->query( $this->entityClass )->id( $id )->limit( $limit )->select();
        $this->entityClass = null;
        if( !is_array( $id ) ) $record = $record[0];
        /** @var $record Entity_Interface */
        return $record;
    }

    /**
     * fetches entities from simple condition.
     * use $select to specify column name to get only the column you want.
     *
     * @param string|array $value
     * @param null         $column
     * @param bool         $packed
     * @return array|\WScore\DataMapper\Entity_Interface[]
     */
    public function fetch( $value, $column=null, $packed=false )
    {
        $query = $this->query( $this->entityClass );
        $this->entityClass = null;
        if( !$column         ) $column = $this->id_name;
        if( $packed === true ) $packed = $column;
        if( is_null( $value ) ) {
            $query->column( $packed );
        } else {
            $query->$column->eq( $value )->column( $packed );
        }
        $record = $query->select();
        if( $packed ) {
            return Model_Helper::packToArray( $record, $packed );
        }
        return $record;
    }

    /**
     * update data. update( $entity ) or update( $id, $values ). 
     *
     * @param array   $values
     * @param null                     $extra
     * @return Model
     */
    public function update( $values, $extra=null )
    {
        if( $extra ) {
            $id = $values;
            $values = $extra;
        } else {
            $id = $values[ $this->id_name ];
        }
        $values = $this->restrict( $values );
        unset( $values[ $this->id_name ] );
        $this->property->updatedAt( $values );
        $this->query()->id( $id )->update( $values );
        return $this;
    }

    /**
     * insert data into database.
     *
     * @param Entity_Interface|array   $values
     * @return string|bool             id of the inserted data or true if id not exist.
     */
    public function insertValue( $values )
    {
        $values = $this->restrict( $values );
        $this->property->updatedAt( $values );
        $this->property->createdAt( $values );
        $this->query()->insert( $values );
        $id = Model_Helper::arrGet( $values, $this->id_name, true );
        return $id;
    }

    /**
     * deletes an id.
     * override this method (i.e. just tag some flag, etc.).
     *
     * @param string $id
     * @return \PdoStatement
     */
    public function delete( $id )
    {
        return $this->query()->clearWhere()
            ->id( $id )->limit(1)->execType( 'Delete' )->exec();
    }

    /**
     * @param Entity_Interface|array   $values
     * @return string                 id of the inserted data
     */
    public function insertId( $values )
    {
        unset( $values[ $this->id_name ] );
        $this->insertValue( $values );
        $id = $this->query->lastId();
        $values[ $this->id_name ] = $id;
        return $id;
    }
    // +----------------------------------------------------------------------+
}

