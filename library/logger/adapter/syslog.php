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

		if ( name ) {

			if ( !fetch option, options["option"] ) {
				$option = LOG_ODELAY;
			}

			if ( !fetch facility, options["facility"] ) {
				$facility = LOG_USER;
			}

			openlog(name, option, facility);
			$this->_opened = true;
		}

    }

    /***
	 * Returns the internal formatter
	 **/
    public function getFormatter() {
		if ( gettype($this->_for (matter) !== "object" ) ) {
			$this->_for (matter = new SyslogFormatter();
		}

		return $this->_for (matter;
    }

    /***
	 * Writes the log to the stream itself
	 **/
    public function logInternal($message , $type , $time , $context ) {

		$appliedFormat = $this->getFormatter()->for (mat(message, type, time, context);
		if ( gettype($appliedFormat) !== "array" ) {
			throw new Exception("The for (matted message is not valid");
		}

		syslog(appliedFormat[0], appliedFormat[1]);
    }

    /***
 	 * Closes the logger
 	 **/
    public function close() {
		if ( !this->_opened ) {
			return true;
		}

		return closelog();
    }

}