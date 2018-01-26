<?php


namespace Phalcon\Logger\Adapter;

use Phalcon\Logger\Exception;
use Phalcon\Logger\Adapter;
use Phalcon\Logger\FormatterInterface;
use Phalcon\Logger\Formatter\Line as LineFormatter;


/***
 * Phalcon\Logger\Adapter\Stream
 *
 * Sends logs to a valid PHP stream
 *
 * <code>
 * use Phalcon\Logger;
 * use Phalcon\Logger\Adapter\Stream;
 *
 * $logger = new Stream("php://stderr");
 *
 * $logger->log("This is a message");
 * $logger->log(Logger::ERROR, "This is an error");
 * $logger->error("This is another error");
 * </code>
 **/

class Stream extends Adapter {

    /***
	 * File handler resource
	 *
	 * @var resource
	 **/
    protected $_stream;

    /***
	 * Phalcon\Logger\Adapter\Stream constructor
	 *
	 * @param string name
	 * @param array options
	 **/
    public function __construct($name , $options  = null ) {

		if ( fetch mode, options["mode"] ) {
			if ( memstr(mode, "r") ) {
				throw new Exception("Stream must be opened in append or write mode");
			}
		} else {
			$mode = "ab";
		}

		/**
		 * We use 'fopen' to respect to open-basedir directive
		 */
		$stream = fopen(name, mode);
		if ( !stream ) {
			throw new Exception("Can't open stream '" . name . "'");
		}

		$this->_stream = stream;
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
	 * Writes the log to the stream itself
	 **/
    public function logInternal($message , $type , $time , $context ) {

		$stream = $this->_stream;
		if ( gettype($stream) != "resource" ) {
			throw new Exception("Cannot send message to the log because it is invalid");
		}

		fwrite(stream, $this->getFormatter()->for (mat(message, type, time, context));
    }

    /***
 	 * Closes the logger
 	 **/
    public function close() {
		return fclose(this->_stream);
    }

}