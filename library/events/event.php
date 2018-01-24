<?php


namespace Phalcon\Events;



/***
 * Phalcon\Events\Event
 *
 * This class offers contextual information of a fired event in the EventsManager
 **/

class Event {

    /***
	 * Event type
	 *
	 * @var string
	 **/
    protected $_type;

    /***
	 * Event source
	 *
	 * @var object
	 **/
    protected $_source;

    /***
	 * Event data
	 *
	 * @var mixed
	 **/
    protected $_data;

    /***
	 * Is event propagation stopped?
	 *
	 * @var boolean
	 **/
    protected $_stopped;

    /***
	 * Is event cancelable?
	 *
	 * @var boolean
	 **/
    protected $_cancelable;

    /***
	 * Phalcon\Events\Event constructor
	 *
	 * @param string type
	 * @param object source
	 * @param mixed data
	 * @param boolean cancelable
	 **/
    public function __construct($type , $source , $data  = null , $cancelable  = true ) {

    }

    /***
	 * Sets event data.
	 * @param mixed data
	 **/
    public function setData($data  = null ) {

    }

    /***
	 * Sets event type.
	 **/
    public function setType($type ) {

    }

    /***
	 * Stops the event preventing propagation.
	 *
	 * <code>
	 * if ($event->isCancelable()) {
	 *     $event->stop();
	 * }
	 * </code>
	 **/
    public function stop() {

    }

    /***
	 * Check whether the event is currently stopped.
	 **/
    public function isStopped() {

    }

    /***
	 * Check whether the event is cancelable.
	 *
	 * <code>
	 * if ($event->isCancelable()) {
	 *     $event->stop();
	 * }
	 * </code>
	 **/
    public function isCancelable() {

    }

}