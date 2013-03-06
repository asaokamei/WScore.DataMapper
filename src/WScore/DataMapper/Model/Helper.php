<?php
namespace WScore\DataMapper;

/**
 * helper class for model class.
 */
class Model_Helper
{
    private static $now=null;
    /**
     * @param mixed $datetime
     */
    public static function setCurrent( $datetime=null ) {
        self::$now = $datetime;
    }

    /**
     * @return string
     */
    public static function getCurrent() {
        $now = self::$now ?: new \DateTime();
        $fmt = 'Y-m-d H:i:s';
        return $now->format( $fmt );
    }

    /**
     * @param array $values
     * @param array $extra
     * @return array
     */
    public static function updatedAt( $values, $extra )
    {
        if( isset( $extra[ 'updated_at' ] ) ) {
            foreach( $extra[ 'updated_at' ] as $column ) {
                $values[ $column ] = self::getCurrent();
            }
        }
        return $values;
    }

    /**
     * @param array $values
     * @param array $extra
     * @return array
     */
    public static function createdAt( $values, $extra )
    {
        if( isset( $extra[ 'created_at' ] ) ) {
            foreach( $extra[ 'created_at' ] as $column ) {
                $values[ $column ] = self::getCurrent();
            }
        }
        return $values;
    }

    /**
     * @param $define
     * @param $relations
     * @param $id_name
     * @return array
     */
    public static function prepare( $define, $relations, $id_name=null )
    {
        // create properties and dataTypes from definition.
        $properties = array();
        $dataTypes  = array();
        $extraTypes = array();
        $protected  = array();
        if( !empty( $define ) ) {
            foreach( $define as $key => $info ) {
                $properties[ $key ] = array( 'label' => $info[0] );
                $dataTypes[  $key ] = $info[1];
                if( isset( $info[2] ) ) {
                    $extraTypes[ $info[2] ][] = $key;
                }
            }
        }
        // set up primaryKey if id_name is set.
        if( isset( $id_name ) ) {
            $extraTypes[ 'primaryKey' ][] = $id_name;
        }
        // protect some properties in extraTypes.
        foreach( $extraTypes as $type => $list ) {
            if( in_array( $type, array( 'primaryKey', 'created_at', 'updated_at' ) ) ) {
                foreach( $list as $key ) {
                    array_push( $protected, $key );
                }
            }
        }
        // protect properties used for relation.
        if( !empty( $relations ) ) {
            foreach( $relations as $relInfo ) {
                if( $relInfo[ 'relation_type' ] == 'HasOne' ) {
                    $column = self::arrGet( $relInfo, 'source_column', $id_name );
                    array_push( $protected, $column );
                }
            }
        }
        return compact( 'properties', 'dataTypes', 'extraTypes', 'protected' );
    }

    /**
     * @param array $arr
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function arrGet( $arr, $key, $default=null ) {
        if( is_array( $arr ) && array_key_exists( $key, $arr ) ) {
            return $arr[ $key ];
        }
        elseif( is_object( $arr ) && isset( $arr->$key ) ) {
            return $arr->$key;
        }
        return $default;
    }

    /**
     * @param array  $data
     * @param string $select
     * @return array
     */
    public static function packToArray( $data, $select )
    {
        $packed = array();
        if( empty( $data ) ) return $packed;
        foreach( $data as $rec ) {
            $packed[] = self::arrGet( $rec, $select );
        }
        $packed = array_values( $packed );
        return $packed;
    }
}