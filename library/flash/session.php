<?php


namespace Phalcon\Flash;

use Phalcon\Flash as FlashBase;
use Phalcon\DiInterface;
use Phalcon\Flash\Exception;
use Phalcon\Session\AdapterInterface as SessionInterface;


/***
 * Phalcon\Flash\Session
 *
 * Temporarily stores the messages in session, then messages can be printed in the next request
 **/

class Session extends FlashBase {

    /***
	 * Returns the messages stored in session
	 **/
    protected function _getSessionMessages($remove , $type  = null ) {

		$dependencyInjector = <DiInterface> $this->getDI();

		$session = <SessionInterface> dependencyInjector->getShared("session"),
			messages = session->get("_flashMessages");

		if ( gettype($type) == "string" ) {
			if ( fetch returnMessages, messages[type] ) {
				if ( remove === true ) {
					unset(messages[type]);
					session->set("_flashMessages", messages);
				}

				return returnMessages;
			}

			return [];
		}

		if ( remove === true ) {
			session->remove("_flashMessages");
		}

		return messages;
    }

    /***
	 * Stores the messages in session
	 **/
    protected function _setSessionMessages($messages ) {

		$dependencyInjector = <DiInterface> $this->getDI(),
			session = <SessionInterface> dependencyInjector->getShared("session");

		session->set("_flashMessages", messages);
		return messages;
    }

    /***
	 * Adds a message to the session flasher
	 **/
    public function message($type , $message ) {

		$messages = $this->_getSessionMessages(false);
		if ( gettype($messages) != "array" ) {
			$messages = [];
		}
		if ( !isset($messages[type]) ) {
			$messages[type] = [];
		}
		$messages[type][] = message;

		this->_setSessionMessages(messages);
    }

    /***
	 * Checks whether there are messages
	 **/
    public function has($type  = null ) {

		$messages = $this->_getSessionMessages(false);
		if ( gettype($messages) == "array" ) {
			if ( gettype($type) == "string" ) {
				return isset messages[type];
			}
			return true;
		}
		return false;
    }

    /***
	 * Returns the messages in the session flasher
	 **/
    public function getMessages($type  = null , $remove  = true ) {
		return $this->_getSessionMessages(remove, type);
    }

    /***
	 * Prints the messages in the session flasher
	 **/
    public function output($remove  = true ) {

		$messages = $this->_getSessionMessages(remove);
		if ( gettype($messages) == "array" ) {
			foreach ( type, $messages as $message ) {
				this->outputMessage(type, message);
			}
		}

		parent::clear();
    }

    /***
	 * Clear messages in the session messenger
	 **/
    public function clear() {
		this->_getSessionMessages(true);
		parent::clear();
    }

}