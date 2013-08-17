<?php
namespace WScore\DataMapper\Model;

class PropertySet
{
    protected $property = array();
    protected $relation = array();

    /**
     * set up properties from csv data.
     *
     * @param string $csv
     */
    public function setupCsv( $csv )
    {
        $return = Helper::analyzeCsv( $csv );
        foreach( $return as $type => $data ) {
            $this->setupProperty( $data );
        }
    }
    
    /**
     * set property to $property or $relation.
     * $property = array(
     *    'column' => [ 'type' => 'relation', ... ],
     * );
     *
     * 'type' must be present in the $property.
     * the property is used as relation if 'type' is 'relation', else use as property.
     *
     * @param array $property
     */
    public function setupProperty( $property )
    {
        if( empty( $property ) ) return;
        foreach( $property as $column => $values ) {
            if( !$type = Helper::arrGet( $values, 'type' ) ) {
                $type = $this->getProperty( $column, 'type' );
            };
            if( !$type ) continue;
            if( $type === 'relation' ) {
                $this->relation = $this->addProperty( $this->relation, $column, $values );
            } else {
                $this->property = $this->addProperty( $this->property, $column, $values );
            }
        }
        $this->setupProtection();
    }

    /**
     * @param $array
     * @param $column
     * @param $values
     * @return mixed
     */
    protected function addProperty( $array, $column, $values )
    {
        if( !is_array( $values ) ) $values = array( $values );
        if( array_key_exists( $column, $array ) ) {
            $array[ $column ] = array_merge( $array[ $column ], $values );
        } else {
            $array[ $column ] = $values;
        }
        return $array;
    }

    /**
     * sets protection for updated_at, created_at, primaryKey, and HasOne relation's column.
     */
    protected function setupProtection()
    {
        if( !empty( $this->property ) ) {
            foreach( $this->property as $column => $info ) {
                $type = $this->getProperty( $column, 'type' );
                if( !in_array( $type, array( 'updated_at', 'created_at', 'primaryKey' ) ) ) continue;
                $this->setProtected( $column );
            }
        }
        if( !empty( $this->relation ) ) {
            foreach( $this->relation as $info ) {
                if( strcasecmp( $info[ 'relation' ], 'HasOne' ) !== 0 ) continue;
                if( !$related = Helper::arrGet( $info, 'source' ) ) continue;
                $this->setProtected( $related );
            }
        }
    }

    /**
     * @param string $column
     * @return bool
     */
    public function exists( $column )
    {
        return array_key_exists( $column, $this->property );
    }

    /**
     * @param string $column
     * @param string $key
     * @return array|bool
     */
    public function getProperty( $column = null, $key = null )
    {
        if( !$column ) {
            return $this->property;
        }
        $property = Helper::arrGet( $this->property, $column );
        if( !$key ) {
            return $property;
        }
        return Helper::arrGet( $property, $key );
    }

    /**
     * @param $column
     * @param $key
     * @param $value
     */
    public function setProperty( $column, $key, $value )
    {
        if( !$this->exists( $column ) ) return;
        $this->property[ $column ][ $key ] = $value;
    }

    /**
     * @param string $column
     * @return bool
     */
    public function isProtected( $column )
    {
        if( !$this->exists( $column ) ) return false;
        return (bool) $this->getProperty( $column, 'protected' );
    }

    /**
     * @param string $column
     * @param bool $set
     */
    public function setProtected( $column, $set=true )
    {
        if( !$this->exists( $column ) ) return;
        $this->property[ $column ][ 'protected' ] = $set;
    }

    /**
     * remove protected data from $data, such as created_at column.
     * $data = array( column => value );
     * 
     * @param array $data
     * @return mixed
     */
    public function protect( $data )
    {
        if( empty( $data ) ) return $data;
        foreach( $data as $column => $val ) {
            if( $this->isProtected( $column ) ) {
                unset( $data[ $column ] );
            }
        }
        return $data;
    }

    /**
     * restrict data which exist in model's property. 
     * $data = array( column => value );
     * 
     * @param array $data
     * @return mixed
     */
    public function restrict( $data )
    {
        if( empty( $data ) ) return $data;
        foreach( $data as $name => $val ) {
            if( !$this->exists( $name ) ) {
                unset( $data[ $name ] );
            }
        }
        return $data;
    }

    /**
     * @param null|string $column
     * @return array
     */
    public function getRelation( $column = null )
    {
        return Helper::arrGet( $this->relation, $column );
    }

    /**
     * get list of columns for a given $type.
     *
     * @param $type
     * @return array
     */
    public function getByType( $type )
    {
        $type = strtolower( $type );
        $found = array();
        foreach( $this->property as $column => $info ) {
            if( strtolower( $info[ 'type' ] == $type ) ) $found[] = $column;
        }
        return $found;
    }
}