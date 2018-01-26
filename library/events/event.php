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
		$this->_type = type,
			this->_source = source;

		if ( data !== null ) {
			$this->_data = data;
		}

		if ( cancelable !== true ) {
			$this->_cancelable = cancelable;
		}
    }

    /***
	 * Sets event data.
	 * @param mixed data
	 **/
    public function setData($data  = null ) {
		$this->_data = data;

		return this;
    }

    /***
	 * Sets event type.
	 **/
    public function setType($type ) {
		$this->_type = type;

		return this;
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
		if ( !this->_cancelable ) {
			throw new Exception("Trying to cancel a non-cancelable event");
		}

		$this->_stopped = true;

		return this;
    }

    /***
	 * Check whether the event is currently stopped.
	 **/
    public function isStopped() {
		return $this->_stopped;
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
		return $this->_cancelable;
    }

}