<?php

namespace WScore\DataMapper\Filter;

/**
 * Class Paginate
 *
 * @package WScore\DataMapper\Filter
 * 
 * @method setOptions
 *
 * @cacheable
 */
class Paginate implements FilterInterface
{
    /**
     * @Inject
     * @var \WScore\DbAccess\Tools\Paginate
     */
    public $paginate;

    /**
     * @param string $name
     * @param mixed  $value
     * @return $this
     */
    public function set( $name, $value ) {
        $this->paginate->$name = $value;
        return $this;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function onQuery( $query )
    {
        $this->paginate->setQuery( $query );
        return $query;
    }

    /**
     * @param $name
     * @param $args
     * @return $this|mixed|Paginate
     */
    public function __call( $name, $args ) 
    {
        $returned = $this;
        if( method_exists( $this->paginate, $name ) ) {
            $returned = call_user_func_array( array( $this->paginate, $name ), $args );
        }
        if( $returned instanceof \WScore\DbAccess\Tools\Paginate ) {
            return $this;
        }
        return $returned;
    }
}