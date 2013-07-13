<?php
namespace WScore\DataMapper\Model;

interface FilterInterface
{
    public function onSave( $query );
    public function onFetch( $entity );
    public function onApply( $entity );
    public function onValidate( $entity );
}