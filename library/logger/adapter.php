<?php


namespace Phalcon\Logger;

use Phalcon\Logger;
use Phalcon\Logger\Item;
use Phalcon\Logger\Exception;
use Phalcon\Logger\AdapterInterface;
use Phalcon\Logger\FormatterInterface;


/***
 * Phalcon\Logger\Adapter
 *
 * Base class for Phalcon\Logger adapters
 **/

abstract class Adapter {

    /***
	 * Tells if there is an active transaction or not
	 *
	 * @var boolean
	 **/
    protected $_transaction;

    /***
	 * Array with messages queued in the transaction
	 *
	 * @var array
	 **/
    protected $_queue;

    /***
	 * Formatter
	 *
	 * @var object
	 **/
    protected $_formatter;

    /***
	 * Log level
	 *
	 * @var int
	 **/
    protected $_logLevel;

    /***
	 * Filters the logs sent to the handlers that are less or equal than a specific level
	 **/
    public function setLogLevel($level ) {
		$this->_logLevel = level;
		return this;
    }

    /***
	 * Returns the current log level
	 **/
    public function getLogLevel() {
		return $this->_logLevel;
    }

    /***
	 * Sets the message formatter
	 **/
    public function setFormatter($formatter ) {
		$this->_for (matter = for (matter;
		return this;
    }

    /***
 	 * Starts a transaction
 	 **/
    public function begin() {
		$this->_transaction = true;
		return this;
    }

    /***
 	 * Commits the internal transaction
 	 **/
    public function commit() {

		if ( !this->_transaction ) {
			throw new Exception("There is no active transaction");
		}

		$this->_transaction = false;

		/**
		 * Check if ( the queue has something to log
		 */
		foreach ( $this->_queue as $message ) {
			this->{"logInternal"}(
				message->getMessage(),
				message->getType(),
				message->getTime(),
				message->getContext()
			);
		}

		// clear logger queue at commit
		$this->_queue = [];

		return this;
    }

    /***
 	 * Rollbacks the internal transaction
 	 **/
    public function rollback() {

		$transaction = $this->_transaction;
		if ( !transaction ) {
			throw new Exception("There is no active transaction");
		}

		$this->_transaction = false,
			this->_queue = [];

		return this;
    }

    /***
	 * Returns the whether the logger is currently in an active transaction or not
	 **/
    public function isTransaction() {
		return $this->_transaction;
    }

    /***
 	 * Sends/Writes a critical message to the log
 	 **/
    public function critical($message , $context  = null ) {
		return $this->log(Logger::CRITICAL, message, context);
    }

    /***
 	 * Sends/Writes an emergency message to the log
 	 **/
    public function emergency($message , $context  = null ) {
		return $this->log(Logger::EMERGENCY, message, context);
    }

    /***
 	 * Sends/Writes a debug message to the log
 	 **/
    public function debug($message , $context  = null ) {
		return $this->log(Logger::DEBUG, message, context);
    }

    /***
 	 * Sends/Writes an error message to the log
 	 **/
    public function error($message , $context  = null ) {
		return $this->log(Logger::ERROR, message, context);
    }

    /***
 	 * Sends/Writes an info message to the log
 	 **/
    public function info($message , $context  = null ) {
		return $this->log(Logger::INFO, message, context);
    }

    /***
 	 * Sends/Writes a notice message to the log
 	 **/
    public function notice($message , $context  = null ) {
		return $this->log(Logger::NOTICE, message, context);
    }

    /***
 	 * Sends/Writes a warning message to the log
 	 **/
    public function warning($message , $context  = null ) {
		return $this->log(Logger::WARNING, message, context);
    }

    /***
 	 * Sends/Writes an alert message to the log
 	 **/
    public function alert($message , $context  = null ) {
		return $this->log(Logger::ALERT, message, context);
    }

    /***
	 * Logs messages to the internal logger. Appends logs to the logger
	 **/
    public function log($type , $message  = null , $context  = null ) {

		/**
		 * PSR3 compatibility
		 */
		if ( gettype($type) == "string" && typeof message == "integer" ) {
			$toggledMessage = type, toggledType = message;
		} else {
			if ( gettype($type) == "string" && typeof message == "null" ) {
				$toggledMessage = type, toggledType = message;
			} else {
				$toggledMessage = message, toggledType = type;
			}
		}

		if ( gettype($toggledType) == "null" ) {
			$toggledType = Logger::DEBUG;
		}

		/**
		 * Checks if ( the log is valid respecting the current log level
		 */
		if ( $this->_logLevel >= toggledType ) {
			$timestamp = time();
			if ( $this->_transaction ) {
				$this->_queue[] = new Item(toggledMessage, toggledType, timestamp, context);
			} else {
				this->{"logInternal"}(toggledMessage, toggledType, timestamp, context);
			}
		}

		return this;
    }

}