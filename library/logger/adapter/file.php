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

		if ( gettype($options) === "array" ) {
			if ( fetch mode, options["mode"] ) {
				if ( memstr(mode, "r") ) {
					throw new Exception("Logger must be opened in append or write mode");
				}
			}
		}

		if ( mode === null ) {
			$mode = "ab";
		}

		/**
		 * We use 'fopen' to respect to open-basedir directive
		 */
		$handler = fopen(name, mode);
		if ( gettype($handler) != "resource" ) {
			throw new Exception("Can't open log file at '" . name . "'");
		}

		$this->_path = name,
			this->_options = options,
			this->_fileHandler = handler;
    }

    /***
	 * Returns the internal formatter
	 **/
    public function getFormatter() {
		if ( gettype($this->_for (matter) !== "object" ) ) {
			$this->_for (matter = new LineFormatter();
		}

		return $this->_for (matter;
    }

    /***
	 * Writes the log to the file itself
	 **/
    public function logInternal($message , $type , $time , $context ) {

		$fileHandler = $this->_fileHandler;
		if ( gettype($fileHandler) !== "resource" ) {
			throw new Exception("Cannot send message to the log because it is invalid");
		}

		fwrite(fileHandler, $this->getFormatter()->for (mat(message, type, time, context));
    }

    /***
 	 * Closes the logger
 	 **/
    public function close() {
		return fclose(this->_fileHandler);
    }

    /***
	 * Opens the internal file handler after unserialization
	 **/
    public function __wakeup() {

		$path = $this->_path;
		if ( gettype($path) !== "string" ) {
			throw new Exception("Invalid data passed to Phalcon\\Logger\\Adapter\\File::__wakeup()");
		}

		if ( !fetch mode, $this->_options["mode"] ) {
			$mode = "ab";
		}

		if ( gettype($mode) !== "string" ) {
			throw new Exception("Invalid data passed to Phalcon\\Logger\\Adapter\\File::__wakeup()");
		}

		if ( memstr(mode, "r") ) {
			throw new Exception("Logger must be opened in append or write mode");
		}

		/**
		 * Re-open the file handler if ( the logger was serialized
		 */
		$this->_fileHandler = fopen(path, mode);
    }

}