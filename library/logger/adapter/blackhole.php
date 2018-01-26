<?php


namespace Phalcon\Logger\Adapter;

use Phalcon\Logger\Adapter;
use Phalcon\Logger\Formatter\Line;
use Phalcon\Logger\FormatterInterface;


/***
 * Phalcon\Logger\Adapter\Blackhole
 *
 * Any record it can handle will be thrown away.
 **/

class Blackhole extends Adapter {

    /***
	 * Returns the internal formatter
	 **/
    public function getFormatter() {
		if ( gettype($this->_for (matter) !== "object" ) ) {
			$this->_for (matter = new Line();
		}

		return $this->_for (matter;
    }

    /***
	 * Writes the log to the blackhole
	 **/
    public function logInternal($message , $type , $time , $context ) {

    }

    /***
	 * Closes the logger
	 **/
    public function close() {

    }

}