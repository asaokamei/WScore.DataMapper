<?php
namespace WScore\DataMapper\Model;

/**
 * helper class for model class.
 */
class Helper
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
                $id = $merged[ 'column' ];
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
            if( strtolower( $val ) === 'true'  ) {
                $val = true;
            }
            elseif( strtolower( $val ) === 'false' ) {
                $val = false;
            }
            else {
                $val = self::parseArray( $val );
            }
            $merged[ $key ] = $val;
        }
        return $merged;
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
    
    public static function parseArray( $string )
    {
        if( strlen( $string ) < 2 ) return $string;
        $check = substr( $string, 0, 1 ) . substr( $string, -1 );
        if( $check != '[]' ) return $string;
        $string = substr( $string, 1, -1 );
        $list   = explode( ',', $string );
        $string = array();
        foreach( $list as $value ) {
            list( $k, $v ) = explode( ':', $value, 2 );
            $k = trim( $k );
            $v = trim( $v );
            $string[ $k ] = $v;
        }
        return $string;
    }
}