<?php


namespace Phalcon\Mvc\Model;

use Phalcon\DiInterface;
use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\Model\Transaction\Failed as TxFailed;
use Phalcon\Mvc\Model\Transaction\ManagerInterface;
use Phalcon\Mvc\Model\TransactionInterface;


/***
 * Phalcon\Mvc\Model\Transaction
 *
 * Transactions are protective blocks where SQL statements are only permanent if they can
 * all succeed as one atomic action. Phalcon\Transaction is intended to be used with Phalcon_Model_Base.
 * Phalcon Transactions should be created using Phalcon\Transaction\Manager.
 *
 * <code>
 * use Phalcon\Mvc\Model\Transaction\Failed;
 * use Phalcon\Mvc\Model\Transaction\Manager;
 *
 * try {
 *     $manager = new Manager();
 *
 *     $transaction = $manager->get();
 *
 *     $robot = new Robots();
 *
 *     $robot->setTransaction($transaction);
 *
 *     $robot->name       = "WALL·E";
 *     $robot->created_at = date("Y-m-d");
 *
 *     if ($robot->save() === false) {
 *         $transaction->rollback("Can't save robot");
 *     }
 *
 *     $robotPart = new RobotParts();
 *
 *     $robotPart->setTransaction($transaction);
 *
 *     $robotPart->type = "head";
 *
 *     if ($robotPart->save() === false) {
 *         $transaction->rollback("Can't save robot part");
 *     }
 *
 *     $transaction->commit();
 * } catch(Failed $e) {
 *     echo "Failed, reason: ", $e->getMessage();
 * }
 * </code>
 **/

class Transaction {

    protected $_connection;

    protected $_activeTransaction;

    protected $_isNewTransaction;

    protected $_rollbackOnAbort;

    protected $_manager;

    protected $_messages;

    protected $_rollbackRecord;

    /***
	 * Phalcon\Mvc\Model\Transaction constructor
	 **/
    public function __construct($dependencyInjector , $autoBegin  = false , $service  = null ) {

		$this->_messages = [];

		if ( service ) {
			$connection = dependencyInjector->get(service);
		} else {
			$connection = dependencyInjector->get("db");
		}

		$this->_connection = connection;
		if ( autoBegin ) {
			connection->begin();
		}
    }

    /***
	 * Sets transaction manager related to the transaction
	 **/
    public function setTransactionManager($manager ) {
		$this->_manager = manager;
    }

    /***
	 * Starts the transaction
	 **/
    public function begin() {
		return $this->_connection->begin();
    }

    /***
	 * Commits the transaction
	 **/
    public function commit() {

		$manager = $this->_manager;
		if ( gettype($manager) == "object" ) {
			manager->notif (yCommit(this);
		}

		return $this->_connection->commit();
    }

    /***
	 * Rollbacks the transaction
	 **/
    public function rollback($rollbackMessage  = null , $rollbackRecord  = null ) {

		$manager = $this->_manager;
		if ( gettype($manager) == "object" ) {
			manager->notif (yRollback(this);
		}

		$connection = $this->_connection;
		if ( connection->rollback() ) {
			if ( !rollbackMessage ) {
				$rollbackMessage = "Transaction aborted";
			}
			if ( gettype($rollbackRecord) == "object" ) {
				$this->_rollbackRecord = rollbackRecord;
			}
			throw new TxFailed(rollbackMessage, $this->_rollbackRecord);
		}

		return true;
    }

    /***
	 * Returns the connection related to transaction
	 **/
    public function getConnection() {
		if ( $this->_rollbackOnAbort ) {
			if ( connection_aborted() ) {
				this->rollback("The request was aborted");
			}
		}
		return $this->_connection;
    }

    /***
	 * Sets if is a reused transaction or new once
	 **/
    public function setIsNewTransaction($isNew ) {
		$this->_isNewTransaction = isNew;
    }

    /***
	 * Sets flag to rollback on abort the HTTP connection
	 **/
    public function setRollbackOnAbort($rollbackOnAbort ) {
		$this->_rollbackOnAbort = rollbackOnAbort;
    }

    /***
	 * Checks whether transaction is managed by a transaction manager
	 **/
    public function isManaged() {
		return gettype($this->_manager) == "object";
    }

    /***
	 * Returns validations messages from last save try
	 **/
    public function getMessages() {
		return $this->_messages;
    }

    /***
	 * Checks whether internal connection is under an active transaction
	 **/
    public function isValid() {
		return $this->_connection->isUnderTransaction();
    }

    /***
	 * Sets object which generates rollback action
	 **/
    public function setRollbackedRecord($record ) {
		$this->_rollbackRecord = record;
    }

}