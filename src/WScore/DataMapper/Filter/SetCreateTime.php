<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Helper;

/**
 * Class CreatedAt
 *
 * sets created_at timestamp in an entity.
 *
 * @package WScore\DataMapper\Filter
 * 
 * @cacheable
 */
class SetCreateTime extends SetTimeAbstract
{
    /**
     * @var string
     */
    public $column_type = 'created_at';

    /**
     * @param array $data
     * @return array
     */
    public function onInsert( $data ) {
        $data = $this->setTime( $data );
        return $data;
    }
}