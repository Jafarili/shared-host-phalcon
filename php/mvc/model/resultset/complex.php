<?php


namespace Phalcon\Mvc\Model\Resultset;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Row;
use Phalcon\Db\ResultInterface;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Cache\BackendInterface;
use Phalcon\Mvc\Model\ResultsetInterface;


/***
 * Phalcon\Mvc\Model\Resultset\Complex
 *
 * Complex resultsets may include complete objects and scalar values.
 * This class builds every complex row as it is required
 **/

class Complex extends Resultset {

    protected $_columnTypes;

    /***
	* Unserialised result-set hydrated all rows already. unserialise() sets _disableHydration to true
	**/
    protected $_disableHydration;

    /***
	 * Phalcon\Mvc\Model\Resultset\Complex constructor
	 *
	 * @param array columnTypes
	 * @param \Phalcon\Db\ResultInterface result
	 * @param \Phalcon\Cache\BackendInterface cache
	 **/
    public function __construct($columnTypes , $result  = null , $cache  = null ) {

    }

    /***
	 * Returns current row in the resultset
	 **/
    public final function current() {

    }

    /***
	 * Returns a complete resultset as an array, if the resultset has a big number of rows
	 * it could consume more memory than currently it does.
	 **/
    public function toArray() {

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