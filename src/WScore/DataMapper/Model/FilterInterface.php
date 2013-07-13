<?php
namespace WScore\DataMapper\Model;

interface FilterInterface
{
    public function onSave(  $entity );
    public function onQuery( $query );
    public function onFetch( $query );
    public function onApply( $entity );
    public function onValidate( $entity );
    public function setModel( $model );
}