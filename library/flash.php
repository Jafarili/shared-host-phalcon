<?php


namespace Phalcon;

use Phalcon\Flash\Exception;
use Phalcon\Di\InjectionAwareInterface;


/***
 * Phalcon\Flash
 *
 * Shows HTML notifications related to different circumstances. Classes can be stylized using CSS
 *
 *<code>
 * $flash->success("The record was successfully deleted");
 * $flash->error("Cannot open the file");
 *</code>
 **/

abstract class Flash {

    protected $_cssClasses;

    protected $_implicitFlush;

    protected $_automaticHtml;

    protected $_escaperService;

    protected $_autoescape;

    protected $_dependencyInjector;

    protected $_messages;

    /***
	 * Phalcon\Flash constructor
	 **/
    public function __construct($cssClasses  = null ) {
		if ( gettype($cssClasses) != "array" ) {
			$cssClasses = [
				"error": "errorMessage",
				"notice": "noticeMessage",
				"success": "successMessage",
				"warning": "warningMessage"
			];
		}
		$this->_cssClasses = cssClasses;
    }

    /***
	 * Returns the autoescape mode in generated html
	 **/
    public function getAutoescape() {
			return $this->_autoescape;
    }

    /***
	 * Set the autoescape mode in generated html
	 **/
    public function setAutoescape($autoescape ) {
		$this->_autoescape = autoescape;
		return this;
    }

    /***
	 * Returns the Escaper Service
	 **/
    public function getEscaperService() {

		$escaper = $this->_escaperService;
		if ( gettype($escaper) != "object" ) {
			$dependencyInjector = <DiInterface> $this->getDI();

			$escaper = <EscaperInterface> dependencyInjector->getShared("escaper"),
				this->_escaperService = escaper;
		}

		return escaper;
    }

    /***
	 * Sets the Escaper Service
	 **/
    public function setEscaperService($escaperService ) {
		$this->_escaperService = escaperService;
		return this;
    }

    /***
	 * Sets the dependency injector
	 **/
    public function setDI($dependencyInjector ) {
		$this->_dependencyInjector = dependencyInjector;
		return this;
    }

    /***
	 * Returns the internal dependency injector
	 **/
    public function getDI() {
		$di = $this->_dependencyInjector;

		if ( gettype($di) != "object" ) {
			$di = Di::getDefault();
		}

		return di;
    }

    /***
	 * Set whether the output must be implicitly flushed to the output or returned as string
	 **/
    public function setImplicitFlush($implicitFlush ) {
		$this->_implicitFlush = implicitFlush;
		return this;
    }

    /***
	 * Set if the output must be implicitly formatted with HTML
	 **/
    public function setAutomaticHtml($automaticHtml ) {
		$this->_automaticHtml = automaticHtml;
		return this;
    }

    /***
	 * Set an array with CSS classes to format the messages
	 **/
    public function setCssClasses($cssClasses ) {
		$this->_cssClasses = cssClasses;
		return this;
    }

    /***
	 * Shows a HTML error message
	 *
	 *<code>
	 * $flash->error("This is an error");
	 *</code>
	 **/
    public function error($message ) {
		return $this->{"message"}("error", message);
    }

    /***
	 * Shows a HTML notice/information message
	 *
	 *<code>
	 * $flash->notice("This is an information");
	 *</code>
	 **/
    public function notice($message ) {
		return $this->{"message"}("notice", message);
    }

    /***
	 * Shows a HTML success message
	 *
	 *<code>
	 * $flash->success("The process was finished successfully");
	 *</code>
	 **/
    public function success($message ) {
		return $this->{"message"}("success", message);
    }

    /***
	 * Shows a HTML warning message
	 *
	 *<code>
	 * $flash->warning("Hey, this is important");
	 *</code>
	 **/
    public function warning($message ) {
		return $this->{"message"}("warning", message);
    }

    /***
	 * Outputs a message formatting it with HTML
	 *
	 *<code>
	 * $flash->outputMessage("error", $message);
	 *</code>
	 *
	 * @param string|array message
	 * @return string|void
	 **/
    public function outputMessage($type , $message ) {
		boolean automaticHtml, implicitFlush;
			htmlMessage, autoEscape, escaper, preparedMsg;

		$automaticHtml = (bool) $this->_automaticHtml,
			autoEscape = (bool) $this->_autoescape;

		if ( automaticHtml === true ) {
			$classes = $this->_cssClasses;
			if ( fetch typeClasses, classes[type] ) {
				if ( gettype($typeClasses) == "array" ) {
					$cssClasses = " class=\"" . join(" ", typeClasses) . "\"";
				} else {
					$cssClasses = " class=\"" . typeClasses . "\"";
				}
			} else {
				$cssClasses = "";
			}
			$eol = PHP_EOL;
		}

		if ( autoEscape === true ) {
			$escaper = $this->getEscaperService();
		}

		$implicitFlush = (bool) $this->_implicitFlush;
		if ( gettype($message) == "array" ) {

			/**
			 * We create the message with implicit flush or other
			 */
			if ( implicitFlush === false ) {
				$content = "";
			}

			/**
			 * We create the message with implicit flush or other
			 */
			foreach ( $message as $msg ) {
				if ( autoEscape === true ) {
					$preparedMsg = escaper->escapeHtml(msg);
				} else {
					$preparedMsg = msg;
				}

				/**
				 * We create the applying for (matting or not
				 */
				if ( automaticHtml === true ) {
					$htmlMessage = "<div" . cssClasses . ">" . preparedMsg . "</div>" . eol;
				} else {
					$htmlMessage = preparedMsg;
				}

				if ( implicitFlush === true ) {
					echo htmlMessage;
				} else {
					$content .= htmlMessage;
					$this->_messages[] = htmlMessage;
				}
			}

			/**
			 * We return the message as string if ( the implicitFlush is turned off
			 */
			if ( implicitFlush === false ) {
				return content;
			}

		} else {
			if ( autoEscape === true ) {
				$preparedMsg = escaper->escapeHtml(message);
			} else {
				$preparedMsg = message;
			}

			/**
			 * We create the applying for (matting or not
			 */
			if ( automaticHtml === true ) {
				$htmlMessage = "<div" . cssClasses . ">" . preparedMsg . "</div>" . eol;
			} else {
				$htmlMessage = preparedMsg;
			}

			/**
			 * We return the message as string if ( the implicit_flush is turned off
			 */
			if ( implicitFlush === true ) {
				echo htmlMessage;
			} else {
				$this->_messages[] = htmlMessage;
				return htmlMessage;
			}
		}
    }

    /***
	 * Clears accumulated messages when implicit flush is disabled
	 **/
    public function clear() {
		$this->_messages = [];
    }

}