<?php


namespace Phalcon\Mvc;

use Phalcon\Mvc\ControllerInterface;
use Phalcon\DispatcherInterface as DispatcherInterfaceBase;


/***
 * Phalcon\Mvc\DispatcherInterface
 *
 * Interface for Phalcon\Mvc\Dispatcher
 **/

interface DispatcherInterface {

    /***
	 * Sets the default controller suffix
	 **/
    public function setControllerSuffix($controllerSuffix ); 

    /***
	 * Sets the default controller name
	 **/
    public function setDefaultController($controllerName ); 

    /***
	 * Sets the controller name to be dispatched
	 **/
    public function setControllerName($controllerName ); 

    /***
	 * Gets last dispatched controller name
	 **/
    public function getControllerName(); 

    /***
	 * Returns the latest dispatched controller
	 **/
    public function getLastController(); 

    /***
	 * Returns the active controller in the dispatcher
	 **/
    public function getActiveController(); 

}