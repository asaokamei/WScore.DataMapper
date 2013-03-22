<?php
namespace WScore\DataMapper\Role;

use \WScore\DataMapper\EntityManager;
use \WScore\Validation\Validation;
use \WScore\Selector\ElementAbstract;
use \WScore\Selector\ElementItemizedAbstract;

class DataIO extends RoleAbstract
{
    const IS_VALID_NAME = 'isValid';
    const ERROR_NAME    = 'error';

    /**
     * @Inject
     * @var \WScore\DataMapper\EntityManager
     */
    protected $em;

    /**
     * @Inject
     * @var \WScore\Validation\Validation
     */
    protected $validation;

    /** @var string */
    public $htmlType = 'html';

    // +----------------------------------------------------------------------+
    //  presentation for HTML form elements.
    // +----------------------------------------------------------------------+
    /**
     * @param string $key
     * @return null|mixed
     */
    public function get( $key ) {
        if( isset( $this->entity->$key ) ) return $this->entity->$key;
        return null;
    }

    /**
     * get html form elements (Selector objects).
     *
     * @param $key
     * @return ElementAbstract|ElementItemizedAbstract
     */
    public function form( $key )
    {
        $model = $this->em->getModel( $this->entity );
        return $model->getSelector( $key );
    }

    /**
     * @param null|string $type
     * @return string
     */
    public function setHtmlType( $type=null ) {
        if( isset( $type ) ) $this->htmlType = $type;
        return $this->htmlType;
    }

    /**
     * @param string $key
     * @param string|null $htmlType
     * @return mixed
     */
    public function popHtml( $key, $htmlType=null )
    {
        if( !$htmlType ) $htmlType = $this->htmlType;
        $form  = $this->form( $key );
        $value = isset( $this->entity->$key ) ? $this->entity->$key: '';
        return $form->popHtml( $htmlType, $value );
    }

    // +----------------------------------------------------------------------+
    //  data manipulation and validation.
    // +----------------------------------------------------------------------+
    /**
     * loads data into entity. default is $_POST is used to load data.
     *
     * @param null|array $data
     * @return $this
     */
    public function load( $data=null )
    {
        if( !$data ) $data = $_POST;
        $model = $this->em->getModel( $this->entity );
        $data = $model->protect( $data );
        foreach( $data as $key => $value ) {
            $this->entity[ $key ] = $value;
        }
        return $this;
    }

    /**
     * validates the entity's property values.
     *
     * @return bool
     */
    public function validate()
    {
        $this->validation->source( $this->entity );
        $model = $this->em->getModel( $this->entity );
        // validate all the properties in the entity.
        $lists = get_object_vars( $this->entity );
        foreach( $lists as $key => $value ) {
            $rule = $model->getRule( $key );
            $this->validation->push( $key, $rule );
        }
        // save the validation result. if not valid, save errors in property attributes.
        $isValid = $this->validation->isValid();
        $this->entity->setEntityAttribute( self::IS_VALID_NAME, $isValid );
        if( !$isValid ) {
            $errors  = $this->validation->popError();
            foreach( $errors as $key => $error ) {
                $this->entity->setPropertyAttribute( $key, self::ERROR_NAME, $error );
            }
        }
        return $isValid;
    }

    /**
     * @return bool|null
     */
    public function isValid() {
        return $this->entity->getEntityAttribute( self::IS_VALID_NAME );
    }

    /**
     * resets valid state of the entity.
     * @return self
     */
    public function resetValid() {
        $this->entity->setEntityAttribute( self::IS_VALID_NAME, null );
        return $this;
    }

    /**
     * returns error message if any.
     *
     * @param $key
     * @return mixed
     */
    public function getError( $key ) {
        return $this->entity->getPropertyAttribute( $key, self::ERROR_NAME );
    }

    /**
     * @param $key
     * @return bool
     */
    public function isError( $key ) {
        return !!$this->entity->getPropertyAttribute( $key, self::ERROR_NAME );
    }
    // +----------------------------------------------------------------------+
}