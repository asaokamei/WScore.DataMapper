<?php

namespace WScore\DataMapper\Filter;

class ForUpdate implements FilterInterface
{
    /**
     * @param \WScore\DbAccess\Query $query
     * @return \WScore\DbAccess\Query
     */
    public function onQuery( $query )
    {
        $query->forUpdate();
        return $query;
    }
}