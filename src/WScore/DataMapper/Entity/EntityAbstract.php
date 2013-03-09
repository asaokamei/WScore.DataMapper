<?php
namespace WScore\DataMapper\Entity;

abstract class EntityAbstract implements EntityInterface, \ArrayAccess
{
    /** @var string */
    public static $_modelName;
    
    /** @var string */
    private $_model;
    
    /** @var string */
    private $_id_type;

    /** @var string */
    private $_id_name;

    /** @var string */
    private $_identifier;

    /** @var bool */
    private $_toDelete = false;
    
    /** @var array */
    private $_attrEntity = array();

    /** @var array */
    private $_attrMember = array();
    
    /** @var int */
    static $_id_for_new = 1;
    
    /**
     * @param string $id_type
     * @param \WScore\DataMapper\Model $model
     */
    public function __construct( $model, $id_type=null )
    {
        $this->_model   = $model->getModelName();
        if( isset( $id_type ) ) {
            $this->_id_type = $id_type;
        } else {
            $this->_id_type = EntityInterface::_ID_TYPE_SYSTEM;
        }
        $this->_id_name = $model->getIdName();
        if( !$this->_identifier = $this->getId() ) {
            $this->_identifier = (string) self::$_id_for_new++;
        }
    }

    /**
     * get static model name. 
     * model name does not have backslash at the beginning. 
     * 
     * @return string
     */
    public static final function getStaticModelName() {
        $model = static::$_modelName;
        if( substr( $model, 0, 1 ) === '\\' ) $model = substr( $model, 1 );
        return $model;
    }
    
    /**
     * @return null|string
     */
    public function getId() {
        $idName = $this->_id_name;
        return ( isset( $this->$idName ) ) ? $this->$idName: null;
    }

    /**
     * @param bool $short
     * @return string
     */
    public function getModelName( $short=false ) 
    {
        $model = $this->_model;
        if( $short && strpos( $model, '\\' ) !== false ) {
            $model = substr( $model, strrpos( $model, '\\' )+1 );
        }
        return $model;
    }

    /**
     * @return string
     */
    public function getCenaId() 
    {
        $model = $this->getModelName( true );
        $type  = $this->_id_type;
        $id    = $this->_identifier;
        return "$model.$type.$id";
    }

    /**
     * @param null|bool $delete
     * @return bool
     */
    public function toDelete( $delete=null ) {
        if( is_bool( $delete ) ) {
            $this->_toDelete = $delete;
        }
        return $this->_toDelete;
    }

    /**
     * returns if the id value is permanent (i.e. id from database).
     *
     * @return bool
     */
    public function isIdPermanent() {
        return $this->_id_type !== self::_ID_TYPE_VIRTUAL;
    }

    /**
     * @param string $id
     */
    public function setSystemId( $id ) 
    {
        $id_name = $this->_id_name;
        $this->$id_name = $id;
        $this->_id_type = self::_ID_TYPE_SYSTEM;
    }
    
    /**
     * @param string $name
     * @return mixed
     */
    public function getEntityAttribute( $name ) {
        return array_key_exists( $name, $this->_attrEntity ) ? $this->_attrEntity[ $name ]: null;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setEntityAttribute( $name, $value ) {
        $this->_attrEntity[ $name ] = $value;
    }
    
    /**
     * @param string $prop
     * @param string $name
     * @return mixed
     */
    public function getPropertyAttribute( $prop, $name=null ) {
        if( !array_key_exists( $prop, $this->_attrMember ) ) return null;
        if( is_null( $name ) ) return $this->_attrMember[ $prop ];
        return array_key_exists( $name, $this->_attrMember[ $prop ] ) ? $this->_attrMember[ $prop ][ $name ]: null;
    }

    /**
     * @param string $prop
     * @param string $name
     * @param mixed  $value
     */
    public function setPropertyAttribute( $prop, $name, $value ) {
        $this->_attrMember[ $prop ][ $name ] = $value;
    }
    // +----------------------------------------------------------------------+
    //  for ArrayAccess
    // +----------------------------------------------------------------------+
    /**
     */
    public function offsetExists( $offset ) {
        if( substr( $offset, 0, 1 ) != '_' && isset( $this->$offset ) ) return true;
        return false;
    }

    /**
     */
    public function offsetGet( $offset ) {
        if( substr( $offset, 0, 1 ) != '_' && isset( $this->$offset ) ) return $this->$offset;
        return null;
    }

    /**
     */
    public function offsetSet( $offset, $value )
    {
        if( is_null( $offset ) ) {
            foreach( $value as $key => $val ) {    $this->offsetSet( $key, $value ); }
        }
        elseif( substr( $offset, 0, 1 ) != '_' ) { $this->$offset = $value; }
    }

    /**
     */
    public function offsetUnset( $offset ) {
        if( substr( $offset, 0, 1 ) != '_' && isset( $this->$offset ) ) unset( $this->$offset );
    }
}
