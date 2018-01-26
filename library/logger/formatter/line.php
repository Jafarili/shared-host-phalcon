<?php


namespace Phalcon\Logger\Formatter;

use Phalcon\Logger\Formatter;


/***
 * Phalcon\Logger\Formatter\Line
 *
 * Formats messages using an one-line string
 **/

class Line extends Formatter {

    /***
	 * Default date format
	 *
	 * @var string
	 **/
    protected $_dateFormat;

    /***
	 * Format applied to each message
	 *
	 * @var string
	 **/
    protected $_format;

    /***
	 * Phalcon\Logger\Formatter\Line construct
	 *
	 * @param string format
	 * @param string dateFormat
	 **/
    public function __construct($format  = null , $dateFormat  = null ) {
		if ( for (mat ) ) {
			$this->_for (mat = for (mat;
		}
		if ( dateFormat ) {
			$this->_dateFormat = dateFormat;
		}
    }

    /***
	 * Applies a format to a message before sent it to the internal log
	 *
	 * @param string message
	 * @param int type
	 * @param int timestamp
	 * @param array $context
	 * @return string
	 **/
    public function format($message , $type , $timestamp , $context  = null ) {

		$for (mat = $this->_for (mat;

		/**
		 * Check if ( the for (mat has the %date% placeholder
		 */
		if ( memstr(for (mat, "%date%") ) ) {
			$for (mat = str_replace("%date%", date(this->_dateFormat, timestamp), for (mat);
		}

		/**
		 * Check if ( the for (mat has the %type% placeholder
		 */
		if ( memstr(for (mat, "%type%") ) ) {
			$for (mat = str_replace("%type%", $this->getTypeString(type), for (mat);
		}

		$for (mat = str_replace("%message%", message, for (mat) . PHP_EOL;

		if ( gettype($context) === "array" ) {
			return $this->interpolate(for (mat, context);
		}

		return for (mat;
    }

}