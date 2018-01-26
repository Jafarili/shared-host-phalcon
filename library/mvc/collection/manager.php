<?php


namespace Phalcon\Mvc\Collection;

use Phalcon\DiInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Mvc\CollectionInterface;
use Phalcon\Mvc\Collection\BehaviorInterface;


/***
 * Phalcon\Mvc\Collection\Manager
 *
 * This components controls the initialization of models, keeping record of relations
 * between the different models of the application.
 *
 * A CollectionManager is injected to a model via a Dependency Injector Container such as Phalcon\Di.
 *
 * <code>
 * $di = new \Phalcon\Di();
 *
 * $di->set(
 *     "collectionManager",
 *     function () {
 *         return new \Phalcon\Mvc\Collection\Manager();
 *     }
 * );
 *
 * $robot = new Robots($di);
 * </code>
 **/

class Manager {

    protected $_dependencyInjector;

    protected $_initialized;

    protected $_lastInitialized;

    protected $_eventsManager;

    protected $_customEventsManager;

    protected $_connectionServices;

    protected $_implicitObjectsIds;

    protected $_behaviors;

    protected $_serviceName;

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
	 * Sets the event manager
	 **/
    public function setEventsManager($eventsManager ) {
		$this->_eventsManager = eventsManager;
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
		$this->_customEventsManager[get_class(model)] = eventsManager;
    }

    /***
	 * Returns a custom events manager related to a model
	 **/
    public function getCustomEventsManager($model ) {

		$customEventsManager = $this->_customEventsManager;
		if ( gettype($customEventsManager) == "array" ) {
			$className = get_class_lower(model);
			if ( isset($customEventsManager[className]) ) {
				return customEventsManager[className];
			}
		}

		return null;
    }

    /***
	 * Initializes a model in the models manager
	 **/
    public function initialize($model ) {

		$className = get_class(model);
		$initialized = $this->_initialized;

		/**
		* Models are just initialized once per request
		*/
		if ( !isset($initialized[className]) ) {

			/**
			* Call the 'initialize' method if ( it's implemented
			*/
			if ( method_exists(model, "initialize") ) {
				model->{"initialize"}();
			}

			/**
			* If an EventsManager is available we pass to it every initialized model
			*/
			$eventsManager = $this->_eventsManager;
			if ( gettype($eventsManager) == "object" ) {
				eventsManager->fire("collectionManager:afterInitialize", model);
			}

			$this->_initialized[className] = model;
			$this->_lastInitialized = model;
		}
    }

    /***
	 * Check whether a model is already initialized
	 **/
    public function isInitialized($modelName ) {
		return isset $this->_initialized[strtolower(modelName)];
    }

    /***
	 * Get the latest initialized model
	 **/
    public function getLastInitialized() {
		return $this->_lastInitialized;
    }

    /***
	 * Sets a connection service for a specific model
	 **/
    public function setConnectionService($model , $connectionService ) {
		$this->_connectionServices[get_class(model)] = connectionService;
    }

    /***
	 * Gets a connection service for a specific model
	 **/
    public function getConnectionService($model ) {

		$service = $this->_serviceName;
		$entityName = get_class(model);
		if ( isset($this->_connectionServices[entityName]) ) {
			$service = $this->_connectionServices[entityName];
		}

		return service;
    }

    /***
	 * Sets whether a model must use implicit objects ids
	 **/
    public function useImplicitObjectIds($model , $useImplicitObjectIds ) {
		$this->_implicitObjectsIds[get_class(model)] = useImplicitObjectIds;
    }

    /***
	 * Checks if a model is using implicit object ids
	 **/
    public function isUsingImplicitObjectIds($model ) {

		/**
		* All collections use by default are using implicit object ids
		*/
		if ( fetch implicit, $this->_implicitObjectsIds[get_class(model)] ) {
			return implicit;
		}

		return true;
    }

    /***
	 * Returns the connection related to a model
	 *
	 * @param \Phalcon\Mvc\CollectionInterface $model
	 * @return \Mongo
	 **/
    public function getConnection($model ) {

		$service = $this->_serviceName;
		$connectionService = $this->_connectionServices;
		if ( gettype($connectionService) == "array" ) {
			$entityName = get_class(model);

			/**
			* Check if ( the model has a custom connection service
			*/
			if ( isset($connectionService[entityName]) ) {
				$service = connectionService[entityName];
			}
		}

		$dependencyInjector = $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injector container is required to obtain the services related to the ORM");
		}

		/**
		 * Request the connection service from the DI
		 */
		$connection = dependencyInjector->getShared(service);
		if ( gettype($connection) != "object" ) {
			throw new Exception("Invalid injected connection service");
		}

		return connection;
    }

    /***
	 * Receives events generated in the models and dispatches them to an events-manager if available
	 * Notify the behaviors that are listening in the model
	 **/
    public function notifyEvent($eventName , $model ) {

		$behaviors = $this->_behaviors;
		if ( gettype($behaviors) == "array" ) {
			if ( fetch modelsBehaviors, behaviors[get_class_lower(model)] ) {

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
		}

		/**
		 * Dispatch events to the global events manager
		 */
		$eventsManager = $this->_eventsManager;
		if ( gettype($eventsManager) == "object" ) {
			$status = eventsManager->fire( "collection:". eventName, model);
			if ( !status ) {
				return status;
			}
		}

		/**
		 * A model can has a specif (ic events manager for ( it
		 */
		$customEventsManager = $this->_customEventsManager;
		if ( gettype($customEventsManager) == "array" ) {
			if ( isset($customEventsManager[get_class_lower(model)]) ) {
				$status = customEventsManager->fire("collection:" . eventName, model);
				if ( !status ) {
					return status;
				}
			}
		}

		return status;
    }

    /***
	 * Dispatch an event to the listeners and behaviors
	 * This method expects that the endpoint listeners/behaviors returns true
	 * meaning that at least one was implemented
	 **/
    public function missingMethod($model , $eventName , $data ) {

		/**
		 * Dispatch events to the global events manager
		 */
		$behaviors = $this->_behaviors;
		if ( gettype($behaviors) == "array" ) {

			if ( fetch modelsBehaviors, behaviors[get_class_lower(model)] ) {

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
		}

		/**
		 * Dispatch events to the global events manager
		 */
		$eventsManager = $this->_eventsManager;
		if ( gettype($eventsManager) == "object" ) {
			return eventsManager->fire("model:" . eventName, model, data);
		}

		return false;
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

}