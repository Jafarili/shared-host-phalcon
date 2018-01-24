<?php


namespace Phalcon\Events;



/***
 * Phalcon\Events\EventInterface
 *
 * Interface for Phalcon\Events\Event class
 **/

interface EventInterface {

    /***
	 * Gets event data
	 **/
    public function getData(); 

    /***
	 * Sets event data
	 * @param mixed data
	 **/
    public function setData($data  = null ); 

    /***
	 * Gets event type
	 **/
    public function getType(); 

    /***
	 * Sets event type
	 **/
    public function setType($type ); 

    /***
	 * Stops the event preventing propagation
	 **/
    public function stop(); 

    /***
	 * Check whether the event is currently stopped
	 **/
    public function isStopped(); 

    /***
	 * Check whether the event is cancelable
	 **/
    public function isCancelable(); 

}