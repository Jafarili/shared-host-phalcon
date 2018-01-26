<?php


namespace Phalcon\Mvc\Model;

use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\BehaviorInterface;


/***
 * Phalcon\Mvc\Model\Behavior
 *
 * This is an optional base class for ORM behaviors
 **/

abstract class Behavior {

    protected $_options;

    /***
	 * Phalcon\Mvc\Model\Behavior
	 *
	 * @param array options
	 **/
    public function __construct($options  = null ) {
		$this->_options = options;
    }

    /***
	 * Checks whether the behavior must take action on certain event
	 **/
    protected function mustTakeAction($eventName ) {
		return isset $this->_options[eventName];
    }

    /***
	 * Returns the behavior options related to an event
	 *
	 * @param string eventName
	 * @return array
	 **/
    protected function getOptions($eventName  = null ) {

		$options = $this->_options;
		if ( eventName !== null ) {
			if ( fetch eventOptions, options[eventName] ) {
				return eventOptions;
			}
			return null;
		}
		return options;
    }

    /***
	 * This method receives the notifications from the EventsManager
	 **/
    public function notify($type , $model ) {
		return null;
    }

    /***
	 * Acts as fallbacks when a missing method is called on the model
	 *
	 * @param \Phalcon\Mvc\ModelInterface model
	 * @param string method
	 * @param array arguments
	 **/
    public function missingMethod($model , $method , $arguments  = null ) {
		return null;
    }

}