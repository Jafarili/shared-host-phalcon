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
		$this->_dependencyInjector = dependencyInjector;
    }

    /***
	 * Returns the DependencyInjector container
	 **/
    public function getDI() {
		return $this->_dependencyInjector;
    }

    /***
	 * Sets a global events manager
	 **/
    public function setEventsManager($eventsManager ) {
		$this->_eventsManager = eventsManager;
		return this;
    }

    /***
	 * Returns the internal event manager
	 **/
    public function getEventsManager() {
		return $this->_eventsManager;
    }

    /***
	 * Sets a custom events manager for a specific model
	 **/
    public function setCustomEventsManager($model , $eventsManager ) {
		$this->_customEventsManager[get_class_lower(model)] = eventsManager;
    }

    /***
	 * Returns a custom events manager related to a model
	 **/
    public function getCustomEventsManager($model ) {

		if ( !fetch eventsManager, $this->_customEventsManager[get_class_lower(model)] ) {
			return false;
		}

		return eventsManager;
    }

    /***
	 * Initializes a model in the model manager
	 **/
    public function initialize($model ) {

		$className = get_class_lower(model);

		/**
		 * Models are just initialized once per request
		 */
		if ( isset($this->_initialized[className]) ) {
			return false;
		}

		/**
		 * Update the model as initialized, this avoid cyclic initializations
		 */
		$this->_initialized[className] = model;

		/**
		 * Call the 'initialize' method if ( it's implemented
		 */
		if ( method_exists(model, "initialize") ) {
			model->{"initialize"}();
		}

		/**
		 * Update the last initialized model, so it can be used in modelsManager:afterInitialize
		 */
		$this->_lastInitialized = model;

		/**
		 * If an EventsManager is available we pass to it every initialized model
		 */
		$eventsManager = <EventsManagerInterface> $this->_eventsManager;
		if ( gettype($eventsManager) == "object" ) {
			eventsManager->fire("modelsManager:afterInitialize", this, model);
		}

		return true;
    }

    /***
	 * Check whether a model is already initialized
	 **/
    public function isInitialized($modelName ) {
		return isset $this->_initialized[strtolower(modelName)];
    }

    /***
	 * Get last initialized model
	 **/
    public function getLastInitialized() {
		return $this->_lastInitialized;
    }

    /***
	 * Loads a model throwing an exception if it doesn't exist
	 **/
    public function load($modelName , $newInstance  = false ) {

		/**
		 * Check if ( a modelName is an alias
		 */
		$colonPos = strpos(modelName, ":");

		if ( colonPos !== false ) {
			$className = substr(modelName, colonPos + 1);
			$namespaceAlias = substr(modelName, 0, colonPos);
			$namespaceName = $this->getNamespaceAlias(namespaceAlias);
			$modelName = namespaceName . "\\" . className;
		}

		/**
		 * The model doesn't exist throw an exception
		 */
		if ( !class_exists(modelName) ) {
			throw new Exception("Model '" . modelName . "' could not be loaded");
		}

		/**
		 * Check if ( a model with the same is already loaded
		 */
		if ( !newInstance ) {
			if ( fetch model, $this->_initialized[strtolower(modelName)] ) {
				model->reset();
				return model;
			}
		}

		/**
		 * Load it using an autoloader
		 */
		return new {modelName}(null, $this->_dependencyInjector, this);
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
		$this->_prefix = prefix;
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
		return $this->_prefix;
    }

    /***
	 * Sets the mapped source for a model
	 **/
    public function setModelSource($model , $source ) {
		$this->_sources[get_class_lower(model)] = source;
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

		$className = get_class(model);

		if ( !isset($this->_modelVisibility[className]) ) {
			$this->_modelVisibility[className] = get_object_vars(model);
		}

		$properties = $this->_modelVisibility[className];

		return array_key_exists(property, properties);
    }

    /***
	 * Returns the mapped source for a model
	 **/
    public function getModelSource($model ) {

		$entityName = get_class_lower(model);

		if ( !isset($this->_sources[entityName]) ) {
			$this->_sources[entityName] = uncamelize(get_class_ns(model));
		}

		return $this->_prefix . $this->_sources[entityName];
    }

    /***
	 * Sets the mapped schema for a model
	 **/
    public function setModelSchema($model , $schema ) {
		$this->_schemas[get_class_lower(model)] = schema;
    }

    /***
	 * Returns the mapped schema for a model
	 **/
    public function getModelSchema($model ) {

		if ( !fetch schema, $this->_schemas[get_class_lower(model)] ) {
			return "";
		}

		return schema;
    }

    /***
	 * Sets both write and read connection service for a model
	 **/
    public function setConnectionService($model , $connectionService ) {
		this->setReadConnectionService(model, connectionService);
		this->setWriteConnectionService(model, connectionService);
    }

    /***
	 * Sets write connection service for a model
	 **/
    public function setWriteConnectionService($model , $connectionService ) {
		$this->_writeConnectionServices[get_class_lower(model)] = connectionService;
    }

    /***
	 * Sets read connection service for a model
	 **/
    public function setReadConnectionService($model , $connectionService ) {
		$this->_readConnectionServices[get_class_lower(model)] = connectionService;
    }

    /***
	 * Returns the connection to read data related to a model
	 **/
    public function getReadConnection($model ) {
		return $this->_getConnection(model, $this->_readConnectionServices);
    }

    /***
	 * Returns the connection to write data related to a model
	 **/
    public function getWriteConnection($model ) {
		return $this->_getConnection(model, $this->_writeConnectionServices);
    }

    /***
	 * Returns the connection to read or write data related to a model depending on the connection services.
	 **/
    protected function _getConnection($model , $connectionServices ) {

		$service = $this->_getConnectionService(model, connectionServices);

		$dependencyInjector = <DiInterface> $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injector container is required to obtain the services related to the ORM");
		}

		/**
		 * Request the connection service from the DI
		 */
		$connection = <AdapterInterface> dependencyInjector->getShared(service);

		if ( gettype($connection) != "object" ) {
			throw new Exception("Invalid injected connection service");
		}

		return connection;
    }

    /***
	 * Returns the connection service name used to read data related to a model
	 **/
    public function getReadConnectionService($model ) {
		return $this->_getConnectionService(model, $this->_readConnectionServices);
    }

    /***
	 * Returns the connection service name used to write data related to a model
	 **/
    public function getWriteConnectionService($model ) {
		return $this->_getConnectionService(model, $this->_writeConnectionServices);
    }

    /***
	 * Returns the connection service name used to read or write data related to
	 * a model depending on the connection services
	 **/
    public function _getConnectionService($model , $connectionServices ) {

		if ( !fetch connection, connectionServices[get_class_lower(model)] ) {
			return "db";
		}

		return connection;
    }

    /***
	 * Receives events generated in the models and dispatches them to an events-manager if available
	 * Notify the behaviors that are listening in the model
	 **/
    public function notifyEvent($eventName , $model ) {

		$status = null;

		/**
		 * Dispatch events to the global events manager
		 */
		if ( fetch modelsBehaviors, $this->_behaviors[get_class_lower(model)] ) {

			/**
			 * Notif (y all the events on the behavior
			 */
			foreach ( $modelsBehaviors as $behavior ) {
				$status = behavior->notif (y(eventName, model);
				if ( status === false ) {
					return false;
				}
			}
		}

		/**
		 * Dispatch events to the global events manager
		 */
		$eventsManager = $this->_eventsManager;
		if ( gettype($eventsManager) == "object" ) {
			$status = eventsManager->fire("model:" . eventName, model);
			if ( status === false ) {
				return status;
			}
		}

		/**
		 * A model can has a specif (ic events manager for ( it
		 */
		if ( fetch customEventsManager, $this->_customEventsManager[get_class_lower(model)] ) {
			$status = customEventsManager->fire("model:" . eventName, model);
			if ( status === false ) {
				return false;
			}
		}

		return status;
    }

    /***
	 * Dispatch an event to the listeners and behaviors
	 * This method expects that the endpoint listeners/behaviors returns true
	 * meaning that a least one was implemented
	 **/
    public function missingMethod($model , $eventName , $data ) {

		/**
		 * Dispatch events to the global events manager
		 */
		if ( fetch modelsBehaviors, $this->_behaviors[get_class_lower(model)] ) {

			/**
			 * Notif (y all the events on the behavior
			 */
			foreach ( $modelsBehaviors as $behavior ) {
				$result = behavior->missingMethod(model, eventName, data);
				if ( result !== null ) {
					return result;
				}
			}
		}

		/**
		 * Dispatch events to the global events manager
		 */
		$eventsManager = $this->_eventsManager;
		if ( gettype($eventsManager) == "object" ) {
			return eventsManager->fire("model:" . eventName, model, data);
		}

		return null;
    }

    /***
	 * Binds a behavior to a model
	 **/
    public function addBehavior($model , $behavior ) {

		$entityName = get_class_lower(model);

		/**
		 * Get the current behaviors
		 */
		if ( !fetch modelsBehaviors, $this->_behaviors[entityName] ) {
			$modelsBehaviors = [];
		}

		/**
		 * Append the behavior to the list of behaviors
		 */
		$modelsBehaviors[] = behavior;

		/**
		 * Update the behaviors list
		 */
		$this->_behaviors[entityName] = modelsBehaviors;
    }

    /***
	 * Sets if a model must keep snapshots
	 **/
    public function keepSnapshots($model , $keepSnapshots ) {
		$this->_keepSnapshots[get_class_lower(model)] = keepSnapshots;
    }

    /***
	 * Checks if a model is keeping snapshots for the queried records
	 **/
    public function isKeepingSnapshots($model ) {
		$keepSnapshots = $this->_keepSnapshots;
		if ( gettype($keepSnapshots) == "array" ) {
			if ( fetch isKeeping, keepSnapshots[get_class_lower(model)] ) {
				return isKeeping;
			}
		}
		return false;
    }

    /***
	 * Sets if a model must use dynamic update instead of the all-field update
	 **/
    public function useDynamicUpdate($model , $dynamicUpdate ) {
		$entityName = get_class_lower(model),
			this->_dynamicUpdate[entityName] = dynamicUpdate,
			this->_keepSnapshots[entityName] = dynamicUpdate;
    }

    /***
	 * Checks if a model is using dynamic update instead of all-field update
	 **/
    public function isUsingDynamicUpdate($model ) {
		$dynamicUpdate = $this->_dynamicUpdate;
		if ( gettype($dynamicUpdate) == "array" ) {
			if ( fetch isUsing, dynamicUpdate[get_class_lower(model)] ) {
				return isUsing;
			}
		}
		return false;
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
			keyRelation, relations, alias, lowerAlias, singleRelations;

		$entityName = get_class_lower(model),
			referencedEntity = strtolower(referencedModel);

		$keyRelation = entityName . "$" . referencedEntity;

		if ( !fetch relations, $this->_hasOne[keyRelation] ) {
			$relations = [];
		}

		/**
		 * Check if ( the number of fields are the same
		 */
		if ( gettype($referencedFields) == "array" ) {
			if ( count(fields) != count(referencedFields) ) {
				throw new Exception("Number of referenced fields are not the same");
			}
		}

		/**
		 * Create a relationship instance
		 */
		$relation = new Relation(
			Relation::HAS_ONE,
			referencedModel,
			fields,
			referencedFields,
			options
		);

		/**
		 * Check an alias for ( the relation
		 */
		if ( fetch alias, options["alias"] ) {
			if ( gettype($alias) != "string" ) {
				throw new Exception("Relation alias must be a string");
			}
			$lowerAlias = strtolower(alias);
		} else {
			$lowerAlias = referencedEntity;
		}

		/**
		 * Append a new relationship
		 * Update the global alias
		 * Update the relations
		 */
		$relations[] = relation,
			this->_aliases[entityName . "$" . lowerAlias] = relation,
			this->_hasOne[keyRelation] = relations;

		/**
		 * Get existing relations by model
		 */
		if ( !fetch singleRelations, $this->_hasOneSingle[entityName] ) {
			$singleRelations = [];
		}

		/**
		 * Append a new relationship
		 */
		$singleRelations[] = relation;

		/**
		 * Update relations by model
		 */
		$this->_hasOneSingle[entityName] = singleRelations;

		return relation;
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

		$entityName = get_class_lower(model),
			referencedEntity = strtolower(referencedModel);

		$keyRelation = entityName . "$" . referencedEntity;

		if ( !fetch relations, $this->_belongsTo[keyRelation] ) {
			$relations = [];
		}

		/**
		 * Check if ( the number of fields are the same
		 */
		if ( gettype($referencedFields) == "array" ) {
			if ( count(fields) != count(referencedFields) ) {
				throw new Exception("Number of referenced fields are not the same");
			}
		}

		/**
		 * Create a relationship instance
		 */
		$relation = new Relation(
			Relation::BELONGS_TO,
			referencedModel,
			fields,
			referencedFields,
			options
		);

		/**
		 * Check an alias for ( the relation
		 */
		if ( fetch alias, options["alias"] ) {
			if ( gettype($alias) != "string" ) {
				throw new Exception("Relation alias must be a string");
			}
			$lowerAlias = strtolower(alias);
		} else {
			$lowerAlias = referencedEntity;
		}

		/**
		 * Append a new relationship
		 * Update the global alias
		 * Update the relations
		 */
		$relations[] = relation,
			this->_aliases[entityName . "$" . lowerAlias] = relation,
			this->_belongsTo[keyRelation] = relations;

		/**
		 * Get existing relations by model
		 */
		if ( !fetch singleRelations, $this->_belongsToSingle[entityName] ) {
			$singleRelations = [];
		}

		/**
		 * Append a new relationship
		 */
		$singleRelations[] = relation;

		/**
		 * Update relations by model
		 */
		$this->_belongsToSingle[entityName] = singleRelations;

		return relation;
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
			keyRelation, relations, alias, lowerAlias, singleRelations;

		$entityName = get_class_lower(model),
			referencedEntity = strtolower(referencedModel),
			keyRelation = entityName . "$" . referencedEntity;

		$hasMany = $this->_hasMany;
		if ( !fetch relations, hasMany[keyRelation] ) {
			$relations = [];
		}

		/**
		 * Check if ( the number of fields are the same
		 */
		if ( gettype($referencedFields) == "array" ) {
			if ( count(fields) != count(referencedFields) ) {
				throw new Exception("Number of referenced fields are not the same");
			}
		}

		/**
		 * Create a relationship instance
		 */
		$relation = new Relation(
			Relation::HAS_MANY,
			referencedModel,
			fields,
			referencedFields,
			options
		);

		/**
		 * Check an alias for ( the relation
		 */
		if ( fetch alias, options["alias"] ) {
			if ( gettype($alias) != "string" ) {
				throw new Exception("Relation alias must be a string");
			}
			$lowerAlias = strtolower(alias);
		} else {
			$lowerAlias = referencedEntity;
		}

		/**
		 * Append a new relationship
		 * Update the global alias
		 * Update the relations
		 */
		$relations[] = relation,
			this->_aliases[entityName . "$" . lowerAlias] = relation,
			this->_hasMany[keyRelation] = relations;

		/**
		 * Get existing relations by model
		 */
		if ( !fetch singleRelations, $this->_hasManySingle[entityName] ) {
			$singleRelations = [];
		}

		/**
		 * Append a new relationship
		 */
		$singleRelations[] = relation;

		/**
		 * Update relations by model
		 */
		$this->_hasManySingle[entityName] = singleRelations;

		return relation;
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
			keyRelation, relations, alias, lowerAlias, singleRelations, intermediateEntity;

		$entityName = get_class_lower(model),
			intermediateEntity = strtolower(intermediateModel),
			referencedEntity = strtolower(referencedModel),
			keyRelation = entityName . "$" . referencedEntity;

		$hasManyToMany = $this->_hasManyToMany;
		if ( !fetch relations, hasManyToMany[keyRelation] ) {
			$relations = [];
		}

		/**
		 * Check if ( the number of fields are the same from the model to the intermediate model
		 */
		if ( gettype($intermediateFields) == "array" ) {
			if ( count(fields) != count(intermediateFields) ) {
				throw new Exception("Number of referenced fields are not the same");
			}
		}

		/**
		 * Check if ( the number of fields are the same from the intermediate model to the referenced model
		 */
		if ( gettype($intermediateReferencedFields) == "array" ) {
			if ( count(fields) != count(intermediateFields) ) {
				throw new Exception("Number of referenced fields are not the same");
			}
		}

		/**
		 * Create a relationship instance
		 */
		$relation = new Relation(
			Relation::HAS_MANY_THROUGH,
			referencedModel,
			fields,
			referencedFields,
			options
		);

		/**
		 * Set extended intermediate relation data
		 */
		relation->setIntermediateRelation(intermediateFields, intermediateModel, intermediateReferencedFields);

		/**
		 * Check an alias for ( the relation
		 */
		if ( fetch alias, options["alias"] ) {
			if ( gettype($alias) != "string" ) {
				throw new Exception("Relation alias must be a string");
			}
			$lowerAlias = strtolower(alias);
		} else {
			$lowerAlias = referencedEntity;
		}

		/**
		 * Append a new relationship
		 */
		$relations[] = relation;

		/**
		 * Update the global alias
		 */
		$this->_aliases[entityName . "$" . lowerAlias] = relation;

		/**
		 * Update the relations
		 */
		$this->_hasManyToMany[keyRelation] = relations;

		/**
		 * Get existing relations by model
		 */
		if ( !fetch singleRelations, $this->_hasManyToManySingle[entityName] ) {
			$singleRelations = [];
		}

		/**
		 * Append a new relationship
		 */
		$singleRelations[] = relation;

		/**
		 * Update relations by model
		 */
		$this->_hasManyToManySingle[entityName] = singleRelations;

		return relation;
    }

    /***
	 * Checks whether a model has a belongsTo relation with another model
	 **/
    public function existsBelongsTo($modelName , $modelRelation ) {

		$entityName = strtolower(modelName);

		/**
		 * Relationship unique key
		 */
		$keyRelation = entityName . "$" . strtolower(modelRelation);

		/**
		 * Initialize the model first
		 */
		if ( !isset($this->_initialized[entityName]) ) {
			this->load(modelName);
		}

		return isset $this->_belongsTo[keyRelation];
    }

    /***
	 * Checks whether a model has a hasMany relation with another model
	 **/
    public function existsHasMany($modelName , $modelRelation ) {

		$entityName = strtolower(modelName);

		/**
		 * Relationship unique key
		 */
		$keyRelation = entityName . "$" . strtolower(modelRelation);

		/**
		 * Initialize the model first
		 */
		if ( !isset($this->_initialized[entityName]) ) {
			this->load(modelName);
		}

		return isset $this->_hasMany[keyRelation];
    }

    /***
	 * Checks whether a model has a hasOne relation with another model
	 **/
    public function existsHasOne($modelName , $modelRelation ) {

		$entityName = strtolower(modelName);

		/**
		 * Relationship unique key
		 */
		$keyRelation = entityName . "$" . strtolower(modelRelation);

		/**
		 * Initialize the model first
		 */
		if ( !isset($this->_initialized[entityName]) ) {
			this->load(modelName);
		}

		return isset $this->_hasOne[keyRelation];
    }

    /***
	 * Checks whether a model has a hasManyToMany relation with another model
	 **/
    public function existsHasManyToMany($modelName , $modelRelation ) {

		$entityName = strtolower(modelName);

		/**
		 * Relationship unique key
		 */
		$keyRelation = entityName . "$" . strtolower(modelRelation);

		/**
		 * Initialize the model first
		 */
		if ( !isset($this->_initialized[entityName]) ) {
			this->load(modelName);
		}

		return isset $this->_hasManyToMany[keyRelation];
    }

    /***
	 * Returns a relation by its alias
	 **/
    public function getRelationByAlias($modelName , $alias ) {

		if ( !fetch relation, $this->_aliases[strtolower(modelName . "$" . alias)] ) {
			return false;
		}

		return relation;
    }

    /***
	 * Merge two arrays of find parameters
	 **/
    protected final function _mergeFindParameters($findParamsOne , $findParamsTwo ) {

		if ( gettype($findParamsOne) == "string" && typeof findParamsTwo == "string" ) {
			return ["(" . findParamsOne . ") AND (" . findParamsTwo . ")"];
		}

		$findParams = [];
		if ( gettype($findParamsOne) == "array"  ) {

			foreach ( key, $findParamsOne as $value ) {

				if ( key === 0 || key === "conditions" ) {
					if ( !isset($findParams[0]) ) {
						$findParams[0] = value;
					} else {
						$findParams[0] = "(" . findParams[0] . ") AND (" . value . ")";
					}
					continue;
				}

				$findParams[key] = value;
			}
		} else {
			if ( gettype($findParamsOne) == "string" ) {
				$findParams = ["conditions": findParamsOne];
			}
		}

		if ( gettype($findParamsTwo) == "array"  ) {

			foreach ( key, $findParamsTwo as $value ) {

				if ( key === 0 || key === "conditions" ) {
					if ( !isset($findParams[0]) ) {
						$findParams[0] = value;
					} else {
						$findParams[0] = "(" . findParams[0] . ") AND (" . value . ")";
					}
					continue;
				}

				if ( key === "bind" || key === "bindTypes" ) {
					if ( !isset($findParams[key]) ) {
						if ( gettype($value) == "array" ) {
							$findParams[key] = value;
						}
					} else {
						if ( gettype($value) == "array" ) {
							$findParams[key] = array_merge(findParams[key], value);
						}
					}
					continue;
				}

				$findParams[key] = value;
			}
		} else {
			if ( gettype($findParamsTwo) == "string" ) {
				if ( !isset($findParams[0]) ) {
					$findParams[0] = findParamsTwo;
				} else {
					$findParams[0] = "(" . findParams[0] . ") AND (" . findParamsTwo . ")";
				}
			}
		}

		return findParams;
    }

    /***
	 * Helper method to query records based on a relation definition
	 *
	 * @return \Phalcon\Mvc\Model\Resultset\Simple|Phalcon\Mvc\Model\Resultset\Simple|int|false
	 **/
    public function getRelationRecords($relation , $method , $record , $parameters  = null ) {
			intermediateFields, joinConditions, fields, builder, extraParameters,
			conditions, refPosition, field, referencedFields, findParams,
			findArguments, retrieveMethod, uniqueKey, records, arguments, rows, firstRow;
		boolean reusable;

		/**
		 * Re-use bound parameters
		 */
		$placeholders = [];

		/**
		 * Returns parameters that must be always used when the related records are obtained
		 */
		$extraParameters = relation->getParams();

		/**
		 * Perfor (m the query on the referenced model
		 */
		$referencedModel = relation->getReferencedModel();

		/**
		 * Check if ( the relation is direct or through an intermediate model
		 */
		if ( relation->isThrough() ) {

			$conditions = [];

			$intermediateModel = relation->getIntermediateModel(),
				intermediateFields = relation->getIntermediateFields();

			/**
			 * Appends conditions created from the fields defined in the relation
			 */
			$fields = relation->getFields();
			if ( gettype($fields) != "array" ) {
				$conditions[] = "[" . intermediateModel . "].[" . intermediateFields . "] = :APR0:",
					placeholders["APR0"] = record->readAttribute(fields);
			} else {
				throw new Exception("Not supported");
			}

			$joinConditions = [];

			/**
			 * Create the join conditions
			 */
			$intermediateFields = relation->getIntermediateReferencedFields();
			if ( gettype($intermediateFields) != "array" ) {
				$joinConditions[] = "[" . intermediateModel . "].[" . intermediateFields . "] = [" . referencedModel . "].[" . relation->getReferencedFields() . "]";
			} else {
				throw new Exception("Not supported");
			}

			/**
			 * We don't trust the user or the database so we use bound parameters
			 * Create a query builder
			 */
			$builder = $this->createBuilder(this->_mergeFindParameters(extraParameters, parameters));

			builder->from(referencedModel);
			builder->innerJoin(intermediateModel, join(" AND ", joinConditions));
			builder->andWhere(join(" AND ", conditions), placeholders);

			if ( method == "count" ) {
				builder->columns("COUNT(*) AS rowcount");

				$rows = builder->getQuery()->execute();

				$firstRow = rows->getFirst();

				return (int) firstRow->readAttribute("rowcount");
			}

			/**
			 * Get the query
			 * Execute the query
			 */
			return builder->getQuery()->execute();
		}

		$conditions = [];

		/**
		 * Appends conditions created from the fields defined in the relation
		 */
		$fields = relation->getFields();
		if ( gettype($fields) != "array" ) {
			$conditions[] = "[". relation->getReferencedFields() . "] = :APR0:",
				placeholders["APR0"] = record->readAttribute(fields);
		} else {

			/**
			 * Compound relation
			 */
			$referencedFields = relation->getReferencedFields();
			foreach ( refPosition, $relation->getFields() as $field ) {
				$conditions[] = "[". referencedFields[refPosition] . "] = :APR" . refPosition . ":",
					placeholders["APR" . refPosition] = record->readAttribute(field);
			}
		}

		/**
		 * We don't trust the user or data in the database so we use bound parameters
		 * Create a valid params array to pass to the find/findFirst method
		 */
		$findParams = [
			join(" AND ", conditions),
			"bind"      : placeholders,
			"di"        : record->{"getDi"}()
		];

		$findArguments = $this->_mergeFindParameters(findParams, parameters);

		if ( gettype($extraParameters) == "array" ) {
			$findParams = $this->_mergeFindParameters(extraParameters, findArguments);
		} else {
			$findParams = findArguments;
		}

		/**
		 * Check the right method to get the data
		 */
		if ( method === null ) {
			switch relation->getType() {

				case Relation::BELONGS_TO:
				case Relation::HAS_ONE:
					$retrieveMethod = "findFirst";
					break;

				case Relation::HAS_MANY:
					$retrieveMethod = "find";
					break;

				default:
					throw new Exception("Unknown relation type");
			}
		} else {
			$retrieveMethod = method;
		}

		$arguments = [findParams];

		/**
		 * Find first results could be reusable
		 */
		$reusable = (boolean) relation->isReusable();
		if ( reusable ) {
			$uniqueKey = unique_key(referencedModel, arguments),
				records = $this->getReusableRecords(referencedModel, uniqueKey);
			if ( gettype($records) == "array" || gettype($records) == "object" ) {
				return records;
			}
		}

		/**
		 * Load the referenced model
		 * Call the function in the model
		 */
		$records = call_user_func_array([this->load(referencedModel), retrieveMethod], arguments);

		/**
		 * Store the result in the cache if ( it's reusable
		 */
		if ( reusable ) {
			this->setReusableRecords(referencedModel, uniqueKey, records);
		}

		return records;
    }

    /***
	 * Returns a reusable object from the internal list
	 **/
    public function getReusableRecords($modelName , $key ) {
		if ( fetch records, $this->_reusable[key] ) {
			return records;
		}
		return null;
    }

    /***
	 * Stores a reusable record in the internal list
	 **/
    public function setReusableRecords($modelName , $key , $records ) {
		$this->_reusable[key] = records;
    }

    /***
	 * Clears the internal reusable list
	 **/
    public function clearReusableObjects() {
		$this->_reusable = null;
    }

    /***
	 * Gets belongsTo related records from a model
	 **/
    public function getBelongsToRecords($method , $modelName , $modelRelation , $record , $parameters  = null ) {

		/**
		 * Check if ( there is a relation between them
		 */
		$keyRelation = strtolower(modelName) . "$" . strtolower(modelRelation);
		if ( !fetch relations, $this->_hasMany[keyRelation] ) {
			return false;
		}

		/**
		 * "relations" is an array with all the belongsTo relationships to that model
		 * Perfor (m the query
		 */
		return $this->getRelationRecords(relations[0], method, record, parameters);
    }

    /***
	 * Gets hasMany related records from a model
	 **/
    public function getHasManyRecords($method , $modelName , $modelRelation , $record , $parameters  = null ) {

		/**
		 * Check if ( there is a relation between them
		 */
		$keyRelation = strtolower(modelName) . "$" . strtolower(modelRelation);
		if ( !fetch relations, $this->_hasMany[keyRelation] ) {
			return false;
		}

		/**
		 * "relations" is an array with all the hasMany relationships to that model
		 * Perfor (m the query
		 */
		return $this->getRelationRecords(relations[0], method, record, parameters);
    }

    /***
	 * Gets belongsTo related records from a model
	 **/
    public function getHasOneRecords($method , $modelName , $modelRelation , $record , $parameters  = null ) {

		/**
		 * Check if ( there is a relation between them
		 */
		$keyRelation = strtolower(modelName) . "$" . strtolower(modelRelation);
		if ( !fetch relations, $this->_hasOne[keyRelation] ) {
			return false;
		}

		/**
		 * "relations" is an array with all the belongsTo relationships to that model
		 * Perfor (m the query
		 */
		return $this->getRelationRecords(relations[0], method, record, parameters);
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

		if ( !fetch relations, $this->_belongsToSingle[get_class_lower(model)] ) {
			return [];
		}

		return relations;
    }

    /***
	 * Gets hasMany relations defined on a model
	 **/
    public function getHasMany($model ) {

		if ( !fetch relations, $this->_hasManySingle[get_class_lower(model)] ) {
			return [];
		}

		return relations;
    }

    /***
	 * Gets hasOne relations defined on a model
	 **/
    public function getHasOne($model ) {

		if ( !fetch relations, $this->_hasOneSingle[get_class_lower(model)] ) {
			return [];
		}

		return relations;
    }

    /***
	 * Gets hasManyToMany relations defined on a model
	 **/
    public function getHasManyToMany($model ) {

		if ( !fetch relations, $this->_hasManyToManySingle[get_class_lower(model)] ) {
			return [];
		}

		return relations;
    }

    /***
	 * Gets hasOne relations defined on a model
	 **/
    public function getHasOneAndHasMany($model ) {
		return array_merge(this->getHasOne(model), $this->getHasMany(model));
    }

    /***
	 * Query all the relationships defined on a model
	 **/
    public function getRelations($modelName ) {

		$entityName = strtolower(modelName),
			allRelations = [];

		/**
		 * Get belongs-to relations
		 */
		if ( fetch relations, $this->_belongsToSingle[entityName] ) {
			foreach ( $relations as $relation ) {
				$allRelations[] = relation;
			}
		}

		/**
		 * Get has-many relations
		 */
		if ( fetch relations, $this->_hasManySingle[entityName] ) {
			foreach ( $relations as $relation ) {
				$allRelations[] = relation;
			}
		}

		/**
		 * Get has-one relations
		 */
		if ( fetch relations, $this->_hasOneSingle[entityName] ) {
			foreach ( $relations as $relation ) {
				$allRelations[] = relation;
			}
		}

		return allRelations;
    }

    /***
	 * Query the first relationship defined between two models
	 **/
    public function getRelationsBetween($first , $second ) {

		$keyRelation = strtolower(first) . "$" . strtolower(second);

		/**
		 * Check if ( it's a belongs-to relationship
		 */
		if ( fetch relations, $this->_belongsTo[keyRelation] ) {
			return relations;
		}

		/**
		 * Check if ( it's a has-many relationship
		 */
		if ( fetch relations, $this->_hasMany[keyRelation] ) {
			return relations;
		}

		/**
		 * Check whether it's a has-one relationship
		 */
		if ( fetch relations, $this->_hasOne[keyRelation] ) {
			return relations;
		}

		return false;
    }

    /***
	 * Creates a Phalcon\Mvc\Model\Query without execute it
	 **/
    public function createQuery($phql ) {

		$dependencyInjector = $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injection object is required to access ORM services");
		}

		/**
		 * Create a query
		 */
		$query = <QueryInterface> dependencyInjector->get("Phalcon\\Mvc\\Model\\Query", [phql, dependencyInjector]);
		$this->_lastQuery = query;
		return query;
    }

    /***
	 * Creates a Phalcon\Mvc\Model\Query and execute it
	 **/
    public function executeQuery($phql , $placeholders  = null , $types  = null ) {

		$query = $this->createQuery(phql);

		if ( gettype($placeholders) == "array" ) {
			query->setBindParams(placeholders);
		}

		if ( gettype($types) == "array" ) {
			query->setBindTypes(types);
		}

		/**
		 * Execute the query
		 */
		return query->execute();
    }

    /***
	 * Creates a Phalcon\Mvc\Model\Query\Builder
	 **/
    public function createBuilder($params  = null ) {

		$dependencyInjector = <DiInterface> $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injection object is required to access ORM services");
		}

		/**
		 * Gets Builder instance from DI container
		 */
		return <BuilderInterface> dependencyInjector->get(
			"Phalcon\\Mvc\\Model\\Query\\Builder",
			[
				params,
				dependencyInjector
			]
		);
    }

    /***
	 * Returns the last query created or executed in the models manager
	 **/
    public function getLastQuery() {
		return $this->_lastQuery;
    }

    /***
	 * Registers shorter aliases for namespaces in PHQL statements
	 **/
    public function registerNamespaceAlias($alias , $namespaceName ) {
		$this->_namespaceAliases[alias] = namespaceName;
    }

    /***
	 * Returns a real namespace from its alias
	 **/
    public function getNamespaceAlias($alias ) {

		if ( fetch namespaceName, $this->_namespaceAliases[alias] ) {
			return namespaceName;
		}
		throw new Exception("Namespace alias '" . alias . "' is not registered");
    }

    /***
	 * Returns all the registered namespace aliases
	 **/
    public function getNamespaceAliases() {
		return $this->_namespaceAliases;
    }

    /***
 	 * Destroys the current PHQL cache
 	 **/
    public function __destruct() {
		phalcon_orm_destroy_cache();
		Query::clean();
    }

}