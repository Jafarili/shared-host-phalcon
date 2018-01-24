<?php


namespace Phalcon\Logger\Adapter;

use Phalcon\Logger\Adapter;
use Phalcon\Logger\Exception;
use Phalcon\Logger\FormatterInterface;
use Phalcon\Logger\Formatter\Firephp as FirePhpFormatter;


/***
 * Phalcon\Logger\Adapter\Firephp
 *
 * Sends logs to FirePHP
 *
 *<code>
 * use Phalcon\Logger\Adapter\Firephp;
 * use Phalcon\Logger;
 *
 * $logger = new Firephp();
 *
 * $logger->log(Logger::ERROR, "This is an error");
 * $logger->error("This is another error");
 *</code>
 *
 * @deprecated Will be removed in 4.0.0
 **/

class Firephp extends Adapter {

    private $_initialized;

    private $_index;

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