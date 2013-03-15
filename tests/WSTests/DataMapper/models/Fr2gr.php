<?php
namespace WSTests\DataMapper\models;

use \WScore\DataMapper\Model;

class Fr2gr extends Model
{
    protected $table = 'fr2gr';
    
    protected $id_name = 'fr2gr_id';

    public function __construct()
    {
        parent::__construct();
        $csv = file_get_contents( __DIR__ . '/fr2gr.csv' );
        $this->property->prepare( $csv );
    }

    public function setupTable()
    {
        $table = $this->table;
        $sql = "DROP TABLE IF EXISTS {$table}";
        $this->persistence->query()->dbAccess()->execSQL( $sql );
        $sql = "
            CREATE TABLE {$table} (
              fr2gr_id          SERIAL,
              friend_id         int,
              group_code        Varchar(64),
              created_at        datetime,
              updated_at        datetime,
              constraint fr2gr_pkey PRIMARY KEY (
                fr2gr_id
              ),
              constraint fr2gr_joins UNIQUE (
                friend_id, group_code
              )
            )
        ";
        $this->persistence->query()->dbAccess()->execSQL( $sql );
    }

    /**
     * @param int $idx
     * @return array
     */
    static function makeContact( $idx=0 )
    {
        $type = array( '1', '2', '3' );
        $type = $type[ $idx % 3 ];
        $values = array(
            'info' => 'my contact',
            'type' => $type,
        );
        if( $idx > 0 ) {
            $values[ 'info' ] .= '#' . $idx;
        }
        return $values;
    }
}