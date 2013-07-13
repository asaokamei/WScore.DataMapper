<?php
namespace WScore\DataMapper\Filter;

interface FilterInterface
{
    public function onSave(  $entity );
    public function onQuery( $entity );
    public function onFetch( $entity );
    public function onApply( $entity );
    public function onValidate( $entity );
    public function setModel( $model );
}