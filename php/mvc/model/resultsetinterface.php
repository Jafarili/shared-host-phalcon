<?php


namespace Phalcon\Mvc\Model;



/***
 * Phalcon\Mvc\Model\ResultsetInterface
 *
 * Interface for Phalcon\Mvc\Model\Resultset
 *
 **/

interface ResultsetInterface {

    /***
	 * Returns the internal type of data retrieval that the resultset is using
	 **/
    public function getType(); 

    /***
	 * Get first row in the resultset
	 *
	 * @return \Phalcon\Mvc\ModelInterface
	 **/
    public function getFirst(); 

    /***
	 * Get last row in the resultset
	 *
	 * @return \Phalcon\Mvc\ModelInterface
	 **/
    public function getLast(); 

    /***
	 * Set if the resultset is fresh or an old one cached
	 **/
    public function setIsFresh($isFresh ); 

    /***
	 * Tell if the resultset if fresh or an old one cached
	 **/
    public function isFresh(); 

    /***
	 * Returns the associated cache for the resultset
	 *
	 * @return \Phalcon\Cache\BackendInterface
	 **/
    public function getCache(); 

    /***
	 * Returns a complete resultset as an array, if the resultset has a big number of rows
	 * it could consume more memory than currently it does.
	 **/
    public function toArray(); 

}