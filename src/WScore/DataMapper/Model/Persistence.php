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
     * @Inject
     * @var \WScore\DbAccess\Query  
     */
    protected $query;

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
     * @param string $table
     * @param string $id_name
     */
    public function setTable( $table, $id_name )
    {
        $this->table   = $table;
        $this->id_name = $id_name;
    }

    /**
     * @param \WScore\DataMapper\Model_Property $property
     */
    public function setProperty( $property )
    {
        $this->property = $property;
    }

    /**
     * @return \WScore\DbAccess\Query
     */
    public function query() {
        // set fetch mode after query is cloned in table() method.
        return $this->query
            ->table( $this->table, $this->id_name );
    }
    // +----------------------------------------------------------------------+
    //  Basic DataBase Access.
    // +----------------------------------------------------------------------+
    /**
     * fetches entities from simple condition.
     * use $select to specify column name to get only the column you want.
     * 
     * packed: packs result to a simple array, i.e. [ 1, 2, 5,..].
     *   false:  ignored,
     *   true:   packs data with the selected column (id or $columnd)
     *   string: packs data with the $pack column. 
     *
     * @param string|array $value
     * @param null         $column
     * @param bool|string  $packed
     * @return \PdoStatement
     */
    public function fetch( $value, $column=null, $packed=false )
    {
        $query = $this->query();
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
     * @param array   $data
     * @param null                     $extra
     * @return Model
     */
    public function update( $data, $extra=null )
    {
        if( $extra ) {
            $id   = $data;
            $data = $extra;
        } else {
            $id = $data[ $this->id_name ];
        }
        $data = $this->property->restrict( $data );
        unset( $data[ $this->id_name ] );
        $data = $this->property->updatedAt( $data );
        $this->query()->id( $id )->update( $data );
        return $this;
    }

    /**
     * insert data into database.
     *
     * @param array   $data
     * @return string|bool             id of the inserted data or true if id not exist.
     */
    public function insertValue( $data )
    {
        $data = $this->property->restrict(  $data );
        $data = $this->property->updatedAt( $data );
        $data = $this->property->createdAt( $data );
        $this->query()->insert( $data );
        $id = Model_Helper::arrGet( $data, $this->id_name, true );
        return $id;
    }

    /**
     * deletes an id.
     * override this method (i.e. just tag some flag, etc.).
     *
     * @param string $id
     */
    public function delete( $id )
    {
        $this->query()->clearWhere()
            ->id( $id )->limit(1)->execType( 'Delete' )->exec();
    }

    /**
     * @param Entity_Interface|array   $data
     * @return string                 id of the inserted data
     */
    public function insertId( $data )
    {
        unset( $data[ $this->id_name ] );
        $this->insertValue( $data );
        $id = $this->query->lastId();
        $data[ $this->id_name ] = $id;
        return $id;
    }
    // +----------------------------------------------------------------------+
}

