<?php
namespace WSTests\DataMapper\models;

use \WScore\DataMapper\Model;

class Groups extends Model
{
    protected $table = 'groups';

    protected $id_name = 'group_code';

    public function __construct()
    {
        parent::__construct();
        $csv = file_get_contents( __DIR__ . '/groups.csv' );
        $this->property->prepare( $csv );
    }

    public function setupTable()
    {
        $table = $this->table;
        $sql = "DROP TABLE IF EXISTS {$table}";
        $this->persistence->query()->dbAccess()->execSQL( $sql );
        $sql = "
            CREATE TABLE {$table} (
              group_code   varchar(64) NOT NULL,
              name text NOT NULL DEFAULT '',
              created_at datetime,
              updated_at datetime,
              constraint groups_pkey PRIMARY KEY (
                group_code
              )
            )
        ";
        $this->persistence->query()->dbAccess()->execSQL( $sql );
    }

    /**
     * @param int $idx
     * @return array
     */
    static function makeGroup( $idx=0 )
    {
        $groups = array(
            array( 'group_code' => 'demo', 'name' => 'demonstration'),
            array( 'group_code' => 'test', 'name' => 'testing'),
            array( 'group_code' => 'more', 'name' => 'more more more'),
        );
        if( isset( $groups[ $idx ] ) ) {
            $values = $groups[ $idx ];
        } else {
            $values = $groups[2];
            $values[ 'group_code' ] .= $idx;
            $values[ 'name' ]       .= '#'.$idx;
        }
        return $values;
    }

    /**
     * @param array $data
     * @return string
     */
    public function insert( $data )
    {
        return $this->persistence->insertValue( $data );
    }
}
