<?php
namespace WSTests\DataMapper\models;

use \WScore\DataMapper\Model;

class Friends extends Model
{
    protected $table = 'friend';
    
    protected $id_name = 'friend_id';
    
    public function __construct()
    {
        parent::__construct();
        $csv = file_get_contents( __DIR__ . '/friends.csv' );
        $this->property->prepare( $csv );
    }

    public function setupTable()
    {
        $table = $this->table;
        $sql = "DROP TABLE IF EXISTS {$table}";
        $this->persistence->query()->dbAccess()->execSQL( $sql );
        $sql = "
            CREATE TABLE {$table} (
              friend_id    SERIAL,
              friend_name  text    NOT NULL,
              gender       char(1) NOT NULL,
              friend_bday  date,
              friend_tel   text    NOT NULL,
              new_dt_friend   datetime,
              mod_dt_friend   datetime,
              constraint friend_pkey PRIMARY KEY (
                friend_id
              )
            )
        ";
        $this->persistence->query()->dbAccess()->execSQL( $sql );
    }
}