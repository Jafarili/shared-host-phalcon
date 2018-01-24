<?php


namespace Phalcon\Logger;



/***
 * Phalcon\Logger\AdapterInterface
 *
 * Interface for Phalcon\Logger adapters
 **/

interface AdapterInterface {

    /***
	 * Sets the message formatter
	 **/
    public function setFormatter($formatter ); 

    /***
	 * Returns the internal formatter
	 **/
    public function getFormatter(); 

    /***
	 * Filters the logs sent to the handlers to be greater or equals than a specific level
	 **/
    public function setLogLevel($level ); 

    /***
	 * Returns the current log level
	 **/
    public function getLogLevel(); 

    /***
	 * Sends/Writes messages to the file log
	 **/
    public function log($type , $message  = null , $context  = null ); 

    /***
 	 * Starts a transaction
 	 **/
    public function begin(); 

    /***
 	 * Commits the internal transaction
 	 **/
    public function commit(); 

    /***
 	 * Rollbacks the internal transaction
 	 **/
    public function rollback(); 

    /***
 	 * Closes the logger
 	 **/
    public function close(); 

    /***
 	 * Sends/Writes a debug message to the log
 	 **/
    public function debug($message , $context  = null ); 

    /***
 	 * Sends/Writes an error message to the log
 	 **/
    public function error($message , $context  = null ); 

    /***
 	 * Sends/Writes an info message to the log
 	 **/
    public function info($message , $context  = null ); 

    /***
 	 * Sends/Writes a notice message to the log
 	 **/
    public function notice($message , $context  = null ); 

    /***
 	 * Sends/Writes a warning message to the log
 	 **/
    public function warning($message , $context  = null ); 

    /***
 	 * Sends/Writes an alert message to the log
 	 **/
    public function alert($message , $context  = null ); 

    /***
 	 * Sends/Writes an emergency message to the log
 	 **/
    public function emergency($message , $context  = null ); 

}