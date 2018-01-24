<?php


namespace Phalcon\Logger\Adapter;

use Phalcon\Logger\Exception;
use Phalcon\Logger\Adapter;
use Phalcon\Logger\Formatter\Syslog as SyslogFormatter;


/***
 * Phalcon\Logger\Adapter\Syslog
 *
 * Sends logs to the system logger
 *
 * <code>
 * use Phalcon\Logger;
 * use Phalcon\Logger\Adapter\Syslog;
 *
 * // LOG_USER is the only valid log type under Windows operating systems
 * $logger = new Syslog(
 *     "ident",
 *     [
 *         "option"   => LOG_CONS | LOG_NDELAY | LOG_PID,
 *         "facility" => LOG_USER,
 *     ]
 * );
 *
 * $logger->log("This is a message");
 * $logger->log(Logger::ERROR, "This is an error");
 * $logger->error("This is another error");
 *</code>
 **/

class Syslog extends Adapter {

    protected $_opened;

    /***
	 * Phalcon\Logger\Adapter\Syslog constructor
	 *
	 * @param string name
	 * @param array options
	 **/
    public function __construct($name , $options  = null ) {

    }

    /***
	 * Returns the internal formatter
	 **/
    public function getFormatter() {

    }

    /***
	 * Writes the log to the stream itself
	 **/
    public function logInternal($message , $type , $time , $context ) {

    }

    /***
 	 * Closes the logger
 	 **/
    public function close() {

    }

}