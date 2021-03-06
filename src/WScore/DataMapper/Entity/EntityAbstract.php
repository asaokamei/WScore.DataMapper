<?php
namespace WScore\DataMapper\Entity;

abstract class EntityAbstract implements EntityInterface, \ArrayAccess
{
    /** @var string */
    public static $_modelName;
    
    /** @var  string */
    public $_entityName;
    
    /** @var string */
    private $_model;
    
    /** @var string */
    private $_id_type;

    /** @var string */
    private $_id_name;

    /** @var string */
    private $_identifier;
    
    /** @var string */
    private $_cena_id;

    /** @var bool */
    private $_toDelete = false;
    
    /** @var array */
    private $_attrEntity = array();

    /** @var array */
    private $_attrMember = array();
    
    /** @var int */
    static $_id_for_new = 1;

    /**
     * @param \WScore\DataMapper\Model\Model $model
     * @param string                   $id_type
     * @param null|string              $identifier
     */
    public function __construct( $model, $id_type=null, $identifier=null )
    {
        $this->_model   = $model->getModelName();
        if( !$this->_entityName ) {
            $this->_entityName = self::getMainClassName( get_called_class() );
        }
        if( isset( $id_type ) ) {
            $this->_id_type = $id_type;
        } else {
            $this->_id_type = EntityInterface::_ID_TYPE_SYSTEM;
        }
        $this->_id_name = $model->getIdName();
        if( !is_null( $identifier ) ) {
            $this->_identifier = $identifier;
        }
        elseif( !$this->_identifier = $this->getId() ) {
            $this->_identifier = (string) self::$_id_for_new++;
        }
        $this->_cena_id = $this->getCenaId(true);
    }

    /**
     * get static model name. 
     * model name does not have backslash at the beginning. 
     *
     * @param bool $short
     * @return string
     */
    public static final function getStaticModelName( $short=false ) {
        $model = static::$_modelName;
        if( substr( $model, 0, 1 ) === '\\' ) $model = substr( $model, 1 );
        if( $short ) {
            $model = self::getMainClassName( $model );
        }
        return $model;
    }

    /**
     * @param $name
     * @return string
     */
    private static function getMainClassName( $name ) {
        if( strpos( $name, '\\' ) !== false ) {
            $name = substr( $name, strrpos( $name, '\\' )+1 );
        }
        return $name;
    }
    
    /**
     * @return null|string
     */
    public function getId() {
        $idName = $this->_id_name;
        return ( isset( $this->$idName ) ) ? $this->$idName: null;
    }

    /**
     * @return string
     */
    public function getIdName() {
        return $this->_id_name;
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
     * @param bool $short
     * @return string
     */
    public function getEntityName( $short=true )
    {
        if( $short ) return $this->_entityName;
        return get_called_class();
    }

    /**
     * returns cenaID. 
     * set $current to true to get cenaID that reflect current state. 
     * 
     * @param bool $current
     * @return string
     */
    public function getCenaId( $current=false ) 
    {
        if( !$current ) {
            return $this->_cena_id;
        }
        $model = $this->getEntityName( true );
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
