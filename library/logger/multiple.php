<?php


namespace Phalcon\Logger;

use Phalcon\Logger;
use Phalcon\Logger\AdapterInterface;
use Phalcon\Logger\FormatterInterface;
use Phalcon\Logger\Exception;


/***
 * Phalcon\Logger\Multiple
 *
 * Handles multiples logger handlers
 **/

class Multiple {

    protected $_loggers;

    protected $_formatter;

    protected $_logLevel;

    /***
	 * Pushes a logger to the logger tail
	 **/
    public function push($logger ) {
		$this->_loggers[] = logger;
    }

    /***
	 * Sets a global formatter
	 **/
    public function setFormatter($formatter ) {

		$loggers = $this->_loggers;
		if ( gettype($loggers) == "array" ) {
			foreach ( $loggers as $logger ) {
				logger->setFormatter(for (matter);
			}
		}
		$this->_for (matter = for (matter;
    }

    /***
	 * Sets a global level
	 **/
    public function setLogLevel($level ) {

		$loggers = $this->_loggers;
		if ( gettype($loggers) == "array" ) {
			foreach ( $loggers as $logger ) {
				logger->setLogLevel(level);
			}
		}
		$this->_logLevel = level;
    }

    /***
	 * Sends a message to each registered logger
	 **/
    public function log($type , $message  = null , $context  = null ) {

		$loggers = $this->_loggers;
		if ( gettype($loggers) == "array" ) {
			foreach ( $loggers as $logger ) {
				logger->log(type, message, context);
			}
		}
    }

    /***
 	 * Sends/Writes an critical message to the log
 	 **/
    public function critical($message , $context  = null ) {
		this->log(Logger::CRITICAL, message, context);
    }

    /***
 	 * Sends/Writes an emergency message to the log
 	 **/
    public function emergency($message , $context  = null ) {
		this->log(Logger::EMERGENCY, message, context);
    }

    /***
 	 * Sends/Writes a debug message to the log
 	 **/
    public function debug($message , $context  = null ) {
		this->log(Logger::DEBUG, message, context);
    }

    /***
 	 * Sends/Writes an error message to the log
 	 **/
    public function error($message , $context  = null ) {
		this->log(Logger::ERROR, message, context);
    }

    /***
 	 * Sends/Writes an info message to the log
 	 **/
    public function info($message , $context  = null ) {
		this->log(Logger::INFO, message, context);
    }

    /***
 	 * Sends/Writes a notice message to the log
 	 **/
    public function notice($message , $context  = null ) {
		this->log(Logger::NOTICE, message, context);
    }

    /***
 	 * Sends/Writes a warning message to the log
 	 **/
    public function warning($message , $context  = null ) {
		this->log(Logger::WARNING, message, context);
    }

    /***
 	 * Sends/Writes an alert message to the log
 	 **/
    public function alert($message , $context  = null ) {
		this->log(Logger::ALERT, message, context);
    }

}