<?php
namespace WScore\DataMapper\Entity;

interface EntityInterface
{
    const _ID_TYPE_VIRTUAL   = '0';   // record from db for update.
    const _ID_TYPE_SYSTEM    = '1';   // id from system.
    const _ID_TYPE_EXTERNAL  = '2';   // id from external system.

    /**
     * @return null|string
     */
    public function getId();

    /**
     * @param bool $short
     * @return string
     */
    public function getModelName( $short=false );

    /**
     * @return string
     */
    public function getCenaId();

    /**
     * @param null|bool $delete
     * @return bool
     */
    public function toDelete( $delete=null );

    /**
     * returns if the id value is permanent (i.e. id from database).
     *
     * @return bool
     */
    public function isIdPermanent();

    /**
     * @param string $name
     * @return mixed
     */
    public function _getAttribute( $name );

    /**
     * @param string $prop
     * @param string $name
     * @return mixed
     */
    public function _getAttributeProperty( $prop, $name=null );
}
