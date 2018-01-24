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

    }

    /***
	 * Returns the current log level
	 **/
    public function getLogLevel() {

    }

    /***
	 * Sets the message formatter
	 **/
    public function setFormatter($formatter ) {

    }

    /***
 	 * Starts a transaction
 	 **/
    public function begin() {

    }

    /***
 	 * Commits the internal transaction
 	 **/
    public function commit() {

    }

    /***
 	 * Rollbacks the internal transaction
 	 **/
    public function rollback() {

    }

    /***
	 * Returns the whether the logger is currently in an active transaction or not
	 **/
    public function isTransaction() {

    }

    /***
 	 * Sends/Writes a critical message to the log
 	 **/
    public function critical($message , $context  = null ) {

    }

    /***
 	 * Sends/Writes an emergency message to the log
 	 **/
    public function emergency($message , $context  = null ) {

    }

    /***
 	 * Sends/Writes a debug message to the log
 	 **/
    public function debug($message , $context  = null ) {

    }

    /***
 	 * Sends/Writes an error message to the log
 	 **/
    public function error($message , $context  = null ) {

    }

    /***
 	 * Sends/Writes an info message to the log
 	 **/
    public function info($message , $context  = null ) {

    }

    /***
 	 * Sends/Writes a notice message to the log
 	 **/
    public function notice($message , $context  = null ) {

    }

    /***
 	 * Sends/Writes a warning message to the log
 	 **/
    public function warning($message , $context  = null ) {

    }

    /***
 	 * Sends/Writes an alert message to the log
 	 **/
    public function alert($message , $context  = null ) {

    }

    /***
	 * Logs messages to the internal logger. Appends logs to the logger
	 **/
    public function log($type , $message  = null , $context  = null ) {

    }

}