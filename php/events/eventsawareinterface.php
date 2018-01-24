<?php


namespace Phalcon\Events;



/***
 * Phalcon\Events\EventsAwareInterface
 *
 * This interface must for those classes that accept an EventsManager and dispatch events
 **/

interface EventsAwareInterface {

    /***
	 * Sets the events manager
	 **/
    public function setEventsManager($eventsManager ); 

    /***
	 * Returns the internal event manager
	 **/
    public function getEventsManager(); 

}