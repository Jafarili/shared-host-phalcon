<?php


namespace Phalcon\Mvc;

use Phalcon\Di;
use Phalcon\DiInterface;
use Phalcon\Mvc\Collection\Document;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Mvc\Collection\ManagerInterface;
use Phalcon\Mvc\Collection\BehaviorInterface;
use Phalcon\Mvc\Collection\Exception;
use Phalcon\Mvc\Model\MessageInterface;
use Phalcon\Mvc\Model\Message as Message;
use Phalcon\ValidationInterface;


/***
 * Phalcon\Mvc\Collection
 *
 * This component implements a high level abstraction for NoSQL databases which
 * works with documents
 **/

abstract class Collection {

    const OP_NONE= 0;

    const OP_CREATE= 1;

    const OP_UPDATE= 2;

    const OP_DELETE= 3;

    const DIRTY_STATE_PERSISTENT= 0;

    const DIRTY_STATE_TRANSIENT= 1;

    const DIRTY_STATE_DETACHED= 2;

    public $_id;

    protected $_dependencyInjector;

    protected $_modelsManager;

    protected $_source;

    protected $_operationMade;

    protected $_dirtyState;

    protected $_connection;

    protected $_errorMessages;

    protected static $_reserved;

    protected static $_disableEvents;

    protected $_skipped;

    /***
	 * Phalcon\Mvc\Collection constructor
	 **/
    public final function __construct($dependencyInjector  = null , $modelsManager  = null ) {
		if ( gettype($dependencyInjector) != "object" ) {
			$dependencyInjector = Di::getDefault();
		}

		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injector container is required to obtain the services related to the ODM");
		}

		$this->_dependencyInjector = dependencyInjector;

		/**
		 * Inject the manager service from the DI
		 */
		if ( gettype($modelsManager) != "object" ) {
			$modelsManager = dependencyInjector->getShared("collectionManager");
			if ( gettype($modelsManager) != "object" ) {
				throw new Exception("The injected service 'modelsManager' is not valid");
			}
		}

		/**
		 * Update the models-manager
		 */
		$this->_modelsManager = modelsManager;

		/**
		 * The manager always initializes the object
		 */
		modelsManager->initialize(this);

		/**
		 * This allows the developer to execute initialization stuff every time an instance is created
		 */
		if ( method_exists(this, "onConstruct") ) {
			this->{"onConstruct"}();
		}
    }

    /***
	 * Sets a value for the _id property, creates a MongoId object if needed
	 *
	 * @param mixed id
	 **/
    public function setId($id ) {

		if ( gettype($id) != "object" ) {

			/**
			 * Check if ( the model use implicit ids
			 */
			if ( $this->_modelsManager->isUsingImplicitObjectIds(this) ) {
				$mongoId = new \MongoId(id);
			} else {
				$mongoId = id;
			}

		} else {
			$mongoId = id;
		}
		$this->_id = mongoId;
    }

    /***
	 * Returns the value of the _id property
	 *
	 * @return \MongoId
	 **/
    public function getId() {
		return $this->_id;
    }

    /***
	 * Sets the dependency injection container
	 **/
    public function setDI($dependencyInjector ) {
		$this->_dependencyInjector = dependencyInjector;
    }

    /***
	 * Returns the dependency injection container
	 **/
    public function getDI() {
		return $this->_dependencyInjector;
    }

    /***
	 * Sets a custom events manager
	 **/
    protected function setEventsManager($eventsManager ) {
		this->_modelsManager->setCustomEventsManager(this, eventsManager);
    }

    /***
	 * Returns the custom events manager
	 **/
    protected function getEventsManager() {
		return $this->_modelsManager->getCustomEventsManager(this);
    }

    /***
	 * Returns the models manager related to the entity instance
	 **/
    public function getCollectionManager() {
		return $this->_modelsManager;
    }

    /***
	 * Returns an array with reserved properties that cannot be part of the insert/update
	 **/
    public function getReservedAttributes() {

		$reserved = self::_reserved;
		if ( gettype($reserved) != "array" ) {
			$reserved = [
				"_connection": true,
				"_dependencyInjector": true,
				"_source": true,
				"_operationMade": true,
				"_errorMessages": true,
				"_dirtyState": true,
				"_modelsManager": true,
				"_skipped" : true
			];
			$self::_reserved = reserved;
		}
		return reserved;
    }

    /***
	 * Sets if a model must use implicit objects ids
	 **/
    protected function useImplicitObjectIds($useImplicitObjectIds ) {
		this->_modelsManager->useImplicitObjectIds(this, useImplicitObjectIds);
    }

    /***
	 * Sets collection name which model should be mapped
	 **/
    protected function setSource($source ) {
		$this->_source = source;
		return this;
    }

    /***
	 * Returns collection name mapped in the model
	 **/
    public function getSource() {

		if ( !this->_source ) {
			$collection = this;
			$this->_source = uncamelize(get_class_ns(collection));
		}

		return $this->_source;
    }

    /***
	 * Sets the DependencyInjection connection service name
	 **/
    public function setConnectionService($connectionService ) {
		this->_modelsManager->setConnectionService(this, connectionService);
		return this;
    }

    /***
	 * Returns DependencyInjection connection service
	 **/
    public function getConnectionService() {
		return $this->_modelsManager->getConnectionService(this);
    }

    /***
	 * Retrieves a database connection
	 *
	 * @return \MongoDb
	 **/
    public function getConnection() {
		if ( gettype($this->_connection) != "object" ) {
			$this->_connection = $this->_modelsManager->getConnection(this);
		}

		return $this->_connection;
    }

    /***
	 * Reads an attribute value by its name
	 *
	 *<code>
	 *	echo $robot->readAttribute("name");
	 *</code>
	 *
	 * @param string attribute
	 * @return mixed
	 **/
    public function readAttribute($attribute ) {
		if ( !isset $this->) {attribute} ) {
			return null;
		}

		return $this->{attribute};
    }

    /***
	 * Writes an attribute value by its name
	 *
	 *<code>
	 *	$robot->writeAttribute("name", "Rosey");
	 *</code>
	 *
	 * @param string attribute
	 * @param mixed value
	 **/
    public function writeAttribute($attribute , $value ) {
		$this->{attribute} = value;
    }

    /***
	 * Returns a cloned collection
	 **/
    public static function cloneResult($collection , $document ) {

		$clonedCollection = clone collection;
		foreach ( key, $document as $value ) {
			clonedCollection->writeAttribute(key, value);
		}

		if ( method_exists(clonedCollection, "afterFetch") ) {
			clonedCollection->{"afterFetch"}();
		}

		return clonedCollection;
    }

    /***
	 * Returns a collection resultset
	 *
	 * @param array params
	 * @param \Phalcon\Mvc\Collection collection
	 * @param \MongoDb connection
	 * @param boolean unique
	 * @return array
	 **/
    protected static function _getResultset($params , $collection , $connection , $unique ) {
			fields, skip, limit, sort, document, collections, className;

		/**
		 * Check if ( "class" clause was defined
		 */
		if ( fetch className, params["class"] ) {
			$base = new {className}();

			if ( !(base instanceof CollectionInterface || base instanceof Collection\Document) ) {
				throw new Exception(
					"Object of class '" . className . "' must be an implementation of Phalcon\\Mvc\\CollectionInterface or an instance of Phalcon\\Mvc\\Collection\\Document"
				);
			}
		} else {
			$base = collection;
		}

        if ( base instanceof Collection ) {
		    base->setDirtyState(self::DIRTY_STATE_PERSISTENT);
        }

		$source = collection->getSource();
		if ( empty source ) {
			throw new Exception("Method getSource() returns empty string");
		}

		$mongoCollection = connection->selectCollection(source);

		if ( gettype($mongoCollection) != "object" ) {
			throw new Exception("Couldn't select mongo collection");
		}

		/**
		 * Convert the string to an array
		 */
		if ( !fetch conditions, params[0] ) {
			if ( !fetch conditions, params["conditions"] ) {
				$conditions = [];
			}
		}

		if ( gettype($conditions) != "array" ) {
			throw new Exception("Find parameters must be an array");
		}

		/**
		 * Perfor (m the find
		 */
		if ( fetch fields, params["fields"] ) {
			$documentsCursor = mongoCollection->find(conditions, fields);
		} else {
			$documentsCursor = mongoCollection->find(conditions);
		}

		/**
		 * Check if ( a "limit" clause was defined
		 */
		if ( fetch limit, params["limit"] ) {
			documentsCursor->limit(limit);
		}

		/**
		 * Check if ( a "sort" clause was defined
		 */
		if ( fetch sort, params["sort"] ) {
			documentsCursor->sort(sort);
		}

		/**
		 * Check if ( a "skip" clause was defined
		 */
		if ( fetch skip, params["skip"] ) {
			documentsCursor->skip(skip);
		}

		if ( unique === true ) {

			/**
			 * Requesting a single result
			 */
			documentsCursor->rewind();

			$document = documentsCursor->current();

			if ( gettype($document) != "array" ) {
				return false;
			}

			/**
			 * Assign the values to the base object
			 */
			return static::cloneResult(base, document);
		}

		/**
		 * Requesting a complete resultset
		 */
		$collections = [];
		for ( document in iterator_to_array(documentsCursor, false) ) {

			/**
			 * Assign the values to the base object
			 */
			$collections[] = static::cloneResult(base, document);
		}

		return collections;
    }

    /***
	 * Perform a count over a resultset
	 *
	 * @param array params
	 * @param \Phalcon\Mvc\Collection collection
	 * @param \MongoDb connection
	 * @return int
	 **/
    protected static function _getGroupResultset($params , $collection , $connection ) {

		$source = collection->getSource();
		if ( empty source ) {
			throw new Exception("Method getSource() returns empty string");
		}

		$mongoCollection = connection->selectCollection(source);

		/**
		 * Convert the string to an array
		 */
		if ( !fetch conditions, params[0] ) {
			if ( !fetch conditions, params["conditions"] ) {
				$conditions = [];
			}
		}

		if ( isset params["limit"] || isset params["sort"] || isset params["skip"] ) {

			/**
			 * Perfor (m the find
			 */
			$documentsCursor = mongoCollection->find(conditions);

			/**
			 * Check if ( a "limit" clause was defined
			 */
			if ( fetch limit, params["limit"] ) {
				documentsCursor->limit(limit);
			}

			/**
			 * Check if ( a "sort" clause was defined
			 */
			if ( fetch sort, params["sort"] ) {
				documentsCursor->sort(sort);
			}

			/**
			 * Check if ( a "skip" clause was defined
			 */
			if ( fetch sort, params["skip"] ) {
				documentsCursor->skip(sort);
			}

			/**
			 * Only "count" is supported
			 */
			return count(documentsCursor);
		}

		return mongoCollection->count(conditions);
    }

    /***
	 * Executes internal hooks before save a document
	 *
	 * @param \Phalcon\DiInterface dependencyInjector
	 * @param boolean disableEvents
	 * @param boolean exists
	 * @return boolean
	 **/
    protected final function _preSave($dependencyInjector , $disableEvents , $exists ) {

		/**
		 * Run Validation Callbacks Befor (e
		 */
		if ( !disableEvents ) {

			if ( $this->fireEventCancel("befor (eValidation") === false ) ) {
				return false;
			}

			if ( !exists ) {
				$eventName = "befor (eValidationOnCreate";
			} else {
				$eventName = "befor (eValidationOnUpdate";
			}

			if ( $this->fireEventCancel(eventName) === false ) {
				return false;
			}

		}

		/**
		 * Run validation
		 */
		if ( $this->fireEventCancel("validation") === false ) {
			if ( !disableEvents ) {
				this->fireEvent("onValidationFails");
			}
			return false;
		}

		if ( !disableEvents ) {

			/**
			 * Run Validation Callbacks After
			 */
			if ( !exists ) {
				$eventName = "afterValidationOnCreate";
			} else {
				$eventName = "afterValidationOnUpdate";
			}

			if ( $this->fireEventCancel(eventName) === false ) {
				return false;
			}

			if ( $this->fireEventCancel("afterValidation") === false ) {
				return false;
			}

			/**
			 * Run Befor (e Callbacks
			 */
			if ( $this->fireEventCancel("befor (eSave") === false ) ) {
				return false;
			}

			if ( exists ) {
				$eventName = "befor (eUpdate";
			} else {
				$eventName = "befor (eCreate";
			}

			if ( $this->fireEventCancel(eventName) === false ) {
				return false;
			}

		}

		return true;
    }

    /***
	 * Executes internal events after save a document
	 **/
    protected final function _postSave($disableEvents , $success , $exists ) {

		if ( success ) {
			if ( !disableEvents ) {
				if ( exists ) {
					$eventName = "afterUpdate";
				} else {
					$eventName = "afterCreate";
				}

				this->fireEvent(eventName);

				this->fireEvent("afterSave");
			}

			return success;
		}

		if ( !disableEvents ) {
			this->fireEvent("notSave");
		}

		this->_cancelOperation(disableEvents);
		return false;
    }

    /***
	 * Executes validators on every validation call
	 *
	 *<code>
	 * use Phalcon\Mvc\Model\Validator\ExclusionIn as ExclusionIn;
	 *
	 * class Subscriptors extends \Phalcon\Mvc\Collection
	 * {
	 *     public function validation()
	 *     {
	 *         // Old, deprecated syntax, use new one below
	 *         $this->validate(
	 *             new ExclusionIn(
	 *                 [
	 *                     "field"  => "status",
	 *                     "domain" => ["A", "I"],
	 *                 ]
	 *             )
	 *         );
	 *
	 *         if ($this->validationHasFailed() == true) {
	 *             return false;
	 *         }
	 *     }
	 * }
	 *</code>
	 *
	 *<code>
	 * use Phalcon\Validation\Validator\ExclusionIn as ExclusionIn;
	 * use Phalcon\Validation;
	 *
	 * class Subscriptors extends \Phalcon\Mvc\Collection
	 * {
	 *     public function validation()
	 *     {
	 *         $validator = new Validation();
	 *         $validator->add("status",
	 *             new ExclusionIn(
	 *                 [
	 *                     "domain" => ["A", "I"]
	 *                 ]
	 *             )
	 *         );
	 *
	 *         return $this->validate($validator);
	 *     }
	 * }
	 *</code>
	 **/
    protected function validate($validator ) {

		if ( validator instanceof Model\ValidatorInterface ) {
			if ( validator->validate(this) === false ) {
				foreach ( $validator->getMessages() as $message ) {
					$this->_errorMessages[] = message;
				}
			}
		} elseif ( validator instanceof ValidationInterface ) {
			$messages = validator->validate(null, this);

			// Call the validation, if ( it returns not the boolean
			// we append the messages to the current object
			if ( gettype($messages) != "boolean" ) {

				messages->rewind();

				// foreach ( $iterator(messages) as $message ) {
				while messages->valid() {

					$message = messages->current();

					this->appendMessage(
						new Message(
							message->getMessage(),
							message->getField(),
							message->getType()
						)
					);

					messages->next();
				}

				// If there is a message, it returns false otherwise true
				return !count(messages);
			}

			return messages;
		} else {
			throw new Exception("You should pass Phalcon\\Mvc\\Model\\ValidatorInterface or Phalcon\\ValidationInterface object");
		}
    }

    /***
	 * Check whether validation process has generated any messages
	 *
	 *<code>
	 * use Phalcon\Mvc\Model\Validator\ExclusionIn as ExclusionIn;
	 *
	 * class Subscriptors extends \Phalcon\Mvc\Collection
	 * {
	 *     public function validation()
	 *     {
	 *         $this->validate(
	 *             new ExclusionIn(
	 *                 [
	 *                     "field"  => "status",
	 *                     "domain" => ["A", "I"],
	 *                 ]
	 *             )
	 *         );
	 *
	 *         if ($this->validationHasFailed() == true) {
	 *             return false;
	 *         }
	 *     }
	 * }
	 *</code>
	 **/
    public function validationHasFailed() {
		return (count(this->_errorMessages) > 0);
    }

    /***
	 * Fires an internal event
	 **/
    public function fireEvent($eventName ) {
		if ( method_exists(this, eventName) ) {
			this->{eventName}();
		}

		/**
		 * Send a notif (ication to the events manager
		 */
		return $this->_modelsManager->notif (yEvent(eventName, this);
    }

    /***
	 * Fires an internal event that cancels the operation
	 **/
    public function fireEventCancel($eventName ) {
		if ( method_exists(this, eventName) ) {
			if ( $this->) {eventName}() === false ) {
				return false;
			}
		}

		/**
		 * Send a notif (ication to the events manager
		 */
		if ( $this->_modelsManager->notif (yEvent(eventName, this) === false ) {
			return false;
		}

		return true;
    }

    /***
	 * Cancel the current operation
	 **/
    protected function _cancelOperation($disableEvents ) {

		if ( !disableEvents ) {
			if ( $this->_operationMade == self::OP_DELETE ) {
				$eventName = "notDeleted";
			} else {
				$eventName = "notSaved";
			}
			this->fireEvent(eventName);
		}
		return false;
    }

    /***
	 * Checks if the document exists in the collection
	 *
	 * @param \MongoCollection collection
	 * @return boolean
	 **/
    protected function _exists($collection ) {

		if ( !fetch id, $this->_id ) {
			return false;
		}

		if ( gettype($id) == "object" ) {
			$mongoId = id;
		} else {

			/**
			 * Check if ( the model use implicit ids
			 */
			if ( $this->_modelsManager->isUsingImplicitObjectIds(this) ) {
				$mongoId = new \MongoId(id);
				$this->_id = mongoId;
			} else {
				$mongoId = id;
			}
		}

		/**
		 * If we already know if ( the document exists we don't check it
		 */
		 if ( !this->_dirtyState ) {
			return true;
		 }

		/**
		 * Perfor (m the count using the function provided by the driver
		 */
		$exists = collection->count(["_id": mongoId]) > 0;

		if ( exists ) {
			$this->_dirtyState = self::DIRTY_STATE_PERSISTENT;
		} else {
			$this->_dirtyState = self::DIRTY_STATE_TRANSIENT;
		}

		return exists;
    }

    /***
	 * Returns all the validation messages
	 *
	 * <code>
	 * $robot = new Robots();
	 *
	 * $robot->type = "mechanical";
	 * $robot->name = "Astro Boy";
	 * $robot->year = 1952;
	 *
	 * if ($robot->save() === false) {
	 *     echo "Umh, We can't store robots right now ";
	 *
	 *     $messages = $robot->getMessages();
	 *
	 *     foreach ($messages as $message) {
	 *         echo $message;
	 *     }
	 * } else {
	 *     echo "Great, a new robot was saved successfully!";
	 * }
	 * </code>
	 **/
    public function getMessages() {
		return $this->_errorMessages;
    }

    /***
	 * Appends a customized message on the validation process
	 *
	 *<code>
	 * use \Phalcon\Mvc\Model\Message as Message;
	 *
	 * class Robots extends \Phalcon\Mvc\Model
	 * {
	 *     public function beforeSave()
	 *     {
	 *         if ($this->name === "Peter") {
	 *             $message = new Message(
	 *                 "Sorry, but a robot cannot be named Peter"
	 *             );
	 *
	 *             $this->appendMessage(message);
	 *         }
	 *     }
	 * }
	 *</code>
	 **/
    public function appendMessage($message ) {
		$this->_errorMessages[] = message;
    }

    /***
	 * Shared Code for CU Operations
	 * Prepares Collection
	 **/
    protected function prepareCU() {

		$dependencyInjector = $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injector container is required to obtain the services related to the ODM");
		}

		$source = $this->getSource();
		if ( empty source ) {
			throw new Exception("Method getSource() returns empty string");
		}

		$connection = $this->getConnection();

		/**
		 * Choose a collection according to the collection name
		 */
		$collection = connection->selectCollection(source);

		return collection;
    }

    /***
	 * Creates/Updates a collection based on the values in the attributes
	 **/
    public function save() {

		$collection = $this->prepareCU();

		/**
		 * Check the dirty state of the current operation to update the current operation
		 */
		$exists = $this->_exists(collection);

		if ( exists === false ) {
			$this->_operationMade = self::OP_CREATE;
		} else {
			$this->_operationMade = self::OP_UPDATE;
		}

		/**
		 * The messages added to the validator are reset here
		 */
		$this->_errorMessages = [];

		/**
		 * Execute the preSave hook
		 */
		if ( $this->_preSave(this->_dependencyInjector, self::_disableEvents, exists) === false ) {
			return false;
		}

		$data = $this->toArray();

		$success = false;

		/**
		 * We always use safe stores to get the success state
		 * Save the document
		 */
		$status = collection->save(data, ["w": true]);
		if ( gettype($status) == "array" ) {
			if ( fetch ok, status["ok"] ) {
				if ( ok ) {
					$success = true;
					if ( exists === false ) {
						if ( fetch id, data["_id"] ) {
							$this->_id = id;
						}
						$this->_dirtyState = self::DIRTY_STATE_PERSISTENT;
					}
				}
			}
		}

		/**
		 * Call the postSave hooks
		 */
		return $this->_postSave(self::_disableEvents, success, exists);
    }

    /***
	 * Creates a collection based on the values in the attributes
	 **/
    public function create() {

		$collection = $this->prepareCU();

		/**
		 * Check the dirty state of the current operation to update the current operation
		 */
		$exists = false;
		$this->_operationMade = self::OP_CREATE;

		/**
		 * The messages added to the validator are reset here
		 */
		$this->_errorMessages = [];

		/**
		 * Execute the preSave hook
		 */
		if ( $this->_preSave(this->_dependencyInjector, self::_disableEvents, exists) === false ) {
			return false;
		}

		$data = $this->toArray();

		$success = false;

		/**
		 * We always use safe stores to get the success state
		 * Save the document
		 */
		$status = collection->insert(data, ["w": true]);
		if ( gettype($status) == "array" ) {
			if ( fetch ok, status["ok"] ) {
				if ( ok ) {
					$success = true;
					if ( exists === false ) {
						if ( fetch id, data["_id"] ) {
							$this->_id = id;
						}
						$this->_dirtyState = self::DIRTY_STATE_PERSISTENT;
					}
				}
			}
		}

		/**
		 * Call the postSave hooks
		 */
		return $this->_postSave(self::_disableEvents, success, exists);
    }

    /***
	 * Creates a document based on the values in the attributes, if not found by criteria
	 * Preferred way to avoid duplication is to create index on attribute
	 *
	 * <code>
	 * $robot = new Robot();
	 *
	 * $robot->name = "MyRobot";
	 * $robot->type = "Droid";
	 *
	 * // Create only if robot with same name and type does not exist
	 * $robot->createIfNotExist(
	 *     [
	 *         "name",
	 *         "type",
	 *     ]
	 * );
	 * </code>
	 **/
    public function createIfNotExist($criteria ) {
			success, status, doc, collection;

		if ( empty criteria ) {
			throw new Exception("Criteria parameter must be array with one or more attributes of the model");
		}

		/**
		 * Choose a collection according to the collection name
		 */
		$collection = $this->prepareCU();

		/**
		 * Assume non-existence to fire befor (eCreate events - no update does occur anyway
		 */
		$exists = false;

		/**
		 * Reset current operation
		 */

		$this->_operationMade = self::OP_NONE;

		/**
		 * The messages added to the validator are reset here
		 */
		$this->_errorMessages = [];

		/**
		 * Execute the preSave hook
		 */
		if ( $this->_preSave(this->_dependencyInjector, self::_disableEvents, exists) === false ) {
			return false;
		}

		$keys = array_flip( criteria );
		$data = $this->toArray();

		if ( array_dif (f_key( keys, data ) ) {
			throw new Exception("Criteria parameter must be array with one or more attributes of the model");
		}

		$query = array_intersect_key( data, keys );

		$success = false;

		/**
		 * $setOnInsert in conjunction with upsert ensures creating a new document
		 * "new": false returns null if ( new document created, otherwise new or old document could be returned
		 */
		$status = collection->findAndModif (y(query,
			["$setOnInsert": data],
			null,
			["new": false, "upsert": true]);
		if ( status == null ) {
			$doc = collection->findOne(query);
			if ( gettype($doc) == "array" ) {
				$success = true;
				$this->_operationMade = self::OP_CREATE;
				$this->_id = doc["_id"];
			}
		} else {
			this->appendMessage( new Message("Document already exists") );
		}

		/**
		 * Call the postSave hooks
		 */
		return $this->_postSave(self::_disableEvents, success, exists);
    }

    /***
	 * Creates/Updates a collection based on the values in the attributes
	 **/
    public function update() {

		$collection = $this->prepareCU();

		/**
		 * Check the dirty state of the current operation to update the current operation
		 */
		$exists = $this->_exists(collection);

		if ( !exists ) {
			throw new Exception("The document cannot be updated because it doesn't exist");
		}

		$this->_operationMade = self::OP_UPDATE;

		/**
		 * The messages added to the validator are reset here
		 */
		$this->_errorMessages = [];

		/**
		 * Execute the preSave hook
		 */
		if ( $this->_preSave(this->_dependencyInjector, self::_disableEvents, exists) === false ) {
			return false;
		}

		$data = $this->toArray();

		$success = false;

		/**
		 * We always use safe stores to get the success state
		 * Save the document
		 */
		$status = collection->update(["_id": $this->_id], data, ["w": true]);
		if ( gettype($status) == "array" ) {
			if ( fetch ok, status["ok"] ) {
				if ( ok ) {
					$success = true;
				}
			}
		} else {
			$success = false;
		}

		/**
		 * Call the postSave hooks
		 */
		return $this->_postSave(self::_disableEvents, success, exists);
    }

    /***
	 * Find a document by its id (_id)
	 *
	 * <code>
	 * // Find user by using \MongoId object
	 * $user = Users::findById(
	 *     new \MongoId("545eb081631d16153a293a66")
	 * );
	 *
	 * // Find user by using id as sting
	 * $user = Users::findById("45cbc4a0e4123f6920000002");
	 *
	 * // Validate input
	 * if ($user = Users::findById($_POST["id"])) {
	 *     // ...
	 * }
	 * </code>
	 **/
    public static function findById($id ) {

		if ( gettype($id) != "object" ) {
			if ( !preg_match("/^[a-f\d]) {24}$/i", id) ) {
				return null;
			}

			$className = get_called_class();

			$collection = new {className}();

			/**
			 * Check if ( the model use implicit ids
			 */
			if ( collection->getCollectionManager()->isUsingImplicitObjectIds(collection) ) {
				$mongoId = new \MongoId(id);
			} else {
				$mongoId = id;
			}

		} else {
			$mongoId = id;
		}

		return static::findFirst([["_id": mongoId]]);
    }

    /***
	 * Allows to query the first record that match the specified conditions
	 *
	 * <code>
	 * // What's the first robot in the robots table?
	 * $robot = Robots::findFirst();
	 *
	 * echo "The robot name is ", $robot->name, "\n";
	 *
	 * // What's the first mechanical robot in robots table?
	 * $robot = Robots::findFirst(
	 *     [
	 *         [
	 *             "type" => "mechanical",
	 *         ]
	 *     ]
	 * );
	 *
	 * echo "The first mechanical robot name is ", $robot->name, "\n";
	 *
	 * // Get first virtual robot ordered by name
	 * $robot = Robots::findFirst(
	 *     [
	 *         [
	 *             "type" => "mechanical",
	 *         ],
	 *         "order" => [
	 *             "name" => 1,
	 *         ],
	 *     ]
	 * );
	 *
	 * echo "The first virtual robot name is ", $robot->name, "\n";
	 *
	 * // Get first robot by id (_id)
	 * $robot = Robots::findFirst(
	 *     [
	 *         [
	 *             "_id" => new \MongoId("45cbc4a0e4123f6920000002"),
	 *         ]
	 *     ]
	 * );
	 *
	 * echo "The robot id is ", $robot->_id, "\n";
	 * </code>
	 **/
    public static function findFirst($parameters  = null ) {

		$className = get_called_class();

		$collection = new {className}();

		$connection = collection->getConnection();

		return static::_getResultset(parameters, collection, connection, true);
    }

    /***
	 * Allows to query a set of records that match the specified conditions
	 *
	 * <code>
	 * // How many robots are there?
	 * $robots = Robots::find();
	 *
	 * echo "There are ", count($robots), "\n";
	 *
	 * // How many mechanical robots are there?
	 * $robots = Robots::find(
	 *     [
	 *         [
	 *             "type" => "mechanical",
	 *         ]
	 *     ]
	 * );
	 *
	 * echo "There are ", count(robots), "\n";
	 *
	 * // Get and print virtual robots ordered by name
	 * $robots = Robots::findFirst(
	 *     [
	 *         [
	 *             "type" => "virtual"
	 *         ],
	 *         "order" => [
	 *             "name" => 1,
	 *         ]
	 *     ]
	 * );
	 *
	 * foreach ($robots as $robot) {
	 *	   echo $robot->name, "\n";
	 * }
	 *
	 * // Get first 100 virtual robots ordered by name
	 * $robots = Robots::find(
	 *     [
	 *         [
	 *             "type" => "virtual",
	 *         ],
	 *         "order" => [
	 *             "name" => 1,
	 *         ],
	 *         "limit" => 100,
	 *     ]
	 * );
	 *
	 * foreach ($robots as $robot) {
	 *	   echo $robot->name, "\n";
	 * }
	 * </code>
	 **/
    public static function find($parameters  = null ) {

		$className = get_called_class();
		$collection = new {className}();
		return static::_getResultset(parameters, collection, collection->getConnection(), false);
    }

    /***
	 * Perform a count over a collection
	 *
	 *<code>
	 * echo "There are ", Robots::count(), " robots";
	 *</code>
	 **/
    public static function count($parameters  = null ) {

		$className = get_called_class();

		$collection = new {className}();

		$connection = collection->getConnection();

		return static::_getGroupResultset(parameters, collection, connection);
    }

    /***
	 * Perform an aggregation using the Mongo aggregation framework
	 **/
    public static function aggregate($parameters  = null ) {

		$className = get_called_class();

		$model = new {className}();

		$connection = model->getConnection();

		$source = model->getSource();
		if ( empty source ) {
			throw new Exception("Method getSource() returns empty string");
		}

		return connection->selectCollection(source)->aggregate(parameters);
    }

    /***
	 * Allows to perform a summatory group for a column in the collection
	 **/
    public static function summatory($field , $conditions  = null , $finalize  = null ) {
			reduce, group, retval, firstRetval;

		$className = get_called_class();

		$model = new {className}();

		$connection = model->getConnection();

		$source = model->getSource();
		if ( empty source ) {
			throw new Exception("Method getSource() returns empty string");
		}

		$collection = connection->selectCollection(source);

		/**
		 * Uses a javascript hash to group the results
		 */
		$initial = ["summatory": []];

		/**
		 * Uses a javascript hash to group the results, however this is slow with larger datasets
		 */
		$reduce = "function (curr, result) ) { if ( (typeof result.summatory[curr." . field . "] === \"undefined\") ) { result.summatory[curr." . field . "] = 1; } else ) { result.summatory[curr." . field . "]++; } }";

		$group = collection->group([], initial, reduce);

		if ( fetch retval, group["retval"] ) {
			if ( fetch firstRetval, retval[0] ) {
				if ( isset firstRetval["summatory"] ) {
					return firstRetval["summatory"];
				}
				return firstRetval;
			}
			return retval;
		}

		return [];
    }

    /***
	 * Deletes a model instance. Returning true on success or false otherwise.
	 *
	 * <code>
	 * $robot = Robots::findFirst();
	 *
	 * $robot->delete();
	 *
	 * $robots = Robots::find();
	 *
	 * foreach ($robots as $robot) {
	 *     $robot->delete();
	 * }
	 * </code>
	 **/
    public function delete() {
			collection, mongoId, success, ok;

		if ( !fetch id, $this->_id ) {
			throw new Exception("The document cannot be deleted because it doesn't exist");
		}

		$disableEvents = self::_disableEvents;

		if ( !disableEvents ) {
			if ( $this->fireEventCancel("befor (eDelete") === false ) ) {
				return false;
			}
		}

		if ( $this->_skipped === true ) {
			return true;
		}

		$connection = $this->getConnection();

		$source = $this->getSource();
		if ( empty source ) {
			throw new Exception("Method getSource() returns empty string");
		}

		/**
		 * Get the \MongoCollection
		 */
		$collection = connection->selectCollection(source);

		if ( gettype($id) == "object" ) {
			$mongoId = id;
		} else {
			/**
			 * Is the collection using implicit object Ids?
			 */
			if ( $this->_modelsManager->isUsingImplicitObjectIds(this) ) {
				$mongoId = new \MongoId(id);
			} else {
				$mongoId = id;
			}
		}

		$success = false;

		/**
		 * Remove the instance
		 */
		$status = collection->remove(["_id": mongoId], ["w": true]);
		if ( gettype($status) != "array" ) {
			return false;
		}

		/**
		 * Check the operation status
		 */
		if ( fetch ok, status["ok"] ) {
			if ( ok ) {
				$success = true;
				if ( !disableEvents ) {
					this->fireEvent("afterDelete");
				}
				$this->_dirtyState = self::DIRTY_STATE_DETACHED;
			}
		} else {
			$success = false;
		}

		return success;
    }

    /***
	 * Sets the dirty state of the object using one of the DIRTY_STATE_* constants
	 **/
    public function setDirtyState($dirtyState ) {
		$this->_dirtyState = dirtyState;
		return this;
    }

    /***
	 * Returns one of the DIRTY_STATE_* constants telling if the document exists in the collection or not
	 **/
    public function getDirtyState() {
		return $this->_dirtyState;
    }

    /***
	 * Sets up a behavior in a collection
	 **/
    protected function addBehavior($behavior ) {
		(<ManagerInterface> $this->_modelsManager)->addBehavior(this, behavior);
    }

    /***
	 * Skips the current operation forcing a success state
	 **/
    public function skipOperation($skip ) {
		$this->_skipped = skip;
    }

    /***
	 * Returns the instance as an array representation
	 *
	 *<code>
	 * print_r(
	 *     $robot->toArray()
	 * );
	 *</code>
	 **/
    public function toArray() {

		$reserved = $this->getReservedAttributes();

		/**
		 * Get an array with the values of the object
		 * We only assign values to the public properties
		 */
		$data = [];
		foreach ( key, $get_object_vars(this) as $value ) {
			if ( key == "_id" ) {
				if ( value ) {
					$data[key] = value;
				}
			} else {
				if ( !isset($reserved[key]) ) {
					$data[key] = value;
				}
			}
		}

		return data;
    }

    /***
	 * Serializes the object ignoring connections or protected properties
	 **/
    public function serialize() {
		return serialize(this->toArray());
    }

    /***
	 * Unserializes the object from a serialized string
	 **/
    public function unserialize($data ) {

		$attributes = unserialize(data);
		if ( gettype($attributes) == "array" ) {

			/**
			 * Obtain the default DI
			 */
			$dependencyInjector = Di::getDefault();
			if ( gettype($dependencyInjector) != "object" ) {
				throw new Exception("A dependency injector container is required to obtain the services related to the ODM");
			}

			/**
			 * Update the dependency injector
			 */
			$this->_dependencyInjector = dependencyInjector;

			/**
			 * Gets the default modelsManager service
			 */
			$manager = dependencyInjector->getShared("collectionManager");
			if ( gettype($manager) != "object" ) {
				throw new Exception("The injected service 'collectionManager' is not valid");
			}

			/**
			 * Update the models manager
			 */
			$this->_modelsManager = manager;

			/**
			 * Update the objects attributes
			 */
			foreach ( key, $attributes as $value ) {
				$this->{key} = value;
			}
		}
    }

}