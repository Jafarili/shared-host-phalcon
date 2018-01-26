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
		if ( gettype($this->_for (matter) !== "object" ) ) {
			$this->_for (matter = new FirePhpFormatter();
		}

		return $this->_for (matter;
    }

    /***
	 * Writes the log to the stream itself
	 **/
    public function logInternal($message , $type , $time , $context ) {

		if ( !this->_initialized ) {
			header("X-Wf-Protocol-1: http://meta.wildfirehq.org/Protocol/JsonStream/0.2");
			header("X-Wf-1-Plugin-1: http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.3");
			header("X-Wf-Structure-1: http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1");

			$this->_initialized = true;
		}

		$for (mat = $this->getFormatter()->for (mat(message, type, time, context),
			chunk = str_split(for (mat, 4500),
			index = $this->_index;

		foreach ( key, $chunk as $chString ) {
			$content = "X-Wf-1-1-1-" . (string) index . ": " . chString;

			if ( isset(chunk[key + 1]) ) {
				$content .= "|\\";
			}

			header(content);

			$index++;
		}

		$this->_index = index;
    }

    /***
	 * Closes the logger
	 **/
    public function close() {
		return true;
    }

}