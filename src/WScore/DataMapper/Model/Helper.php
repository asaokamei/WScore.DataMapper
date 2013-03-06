<?php
namespace WScore\DataMapper;

/**
 * helper class for model class.
 */
class Model_Helper
{
    // +----------------------------------------------------------------------+
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

    // +----------------------------------------------------------------------+
    /**
     * @param string $csv_string
     * @return array
     */
    public static function analyzeCsv( $csv_string )
    {
        $list    = explode( "\n", $csv_string );
        $result  = array();
        $header  = true;
        $section = false;
        foreach( $list as $line )
        {
            $csv = str_getcsv( $line );
            if( !$csv[0] ) continue;
            if( substr( $csv[0], 0, 1 ) === '#' ) { // it's a section.
                $section = strtolower( substr( $csv[0], 1 ) );
                $header  = false;
                continue;
            }
            if( !$header ) { // it's a header line.
                $header = $csv;
                continue;
            }
            if( $section ) {
                $merged = self::mergeCsvWithHeader( $header, $csv );
                $id = $merged[ 'id' ];
                $result[ $section ][ $id ] = $merged;
            }
        }
        return $result;
    }

    public static function mergeCsvWithHeader( $header, $csv )
    {
        $merged = array();
        foreach( $header as $idx => $key ) {
            if( !$key ) continue;
            $val = $csv[ $idx ];
            if( strtolower( $val ) === 'true'  ) $val = true;
            if( strtolower( $val ) === 'false' ) $val = false;
            $merged[ $key ] = $val;
        }
        return $merged;
    }

    public static function analyzeTypes( $properties, $relations, $id_name )
    {
        $dataTypes  = array();
        $extraTypes = array();
        foreach( $properties as $key => $info ) {
            if( isset( $info[ 'bindType' ] ) ) {
                $dataTypes[ $key ] = $info[ 'bindType' ];
            }
            if( isset( $info[ 'extra' ] ) ) {
                $extraTypes[ $info[ 'extra' ] ][] = $key;
            }
        }
        // set up primaryKey if id_name is set.
        if( isset( $id_name ) ) {
            $extraTypes[ 'primaryKey' ][] = $id_name;
        }
        $protected  = array();
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
                if( $relInfo[ 'type' ] == 'HasOne' ) {
                    $column = self::arrGet( $relInfo, 'source', $id_name );
                    array_push( $protected, $column );
                }
            }
        }
        return compact( 'dataTypes', 'extraTypes', 'protected' );
    }
    // +----------------------------------------------------------------------+
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

    // +----------------------------------------------------------------------+
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