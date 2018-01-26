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
		$this->_handlerSuffix = taskSuffix;
    }

    /***
	 * Sets the default task name
	 **/
    public function setDefaultTask($taskName ) {
		$this->_defaultHandler = taskName;
    }

    /***
	 * Sets the task name to be dispatched
	 **/
    public function setTaskName($taskName ) {
		$this->_handlerName = taskName;
    }

    /***
	 * Gets last dispatched task name
	 **/
    public function getTaskName() {
		return $this->_handlerName;
    }

    /***
	 * Throws an internal exception
	 **/
    protected function _throwDispatchException($message , $exceptionCode  = 0 ) {

		$exception = new Exception(message, exceptionCode);

		if ( $this->_handleException(exception) === false ) {
			return false;
		}

		throw exception;
    }

    /***
	 * Handles a user exception
	 **/
    protected function _handleException($exception ) {
		$eventsManager = <ManagerInterface> $this->_eventsManager;
		if ( gettype($eventsManager) == "object" ) {
			if ( eventsManager->fire("dispatch:befor (eException", this, exception) === false ) ) {
				return false;
			}
		}
    }

    /***
	 * Returns the latest dispatched controller
	 **/
    public function getLastTask() {
		return $this->_lastHandler;
    }

    /***
	 * Returns the active task in the dispatcher
	 **/
    public function getActiveTask() {
		return $this->_activeHandler;
    }

    /***
	 * Set the options to be dispatched
	 **/
    public function setOptions($options ) {
		$this->_options = options;
    }

    /***
	 * Get dispatched options
	 **/
    public function getOptions() {
		return $this->_options;
    }

    /***
	 * Gets an option by its name or numeric index
	 *
	 * @param  mixed $option
	 * @param  string|array $filters
	 * @param  mixed $defaultValue
	 **/
    public function getOption($option , $filters  = null , $defaultValue  = null ) {

		$options = $this->_options;
		if ( !fetch optionValue, options[option] ) {
			return defaultValue;
		}

		if ( filters === null ) {
			return optionValue;
		}

		$dependencyInjector = $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			this->{"_throwDispatchException"}(
				"A dependency injection object is required to access the 'filter' service",
				CliDispatcher::EXCEPTION_NO_DI
			);
		}
		$filter = <FilterInterface> dependencyInjector->getShared("filter");

		return filter->sanitize(optionValue, filters);
    }

    /***
	 * Check if an option exists
	 **/
    public function hasOption($option ) {
		return isset $this->_options[option];
    }

    /***
	 * Calls the action method.
	 **/
    public function callActionMethod($handler , $actionMethod , $params ) {

		$options = $this->_options;
		
		return call_user_func_array([handler, actionMethod], [params, options]);
    }

}