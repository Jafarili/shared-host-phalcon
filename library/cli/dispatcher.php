<?php


namespace Phalcon\Cli;

use Phalcon\FilterInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Cli\Dispatcher\Exception;
use Phalcon\Dispatcher as CliDispatcher;


/***
 * Phalcon\Cli\Dispatcher
 *
 * Dispatching is the process of taking the command-line arguments, extracting the module name,
 * task name, action name, and optional parameters contained in it, and then
 * instantiating a task and calling an action on it.
 *
 * <code>
 * use Phalcon\Di;
 * use Phalcon\Cli\Dispatcher;
 *
 * $di = new Di();
 * $dispatcher = new Dispatcher();
 * $dispatcher->setDi($di);
 *
 * $dispatcher->setTaskName("posts");
 * $dispatcher->setActionName("index");
 * $dispatcher->setParams([]);
 *
 * $handle = $dispatcher->dispatch();
 * </code>
 **/

class Dispatcher extends CliDispatcher {

    protected $_handlerSuffix;

    protected $_defaultHandler;

    protected $_defaultAction;

    protected $_options;

    /***
	 * Sets the default task suffix
	 **/
    public function setTaskSuffix($taskSuffix ) {

    }

    /***
	 * Sets the default task name
	 **/
    public function setDefaultTask($taskName ) {

    }

    /***
	 * Sets the task name to be dispatched
	 **/
    public function setTaskName($taskName ) {

    }

    /***
	 * Gets last dispatched task name
	 **/
    public function getTaskName() {

    }

    /***
	 * Throws an internal exception
	 **/
    protected function _throwDispatchException($message , $exceptionCode  = 0 ) {

    }

    /***
	 * Handles a user exception
	 **/
    protected function _handleException($exception ) {

    }

    /***
	 * Returns the latest dispatched controller
	 **/
    public function getLastTask() {

    }

    /***
	 * Returns the active task in the dispatcher
	 **/
    public function getActiveTask() {

    }

    /***
	 * Set the options to be dispatched
	 **/
    public function setOptions($options ) {

    }

    /***
	 * Get dispatched options
	 **/
    public function getOptions() {

    }

    /***
	 * Gets an option by its name or numeric index
	 *
	 * @param  mixed $option
	 * @param  string|array $filters
	 * @param  mixed $defaultValue
	 **/
    public function getOption($option , $filters  = null , $defaultValue  = null ) {

    }

    /***
	 * Check if an option exists
	 **/
    public function hasOption($option ) {

    }

    /***
	 * Calls the action method.
	 **/
    public function callActionMethod($handler , $actionMethod , $params ) {

    }

}