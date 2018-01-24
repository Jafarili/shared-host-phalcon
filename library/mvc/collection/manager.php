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

    }

    /***
	 * Returns the DependencyInjector container
	 **/
    public function getDI() {

    }

    /***
	 * Sets the event manager
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
	 * Initializes a model in the models manager
	 **/
    public function initialize($model ) {

    }

    /***
	 * Check whether a model is already initialized
	 **/
    public function isInitialized($modelName ) {

    }

    /***
	 * Get the latest initialized model
	 **/
    public function getLastInitialized() {

    }

    /***
	 * Sets a connection service for a specific model
	 **/
    public function setConnectionService($model , $connectionService ) {

    }

    /***
	 * Gets a connection service for a specific model
	 **/
    public function getConnectionService($model ) {

    }

    /***
	 * Sets whether a model must use implicit objects ids
	 **/
    public function useImplicitObjectIds($model , $useImplicitObjectIds ) {

    }

    /***
	 * Checks if a model is using implicit object ids
	 **/
    public function isUsingImplicitObjectIds($model ) {

    }

    /***
	 * Returns the connection related to a model
	 *
	 * @param \Phalcon\Mvc\CollectionInterface $model
	 * @return \Mongo
	 **/
    public function getConnection($model ) {

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
	 * meaning that at least one was implemented
	 **/
    public function missingMethod($model , $eventName , $data ) {

    }

    /***
	 * Binds a behavior to a model
	 **/
    public function addBehavior($model , $behavior ) {

    }

}