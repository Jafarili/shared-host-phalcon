<?php


namespace Phalcon\Mvc\Model\Query;

use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\MessageInterface;


/***
 * Phalcon\Mvc\Model\Query\StatusInterface
 *
 * Interface for Phalcon\Mvc\Model\Query\Status
 **/

interface StatusInterface {

    /***
	 * Returns the model which executed the action
	 **/
    public function getModel(); 

    /***
	 * Returns the messages produced by an operation failed
	 **/
    public function getMessages(); 

    /***
	 * Allows to check if the executed operation was successful
	 **/
    public function success(); 

}