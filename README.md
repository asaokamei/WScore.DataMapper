WScore.DataMapper
=================

O/R and Cena DataMapper.

Cena
----

Cena is a patented (yeap...) technology for resource oriented 
data to transfer protocol.

You may risk patent infringement whenever using this package.

NOTICE: no license is set, yet.

Entity
------

Entity objects represents domain model. 

Entity has only one references to Model which determines how to map 
for persistence (i.e. save to database). 

example of entity code (what it should look like in the future). 

```php
/**
 * @model \My\Project\Model\Users
 */
class UserEntity
{
    public $user_id;
    public $name;
}
```

Model
-----

Model objects represents persistence and presentation information about entity. 

*   Persistence Model:
    defines how to access database, where as
*   Presentational Model: 
    represents how to present the entity in view (i.e. html). 
*   Property Model:
    sits in the middle managing the information about these models. 

Role
----

A simple wrapper for entities to provide use-case specific methods called interactions. 

Relation
--------

defines how entity is related to other entities. Avalable relations are: 
hasOne, isRef, and isJoined. 

Some relation, such as isJoined, requires models to be defined. 