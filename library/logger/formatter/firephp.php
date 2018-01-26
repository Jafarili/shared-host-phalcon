<?php


namespace Phalcon\Logger\Formatter;

use Phalcon\Logger;
use Phalcon\Logger\Formatter;


/***
 * Phalcon\Logger\Formatter\Firephp
 *
 * Formats messages so that they can be sent to FirePHP
 *
 * @deprecated Will be removed in 4.0.0
 **/

class Firephp extends Formatter {

    protected $_showBacktrace;

    protected $_enableLabels;

    /***
	 * Returns the string meaning of a logger constant
	 **/
    public function getTypeString($type ) {
		switch type {

			case Logger::EMERGENCY:
			case Logger::CRITICAL:
			case Logger::ERROR:
				return "ERROR";

			case Logger::ALERT:
			case Logger::WARNING:
				return "WARN";

			case Logger::INFO:
			case Logger::NOTICE:
			case Logger::CUSTOM:
				return "INFO";

			case Logger::DEBUG:
			case Logger::SPECIAL:
				return "LOG";
		}

		return "CUSTOM";
    }

    /***
	 * Returns the string meaning of a logger constant
	 **/
    public function setShowBacktrace($isShow  = null ) {
		$this->_showBacktrace = isShow;
		return this;
    }

    /***
	 * Returns the string meaning of a logger constant
	 **/
    public function getShowBacktrace() {
		return $this->_showBacktrace;
    }

    /***
	 * Returns the string meaning of a logger constant
	 **/
    public function enableLabels($isEnable  = null ) {
		$this->_enableLabels = isEnable;
		return this;
    }

    /***
	 * Returns the labels enabled
	 **/
    public function labelsEnabled() {
		return $this->_enableLabels;
    }

    /***
	 * Applies a format to a message before sending it to the log
	 *
	 * @param string $message
	 * @param int $type
	 * @param int $timestamp
	 * @param array $context
	 *
	 * @return string
	 **/
    public function format($message , $type , $timestamp , $context  = null ) {

		if ( gettype($context) === "array" ) {
			$message = $this->interpolate(message, context);
		}

		$meta = ["Type": $this->getTypeString(type)];

		if ( $this->_showBacktrace ) {
			$param = DEBUG_BACKTRACE_IGNORE_ARGS;

			$backtrace = debug_backtrace(param),
				lastTrace = end(backtrace);

			if ( isset(lastTrace["file"]) ) {
				$meta["File"] = lastTrace["file"];
			}

			if ( isset(lastTrace["line"]) ) {
				$meta["Line"] = lastTrace["line"];
			}

			foreach ( key, $backtrace as $backtraceItem ) {
				unset(backtraceItem["object"]);
				unset(backtraceItem["args"]);

				$backtrace[key] = backtraceItem;
			}
		}

		if ( $this->_enableLabels ) {
			$meta["Label"] = message;
		}

		if ( !this->_enableLabels && !this->_showBacktrace ) {
			$body = message;
		} elseif ( $this->_enableLabels && !this->_showBacktrace ) {
			$body = "";
		} else {
			$body = [];

			if ( $this->_showBacktrace ) {
				$body["backtrace"] = backtrace;
			}

			if ( !this->_enableLabels ) {
				$body["message"] = message;
			}
		}

		$encoded = json_encode([meta, body]),
			len = strlen(encoded);

		return len . "|" . encoded . "|";
    }

}