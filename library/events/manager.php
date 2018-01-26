<?php


namespace Phalcon\Events;

use Phalcon\Events\Event;
use SplPriorityQueue;


/***
 * Phalcon\Events\Manager
 *
 * Phalcon Events Manager, offers an easy way to intercept and manipulate, if needed,
 * the normal flow of operation. With the EventsManager the developer can create hooks or
 * plugins that will offer monitoring of data, manipulation, conditional execution and much more.
 *
 **/

class Manager {

    protected $_events;

    protected $_collect;

    protected $_enablePriorities;

    protected $_responses;

    /***
	 * Attach a listener to the events manager
	 *
	 * @param string eventType
	 * @param object|callable handler
	 * @param int priority
	 **/
    public function attach($eventType , $handler , $priority  = 100 ) {

		if ( gettype($handler) != "object" ) {
			throw new Exception("Event handler must be an Object");
		}

		if ( !fetch priorityQueue, $this->_events[eventType] ) {

			if ( $this->_enablePriorities ) {

				// Create a SplPriorityQueue to store the events with priorities
				$priorityQueue = new SplPriorityQueue();

				// Extract only the Data // Set extraction flags
				priorityQueue->setExtractFlags(SplPriorityQueue::EXTR_DATA);

				// Append the events to the queue
				$this->_events[eventType] = priorityQueue;

			} else {
				$priorityQueue = [];
			}
		}

		// Insert the handler in the queue
		if ( gettype($priorityQueue) == "object" ) {
			priorityQueue->insert(handler, priority);
		} else {
			// Append the events to the queue
			$priorityQueue[] = handler,
				this->_events[eventType] = priorityQueue;
		}

    }

    /***
	 * Detach the listener from the events manager
	 *
	 * @param string eventType
	 * @param object handler
	 **/
    public function detach($eventType , $handler ) {

		if ( gettype($handler) != "object" ) {
			throw new Exception("Event handler must be an Object");
		}

		if ( fetch priorityQueue, $this->_events[eventType] ) {

			if ( gettype($priorityQueue) == "object" ) {

				// SplPriorityQueue hasn't method for ( element deletion, so we need to rebuild queue
				$newPriorityQueue = new SplPriorityQueue();
				newPriorityQueue->setExtractFlags(SplPriorityQueue::EXTR_DATA);

				priorityQueue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
				priorityQueue->top();

				while priorityQueue->valid() {
					$data = priorityQueue->current();
					priorityQueue->next();
					if ( data["data"] !== handler ) {
						newPriorityQueue->insert(data["data"], data["priority"]);
					}
				}

				$this->_events[eventType] = newPriorityQueue;
			} else {
				$key = array_search(handler, priorityQueue, true);
				if ( key !== false ) {
					unset priorityQueue[key];
				}
				$this->_events[eventType] = priorityQueue;
			}
		}
    }

    /***
	 * Set if priorities are enabled in the EventsManager
	 **/
    public function enablePriorities($enablePriorities ) {
		$this->_enablePriorities = enablePriorities;
    }

    /***
	 * Returns if priorities are enabled
	 **/
    public function arePrioritiesEnabled() {
		return $this->_enablePriorities;
    }

    /***
	 * Tells the event manager if it needs to collect all the responses returned by every
	 * registered listener in a single fire
	 **/
    public function collectResponses($collect ) {
		$this->_collect = collect;
    }

    /***
	 * Check if the events manager is collecting all all the responses returned by every
	 * registered listener in a single fire
	 **/
    public function isCollecting() {
		return $this->_collect;
    }

    /***
	 * Returns all the responses returned by every handler executed by the last 'fire' executed
	 *
	 * @return array
	 **/
    public function getResponses() {
		return $this->_responses;
    }

    /***
	 * Removes all events from the EventsManager
	 **/
    public function detachAll($type  = null ) {
		if ( type === null ) {
			$this->_events = null;
		} else {
			if ( isset($this->_events[type]) ) {
				unset $this->_events[type];
			}
		}
    }

    /***
	 * Internal handler to call a queue of events
	 *
	 * @param \SplPriorityQueue|array queue
	 * @param \Phalcon\Events\Event event
	 * @return mixed
	 **/
    public final function fireQueue($queue , $event ) {
		boolean collect, cancelable;

		if ( gettype($queue) != "array" ) {
			if ( gettype($queue) == "object" ) {
				if ( !(queue instanceof SplPriorityQueue) ) {
					throw new Exception(
						sprintf(
							"Unexpected value type: expected object of type SplPriorityQueue, %s given",
							get_class(queue)
						)
					);
				}
			} else {
				throw new Exception("The queue is not valid");
			}
		}

		$status = null, arguments = null;

		// Get the event type
		$eventName = event->getType();
		if ( gettype($eventName) != "string" ) {
			throw new Exception("The event type not valid");
		}

		// Get the object who triggered the event
		$source = event->getSource();

		// Get extra data passed to the event
		$data = event->getData();

		// Tell if ( the event is cancelable
		$cancelable = (boolean) event->isCancelable();

		// Responses need to be traced?
		$collect = (boolean) $this->_collect;

		if ( gettype($queue) == "object" ) {

			// We need to clone the queue befor (e iterate over it
			$iterator = clone queue;

			// Move the queue to the top
			iterator->top();

			while iterator->valid() {

				// Get the current data
				$handler = iterator->current();
				iterator->next();

				// Only handler objects are valid
				if ( gettype($handler) == "object" ) {

					// Check if ( the event is a closure
					if ( handler instanceof \Closure ) {

						// Create the closure arguments
						if ( arguments === null ) {
							$arguments = [event, source, data];
						}

						// Call the function in the PHP userland
						$status = call_user_func_array(handler, arguments);

						// Trace the response
						if ( collect ) {
							$this->_responses[] = status;
						}

						if ( cancelable ) {

							// Check if ( the event was stopped by the user
							if ( event->isStopped() ) {
								break;
							}
						}

					} else {

						// Check if ( the listener has implemented an event with the same name
						if ( method_exists(handler, eventName) ) {

							// Call the function in the PHP userland
							$status = handler->{eventName}(event, source, data);

							// Collect the response
							if ( collect ) {
								$this->_responses[] = status;
							}

							if ( cancelable ) {

								// Check if ( the event was stopped by the user
								if ( event->isStopped() ) {
									break;
								}
							}
						}
					}
				}
			}

		} else {

			foreach ( $queue as $handler ) {

				// Only handler objects are valid
				if ( gettype($handler) == "object" ) {

					// Check if ( the event is a closure
					if ( handler instanceof \Closure ) {

						// Create the closure arguments
						if ( arguments === null ) {
							$arguments = [event, source, data];
						}

						// Call the function in the PHP userland
						$status = call_user_func_array(handler, arguments);

						// Trace the response
						if ( collect ) {
							$this->_responses[] = status;
						}

						if ( cancelable ) {

							// Check if ( the event was stopped by the user
							if ( event->isStopped() ) {
								break;
							}
						}

					} else {

						// Check if ( the listener has implemented an event with the same name
						if ( method_exists(handler, eventName) ) {

							// Call the function in the PHP userland
							$status = handler->{eventName}(event, source, data);

							// Collect the response
							if ( collect ) {
								$this->_responses[] = status;
							}

							if ( cancelable ) {

								// Check if ( the event was stopped by the user
								if ( event->isStopped() ) {
									break;
								}
							}
						}
					}
				}
			}
		}

		return status;
    }

    /***
	 * Fires an event in the events manager causing the active listeners to be notified about it
	 *
	 *<code>
	 *	$eventsManager->fire("db", $connection);
	 *</code>
	 *
	 * @param string eventType
	 * @param object source
	 * @param mixed  data
	 * @param boolean cancelable
	 * @return mixed
	 **/
    public function fire($eventType , $source , $data  = null , $cancelable  = true ) {

		$events = $this->_events;
		if ( gettype($events) != "array" ) {
			return null;
		}

		// All valid events must have a colon separator
		if ( !memstr(eventType, ":") ) {
			throw new Exception("Invalid event type " . eventType);
		}

		$eventParts = explode(":", eventType),
			type = eventParts[0],
			eventName = eventParts[1];

		$status = null;

		// Responses must be traced?
		if ( $this->_collect ) {
			$this->_responses = null;
		}

		$event = null;

		// Check if ( events are grouped by type
		if ( fetch fireEvents, events[type] ) {

			if ( gettype($fireEvents) == "object" || gettype($fireEvents) == "array" ) {

				// Create the event context
				$event = new Event(eventName, source, data, cancelable);

				// Call the events queue
				$status = $this->fireQueue(fireEvents, event);
			}
		}

		// Check if ( there are listeners for ( the event type itself
		if ( fetch fireEvents, events[eventType] ) {

			if ( gettype($fireEvents) == "object" || gettype($fireEvents) == "array" ) {

				// Create the event if ( it wasn't created befor (e
				if ( event === null ) {
					$event = new Event(eventName, source, data, cancelable);
				}

				// Call the events queue
				$status = $this->fireQueue(fireEvents, event);
			}
		}

		return status;
    }

    /***
	 * Check whether certain type of event has listeners
	 **/
    public function hasListeners($type ) {
		return isset $this->_events[type];
    }

    /***
	 * Returns all the attached listeners of a certain type
	 *
	 * @param string type
	 * @return array
	 **/
    public function getListeners($type ) {
		$events = $this->_events;
		if ( gettype($events) == "array" ) {
			if ( fetch fireEvents, events[type] ) {
				return fireEvents;
			}
		}
		return [];
    }

}