<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Helper;

/**
 * Class UpdatedAt
 *
 * @package WScore\DataMapper\Filter
 * 
 * @cacheable
 */
class SetUpdateTime extends SetTimeAbstract
{
    /**
     * @var string
     */
    public $column_name = 'updated_at';

    /**
     * @param array $data
     * @return array
     */
    public function onInsert( $data ) {
        $data = $this->setTime( $data );
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function onUpdate( $data ) {
        $data = $this->setTime( $data );
        return $data;
    }
}