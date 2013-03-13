<?php
namespace WScore\DataMapper\Model;

interface PropertyInterface
{
    public function setTable( $table, $id_name );
    public function exists( $name );
    public function isProtected( $name );
    public function updatedAt( $data );
    public function createdAt( $data );
    public function getLabel( $name );
    public function getPattern( $name );
    public function restrict( $data );
    public function protect( $data, $onlyTo=array() );
    public function getRelationInfo( $name=null );
    public function getValidateInfo( $name );
    public function isRequired( $name );
    public function getSelectInfo( $name );
}