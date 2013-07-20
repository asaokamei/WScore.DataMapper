<?php

namespace WScore\DataMapper;

class Filters
{
    /**
     * @var Model\Model
     */
    public $model;
    
    /**
     * @var Filter\FilterInterface[][]|\Closure[][]
     */
    public $filters = array();

    /**
     * @var Filter\FilterInterface[]|\Closure[]
     */
    public $rules = array();


    /**
     * @param string                   $event
     * @param Filter\FilterInterface|\Closure $filter
     * @return $this
     */
    public function addFilter( $event, $filter )
    {
        $this->filters[ $event ][] = $filter;
        return $this;
    }

    /**
     * @param Filter\FilterInterface $rule
     * @return $this
     */
    public function addRule( $rule )
    {
        $name = get_class( $rule );
        $this->rules[ $name ] = $rule;
        return $this;
    }

    /**
     * @return $this
     */
    public function clearRules() {
        $this->rules = array();
        return $this;
    }

    /**
     * @param Model\Model $model
     * @return $this
     */
    public function setModel( $model ) 
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @param string $event
     * @param mixed $data
     * @return mixed
     */
    public function event( $event, $data )
    {
        if( !isset( $this->filters[ $event ] ) ) return $data;
        $method = 'on' . ucwords( $event );
        $data   = $this->apply( $this->filters[ $event ], $method, $data );
        
        return $data;
    }

    /**
     * @param Filter\FilterInterface[]|\Closer[] $filters
     * @param                                    $method
     * @param                                    $data
     */
    private function apply( $filters, $method, $data )
    {
        if( !$filters || empty( $filters ) ) return $data;
        foreach( $filters as $filter ) {

            if( !$filter instanceof Filter\FilterInterface ) continue;
            if( method_exists( $filter, 'setModel' ) ) {
                $filter->setModel( $this->model );
            }
            if( is_callable( $filter ) ) {
                $data = $filter( $data );
            } elseif( method_exists( $filter, $method ) ) {
                $data = $filter->$method( $data );
            }
        }
        return $data;
    }
}