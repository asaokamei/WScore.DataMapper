WScore.DataMapper
=================

A simple data mapper component. 

Entity
------

Entity is a class that roughly represents a record of a database table. 

The class is not pure; one needs to extend an EntityAbstract and set a modelName 
to use it in this component. For example

```php
namespace Tasks\Entity;

class Task extends EntityAbstract
{
    const STATUS_ACTIVE = '1';
    const STATUS_DONE   = '9';

    public static $_modelName = '\App\Tasks\Model\Tasks';
    public $task_id = null;
    public $memo = '';
    public $status = self::STATUS_ACTIVE;
}
```

Properties should be defined as public for PDO to populate the value. 

A public static variable $_modelName should reference to the model class to handle this entity. 

Models
------

Model class(es) determines how an entity object be  
-    saved to a database, 
-    represented in html forms,
-    validated its value. 

The models is consisted of several classes: 

*   WScore\DataMapper\Model
    a master controller model for all other models. 
*   WScore\DataMapper\Model\Persistence
    manages how to access database layer. 
*   WScore\DataMapper\Model\Presentation
    manages html form presentation, as well as validation. 
*   WScore\DataMapper\Model\Property 
    a hidden object to control attribute of each properties for Persistence and Presentation models. 

```php
class Tasks extends Model
{
    /** @var string     name of database table     */
    protected $table = 'task';

    /** @var string     name of primary key        */
    protected $id_name = 'task_id';

    public function __construct()
    {
        parent::__construct();
        $csv = file_get_contents( __DIR__ . '/tasks.csv' );
        $this->property->prepare( $csv );
    }
}
```

All models are PHP code. 

Possible to overwrite/hack/rewrite any of the models to suite need. 

Attributes of entity's properties is defined by CSV. 

TODO: Presentation model simply returns information about html form and validation. 
      maybe model should be responsible to construct form and perform validation. 


Role
----

A simple wrapper for entities to provide use-case specific methods called interactions. 

Following shows an example to load post data into entity, validate value, and save to database. 

```php
$task = $this->em->fetch( 'Tasks\Entity\Task' );
$role = $this->role->applyDataIO( $task );
$role->load( $_POST );
if( $role->validate() ) {
    $active = $this->role->applyActive( $role );
    $active->save();
}
```

Relation
--------

defines how entity is related to other entities. 

Available relations are: HasOne, Joined, and JoinBy. 

Some relation, such as isJoined, requires models to be defined. 


Property Annotation
-------------------

write down the following annotations in PHP or CSV. 

*   column:

    database column name. 

*   title:

    human readable name for the column. 

*   type:
    
    type of the column; string, number, char (i.e. only ascii code), datetime, date, time, 
    created_at (i.e. datetime), updated_at (i.e. datetime), etc. 

*   dbDef:
    
    database definition. not really used. 

*   notNull:
    
    not null or not. not really used. 

*   default:
    
    specify the default value used for validation. 
    maybe used for presentation html as default value. 

*   required:

    used for validation. will be used for presentation for html form, marked as required. 

*   protected:

    indicates the column will not be mass-assigned. 


*   presentedAs:

    overwrites types for presentation html. 
    specify types such as textarea, radio, check, etc.

*   style:
    
    form/tags style.

*   validateAs:
    
    overwrites types for validation rule. 
    specify types such as tel, mail, etc. 

*   pattern:
    
    regular expression


