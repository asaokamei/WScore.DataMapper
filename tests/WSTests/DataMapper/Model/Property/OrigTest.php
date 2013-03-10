<?php
namespace WSTests\DataMapper\Model;

require( __DIR__ . '/../../../../autoloader.php' );

class Property_OrigTest extends Property_AllTests
{
    /** @var \WScore\DataMapper\Model\Property */
    public $property;

    public $define;
    public $relation;
    public $validation;
    public $selector;
    // +----------------------------------------------------------------------+
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $this->property = $container->get( '\WScore\DataMapper\Model\Property' );
        $this->define = array(
            'friend_id'     => array( 'friend code', 'number', ),
            'friend_name'   => array( 'name',        'string', ),
            'gender'        => array( 'gender',      'string', ),
            'friend_bday'   => array( 'birthday',    'string', ),
            'friend_tel'    => array( 'telephone',   'string', ),
            'tag_id'        => array( 'tag ID',      'number', ),
            'new_dt_friend' => array( 'created at',  'string', 'created_at'),
            'mod_dt_friend' => array( 'updated at',  'string', 'updated_at'),
        );
        $this->relation = array(
            'tag' => array(
                'relation_type' => 'HasOne',
                'target_model'  => 'WSTests\DbAccess\Tags',
                'source_column' => 'tag_id',
            ),
        );
        $this->validation = array(
            'friend_id'   => array( 'number' ),
            'friend_name' => array( 'text', 'required' => true ),
            'gender'      => array( 'text', 'required' => true, 'choice' => '[m:male,f:female]' ),
            'friend_bday' => array( 'date', 'required' => false ),
            'friend_tel'  => array( 'tel',  'pattern' => '[-0-9]*' ),
        );
        $this->selector = array(
            'friend_id'   => array( 'Selector', 'type' => 'text' ),
            'friend_name' => array( 'Selector', 'type' => 'text',    'extra' => 'width:43 | ime:on' ),
            'friend_bday' => array( 'Selector', 'type' => 'DateYMD', 'extra' => 'ime:off' ),
            'friend_tel'  => array( 'Selector', 'type' => 'text',    'extra' => 'ime:off' ),
        );
        $this->property->setTable( 'friend', 'friend_id' );
        $this->property->prepare( $this->define, $this->relation );
        $this->property->present( $this->validation, $this->selector );
    }
    // +----------------------------------------------------------------------+
}