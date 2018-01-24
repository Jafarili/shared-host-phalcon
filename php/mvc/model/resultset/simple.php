<?php


namespace Phalcon\Mvc\Model\Resultset;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Cache\BackendInterface;


/***
 * Phalcon\Mvc\Model\Resultset\Simple
 *
 * Simple resultsets only contains a complete objects
 * This class builds every complete object as it is required
 **/

class Simple extends Resultset {

    protected $_model;

    protected $_columnMap;

    protected $_keepSnapshots;

    /***
	 * Phalcon\Mvc\Model\Resultset\Simple constructor
	 *
	 * @param array columnMap
	 * @param \Phalcon\Mvc\ModelInterface|Phalcon\Mvc\Model\Row model
	 * @param \Phalcon\Db\Result\Pdo|null result
	 * @param \Phalcon\Cache\BackendInterface cache
	 * @param boolean keepSnapshots
	 **/
    public function __construct($columnMap , $model , $result , $cache  = null , $keepSnapshots  = null ) {

    }

    /***
	 * Returns current row in the resultset
	 **/
    public final function current() {

    }

    /***
	 * Returns a complete resultset as an array, if the resultset has a big number of rows
	 * it could consume more memory than currently it does. Export the resultset to an array
	 * couldn't be faster with a large number of records
	 **/
    public function toArray($renameColumns  = true ) {

    }

    /***
	 * Serializing a resultset will dump all related rows into a big array
	 **/
    public function serialize() {

    }

    /***
	 * Unserializing a resultset will allow to only works on the rows present in the saved state
	 **/
    public function unserialize($data ) {

    }

}