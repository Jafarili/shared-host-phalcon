<?php


namespace Phalcon\Mvc\Model\Transaction;



/***
 * Phalcon\Mvc\Model\Transaction\ManagerInterface
 *
 * Interface for Phalcon\Mvc\Model\Transaction\Manager
 **/

interface ManagerInterface {

    /***
	 * Checks whether manager has an active transaction
	 **/
    public function has(); 

    /***
	 * Returns a new \Phalcon\Mvc\Model\Transaction or an already created once
	 **/
    public function get($autoBegin  = true ); 

    /***
	 * Rollbacks active transactions within the manager
	 **/
    public function rollbackPendent(); 

    /***
	 * Commits active transactions within the manager
	 **/
    public function commit(); 

    /***
	 * Rollbacks active transactions within the manager
	 * Collect will remove transaction from the manager
	 *
	 * @param boolean collect
	 **/
    public function rollback($collect  = false ); 

    /***
	 * Notifies the manager about a rollbacked transaction
	 **/
    public function notifyRollback($transaction ); 

    /***
	 * Notifies the manager about a committed transaction
	 **/
    public function notifyCommit($transaction ); 

    /***
	 * Remove all the transactions from the manager
	 **/
    public function collectTransactions(); 

}