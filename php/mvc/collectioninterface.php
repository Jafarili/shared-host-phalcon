<?php


namespace Phalcon\Mvc;

use Phalcon\Mvc\Model\MessageInterface;


/***
 * Phalcon\Mvc\CollectionInterface
 *
 * Interface for Phalcon\Mvc\Collection
 **/

interface CollectionInterface {

    /***
	 * Sets a value for the _id property, creates a MongoId object if needed
	 *
	 * @param mixed id
	 **/
    public function setId($id ); 

    /***
	 * Returns the value of the _id property
	 *
	 * @return MongoId
	 **/
    public function getId(); 

    /***
	 * Returns an array with reserved properties that cannot be part of the insert/update
	 **/
    public function getReservedAttributes(); 

    /***
	 * Returns collection name mapped in the model
	 **/
    public function getSource(); 

    /***
	 * Sets a service in the services container that returns the Mongo database
	 **/
    public function setConnectionService($connectionService ); 

    /***
	 * Retrieves a database connection
	 *
	 * @return MongoDb
	 **/
    public function getConnection(); 

    /***
	 * Sets the dirty state of the object using one of the DIRTY_STATE_* constants
	 **/
    public function setDirtyState($dirtyState ); 

    /***
	 * Returns one of the DIRTY_STATE_* constants telling if the record exists in the database or not
	 *
	 * @return int
	 **/
    public function getDirtyState(); 

    /***
	 * Returns a cloned collection
	 **/
    public static function cloneResult($collection , $document ); 

    /***
	 * Fires an event, implicitly calls behaviors and listeners in the events manager are notified
	 **/
    public function fireEvent($eventName ); 

    /***
	 * Fires an event, implicitly listeners in the events manager are notified
	 * This method stops if one of the callbacks/listeners returns boolean false
	 **/
    public function fireEventCancel($eventName ); 

    /***
	 * Check whether validation process has generated any messages
	 **/
    public function validationHasFailed(); 

    /***
	 * Returns all the validation messages
	 **/
    public function getMessages(); 

    /***
	 * Appends a customized message on the validation process
	 **/
    public function appendMessage($message ); 

    /***
	 * Creates/Updates a collection based on the values in the attributes
	 **/
    public function save(); 

    /***
	 * Find a document by its id
	 *
	 * @param string id
	 * @return \Phalcon\Mvc\Collection
	 **/
    public static function findById($id ); 

    /***
	 * Allows to query the first record that match the specified conditions
	 *
	 * @param array parameters
	 * @return array
	 **/
    public static function findFirst($parameters  = null ); 

    /***
	 * Allows to query a set of records that match the specified conditions
	 *
	 * @param 	array parameters
	 * @return  array
	 **/
    public static function find($parameters  = null ); 

    /***
	 * Perform a count over a collection
	 *
	 * @param array parameters
	 * @return array
	 **/
    public static function count($parameters  = null ); 

    /***
	 * Deletes a model instance. Returning true on success or false otherwise
	 **/
    public function delete(); 

}