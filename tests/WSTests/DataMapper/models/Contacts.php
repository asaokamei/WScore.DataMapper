<?php
namespace WSTests\DataMapper\models;

use \WScore\DataMapper\Model;

class Contacts extends Model
{
    protected $table = 'contact';
    
    protected $id_name = 'contact_id';

    public function __construct()
    {
        parent::__construct();
        $csv = file_get_contents( __DIR__ . '/contacts.csv' );
        $this->property->prepare( $csv );
    }

    public function setupTable()
    {
        $table = $this->table;
        $sql = "DROP TABLE IF EXISTS {$table}";
        $this->persistence->query()->dbAccess()->execSQL( $sql );
        $sql = "
            CREATE TABLE {$table} (
              contact_id       SERIAL,
              friend_id        int,
              contact_info     text,
              new_dt_contact   datetime,
              mod_dt_contact   datetime,
              constraint contact_id PRIMARY KEY (
                contact_id
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
        $values = array(
            'contact_info' => 'my contact',
        );
        if( $idx > 0 ) {
            $values[ 'contact_info' ] .= '#' . $idx;
        }
        return $values;
    }
}