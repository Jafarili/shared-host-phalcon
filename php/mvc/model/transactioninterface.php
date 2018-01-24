<?php


namespace Phalcon\Mvc\Model;

use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\Transaction\ManagerInterface;


/***
 * Phalcon\Mvc\Model\TransactionInterface
 *
 * Interface for Phalcon\Mvc\Model\Transaction
 **/

interface TransactionInterface {

    /***
	 * Sets transaction manager related to the transaction
	 **/
    public function setTransactionManager($manager ); 

    /***
	 * Starts the transaction
	 **/
    public function begin(); 

    /***
	 * Commits the transaction
	 **/
    public function commit(); 

    /***
	 * Rollbacks the transaction
	 **/
    public function rollback($rollbackMessage  = null , $rollbackRecord  = null ); 

    /***
	 * Returns connection related to transaction
	 **/
    public function getConnection(); 

    /***
	 * Sets if is a reused transaction or new once
	 **/
    public function setIsNewTransaction($isNew ); 

    /***
	 * Sets flag to rollback on abort the HTTP connection
	 **/
    public function setRollbackOnAbort($rollbackOnAbort ); 

    /***
	 * Checks whether transaction is managed by a transaction manager
	 **/
    public function isManaged(); 

    /***
	 * Returns validations messages from last save try
	 **/
    public function getMessages(); 

    /***
	 * Checks whether internal connection is under an active transaction
	 **/
    public function isValid(); 

    /***
	 * Sets object which generates rollback action
	 **/
    public function setRollbackedRecord($record ); 

}