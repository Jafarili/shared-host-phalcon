<?php


namespace Phalcon\Mvc\Collection;

use Phalcon\Mvc\CollectionInterface;


/***
 * Phalcon\Mvc\Collection\Behavior
 *
 * This is an optional base class for ORM behaviors
 **/

abstract class Behavior {

    protected $_options;

    /***
	 * Phalcon\Mvc\Collection\Behavior
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
	 * Acts as fallbacks when a missing method is called on the collection
	 **/
    public function missingMethod($model , $method , $arguments  = null ) {
		return null;
    }

}