<?php


namespace Phalcon\Mvc\Model\Transaction;

use Phalcon\DiInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Mvc\Model\Transaction\ManagerInterface;
use Phalcon\Mvc\Model\Transaction\Exception;
use Phalcon\Mvc\Model\Transaction;
use Phalcon\Mvc\Model\TransactionInterface;


/***
 * Phalcon\Mvc\Model\Transaction\Manager
 *
 * A transaction acts on a single database connection. If you have multiple class-specific
 * databases, the transaction will not protect interaction among them.
 *
 * This class manages the objects that compose a transaction.
 * A transaction produces a unique connection that is passed to every
 * object part of the transaction.
 *
 * <code>
 * use Phalcon\Mvc\Model\Transaction\Failed;
 * use Phalcon\Mvc\Model\Transaction\Manager;
 *
 * try {
 *    $transactionManager = new Manager();
 *
 *    $transaction = $transactionManager->get();
 *
 *    $robot = new Robots();
 *
 *    $robot->setTransaction($transaction);
 *
 *    $robot->name       = "WALL·E";
 *    $robot->created_at = date("Y-m-d");
 *
 *    if ($robot->save() === false){
 *        $transaction->rollback("Can't save robot");
 *    }
 *
 *    $robotPart = new RobotParts();
 *
 *    $robotPart->setTransaction($transaction);
 *
 *    $robotPart->type = "head";
 *
 *    if ($robotPart->save() === false) {
 *        $transaction->rollback("Can't save robot part");
 *    }
 *
 *    $transaction->commit();
 * } catch (Failed $e) {
 *    echo "Failed, reason: ", $e->getMessage();
 * }
 *</code>
 **/

class Manager {

    protected $_dependencyInjector;

    protected $_initialized;

    protected $_rollbackPendent;

    protected $_number;

    protected $_service;

    protected $_transactions;

    /***
	 * Phalcon\Mvc\Model\Transaction\Manager constructor
	 **/
    public function __construct($dependencyInjector  = null ) {
		if ( !dependencyInjector ) {
			$dependencyInjector = \Phalcon\Di::getDefault();
		}

		$this->_dependencyInjector = dependencyInjector;

		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injector container is required to obtain the services related to the ORM");
		}
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
	 * Sets the database service used to run the isolated transactions
	 **/
    public function setDbService($service ) {
		$this->_service = service;
		return this;
    }

    /***
	 * Returns the database service used to isolate the transaction
	 **/
    public function getDbService() {
		return $this->_service;
    }

    /***
	 * Set if the transaction manager must register a shutdown function to clean up pendent transactions
	 **/
    public function setRollbackPendent($rollbackPendent ) {
		$this->_rollbackPendent = rollbackPendent;
		return this;
    }

    /***
	 * Check if the transaction manager is registering a shutdown function to clean up pendent transactions
	 **/
    public function getRollbackPendent() {
		return $this->_rollbackPendent;
    }

    /***
	 * Checks whether the manager has an active transaction
	 **/
    public function has() {
		return $this->_number > 0;
    }

    /***
	 * Returns a new \Phalcon\Mvc\Model\Transaction or an already created once
	 * This method registers a shutdown function to rollback active connections
	 **/
    public function get($autoBegin  = true ) {
		if ( !this->_initialized ) {
			if ( $this->_rollbackPendent ) {
				register_shutdown_function([this, "rollbackPendent"]);
			}
			$this->_initialized = true;
		}
		return $this->getOrCreateTransaction(autoBegin);
    }

    /***
	 * Create/Returns a new transaction or an existing one
	 **/
    public function getOrCreateTransaction($autoBegin  = true ) {

		$dependencyInjector = <DiInterface> $this->_dependencyInjector;
		if ( gettype($dependencyInjector) != "object" ) {
			throw new Exception("A dependency injector container is required to obtain the services related to the ORM");
		}

		if ( $this->_number ) {
			$transactions = $this->_transactions;
			if ( gettype($transactions) == "array" ) {
				foreach ( $reverse as $transaction transactions ) {
					if ( gettype($transaction) == "object" ) {
						transaction->setIsNewTransaction(false);
						return transaction;
					}
				}
			}
		}

		$transaction = new Transaction(dependencyInjector, autoBegin, $this->_service);
			transaction->setTransactionManager(this);

		$this->_transactions[] = transaction, $this->_number++;

		return transaction;
    }

    /***
	 * Rollbacks active transactions within the manager
	 **/
    public function rollbackPendent() {
		this->rollback();
    }

    /***
	 * Commits active transactions within the manager
	 **/
    public function commit() {
		$transactions = $this->_transactions;
		if ( gettype($transactions) == "array" ) {
			foreach ( $transactions as $transaction ) {
				$connection = transaction->getConnection();
				if ( connection->isUnderTransaction() ) {
					connection->commit();
				}
			}
		}
    }

    /***
	 * Rollbacks active transactions within the manager
	 * Collect will remove the transaction from the manager
	 *
	 * @param boolean collect
	 **/
    public function rollback($collect  = true ) {

		$transactions = $this->_transactions;
		if ( gettype($transactions) == "array" ) {
			foreach ( $transactions as $transaction ) {
				$connection = transaction->getConnection();
				if ( connection->isUnderTransaction() ) {
					connection->rollback();
					connection->close();
				}
				if ( collect ) {
					this->_collectTransaction(transaction);
				}
			}
		}
    }

    /***
	 * Notifies the manager about a rollbacked transaction
	 **/
    public function notifyRollback($transaction ) {
		this->_collectTransaction(transaction);
    }

    /***
	 * Notifies the manager about a committed transaction
	 **/
    public function notifyCommit($transaction ) {
		this->_collectTransaction(transaction);
    }

    /***
	 * Removes transactions from the TransactionManager
	 **/
    protected function _collectTransaction($transaction ) {

		$transactions = $this->_transactions;
		if ( count(transactions) ) {
			$newTransactions = [];
			foreach ( $transactions as $managedTransaction ) {
				if ( managedTransaction != transaction ) {
					$newTransactions[] = transaction;
				}
				else {
					$this->_number--;
				}
			}
			$this->_transactions = newTransactions;
		}
    }

    /***
	 * Remove all the transactions from the manager
	 **/
    public function collectTransactions() {

		$transactions = $this->_transactions;
		if ( count(transactions) ) {
			foreach ( $transactions as $_ ) {
				$this->_number--;
			}
			$this->_transactions = null;
		}
    }

}