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


Model/CSV for Property
----------------------

experimental. 

###Current Model/CSV for Property

*   id
    database column name. 
    -> rename to column. 

*   active
    not really used. 

*   label
    column for human readable name for a column. 
    -> rename to title. 

*   dbDef
    database definition. 

*   notNull

*   default
    set default value if any. 

*   required
    set to true/1 if some value is required. space is considered as a value. 

*   extra
    currently, specify created_at and updated_at. 

*   bindType
    used for PDO's bind type. 

should be:

*   column:
    database column name. 

*   title:
    human readable name for the column. 

*   type:
    type of the column; string, number, char (i.e. only ascii code), datetime, date, time, 
    created_at (i.e. datetime), updated_at (i.e. datetime), etc. 

*   dbDef:
    database definition. 

*   notNull:

*   default:

*   required:

*   protected:


###Current Model/CSV for Presentation

*   id
    column name. must be the same as Model/CSV for Property. 

*   selector
    set Selector. Or class name. 

*   type
    Selector type, such as mail, date, time, text, etc. 
    -> guess from Property/type if type is not set. 

*   choice
    possible choices. for checks and multiSelect. 

*   extra
    specify presentational extra option for Selector, such as placeholder. 

###Current Model/CSV for Validation

*   id
    column name. must be the same as Model/CSV for Property. 

*   type
    validation type such as mail, text, code, number, etc. 
    -> guess from Property/type if type is not set. 

*   extra
    another option for validation. 

*   pattern
    regular expression

*   (required)
    error if value is not present. 

*   (default): 
    default value if not set. 

*   (choice):
    used to validate. 

