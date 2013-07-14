<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Helper;

/**
 * Class CreatedAt
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
    public $column_name = 'created_at';

    /**
     * @param array $data
     * @return array
     */
    public function __invoke( $data ) {
        $data = $this->setTime( $data );
        return $data;
    }
}