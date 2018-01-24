<?php


namespace Phalcon;

use Phalcon\Application\Exception;
use Phalcon\DiInterface;
use Phalcon\Di\Injectable;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface;


/***
 * Phalcon\Application
 *
 * Base class for Phalcon\Cli\Console and Phalcon\Mvc\Application.
 **/

abstract class Application extends Injectable {

    protected $_eventsManager;

    protected $_dependencyInjector;

    /***
	 * @var string
	 **/
    protected $_defaultModule;

    /***
	 * @var array
	 **/
    protected $_modules;

    /***
	 * Phalcon\Application
	 **/
    public function __construct($dependencyInjector  = null ) {

    }

    /***
	 * Sets the events manager
	 **/
    public function setEventsManager($eventsManager ) {

    }

    /***
	 * Returns the internal event manager
	 **/
    public function getEventsManager() {

    }

    /***
	 * Register an array of modules present in the application
	 *
	 * <code>
	 * $this->registerModules(
	 *     [
	 *         "frontend" => [
	 *             "className" => "Multiple\\Frontend\\Module",
	 *             "path"      => "../apps/frontend/Module.php",
	 *         ],
	 *         "backend" => [
	 *             "className" => "Multiple\\Backend\\Module",
	 *             "path"      => "../apps/backend/Module.php",
	 *         ],
	 *     ]
	 * );
	 * </code>
	 **/
    public function registerModules($modules , $merge  = false ) {

    }

    /***
	 * Return the modules registered in the application
	 **/
    public function getModules() {

    }

    /***
	 * Gets the module definition registered in the application via module name
	 **/
    public function getModule($name ) {

    }

    /***
	 * Sets the module name to be used if the router doesn't return a valid module
	 **/
    public function setDefaultModule($defaultModule ) {

    }

    /***
	 * Returns the default module name
	 **/
    public function getDefaultModule() {

    }

    /***
	 * Handles a request
	 **/
    abstract public function handle() {

    }

}