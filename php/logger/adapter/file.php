<?php


namespace Phalcon\Logger\Adapter;

use Phalcon\Logger\Adapter;
use Phalcon\Logger\Exception;
use Phalcon\Logger\FormatterInterface;
use Phalcon\Logger\Formatter\Line as LineFormatter;


/***
 * Phalcon\Logger\Adapter\File
 *
 * Adapter to store logs in plain text files
 *
 *<code>
 * $logger = new \Phalcon\Logger\Adapter\File("app/logs/test.log");
 *
 * $logger->log("This is a message");
 * $logger->log(\Phalcon\Logger::ERROR, "This is an error");
 * $logger->error("This is another error");
 *
 * $logger->close();
 *</code>
 **/

class File extends Adapter {

    /***
	 * File handler resource
	 *
	 * @var resource
	 **/
    protected $_fileHandler;

    /***
	 * File Path
	 **/
    protected $_path;

    /***
	 * Path options
	 **/
    protected $_options;

    /***
	 * Phalcon\Logger\Adapter\File constructor
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
	 * Writes the log to the file itself
	 **/
    public function logInternal($message , $type , $time , $context ) {

    }

    /***
 	 * Closes the logger
 	 **/
    public function close() {

    }

    /***
	 * Opens the internal file handler after unserialization
	 **/
    public function __wakeup() {

    }

}