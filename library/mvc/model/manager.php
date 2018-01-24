<?php


namespace Phalcon\Mvc\Model;

use Phalcon\DiInterface;
use Phalcon\Mvc\Model\Relation;
use Phalcon\Mvc\Model\RelationInterface;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Mvc\ModelInterface;
use Phalcon\Db\AdapterInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\ManagerInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\Model\QueryInterface;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Query\BuilderInterface;
use Phalcon\Mvc\Model\BehaviorInterface;
use Phalcon\Events\ManagerInterface as EventsManagerInterface;


/***
 * Phalcon\Mvc\Model\Manager
 *
 * This components controls the initialization of models, keeping record of relations
 * between the different models of the application.
 *
 * A ModelsManager is injected to a model via a Dependency Injector/Services Container such as Phalcon\Di.
 *
 * <code>
 * use Phalcon\Di;
 * use Phalcon\Mvc\Model\Manager as ModelsManager;
 *
 * $di = new Di();
 *
 * $di->set(
 *     "modelsManager",
 *     function() {
 *         return new ModelsManager();
 *     }
 * );
 *
 * $robot = new Robots($di);
 * </code>
 **/

class Manager {

    protected $_dependencyInjector;

    protected $_eventsManager;

    protected $_customEventsManager;

    protected $_readConnectionServices;

    protected $_writeConnectionServices;

    protected $_aliases;

    protected $_modelVisibility;

    /***
	 * Has many relations
	 **/
    protected $_hasMany;

    /***
	 * Has many relations by model
	 **/
    protected $_hasManySingle;

    /***
	 * Has one relations
	 **/
    protected $_hasOne;

    /***
	 * Has one relations by model
	 **/
    protected $_hasOneSingle;

    /***
	 * Belongs to relations
	 **/
    protected $_belongsTo;

    /***
	 * All the relationships by model
	 **/
    protected $_belongsToSingle;

    /***
	 * Has many-Through relations
	 **/
    protected $_hasManyToMany;

    /***
	 * Has many-Through relations by model
	 **/
    protected $_hasManyToManySingle;

    /***
	 * Mark initialized models
	 **/
    protected $_initialized;

    protected $_prefix;

    protected $_sources;

    protected $_schemas;

    /***
	 * Models' behaviors
	 **/
    protected $_behaviors;

    /***
	 * Last model initialized
	 **/
    protected $_lastInitialized;

    /***
	 * Last query created/executed
	 **/
    protected $_lastQuery;

    /***
	 * Stores a list of reusable instances
	 **/
    protected $_reusable;

    protected $_keepSnapshots;

    /***
	 * Does the model use dynamic update, instead of updating all rows?
	 **/
    protected $_dynamicUpdate;

    protected $_namespaceAliases;

    /***
	 * Sets the DependencyInjector container
	 **/
    public function setDI($dependencyInjector ) {

    }

    /***
	 * Returns the DependencyInjector container
	 **/
    public function getDI() {

    }

    /***
	 * Sets a global events manager
	 **/
    public function setEventsManager($eventsManager ) {

    }

    /***
	 * Returns the internal event manager
	 **/
    public function getEventsManager() {

    }

    /***
	 * Sets a custom events manager for a specific model
	 **/
    public function setCustomEventsManager($model , $eventsManager ) {

    }

    /***
	 * Returns a custom events manager related to a model
	 **/
    public function getCustomEventsManager($model ) {

    }

    /***
	 * Initializes a model in the model manager
	 **/
    public function initialize($model ) {

    }

    /***
	 * Check whether a model is already initialized
	 **/
    public function isInitialized($modelName ) {

    }

    /***
	 * Get last initialized model
	 **/
    public function getLastInitialized() {

    }

    /***
	 * Loads a model throwing an exception if it doesn't exist
	 **/
    public function load($modelName , $newInstance  = false ) {

    }

    /***
	 * Sets the prefix for all model sources.
	 *
	 * <code>
	 * use Phalcon\Mvc\Model\Manager;
	 *
	 * $di->set("modelsManager", function () {
	 *     $modelsManager = new Manager();
	 *     $modelsManager->setModelPrefix("wp_");
	 *
	 *     return $modelsManager;
	 * });
	 *
	 * $robots = new Robots();
	 * echo $robots->getSource(); // wp_robots
	 * </code>
	 **/
    public function setModelPrefix($prefix ) {

    }

    /***
	 * Returns the prefix for all model sources.
	 *
	 * <code>
	 * use Phalcon\Mvc\Model\Manager;
	 *
	 * $di->set("modelsManager", function () {
	 *     $modelsManager = new Manager();
	 *     $modelsManager->setModelPrefix("wp_");
	 *
	 *     return $modelsManager;
	 * });
	 *
	 * $robots = new Robots();
	 * echo $robots->getSource(); // wp_robots
	 * </code>
	 **/
    public function getModelPrefix() {

    }

    /***
	 * Sets the mapped source for a model
	 **/
    public function setModelSource($model , $source ) {

    }

    /***
	 * Check whether a model property is declared as public.
	 *
	 * <code>
	 * $isPublic = $manager->isVisibleModelProperty(
	 *     new Robots(),
	 *     "name"
	 * );
	 * </code>
	 **/
    public final function isVisibleModelProperty($model , $property ) {

    }

    /***
	 * Returns the mapped source for a model
	 **/
    public function getModelSource($model ) {

    }

    /***
	 * Sets the mapped schema for a model
	 **/
    public function setModelSchema($model , $schema ) {

    }

    /***
	 * Returns the mapped schema for a model
	 **/
    public function getModelSchema($model ) {

    }

    /***
	 * Sets both write and read connection service for a model
	 **/
    public function setConnectionService($model , $connectionService ) {

    }

    /***
	 * Sets write connection service for a model
	 **/
    public function setWriteConnectionService($model , $connectionService ) {

    }

    /***
	 * Sets read connection service for a model
	 **/
    public function setReadConnectionService($model , $connectionService ) {

    }

    /***
	 * Returns the connection to read data related to a model
	 **/
    public function getReadConnection($model ) {

    }

    /***
	 * Returns the connection to write data related to a model
	 **/
    public function getWriteConnection($model ) {

    }

    /***
	 * Returns the connection to read or write data related to a model depending on the connection services.
	 **/
    protected function _getConnection($model , $connectionServices ) {

    }

    /***
	 * Returns the connection service name used to read data related to a model
	 **/
    public function getReadConnectionService($model ) {

    }

    /***
	 * Returns the connection service name used to write data related to a model
	 **/
    public function getWriteConnectionService($model ) {

    }

    /***
	 * Returns the connection service name used to read or write data related to
	 * a model depending on the connection services
	 **/
    public function _getConnectionService($model , $connectionServices ) {

    }

    /***
	 * Receives events generated in the models and dispatches them to an events-manager if available
	 * Notify the behaviors that are listening in the model
	 **/
    public function notifyEvent($eventName , $model ) {

    }

    /***
	 * Dispatch an event to the listeners and behaviors
	 * This method expects that the endpoint listeners/behaviors returns true
	 * meaning that a least one was implemented
	 **/
    public function missingMethod($model , $eventName , $data ) {

    }

    /***
	 * Binds a behavior to a model
	 **/
    public function addBehavior($model , $behavior ) {

    }

    /***
	 * Sets if a model must keep snapshots
	 **/
    public function keepSnapshots($model , $keepSnapshots ) {

    }

    /***
	 * Checks if a model is keeping snapshots for the queried records
	 **/
    public function isKeepingSnapshots($model ) {

    }

    /***
	 * Sets if a model must use dynamic update instead of the all-field update
	 **/
    public function useDynamicUpdate($model , $dynamicUpdate ) {

    }

    /***
	 * Checks if a model is using dynamic update instead of all-field update
	 **/
    public function isUsingDynamicUpdate($model ) {

    }

    /***
	 * Setup a 1-1 relation between two models
	 *
	 * @param   Phalcon\Mvc\Model model
	 * @param	mixed fields
	 * @param	string referencedModel
	 * @param	mixed referencedFields
	 * @param	array options
	 * @return  Phalcon\Mvc\Model\Relation
	 **/
    public function addHasOne($model , $fields , $referencedModel , $referencedFields , $options  = null ) {

    }

    /***
	 * Setup a relation reverse many to one between two models
	 *
	 * @param   Phalcon\Mvc\Model model
	 * @param	mixed fields
	 * @param	string referencedModel
	 * @param	mixed referencedFields
	 * @param	array options
	 * @return  Phalcon\Mvc\Model\Relation
	 **/
    public function addBelongsTo($model , $fields , $referencedModel , $referencedFields , $options  = null ) {

    }

    /***
	 * Setup a relation 1-n between two models
	 *
	 * @param 	Phalcon\Mvc\ModelInterface model
	 * @param	mixed fields
	 * @param	string referencedModel
	 * @param	mixed referencedFields
	 * @param	array options
	 **/
    public function addHasMany($model , $fields , $referencedModel , $referencedFields , $options  = null ) {

    }

    /***
	 * Setups a relation n-m between two models
	 *
	 * @param 	Phalcon\Mvc\ModelInterface model
	 * @param	string fields
	 * @param	string intermediateModel
	 * @param	string intermediateFields
	 * @param	string intermediateReferencedFields
	 * @param	string referencedModel
	 * @param	string referencedFields
	 * @param   array options
	 * @return  Phalcon\Mvc\Model\Relation
	 **/
    public function addHasManyToMany($model , $fields , $intermediateModel , $intermediateFields , $intermediateReferencedFields , $referencedModel , $referencedFields , $options  = null ) {

    }

    /***
	 * Checks whether a model has a belongsTo relation with another model
	 **/
    public function existsBelongsTo($modelName , $modelRelation ) {

    }

    /***
	 * Checks whether a model has a hasMany relation with another model
	 **/
    public function existsHasMany($modelName , $modelRelation ) {

    }

    /***
	 * Checks whether a model has a hasOne relation with another model
	 **/
    public function existsHasOne($modelName , $modelRelation ) {

    }

    /***
	 * Checks whether a model has a hasManyToMany relation with another model
	 **/
    public function existsHasManyToMany($modelName , $modelRelation ) {

    }

    /***
	 * Returns a relation by its alias
	 **/
    public function getRelationByAlias($modelName , $alias ) {

    }

    /***
	 * Merge two arrays of find parameters
	 **/
    protected final function _mergeFindParameters($findParamsOne , $findParamsTwo ) {

    }

    /***
	 * Helper method to query records based on a relation definition
	 *
	 * @return \Phalcon\Mvc\Model\Resultset\Simple|Phalcon\Mvc\Model\Resultset\Simple|int|false
	 **/
    public function getRelationRecords($relation , $method , $record , $parameters  = null ) {

    }

    /***
	 * Returns a reusable object from the internal list
	 **/
    public function getReusableRecords($modelName , $key ) {

    }

    /***
	 * Stores a reusable record in the internal list
	 **/
    public function setReusableRecords($modelName , $key , $records ) {

    }

    /***
	 * Clears the internal reusable list
	 **/
    public function clearReusableObjects() {

    }

    /***
	 * Gets belongsTo related records from a model
	 **/
    public function getBelongsToRecords($method , $modelName , $modelRelation , $record , $parameters  = null ) {

    }

    /***
	 * Gets hasMany related records from a model
	 **/
    public function getHasManyRecords($method , $modelName , $modelRelation , $record , $parameters  = null ) {

    }

    /***
	 * Gets belongsTo related records from a model
	 **/
    public function getHasOneRecords($method , $modelName , $modelRelation , $record , $parameters  = null ) {

    }

    /***
	 * Gets all the belongsTo relations defined in a model
	 *
	 *<code>
	 * $relations = $modelsManager->getBelongsTo(
	 *     new Robots()
	 * );
	 *</code>
	 **/
    public function getBelongsTo($model ) {

    }

    /***
	 * Gets hasMany relations defined on a model
	 **/
    public function getHasMany($model ) {

    }

    /***
	 * Gets hasOne relations defined on a model
	 **/
    public function getHasOne($model ) {

    }

    /***
	 * Gets hasManyToMany relations defined on a model
	 **/
    public function getHasManyToMany($model ) {

    }

    /***
	 * Gets hasOne relations defined on a model
	 **/
    public function getHasOneAndHasMany($model ) {

    }

    /***
	 * Query all the relationships defined on a model
	 **/
    public function getRelations($modelName ) {

    }

    /***
	 * Query the first relationship defined between two models
	 **/
    public function getRelationsBetween($first , $second ) {

    }

    /***
	 * Creates a Phalcon\Mvc\Model\Query without execute it
	 **/
    public function createQuery($phql ) {

    }

    /***
	 * Creates a Phalcon\Mvc\Model\Query and execute it
	 **/
    public function executeQuery($phql , $placeholders  = null , $types  = null ) {

    }

    /***
	 * Creates a Phalcon\Mvc\Model\Query\Builder
	 **/
    public function createBuilder($params  = null ) {

    }

    /***
	 * Returns the last query created or executed in the models manager
	 **/
    public function getLastQuery() {

    }

    /***
	 * Registers shorter aliases for namespaces in PHQL statements
	 **/
    public function registerNamespaceAlias($alias , $namespaceName ) {

    }

    /***
	 * Returns a real namespace from its alias
	 **/
    public function getNamespaceAlias($alias ) {

    }

    /***
	 * Returns all the registered namespace aliases
	 **/
    public function getNamespaceAliases() {

    }

    /***
 	 * Destroys the current PHQL cache
 	 **/
    public function __destruct() {

    }

}