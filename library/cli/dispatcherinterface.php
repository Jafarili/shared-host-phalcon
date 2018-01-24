<?php


namespace Phalcon\Cli;

use Phalcon\DispatcherInterface as DispatcherInterfaceBase;


/***
 * Phalcon\Cli\DispatcherInterface
 *
 * Interface for Phalcon\Cli\Dispatcher
 **/

interface DispatcherInterface {

    /***
	 * Sets the default task suffix
	 **/
    public function setTaskSuffix($taskSuffix ); 

    /***
	 * Sets the default task name
	 **/
    public function setDefaultTask($taskName ); 

    /***
	 * Sets the task name to be dispatched
	 **/
    public function setTaskName($taskName ); 

    /***
	 * Gets last dispatched task name
	 **/
    public function getTaskName(); 

    /***
	 * Returns the latest dispatched controller
	 **/
    public function getLastTask(); 

    /***
	 * Returns the active task in the dispatcher
	 **/
    public function getActiveTask(); 

}